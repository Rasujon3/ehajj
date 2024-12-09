<?php

namespace App\Modules\REUSELicenseIssue\Services;

use App\Libraries\Encryption;
use App\Libraries\PostApiData;
use App\Modules\ProcessPath\Models\GuideRequestPilgrim;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\REUSELicenseIssue\Http\FormHandler;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PilgrimAssignByGuide extends FormHandler
{
    public function __construct(Object $processInfo)
    {

        parent::__construct($processInfo);
    }

    public function createForm(): string
    {
        $data['process_type_id'] = $this->process_type_id;
        $data['session_id'] = $this->session_id;
        return strval(view("REUSELicenseIssue::Listing.PilgrimAssignByGuide.form",$data));
    }

    public function storeForm(Request $request): RedirectResponse
    {
        $app_id = (!empty($request->get('app_id')) ? Encryption::decodeId($request->get('app_id')) : '');
        $mode = (!empty($request->get('app_id')) ? '-E-' : '-A-');
        $hajjSessionId = HajjSessions::where(['state' => 'active'])->first('id');


        if ($request->get('actionBtn') != "cancel") {

            if ($request->get('app_id')) {
                $appData = PilgrimDataList::find($app_id);
                $processData = ProcessList::where([
                    'process_type_id' => $this->process_type_id,
                    'ref_id' => $appData->id
                ])->first();
            } else {
                $appData = new PilgrimDataList();
                $processData = new ProcessList();
            }

            DB::beginTransaction();
            $checked_pid = [];
            if ($request->check_data) {
                $i = 0;
                foreach ($request->check_data as $arrKey => $data) {
                    if($request->get('actionBtn') != "submit"){
                        $previously_added = GuideRequestPilgrim::where('pid',$arrKey)
                            ->where('session_id',$hajjSessionId->id)
                            ->where('guide_id','!=',Auth::user()->hmis_guide_id)
                            ->first();
                        if(isset($previously_added)){
                            Session::flash('error', 'Already Added On other Guide with this Tracking no ' .$request->tracking_no2[$arrKey]."flight no".$previously_added->guide_id);
                            return Redirect::back()->withInput();
                        }
                    }
                    $pilgrimData2[$i]['full_name_english'] = $request->full_name_english2[$arrKey];
                    $pilgrimData2[$i]['tracking_no'] = $request->tracking_no2[$arrKey];
                    $pilgrimData2[$i]['pilgrim_id'] = $request->pilgrim_id2[$arrKey];
                    $pilgrimData2[$i]['pid'] = $request->pid2[$arrKey];
                    $pilgrimData2[$i]['guide_id'] = Auth::user()->hmis_guide_id;
                    $pilgrimData2[$i]['mobile'] = $request->mobile2[$arrKey];
                    $i++;
                    array_push($checked_pid,$arrKey);
                }
                $jsonData2 = json_encode($pilgrimData2);
            }


            if ($request->full_name_english) {
                foreach ($request->full_name_english as $arrKey => $data) {
                    $pilgrimData[$arrKey]['full_name_english'] = $data;
                    $pilgrimData[$arrKey]['tracking_no'] = $request->tracking_no[$arrKey];
                    $pilgrimData[$arrKey]['pilgrim_id'] = $request->pilgrim_id[$arrKey];
                    $pilgrimData[$arrKey]['pid'] = $request->pid[$arrKey];
                    $pilgrimData[$arrKey]['guide_id'] = Auth::user()->hmis_guide_id;
                    $pilgrimData[$arrKey]['mobile'] = $request->mobile[$arrKey];
                    if(in_array($request->pid[$arrKey],$checked_pid)){
                        $pilgrimData[$arrKey]['is_checked'] = 1;
                    }else{
                        $pilgrimData[$arrKey]['is_checked'] = 0;
                    }
                }
                $jsonData = json_encode($pilgrimData);
            }



            if ($request->check_data) {
                $appData->json_object = $jsonData;
            }


            $appData->listing_id = Auth::user()->hmis_guide_id;
            $appData->process_type = $request->process_type;
            $appData->process_type_id = $this->process_type_id;
            $appData->session_id = !empty($hajjSessionId->id) ? $hajjSessionId->id : 0;
            $appData->request_type = 'Pilgrim_Assign_By_Guide';

            $appData->save();

            $search_paramers = ['Applicant Guide Name' => Auth::user()->user_full_name];

            $processData->submitted_at = date('Y-m-d H:i:s', time());
            $processData->company_id = 0;
            $processData->locked_at = date('Y-m-d H:i:s');
            $processData->hash_value = '';
            $processData->previous_hash = '';
            $processData->process_desc = 'desc';
            $processData->pid = Str::uuid()->toString();

            if ($request->get('actionBtn') == "draft") {
                $processData->status_id = -1;
                $processData->desk_id = 0;
            } else {
                if ($processData->status_id == 5) {
                    // Get last desk and status
                    $submission_sql_param = [
                        'app_id' => $appData->id,
                        'process_type_id' => $this->process_type_id,
                    ];
                    $process_type_info = ProcessType::where('id', $this->process_type_id)
                        ->orderBy('id', 'desc')
                        ->first([
                            'form_url',
                            'process_type.process_desk_status_json',
                            'process_type.name',
                        ]);
                    $resubmission_data = $this->getProcessDeskStatus('resubmit_json', $process_type_info->process_desk_status_json, $submission_sql_param);
                    $processData->status_id = $resubmission_data['process_starting_status'];
                    $processData->desk_id = $resubmission_data['process_starting_desk'];
                    $processData->process_desc = 'Re-submitted form applicant';
                    $processData->resubmitted_at = Carbon::now(); // application resubmission Date

                    $resultData = $processData->id . '-' . $processData->tracking_no .
                        $processData->desk_id . '-' . $processData->status_id . '-' . $processData->user_id . '-' .
                        $processData->updated_by;

                    $processData->previous_hash = $processData->hash_value ?? "";
                    $processData->hash_value = Encryption::encode($resultData);
                } else {
                    $processData->status_id = 1;

                    $processData->desk_id   = 6;

                }
            }

            $processData->process_type_id = $this->process_type_id;
            $processData->priority = 1;
            $processData->ref_id = $appData->id;

            $processData->json_object = json_encode($search_paramers);
            $processData->created_at = date('Y-m-d H:i:s');


            //tracking no
            $trackingPrefix = 'PABG';
            $processTypeId = $this->process_type_id;

            $tracking_no = $trackingPrefix . strtoupper(dechex($processTypeId . $appData->id));

            $processData->tracking_no = $tracking_no;
            $processData->save();
            foreach ($pilgrimData2 as $item) {
                $guide_request_pilgrims = GuideRequestPilgrim::where('tracking_no', $item['tracking_no'])
                    ->where('pilgrim_data_list_id', $appData->id)
                    ->where('process_list_id', $processData->id)
                    ->where('session_id', $hajjSessionId->id)
                    ->first();
                if ($guide_request_pilgrims == null) {
                    $guide_request_pilgrims = new GuideRequestPilgrim();
                    $guide_request_pilgrims->pilgrim_data_list_id = $appData->id;
                    $guide_request_pilgrims->tracking_no = $item['tracking_no'];
                    $guide_request_pilgrims->process_list_id = $processData->id;
                    $guide_request_pilgrims->status = 0;
                    $guide_request_pilgrims->guide_id = Auth::user()->hmis_guide_id;
                    $guide_request_pilgrims->session_id = $hajjSessionId->id;
                    $guide_request_pilgrims->pid = $item['pid'];
                    $guide_request_pilgrims->save();
                } else {
                    $guide_request_pilgrims->pilgrim_data_list_id = $appData->id;
                    $guide_request_pilgrims->tracking_no = $item['tracking_no'];
                    $guide_request_pilgrims->process_list_id = $processData->id;
                    $guide_request_pilgrims->status = 1;
                    $guide_request_pilgrims->guide_id = Auth::user()->hmis_guide_id;
                    $guide_request_pilgrims->session_id = $hajjSessionId->id;
                    $guide_request_pilgrims->pid = $item['pid'];
                    $guide_request_pilgrims->save();
                }
            }
        } else {
            $processData = ProcessList::where([
                'process_type_id' => $this->process_type_id,
                'ref_id' => $app_id
            ])->first();
            $processData->status_id = -1;
            $processData->desk_id = 0;
            $processData->save();
        }
        DB::commit();
        return Redirect::to('/process/application_list');

        // TODO: Implement storeForm() method.
    }

    public function viewForm($processTypeId, $applicationId): JsonResponse
    {
        $appmasterId = Encryption::decodeId($applicationId);
        $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
            ->first();
        $data = array();
        $data['data'] = $pilgrimdata;
        $data['session_id'] = $this->session_id;
        $data['process_type_id'] = $processTypeId;

        $public_html = (string)view("REUSELicenseIssue::Listing.PilgrimAssignByGuide.view", $data);
        return response()->json(['responseCode' => 1, 'html' => $public_html]);

    }

    public function editForm($processTypeId, $applicationId): JsonResponse
    {
        $appmasterId = Encryption::decodeId($applicationId);
        $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
            ->first();
        $data = array();

        $data['data'] = $pilgrimdata;
        $data['session_id'] = $this->session_id;

        $data['process_type_id'] = $this->process_type_id;
        $data['guide_request_pilgrims_array'] = GuideRequestPilgrim::where('pilgrim_data_list_id', $pilgrimdata->id)->where('session_id', $data['session_id']->session_id)->pluck('pid')->toArray();
        $first_guide_request = GuideRequestPilgrim::where('pid',$data['guide_request_pilgrims_array'][0])->where('session_id', $data['session_id']->session_id)->first('tracking_no');

        $data['pilgrim_array'] = json_encode($data['guide_request_pilgrims_array']);
        $data['tracking_no'] = $first_guide_request->tracking_no;
        $public_html = (string)view("REUSELicenseIssue::Listing.PilgrimAssignByGuide.edit", $data);
        return response()->json(['responseCode' => 1, 'html' => $public_html]);
    }
}
