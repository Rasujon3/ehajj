<?php

namespace App\Modules\REUSELicenseIssue\Services;

use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\Documents\Http\Controllers\DocumentsController;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\REUSELicenseIssue\Http\FormHandler;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\REUSELicenseIssue\Models\StickerVisa\StickerPilgrims;
use App\Modules\REUSELicenseIssue\Models\StickerVisa\StickerVisaMetadata;
use App\Modules\REUSELicenseIssue\Traits\HmisApiRequest;
use App\Modules\SonaliPayment\Services\SPAfterPaymentManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StickerVisa extends FormHandler
{
    use HmisApiRequest;
    use SPAfterPaymentManager;

    public function __construct(object $processInfo)
    {
        parent::__construct($processInfo);
    }

    public function createForm(): string
    {
        $data['process_type_id'] = $this->process_type_id;
        $data['team_type'] = $this->getTeamData();
        return strval(view("REUSELicenseIssue::StickerVisa.form", $data));
    }

    public function storeForm(Request $request): RedirectResponse
    {
        if ($request->get('app_id')) {
            $appData = PilgrimDataList::find(Encryption::decodeId($request->get('app_id')));
            $processData = ProcessList::where([
                'process_type_id' => $this->process_type_id,
                'ref_id' => $appData->id
            ])->first();
        } else {
            $appData = new PilgrimDataList();
            $processData = new ProcessList();
        }

        $masterData = [
            'team_type' => $request->get('team_type'),
            'sub_team_type' => $request->get('sub_team_type'),
            'go_number' => $request->get('go_number'),
            'go_member' => $request->get('go_member'),
        ];

        # store master data
        $hajjSessionId = HajjSessions::where(['state' => 'active'])->value('id');
        $appData->process_type = $this->process_type_name;
        $appData->process_type_id = $this->process_type_id;
        $appData->json_object = json_encode($masterData);
        $appData->session_id = $hajjSessionId ?? 0;
        $appData->request_type = 'Sticker_Visa';
        $appData->save();

        if ($appData->id) {
            # store sticker pilgrims information
            $this->storeStickerPilgrims($request, $appData->id, $request->get('remaining_member', 0));
            # store process list information
            $this->storeProcessListData($request, $processData, $appData, $masterData);
            # store meta data
            $this->storeMetaData($request, $appData->id);
        }

        DB::commit();
        CommonFunction::setFlashMessageByStatusId($processData->status_id);
        return redirect('/process/list');
    }

    public function viewForm(Request $request, $applicationId): JsonResponse
    {
        $appMasterId = Encryption::decodeId($applicationId);
        $masterData = PilgrimDataList::findOrFail($appMasterId);
        if (empty($masterData)) {
            Session::flash('error', 'Something went wrong ! [SVS-007]');
            return '';
        }
        $stickerVisaData = json_decode($masterData->json_object);
        $teamData = $this->getTeamData();
        $subTeamData = $this->getSubTeamDataByTeamId($stickerVisaData->team_type);
        $stickerVisaData->team_type = $teamData[$stickerVisaData->team_type] ?? '';
        $stickerVisaData->sub_team_type = $subTeamData[$stickerVisaData->sub_team_type] ?? '';
        $stickerPilgrimsData = StickerPilgrims::where('ref_id', $appMasterId)->get();
        $data = compact('stickerPilgrimsData', 'stickerVisaData');
        $public_html = strval(view("REUSELicenseIssue::StickerVisa.form-view", $data));
        return response()->json(['responseCode' => 1, 'html' => $public_html]);
    }

    public function editForm(Request $request, $applicationId): JsonResponse
    {
        $process_type_id = $this->process_type_id;
        $appMasterId = Encryption::decodeId($applicationId);
        $appInfo = ProcessList::join('pilgrim_data_list as apps', 'apps.id', '=', 'process_list.ref_id')
            ->join('process_status as ps', function ($join) use ($process_type_id) {
                $join->on('ps.id', '=', 'process_list.status_id');
                $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
            })
            ->where('process_list.ref_id', $appMasterId)
            ->where('process_list.process_type_id', $process_type_id)
            ->first([
                'process_list.id as process_list_id',
                'process_list.desk_id',
                'process_list.process_type_id',
                'process_list.status_id',
                'process_list.locked_by',
                'process_list.locked_at',
                'process_list.ref_id',
                'process_list.tracking_no',
                'process_list.company_id',
                'process_list.process_desc',
                'process_list.submitted_at',
                'ps.status_name',
                'apps.*',
            ]);

        if (empty($appInfo)) {
            Session::flash('error', 'Something went wrong ! [SVS-007]');
            return '';
        }
        $team_type = $this->getTeamData();
        $stickerVisaData = json_decode($appInfo->json_object);
        $stickerPilgrimsData = StickerPilgrims::where('ref_id', $appMasterId)->get();
        $process_type_id = $this->process_type_id;
        $data = compact('appInfo', 'stickerPilgrimsData', 'stickerVisaData', 'process_type_id', 'team_type', 'appMasterId');
        $public_html = strval(view("REUSELicenseIssue::StickerVisa.form-edit", $data));
        return response()->json(['responseCode' => 1, 'html' => $public_html]);
    }

    private function storeStickerPilgrims(Request $request, $appDataId, $totalGoMember = 0)
    {
        $stickerPilgrims = $request->input('name', []);
        $totalEntryMembers = count($stickerPilgrims);
        if ($totalEntryMembers === 0) {
            return false;
        }
        if ($totalGoMember < $totalEntryMembers) {
            $remainingMembers = $totalEntryMembers - $totalGoMember;
            array_splice($stickerPilgrims, -$remainingMembers);
        }

        $stickerPilgrimsData = [];
        // Delete existing sticker pilgrim data
        StickerPilgrims::where('ref_id', $appDataId)->delete();

        foreach ($stickerPilgrims as $index => $pilgrimName) {
            $stickerPilgrimsData[] = [
                'name' => $pilgrimName,
                'ref_id' => $appDataId,
                'dob' => $request->dob[$index] ? date('Y-m-d', strtotime($request->dob[$index])) : null,
                'gender' => $request->input('gender.' . $index),
                'identity_type' => $request->input('identity_type.' . $index, 'NONE'),
                'identity_no' => $request->input('identity_no.' . $index),
                'passport_type' => $request->input('passport_type.' . $index, 'NONE'),
                'passport_no' => $request->input('passport_no.' . $index),
                'passport_dob' => $request->passport_dob[$index] ? date('Y-m-d', strtotime($request->passport_dob[$index])) : null,
                'mobile_no' => $request->input('mobile_no.' . $index),
                'go_serial_no' => $request->input('go_serial_no.' . $index, 0),
                'created_at' => now(),
                'created_by' => Auth::id(),
            ];
        }
        StickerPilgrims::insert($stickerPilgrimsData);
    }

    private function storeProcessListData(Request $request, $processListObj, $appData, $masterData = [])
    {
        $processListObj->company_id = 0;
        //Set category id for process differentiation
        $processListObj->cat_id = 1;
        if ($request->get('actionBtn') === 'draft') {
            $processListObj->status_id = self::DRAFT_STATUS_ID;
            $processListObj->desk_id = 0;
        } elseif ($processListObj->status_id === self::SHORTFALL_STATUS_ID) {
            // For shortfall
            $submission_sql_param = [
                'app_id' => $appData->id,
                'process_type_id' => $this->process_type_id,
            ];

            $process_type_info = ProcessType::where('id', $this->process_type_id)
                ->orderBy('id', 'desc')
                ->first([
                    'form_url',
                    'process_type.process_desk_status_json',
                    'process_type.name'
                ]);

            $resubmission_data = $this->getProcessDeskStatus('resubmit_json', $process_type_info->process_desk_status_json, $submission_sql_param);
            $processListObj->status_id = $resubmission_data['process_starting_status'];
            $processListObj->desk_id = $resubmission_data['process_starting_desk'];
            $processListObj->process_desc = 'Re-submitted form applicant';
            $processListObj->resubmitted_at = Carbon::now(); // application resubmission Date

            $resultData = "{$processListObj->id}-{$processListObj->tracking_no}{$processListObj->desk_id}-{$processListObj->status_id}-{$processListObj->user_id}-{$processListObj->updated_by}";

            $processListObj->previous_hash = $processListObj->hash_value ?? '';
            $processListObj->hash_value = Encryption::encode($resultData);

        } else {
            $processListObj->status_id = self::SUBMITTED_STATUS_ID;
            $processListObj->desk_id = self::APPROVED_DESK_ID;
        }
        # generate tracking no
        if ($request->get('actionBtn') == 'submit') {
            if (empty($processListObj->tracking_no)) {
                $trackingPrefix = 'SV-';
                $processListObj->tracking_no = $trackingPrefix . strtoupper(dechex($this->process_type_id . $appData->id));
            }
        }

        $processListObj->ref_id = $appData->id;
        $processListObj->process_type_id = $this->process_type_id;
        $processListObj->office_id = 0;
        $processListObj['json_object'] = json_encode($masterData);
        $processListObj->submitted_at = Carbon::now();
        $processListObj->save();
        return $processListObj;
    }

    private function storeMetaData(Request $request, $masterId)
    {
        StickerVisaMetadata::where('ref_id', $masterId)->delete();
        $metaData = new StickerVisaMetadata();
        $metaData->ref_id = $masterId;
        $metaData->team_type = $request->get('team_type');
        $metaData->team_sub_type = $request->get('sub_team_type');
        $metaData->go_number = $request->get('go_number');
        $metaData->go_members = $request->get('go_member');
        $metaData->go_entry_members = count($request->input('name', []));
        $metaData->created_by = Auth::id();
        $metaData->created_at = now();
        $metaData->save();
    }

    public static function getStickerVisaGoMemberInfo(Request $request,$isAjaxRequest = false){
        $process_type_id = 8 ; // 8 = Sticker Visa
        $go_number = $request->get('go_number');
        $ref_id = $request->get('ref_id',null);
        $stickerGoMembersInfo = DB::table('sticker_visa_metadata as svm')
                ->join('process_list',function ($query) use ($process_type_id) {
                    $query->on('process_list.ref_id','=','svm.ref_id')
                        ->where('process_list.process_type_id',$process_type_id)
                        ->whereNotIn('process_list.status_id',[self::REJECTED_STATUS_ID]);
                })
                ->when(!empty($ref_id),function ($query) use ($ref_id) {
                    $query->where('svm.ref_id','!=',$ref_id);
                })
                ->groupBy('svm.go_number')
                ->where('svm.go_number',$go_number)
                ->select(DB::raw("svm.go_number,svm.go_members, (svm.go_members - SUM(svm.go_entry_members)) as  remainingMembers"))->first();

        if (!$isAjaxRequest){
            return $stickerGoMembersInfo;
        }
        if (empty($stickerGoMembersInfo)){
            return response()->json(['responseCode' => 0],200);
        }
        return response()->json([
            'responseCode' => 1,
            'stickerVisaGoMemberInfo' => $stickerGoMembersInfo,
            'encryptedRemainingMembers' => Encryption::encodeId($stickerGoMembersInfo->remainingMembers)
        ],200);
    }
}
