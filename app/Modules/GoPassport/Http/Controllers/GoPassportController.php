<?php namespace App\Modules\GoPassport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\InsightDbApiManager;
use App\Libraries\PostApiData;
use App\Modules\GoPassport\Models\TempPassportDelivery;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\REUSELicenseIssue\Models\StickerVisa\StickerPilgrims;
use App\Modules\REUSELicenseIssue\Models\StickerVisa\StickerVisaMetadata;
use App\Modules\REUSELicenseIssue\Http\ProcessConstantsId;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use yajra\Datatables\Datatables;
use App\Modules\REUSELicenseIssue\Services\StickerVisa;
use App\Modules\REUSELicenseIssue\Traits\HmisApiRequest;
use Illuminate\Support\Facades\DB;
use App\Modules\GoPassport\Models\StickerPassportReturn;


class GoPassportController extends Controller implements ProcessConstantsId
{
    use HmisApiRequest;
    protected $api_url;

    public function __construct()
    {
        $this->api_url =  env('API_URL');
    }

    public function indexGo(Request $request, $encoded_process_type_id)
    {
        try {
            $decoded_process_type_id = Encryption::decodeId($encoded_process_type_id);
            $process_info = ProcessType::where('id', $decoded_process_type_id)->first([
                'id as process_type_id',
                'acl_name',
                'form_id',
                'name'
            ]);
            $process_type_id = Encryption::encodeId( $process_info->process_type_id);
            $team_type = $this->getTeamData();
            $type_key = $process_info->acl_name;

            return view("GoPassport::list", compact('process_info', 'type_key', 'team_type', 'process_type_id'));
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[PCC-1001]');
            return redirect()->back();
        }

    }

    public function getGoList(Request $request)
    {
        try {
            $process_type_id_app = Encryption::decodeId( $request->get('process_type_id'));
            if($process_type_id_app){
                $process_type_id = $process_type_id_app;
            }
            $hajjSession = HajjSessions::where('state', 'active')->value('id');
            $list = StickerVisaMetadata::leftJoin('pilgrim_data_list', 'sticker_visa_metadata.ref_id' , '=','pilgrim_data_list.id')
                ->where([
                    'pilgrim_data_list.process_type_id' => $process_type_id,
                    'pilgrim_data_list.request_type' => $request->get('type_key'),
                    'pilgrim_data_list.session_id' => $hajjSession
                ])

                ->orderby('sticker_visa_metadata.id','desc')
                ->get([
                    'sticker_visa_metadata.*',
                    'sticker_visa_metadata.ref_id as refId',
                    'sticker_visa_metadata.updated_at',
                    'pilgrim_data_list.process_type_id as process_type_id',
                    'pilgrim_data_list.json_object'
                ]);

            #get team type data from API Call
            $get_team_type = $this->getTeamData();
            $get_team_sub_type = $this->getTeamSubTypeData();

            return Datatables::of($list)
                ->addColumn('action', function ($list) use ($request) {

                    $html = '<a href="' . url('/go-passport/edit/' . Encryption::encodeId($list->refId) . '/' . Encryption::encodeId($list->process_type_id)) . '"
                              class="btn btn-xs btn-outline-success">
                              <i class="fa fa-box-open"></i> Open
                            </a> &nbsp;';
                    return $html;
                })
                ->editColumn('pilgrims', function ($list) {
                    $pilgrims = StickerPilgrims::where('ref_id','=',$list->refId)->where('is_archived','=',0)->count();
                    return $pilgrims;
                })
                ->editColumn('team_type', function ($list) use($get_team_type) {
                    if($get_team_type && $list->team_type != 0){
                        $team_type = !empty($get_team_type[$list->team_type]) ? $get_team_type[$list->team_type] : "";
                    }else{
                        $team_type = 'GO';
                    }
                    return $team_type;
                })
                ->editColumn('team_sub_type', function ($list) use($get_team_sub_type) {
                    $jsonArr = json_decode($list->json_object, true);
                    if($jsonArr['sub_team_type'] && $jsonArr['sub_team_type'] != 0){
                        $team_type = !empty($get_team_sub_type[$jsonArr['sub_team_type']]) ? $get_team_sub_type[$jsonArr['sub_team_type']] : "";
                    }else{
                        $team_type = 'N/A';
                    }
                    return $team_type;
                })
                ->editColumn('updated_at', function ($list) {
                    $time = $list->updated_at;
                    $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $time)->format('d-M-Y');
                    return $updated_at;
                })
                ->editColumn('go_date', function ($list) {
                    $time = $list->go_date;
                    if($time){
                        $go_date = Carbon::createFromFormat('Y-m-d', $time)->format('d-M-Y');
                    }else{
                        $go_date = "N/A";
                    }
                    return $go_date;
                })
                ->addIndexColumn('DT_RowIndex')
                ->rawColumns(['action','DT_RowIndex'])
                ->make(true);
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[PCC-1002]');
            return redirect()->back();
        }
    }

    public function storeGoPassport(Request $request,$process_type_id)
    {
        try {

            if (empty($process_type_id) || $process_type_id == null ) {
                Session::flash('error', 'Invalid Process Type Id');
                return redirect()->back();
            }

            if (empty($request->get('team_type'))) {
                Session::flash('error', 'Sorry! Fill Required Field');
                return redirect()->back();
            }

            if (empty($request->get('go_number')) && empty($request->get('go_member')) ) {
                Session::flash('error', 'Sorry! GO number & member is required');
                return redirect()->back();
            }


            if (!empty($request->get('fee_applicable')) && $request->get('fee_applicable') == "yes" ) {
                if (empty($request->get('payable_amount'))) {
                    Session::flash('error', 'Sorry! Payable amount is required');
                    return redirect()->back();
                }
            }

            DB::beginTransaction();
            // store meta table data ...........
            $decoded_process_type_id = Encryption::decodeId($process_type_id);
            $process_info = ProcessType::where('id', $decoded_process_type_id)->first([
                'id as process_type_id',
                'acl_name',
                'form_id',
                'name'
            ]);

            $appData = new PilgrimDataList();

            $masterData = [
                'team_type' => $request->get('team_type'),
                'sub_team_type' => $request->get('sub_team_type'),
                'go_number' => $request->get('go_number'),
                'go_member' => $request->get('go_member'),
                'fee_applicable' => $request->get('fee_applicable'),
                'payable_amount' => $request->get('payable_amount'),
            ];
            # store master data
            $hajjSessionId = HajjSessions::where(['state' => 'active'])->value('id');
            $appData->process_type = $process_info->name;
            $appData->process_type_id = $process_info->process_type_id;
            $appData->json_object = json_encode($masterData);
            $appData->session_id = $hajjSessionId ?? 0;
            $appData->request_type = 'Sticker_Visa';
            $appData->save();

            if ($appData->id) {
                $metaData = new StickerVisaMetadata();
                $metaData->ref_id = $appData->id;
                $metaData->team_type = $request->get('team_type');
                $metaData->team_sub_type = $request->get('sub_team_type');
                $metaData->go_number = $request->get('go_number');
                $metaData->go_members = $request->get('go_member');
                $metaData->go_date = $request->get('go_date') ? date('Y-m-d', strtotime($request->get('go_date'))) : null;
                $metaData->fee_applicable = $request->get('fee_applicable');
                $metaData->payable_amount = $request->get('payable_amount');
                $metaData->created_by = Auth::id();
                $metaData->created_at = now();
                $metaData->save();
            }

            Session::flash('success','GO Passport has been created successfully !!!');
            DB::commit();
            //return redirect('/go-passport/list/' . ($process_type_id));
            $encode_ref_id = Encryption::encodeId($appData->id);
            return redirect('/go-passport/edit/'.($encode_ref_id).'/' . ($process_type_id));
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[PCC-1003]');
            return redirect()->back();
        }
    }

    public function editGoPassport(Request $request,$refId, $process_type_id)
    {
        try {
            if (empty($process_type_id) || $process_type_id == null) {
                Session::flash('error', 'Invalid Process Type Id :[PCC 1000]');
                return redirect()->back();
            }
            if (empty($refId) || $refId == null) {
                Session::flash('error', 'Invalid Ref Id :[PCC 1001]');
                return redirect()->back();
            }
            $encode_process_type_id = $process_type_id;
            $decode_process_type_id = Encryption::decodeId($encode_process_type_id);
            $encode_ref_id = $refId;
            $refId = Encryption::decodeId($encode_ref_id);
            $viewMode = 'edit';
            $appInfo = PilgrimDataList::where('id', $refId)
                ->where('process_type_id', $decode_process_type_id)
                ->first();

            if (empty($appInfo)) {
                Session::flash('error', 'Something went wrong ! [PCC-007]');
                return redirect()->back();
            }
            $processListCheck = ProcessList::where('ref_id', $refId)
                ->where('process_type_id', $decode_process_type_id)
                ->whereIn('status_id', [1, 25])
                ->first();
            $goMemberCheck = StickerVisaMetadata::where('ref_id', $refId)->first();

            $stickerVisaData = json_decode($appInfo->json_object);
            $stickerPilgrimsData = StickerPilgrims::where('ref_id', $refId)
                ->where('is_archived', 0)
                ->where('pid','!=', null)
                ->where('pilgrim_tracking_no','!=', null)
                ->get();

            $team_type = '';
            $team_sub_type = '';
            #get team type data from API Call
            $team_type = $this->getTeamData();
            if ($team_type) {
                $teamId = isset($stickerVisaData->team_type) ? $stickerVisaData->team_type: '';
                $teamSubTypeId = isset($stickerVisaData->sub_team_type) ? $stickerVisaData->sub_team_type: '';
                if (!isset($stickerVisaData->team_type) || empty($teamId) || $teamId == '') {
                    Session::flash('error', 'Invalid team type');
                    return redirect()->back();
                }
                // if (!isset($stickerVisaData->sub_team_type) || empty($teamSubTypeId) || $teamSubTypeId == '') {
                //     Session::flash('error', 'Invalid team sub type');
                //     return redirect()->back();
                // }
                # get team sub type data from API Call
                if($teamSubTypeId != ''){
                    $get_team_sub_type = json_decode($this->getSubTeamDataByTeamId($teamId, true), true);
                    if(count($get_team_sub_type['data']) == 0){
                        $team_sub_type = 'no';
                    }else{
                        $team_sub_type = array_filter($get_team_sub_type['data'], function ($item) use ($teamSubTypeId) {
                            return $item['id'] == $teamSubTypeId;
                        });
                        $team_sub_type = array_values(array_column($team_sub_type, 'name'));
                    }

                }else{
                    $team_sub_type = 'no';
                }
            }
            $data = compact('goMemberCheck','appInfo','team_sub_type','processListCheck', 'stickerPilgrimsData','viewMode', 'stickerVisaData', 'encode_process_type_id', 'team_type', 'encode_ref_id');
            return view("GoPassport::form-edit", $data);
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[PCC-1008]');
            return redirect()->back();
        }
    }

    public function updateGoPassport(Request $request,$encode_ref_id,$encode_process_type_id)
    {
        try {
            $refId = Encryption::decodeId($encode_ref_id);
            $stickerPilgrimsData = StickerPilgrims::where('ref_id', $refId)->where('is_archived','=',0)->get();
            $additionalData = [];
            if(count($stickerPilgrimsData) <= 0){
                if (empty($request->get('team_type')) && empty($request->get('sub_team_type'))) {
                    Session::flash('error', 'Sorry! Fill Required Field');
                    return redirect()->back();
                }

                if (empty($request->get('go_number')) && empty($request->get('go_member'))) {
                    Session::flash('error', 'Sorry! GO number & member is required');
                    return redirect()->back();
                }
            }
            if (empty($request->get('go_member'))) {
                Session::flash('error', 'Sorry! GO member is required');
                return redirect()->back();
            }
            if (!empty($request->get('fee_applicable')) && $request->get('fee_applicable') == "yes" ) {
                if (empty($request->get('payable_amount'))) {
                    Session::flash('error', 'Sorry! Payable amount is required');
                    return redirect()->back();
                }
            }

            if (empty($encode_process_type_id) || $encode_process_type_id == null ) {
                Session::flash('error', 'Invalid Process Type Id');
                return redirect()->back();
            }
            DB::beginTransaction();
            // fetch sub_team_type
            $teamType = !empty($request->get('team_type')) ? $request->get('team_type') : 0;
            $subTeamDataArr = [];
            if($teamType) {
                $subTeamData = $this->getSubTeamDataByTeamId($teamType,true);
                $subTeamDataArr = json_decode($subTeamData, true)['data'];
            }

            // store meta table data ...........
            $decoded_process_type_id = Encryption::decodeId($encode_process_type_id);
            $decoded_ref_id = Encryption::decodeId($encode_ref_id);
            $metaData = StickerVisaMetadata::where('ref_id', '=', $decoded_ref_id)->first();
            $checkaddedPilgrim = StickerPilgrims::where('ref_id', '=', $decoded_ref_id)->where('is_archived',0)->get();
            $process_info = ProcessType::where('id', $decoded_process_type_id)->first([
                'id as process_type_id',
                'acl_name',
                'form_id',
                'name'
            ]);
            $chk_sub_type = $request->get('sub_team_type');
            if((empty($chk_sub_type) || $chk_sub_type == null || $chk_sub_type == '' ) && $request->get('go_number') == null){
                $additionalData = [
                    'sub_team_type' => $metaData->team_sub_type,
                    'go_number' => $metaData->go_number,
                ];
            }elseif((empty($chk_sub_type) || $chk_sub_type == null || $chk_sub_type == '' ) && $request->get('go_number') != null){
                if($teamType && count($subTeamDataArr) > 0) {
                    $additionalData = [
                        'sub_team_type' => $metaData->team_sub_type,
                        'go_number' => $metaData->go_number,
                    ];
                } else {
                    $additionalData = [
                        'sub_team_type' => '',
                        'go_number' => $request->get('go_number'),
                    ];
                }
            } else{
                $additionalData = [
                    'sub_team_type' => $request->get('sub_team_type'),
                    'go_number' => $request->get('go_number'),
                ];
            }

            $appData = PilgrimDataList::where(['id' => $decoded_ref_id, 'process_type_id' => $decoded_process_type_id])->first();
            $masterData = [
                'team_type' => !empty($request->get('team_type')) ? $request->get('team_type') : $metaData->team_type ,
                'go_member' => $request->get('go_member'),
                'fee_applicable' => $request->get('fee_applicable'),
                'payable_amount' => $request->get('payable_amount'),
            ];
            $mergedData = array_merge($masterData, $additionalData);
            # store master data
            $hajjSessionId = HajjSessions::where(['state' => 'active'])->value('id');
            $appData->process_type = $process_info->name;
            $appData->process_type_id = $process_info->process_type_id;
            $appData->json_object = json_encode($mergedData);
            // if(count($stickerPilgrimsData) <= 0) {
            //     $appData->json_object = json_encode($mergedData);
            // }else{
            //     $appData->json_object = json_encode($masterData);
            // }
            $appData->session_id = $hajjSessionId ?? 0;
            $appData->request_type = 'Sticker_Visa';
            $appData->save();

            if ($appData->id) {

                $metaData->team_type = !empty($request->get('team_type')) ? $request->get('team_type') : $metaData->team_type;
                if((!empty($request->get('sub_team_type')) || $request->get('sub_team_type') != null || $request->get('sub_team_type') != "") && $teamType && count($checkaddedPilgrim) <= 0) {
                    $metaData->team_sub_type = $request->get('sub_team_type');
                } elseif ($teamType && count($subTeamDataArr) > 0) {
                    $metaData->team_sub_type =  $metaData->team_sub_type;
                } elseif($teamType && count($checkaddedPilgrim) <= 0) {
                    $metaData->team_sub_type = 0;
                } else {
                    $metaData->team_sub_type = $metaData->team_sub_type;
                }

                if(!empty($request->get('go_number')) || $request->get('go_number') != null ) {
                    $metaData->go_number = $request->get('go_number');
                }
                $metaData->go_members = $request->get('go_member');
                $metaData->go_date = $request->get('go_date') ? date('Y-m-d', strtotime($request->get('go_date'))) : null;
                $metaData->fee_applicable = $request->get('fee_applicable');
                $metaData->payable_amount = $request->get('payable_amount');
                $metaData->updated_by = Auth::id();
                $metaData->updated_at = now();
                $metaData->save();

            }

            Session::flash('success','GO Passport has been updated successfully !!!');
            DB::commit();
            return redirect()->back();
            //return redirect('/go-passport/edit/'.($encode_ref_id).'/' . ($encode_process_type_id));
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Sorry!' . $e->getMessage() . '[PCC-1003]');
            return redirect()->back();
        }
    }

    public function storeStickerPilgrims(Request $request,$encode_ref_id,$encode_process_type_id)
    {
        try{
            if (empty($encode_ref_id) || $encode_ref_id == null ) {
                Session::flash('error', 'Sorry! Invalid Ref Id');
                return redirect()->back();
            }
            if (empty($encode_process_type_id) || $encode_process_type_id == null ) {
                Session::flash('error', 'Sorry! Invalid Process Type');
                return redirect()->back();
            }
            $decoded_process_type_id = Encryption::decodeId($encode_process_type_id);
            $decoded_ref_id = Encryption::decodeId($encode_ref_id);
            $object = Encryption::api_decode($request->get('requestData'));
            $decodeObject = (json_decode($object));
            if (empty($decodeObject) || $decodeObject == null ) {
                Session::flash('error', 'Invalid Passport Information');
                return redirect()->back();
            }
            if (empty($decoded_ref_id) || $decoded_ref_id == null ) {
                Session::flash('error', 'Invalid Ref Id !!!');
                return redirect()->back();
            }
            if (empty($request->get('pass_name')) || $request->get('pass_name') == null ) {
                Session::flash('error', 'Required Name');
                return redirect()->back();
            }

            if ($decodeObject->passport_type != $request->get('selected_passport_type')) {
                Session::flash('error', ' Passport type not match');
                return redirect()->back();
            }

            if ($decodeObject->dob != $request->get('passport_dob')) {
                Session::flash('error', ' Invalid passport birth date');
                return redirect()->back();
            }
            if ($decodeObject->passport_no != $request->get('passport_no')) {
                Session::flash('error', 'Invalid passport no');
                return redirect()->back();
            }
            if (empty($request->get('mobile_no')) || $request->get('mobile_no') == null ) {
                Session::flash('error', 'Mobile number is required ');
                return redirect()->back();
            }

            $mobile_no_validate = CommonFunction::validateMobileNumber($request->get('mobile_no'));
            if ($mobile_no_validate != 'ok') {
                Session::flash('error', $mobile_no_validate.' [PP:001]');
                return redirect()->back();
            }

            $checkDuplicatePassportEntry = StickerPilgrims::where([
                'ref_id' => $decoded_ref_id,
                'is_archived' => 0,
                'passport_no' => $request->get('passport_no'),
            ])->count();

            if ($checkDuplicatePassportEntry >0 ) {
                Session::flash('error', 'এই পাসপোর্ট নম্বরটি ইতিমধ্যে জিও সিস্টেমে এন্ট্রি করা হয়েছে');
                return redirect()->back();
            }

            $this->validatePassportNo($request->get('selected_passport_type'), $request->get('passport_no'));

            DB::beginTransaction();
            $masterData = PilgrimDataList::where('id',$decoded_ref_id)->first();
            $json_data = json_decode($masterData->json_object);

            $postPilgrimsData = [];
            $postPilgrimsData = [
                'team_type' => $json_data->team_type,
                'team_sub_type' =>isset($json_data->sub_team_type) ? $json_data->sub_team_type: '' ,
                'go_number' => $json_data->go_number,
                'gender' => $request->get('gender'),
                'passport_type' => $request->get('selected_passport_type'),
                'passport_no' => $request->get('passport_no'),
                'dob' => $request->get('passport_dob') ? date('Y-m-d', strtotime($request->get('passport_dob'))) : null,
                'mobile' => $request->get('mobile_no'),
                'go_serial_no' => $request->get('go_serial_no'),
                'user_sub_type' => Auth::user()->user_sub_type,
                'user_type' => Auth::user()->user_type,
                'prp_user_id' => Auth::user()->prp_user_id,
            ];

            $ApiRequestToStore = $this->GOPilgrimApiRequest($postPilgrimsData);
            if(!$ApiRequestToStore){
                DB::rollBack();
                return redirect()->back();
            }else {
                $goPilgrimsData = [];
                $goPilgrimsData = [
                    'name' => $ApiRequestToStore->data->passport_response->pass_name,
                    'ref_id' => $decoded_ref_id,
                    'gender' => $ApiRequestToStore->data->passport_response->gender,
                    'passport_type' => $request->get('selected_passport_type'),
                    'identity_type' => 'PASSPORT',
                    'passport_no' => $ApiRequestToStore->data->passport_response->passport_no,
                    'passport_dob' => $ApiRequestToStore->data->passport_response->pass_dob ? date('Y-m-d', strtotime($ApiRequestToStore->data->passport_response->pass_dob)) : null,
                    'mobile_no' => $request->get('mobile_no'),
                    'go_serial_no' => $request->get('go_serial_no'),
                    'created_at' => now(),
                    'created_by' => Auth::id(),
                    'amount' => $request->get('amount'),
                    'taka_received_date' => $request->get('taka_received_date') ? date('Y-m-d', strtotime($request->get('taka_received_date'))) : null,
                    'referance_no' => $request->get('referance_no'),
                    'is_archived' => 0,
                    'passport_response' => json_encode($ApiRequestToStore->data->passport_response),
                    'pid' => $ApiRequestToStore->data->pid,
                    'pilgrim_tracking_no' => $ApiRequestToStore->data->pilgrim_tracking_no,
                ];
                StickerPilgrims::create($goPilgrimsData);

                $go_entry_members = StickerPilgrims::where('ref_id', $decoded_ref_id)->where('is_archived','=',0)->count();
                $metaData = StickerVisaMetadata::where('ref_id', '=', $decoded_ref_id)->first();
                $metaData->go_entry_members = $go_entry_members;
                $metaData->save();
            }

            Session::flash('success','GO Passport Member has been inserted successfully !!!');
            DB::commit();
            return redirect('/go-passport/edit/'.($encode_ref_id).'/' . ($encode_process_type_id));
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[PCC-1007]');
            return redirect()->back();
        }
    }

    public function validatePassportNo($passportType, $passportNo)
    {
        $error = null;

        if ($passportType === 'E-PASSPORT' && (!preg_match('/^[A-Z]{1}[0-9]{8}$/', $passportNo) || strlen($passportNo) !== 9)) {
            $error = 'Please enter a valid Passport No';
        } elseif ($passportType === 'MRP' && (!preg_match('/^[A-Z]{2}[0-9]{7}$/', $passportNo) || strlen($passportNo) !== 9)) {
            $error = 'Please enter a valid Passport No';
        }
        Session::flash('error', $error);
        return redirect()->back();
    }

    public function deleteMember(Request $request, $id,$encode_ref_id,$encode_process_type_id)
    {
        try{
            if (empty($id) || $id == null ) {
                Session::flash('error', 'Sorry! Invalid Id');
                return redirect()->back();
            }
            $decoded_stickerPilgrim_id = Encryption::decodeId($id);
            DB::beginTransaction();

            $getGoPligrimData = StickerPilgrims::where('id','=',$decoded_stickerPilgrim_id)->where('is_archived','=',0)->first();
            if(!$getGoPligrimData){
                Session::flash('error', 'Sorry! Pilgrim Already Archived.');
                return redirect()->back();
            }
            $postData = [
                'pid' => $getGoPligrimData->pid,
                'tracking_no' => $getGoPligrimData->pilgrim_tracking_no,
                'passport_no' => $getGoPligrimData->passport_no
            ];
            $token = $this->getTokenData();
            if (!$token) {
                Session::flash('error', 'Sorry! Invalid Token.');
                return redirect()->back();
            }
            $apiUrl = env('API_URL')."/api/archived-go-pilgrim-data";
            $headers = [
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/x-www-form-urlencoded',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers);

            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponse['http_code'] !== 200) {
                Session::flash('error', 'Sorry! Pilgrim not Archived ');
                return redirect()->back();
            }

            if ($apiResponseDataArr->status !== 200) {
                Session::flash('error', 'Sorry! Pilgrim not Archived.');
                return redirect()->back();
            }
            StickerPilgrims::where('id','=',$decoded_stickerPilgrim_id)->update([
                'is_archived' => 1,
                'archived_reason' => $request->get('archived_reason'),
            ]);
            DB::commit();
            Session::flash('success', 'Member has been removed successfully !!');
            return redirect('/go-passport/edit/'.($encode_ref_id).'/' . ($encode_process_type_id));
        } catch (\Exception $e) {
        DB::rollBack();
        Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[PP-104]');
        return redirect()->back();
        }
    }

    public function editMember($id)
    {
        try{
            if (empty($id) || $id == null ) {
                Session::flash('error', 'Sorry! Invalid Id');
                return redirect()->back();
            }
            $decoded_stickerPilgrim_id = Encryption::decodeId($id);

            $membersInfo = StickerPilgrims::where('id','=',$decoded_stickerPilgrim_id)->first();
            $taka_received_date = date('d-M-Y', strtotime($membersInfo->taka_received_date));
            $membersVisaMetadata = StickerVisaMetadata::where('ref_id','=',$membersInfo->ref_id)->first();
            $getRefProcessId = PilgrimDataList::where('id',$membersInfo->ref_id)->first();
            $encode_ref_id = Encryption::encodeId($membersInfo->ref_id);
            $encode_process_type_id = Encryption::encodeId($getRefProcessId->process_type_id);

            $picture = 'data:image/jpeg;base64,' . (json_decode($membersInfo->passport_response)->passportPhoto);

            if(empty($membersInfo)){
                return response()->json(['responseCode' => -1, 'html' => '','msg'=>'Please select a date first!!!']);
            }

            $html = strval(view("GoPassport::modal.edit-member-modal-content", compact('membersVisaMetadata','membersInfo','picture','id','encode_ref_id','encode_process_type_id', 'taka_received_date')));
            return response()->json(['responseCode' => 1, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['responseCode' => -1, 'html' => '','msg'=>'Sorry! Something is Wrong.']);
        }
    }

    public function getDeleteModal($id){
        try{
            if (empty($id) || $id == null ) {
                Session::flash('error', 'Sorry! Invalid Id');
                return redirect()->back();
            }
            $decoded_stickerPilgrim_id = Encryption::decodeId($id);
            $membersInfo = StickerPilgrims::where('id','=',$decoded_stickerPilgrim_id)->first();
            $getRefProcessId = PilgrimDataList::where('id',$membersInfo->ref_id)->first();
            $encode_ref_id = Encryption::encodeId($membersInfo->ref_id);
            $encode_process_type_id = Encryption::encodeId($getRefProcessId->process_type_id);
            if(empty($membersInfo)){
                return response()->json(['responseCode' => -1, 'html' => '','msg'=>'Data not found!!!']);
            }

            $html = strval(view("GoPassport::modal.delete-modal-content", compact('id','encode_ref_id','encode_process_type_id')));
            return response()->json(['responseCode' => 1, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['responseCode' => -1, 'html' => '','msg'=>'Sorry! Something is Wrong.']);
        }
    }

    public function updateMemberEntryPassport(Request $request,$id,$encode_ref_id,$encode_process_type_id)
    {
        try{
            if (empty($id) || $id == null ) {
                Session::flash('error', 'Sorry! Invalid Id');
                return redirect()->back();
            }
            $decoded_stickerPilgrim_id = Encryption::decodeId($id);

            if (empty($request->get('mobile_no')) || $request->get('mobile_no') == null ) {
                Session::flash('error', 'Sorry! Mobile number is required ');
                return redirect()->back();
            }

            $mobile_no_validate = CommonFunction::validateMobileNumber($request->get('mobile_no'));
            if ($mobile_no_validate != 'ok') {
                Session::flash('error', $mobile_no_validate.' [PP:001]');
                return redirect()->back();
            }

            $getPilgrimInfo = StickerPilgrims::where('id','=',$decoded_stickerPilgrim_id)->first();
            $postData = [
                'pid' => $getPilgrimInfo->pid,
                'tracking_no' => $getPilgrimInfo->tracking_no,
                'passport_no' => $getPilgrimInfo->passport_no,
                'go_serial_no' => $request->get('go_serial_no'),
                'mobile_no' => $request->get('mobile_no'),
            ];
            $token = $this->getTokenData();
            if (!$token) {
                Session::flash('error', 'Sorry! Invalid Token.');
                return redirect()->back();
            }
            $apiUrl = env('API_URL')."/api/update-go-pilgrim-data";
            $headers = [
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/x-www-form-urlencoded',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if($apiResponseDataArr == null){
                Session::flash('error', $apiResponseDataArr->msg.' [PP:005]');
                return redirect()->back();
            }
            if ($apiResponse['http_code'] !== 200) {
                Session::flash('error', $apiResponseDataArr->msg.' [PP:006]');
                return redirect()->back();
            }
            if ($apiResponseDataArr->status !== 200 ) {
                Session::flash('error', $apiResponseDataArr->msg.' [PP:007]');
                return redirect()->back();
            }

            DB::beginTransaction();
            $updateMembersData = StickerPilgrims::where('id','=',$decoded_stickerPilgrim_id)->update([
                'go_serial_no' => $request->get('go_serial_no'),
                'mobile_no' => $request->get('mobile_no'),
                'amount' => $request->get('amount'),
                'taka_received_date' => $request->get('taka_received_date') ? date('Y-m-d', strtotime($request->get('taka_received_date'))) : null,
                'referance_no' => $request->get('referance_no'),
            ]);
            if(!$updateMembersData){
                DB::rollBack();
                Session::flash('error', 'Data No save');
                return redirect()->back();
            }
            Session::flash('success','Update GO Passport Member Successfully !!!');
            DB::commit();
            return redirect('/go-passport/edit/'.($encode_ref_id).'/' . ($encode_process_type_id));
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[PCC-1008]');
            return redirect()->back();
        }
    }

    public function getTokenData(){
        $tokenUrl = "$this->api_url/api/getToken";
        $credential = [
            'clientid' => env('CLIENT_ID'),
            'username' => env('CLIENT_USER_NAME'),
            'password' => env('CLIENT_PASSWORD')
        ];

        return CommonFunction::getApiToken($tokenUrl, $credential);
    }

    /*
     * This passportVerifyRequest Function are used multiple time and multiple file
     * */
    public function passportVerifyRequest(Request $request)
    {
        try {
            $token = $this->getTokenData();
            if (!$token) {
                $msg = 'Failed to generate API Token!!!';
                Session::flash('error', $msg);
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$this->api_url/api/passport-verify";
            $post_data['dob'] = $request->get('dob');
            $post_data['passport_no'] = $request->get('passport_no');
            $post_data['passport_type'] = $request->get('passport_type');
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/x-www-form-urlencoded',
            );
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $post_data, $headers);
            $apiResponseDataArr = json_decode($apiResponse['data']);

            if($apiResponseDataArr == null){
                $msg = 'দয়া করে সঠিকভাবে সকল তথ্য পূরণ করুন';
                return response()->json(['responseCode' => -1, 'msg' => $msg, 'status' => null]);
            }
            if ($apiResponse['http_code'] != 200) {
                $msg = $apiResponseDataArr->msg ;
                $status = null;
                if(!empty($apiResponseDataArr->data->responseCode) && $apiResponseDataArr->data->responseCode == -1){
                    $status = $apiResponseDataArr->data->status;
                }
                return response()->json(['responseCode' => -1, 'msg' => $msg, 'status' => $status]);
            }

            if ($apiResponseDataArr->status != 200) {
                $msg = 'Something went wrong from API server!!';
                return response()->json(['responseCode' => -1, 'msg' => $msg, 'status' => null]);
            }
            return response()->json(['responseCode' => 1, 'data' => $apiResponseDataArr->data]);
        } catch (\Exception $e) {
            $msg = 'Something went wrong from api server!!! [PV-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }

    public function GOPilgrimApiRequest($postPilgrimsData){
        $token = $this->getTokenData();
        if (!$token) {
            Session::flash('error', 'Sorry! Invalid Token.');
            return false;
        }
        $apiUrl = env('API_URL')."/api/store-go-pilgrim-data";
        $headers = [
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/x-www-form-urlencoded',
        ];
        $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postPilgrimsData, $headers);
        $apiResponseDataArr = json_decode($apiResponse['data']);

        if($apiResponseDataArr == null){
            Session::flash('error', 'Sorry!  API Request Fail !!!!!!!!');
            return false;
        }
        if ($apiResponse['http_code'] !== 200) {
            Session::flash('error', 'Sorry!' . $apiResponseDataArr->msg . '[PP-1001]');
            return false;
        }

        if ($apiResponseDataArr->status !== 200 ) {
            Session::flash('error', 'Sorry!' . $apiResponseDataArr->msg . '[PP-1002]');
            return false;
        }
        return $apiResponseDataArr;
    }

    public function GoPassportProcess(Request $request, $encode_ref_id,$encode_process_type_id)
    {
        try{
            if (empty($encode_process_type_id) || $encode_process_type_id == null ) {
                Session::flash('error', 'Sorry! Invalid Process Type');
                return redirect()->back();
            }
            if (empty($encode_ref_id) || $encode_ref_id == null ) {
                Session::flash('error', 'Sorry! Invalid Ref Id');
                return redirect()->back();
            }

            echo "Task in Ongoing";
            exit();

            DB::beginTransaction();

            #store sticker pilgrims information
            $decoded_process_type_id = Encryption::decodeId($encode_process_type_id);
            $decoded_ref_id = Encryption::decodeId($encode_ref_id);

            $processListCheck = ProcessList::where('ref_id', $decoded_ref_id)
                ->where('process_type_id', $decoded_process_type_id)
                ->where('status_id','<',1)
                ->first();
            if($processListCheck != null){
                Session::flash('error', 'Sorry! This is Already in Process. [PCC-1003]');
                return redirect()->back();
            }

            $appData = PilgrimDataList:: where('id', '=',$decoded_ref_id )->first();
            if($appData == null){
                Session::flash('error', 'Sorry! GO not exist. [PCC-1004]');
                return redirect()->back();
            }
            $masterData = $appData->json_object;
            $processData = ProcessList::where([
                'process_type_id' => $appData->process_type_id,
                'ref_id' => $appData->id
            ])->first();
            if($processData == null){
                $processData = new ProcessList();
            }

            # store sticker pilgrims information
            #$this->storeStickerPilgrims($request, $decoded_ref_id, $remainingMember);

            # store process list information
            if($request->get('actionBtn') == 'submit'){
                $storeProcessList = $this->storeProcessListData($request, $processData, $appData, $masterData);

                if(!$storeProcessList){
                    DB::rollBack();
                    Session::flash('error', 'Sorry! Failed to store data in Process List . [PCC-1005]');
                    return redirect()->back();
                }else{
                    DB::commit();
                    Session::flash('success', 'Successfully store data in Process List . [PCC-1006]');
                    return redirect('/go-passport/list/'. ($encode_process_type_id));
                }
            }

            DB::commit();
            return redirect('/go-passport/edit/'.($encode_ref_id).'/' . ($encode_process_type_id));
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Sorry!' . $e->getMessage() . '[PCC-1007]');
            return redirect()->back();
        }
    }
    public function storeProcessListData(Request $request, $processListObj, $appData, $masterData = [])
    {
        $processListObj->company_id = 0;
        //Set category id for process differentiation
        $processListObj->cat_id = 1;

        # generate tracking no
        if ($request->get('actionBtn') == 'submit') {
            if (empty($processListObj->tracking_no)) {
                $trackingPrefix = 'SV-';
                $processListObj->tracking_no = $trackingPrefix . strtoupper(dechex($appData->process_type_id . $appData->id));
            }
            $processListObj->status_id = self::SUBMITTED_STATUS_ID;
            $processListObj->desk_id = self::APPROVED_DESK_ID;
        }
        $processListObj->ref_id = $appData->id;
        $processListObj->process_type_id = $appData->process_type_id;
        $processListObj->office_id = 0;
        $processListObj['json_object'] = $masterData;
        $processListObj->submitted_at = Carbon::now();
        $processListObj->save();
        return $processListObj;
    }

    public function pdfGenarate(Request $request, $encode_ref_id,$encode_process_type_id) {
        try {
            if (empty($encode_process_type_id) || $encode_process_type_id == null ) {
                Session::flash('error', 'Sorry! Invalid Process Type');
                return redirect()->back();
            }
            if (empty($encode_ref_id) || $encode_ref_id == null ) {
                Session::flash('error', 'Sorry! Invalid Ref Id');
                return redirect()->back();
            }

            $decoded_process_type_id = Encryption::decodeId($encode_process_type_id);
            $decoded_ref_id = Encryption::decodeId($encode_ref_id);

            $pilgrimData = StickerPilgrims::where('ref_id', $decoded_ref_id)->where('is_archived', 0)->get();
            $pilgrimMetaData = StickerVisaMetadata::where('ref_id', $decoded_ref_id)->first();
            $pdfName = $pilgrimMetaData->id.strtotime($pilgrimMetaData->created_at).$pilgrimMetaData->ref_id.$pilgrimMetaData->team_type;

            $current_time = date('d M, Y');

            // Make PDF File directory
            // $pdfFilePath = CommonFunction::directoryFunction('GoPassport',"go_passport_");
            $pdfFilePath = 'news/uploads/GoPassport/'.$pdfName.'.pdf';
            $fullPath = request()->root()."/".$pdfFilePath;
            if (file_exists(public_path($pdfFilePath))) {
                unlink(public_path($pdfFilePath));
            }
            $qrCodeUrl = url($fullPath);
            // $application_tracking_no = $process_info->tracking_no;
            $contents = view("GoPassport::go-pdf", compact('current_time', 'qrCodeUrl', 'pilgrimMetaData', 'pilgrimData'))->render();
            $subject = 'GO Passport PDF';
            $title = 'GO Passport PDF';

            $pdfGeneration = CommonFunction::pdfGeneration($title, $subject, '', $contents, $pdfFilePath,'F',true);
            if($fullPath){
                $pdfUrl = url($fullPath);
                return redirect()->to($pdfUrl);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Sorry!' . $e->getMessage() . '[PCC-1008]');
            return redirect()->back();
        }
    }

    public function pdfGenarateSingle(Request $request, $encode_ref_id,$encode_process_type_id, $pilgrim_id) {
        try {
            $decoded_pilgrim_id = Encryption::decodeId($pilgrim_id);
            if (empty($encode_process_type_id) || $encode_process_type_id == null ) {
                Session::flash('error', 'Sorry! Invalid Process Type');
                return redirect()->back();
            }
            if (empty($encode_ref_id) || $encode_ref_id == null ) {
                Session::flash('error', 'Sorry! Invalid Ref Id');
                return redirect()->back();
            }

            $decoded_process_type_id = Encryption::decodeId($encode_process_type_id);
            $decoded_ref_id = Encryption::decodeId($encode_ref_id);

            $pilgrimData = StickerPilgrims::where('id', $decoded_pilgrim_id)->where('is_archived', 0)->get();

            $pilgrimMetaData = StickerVisaMetadata::where('ref_id', $decoded_ref_id)->first();
            $pdfName = $pilgrimData[0]->pid.$pilgrimMetaData->id.strtotime($pilgrimMetaData->created_at).$pilgrimMetaData->ref_id.$pilgrimMetaData->team_type;
            $current_time = date('d M, Y');

            // Make PDF File directory
            // $pdfFilePath = CommonFunction::directoryFunction('GoPassport',"go_passport_");
            $pdfFilePath = 'news/uploads/GoPassport/'.$pdfName.'.pdf';
            $fullPath = request()->root()."/".$pdfFilePath;
            $qrCodeUrl = url($fullPath);
            // $application_tracking_no = $process_info->tracking_no;
            $contents = view("GoPassport::go-pdf", compact('current_time', 'qrCodeUrl', 'pilgrimMetaData', 'pilgrimData'))->render();
            $subject = 'GO Passport PDF';
            $title = 'GO Passport PDF';

            $pdfGeneration = CommonFunction::pdfGeneration($title, $subject, '', $contents, $pdfFilePath,'F',true);
            if($fullPath){
                $pdfUrl = url($fullPath);
                return redirect()->to($pdfUrl);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Sorry!' . $e->getMessage() . '[PCC-1008]');
            return redirect()->back();
        }
    }


    /*   ################### Passport Delivery related function Start #####################   */

    public function createDeliveryPassport(Request $request, $encoded_process_type_id){
        try {
            $decoded_process_type_id = Encryption::decodeId($encoded_process_type_id);
            $AuthID = Auth::user()->id;
            $returnPassports = TempPassportDelivery::where(['created_by' => $AuthID])->get();
            return view("GoPassport::create-delivery", compact('encoded_process_type_id','returnPassports'));
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[PCC-1001]');
            return redirect()->back();
        }

    }
    public function addDeliveryPassport(Request $request, $encoded_process_type_id)
    {
        try {
            if(empty($request->get('passport_no'))){
                Session::flash('error', 'Please provide passport number');
                return redirect()->back();
            }
            $pilgrimInfo = StickerPilgrims::where([
                'passport_no' => $request->get('passport_no'),
                'is_archived' => 0,
                'is_delivery' => 0,
            ])->get();

            $passportNo = trim($request->get('passport_no'));
            $AuthID = Auth::user()->id;
            $checkExistData = TempPassportDelivery::where(['passport_no' => $passportNo, 'created_by' => $AuthID])->count();
            if($checkExistData === 1 ){
                    Session::flash('error', "$passportNo Passport already exists");
                    return redirect()->back();
            }
            if (count($pilgrimInfo) > 0) {
                DB::beginTransaction();
                $tempPassportDeliveryData = new TempPassportDelivery();
                $tempPassportDeliveryData->passport_no = $pilgrimInfo[0]->passport_no;
                $tempPassportDeliveryData->pilgrim_id = $pilgrimInfo[0]->id;
                $tempPassportDeliveryData->tracking_no = strtoupper($pilgrimInfo[0]->pilgrim_tracking_no);
                $tempPassportDeliveryData->go_serial_no = $pilgrimInfo[0]->go_serial_no;
                $tempPassportDeliveryData->passport_type = $pilgrimInfo[0]->passport_type;
                $tempPassportDeliveryData->passport_dob = $pilgrimInfo[0]->passport_dob;
                $tempPassportDeliveryData->status = 0;
                $tempPassportDeliveryData->created_at = Carbon::now();
                $tempPassportDeliveryData->created_by = CommonFunction::getUserId();
                $tempPassportDeliveryData->updated_at = Carbon::now();
                $tempPassportDeliveryData->updated_by = CommonFunction::getUserId();
                $tempPassportDeliveryDataInsert = $tempPassportDeliveryData->save();

                if ($tempPassportDeliveryDataInsert) {
                    DB::commit();
                    Session::flash('success', 'Successfully inserted passport.');
                } else {
                        DB::rollback();
                        Session::flash('error','Data not save [DVP-989]');
                }
                return redirect()->back();
            } else {
                Session::flash('error', 'Passport already exists in delivery list');
                return  redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[DVP-990]');
            return redirect()->back();
        }

    }

    public function passportRemoveFromChartReturn(Request $request)
    {
        try {
            $passportNo = Encryption::decodeId($request->get('passport_no'));
            $deleteData = TempPassportDelivery::where(['passport_no' => $passportNo])->delete();
            if ($deleteData === 1) {
                Session::flash('success', 'Passport Removed successfully');
            } else {
                Session::flash('error', 'Passport Removed failed');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withInput();
        }
    }

    public function saveReturnPassport(Request $request)
    {
        try {
            if(empty($request->get('purpose'))){
                Session::flash('error', 'Purpose is Required');
                return redirect()->back();
            }
            if(empty($request->get('return_date'))){
                Session::flash('error', 'Delivery date is Required');
                return redirect()->back();
            }
            if(empty($request->get('process_type_id'))){
                Session::flash('error', 'Invalid Process Type');
                return redirect()->back();
            }
            $encoded_process_type_id = $request->get('process_type_id');
            $process_type_id = Encryption::decodeId($encoded_process_type_id);
            $AuthID = Auth::user()->id;
            $no_of_pilgrim_count = TempPassportDelivery::where(['created_by' => $AuthID])->count();

            DB::beginTransaction();
            $passportMasterData = new StickerPassportReturn();
            $passportMasterData->purpose = $request->get('purpose');
            $passportMasterData->return_date = $request->get('return_date') ? date('Y-m-d', strtotime($request->get('return_date'))) : null;
            $passportMasterData->no_of_pilgrim = $no_of_pilgrim_count;
            $passportMasterData->comment = $request->get('comment');
            $detailInsert = $passportMasterData->save();
            if ($detailInsert) {
                $masterId = $passportMasterData->id;
                $returnPassports = TempPassportDelivery::where(['created_by' => $AuthID])->get();
                if(count($returnPassports) > 0){
                    foreach ($returnPassports as $data) {
                        if (isset($data['pilgrim_id'])) {
                            StickerPilgrims::where('id', $data['pilgrim_id'])
                                ->where(['is_archived' => 0, 'is_delivery' => 0])
                                ->update([
                                    'sticker_passport_return_id' => $masterId,
                                    'is_delivery' => 1
                                ]);
                        }
                    }
                    // Generate delivery passport tracking number
                    $trackingPrefix = 'DVP-';
                    $tracking_no = $trackingPrefix . strtoupper(dechex($process_type_id . $masterId));
                    StickerPassportReturn::where('id', $masterId)->update(['tracking_no' => $tracking_no,]);
                }else{
                    DB::rollback();
                    Session::flash('error','Data not save [DVP:1001]');
                    return redirect()->back();
                }

            } else {
                DB::rollback();
                Session::flash('error','Data not save [DVP:1002]');
                return redirect()->back();
            }

            if ($detailInsert) {
                $encodedId = Encryption::encodeId($masterId);
                DB::commit();
                TempPassportDelivery::where(['created_by' => $AuthID])->delete();
                Session::flash('success', 'Passports Returned successfully');
                return redirect('/go-passport/view/delivery-passport/'.($encodedId). '/'.($encoded_process_type_id));
            }
            DB::rollback();
            Session::flash('error', 'Sorry! Operation failed.');
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error','Sorry Failed !!');
            return redirect()->back()->withInput();
        }
    }
    public function viewReturnPassport(Request $request,$encodedId,$encoded_process_type_id)
    {
        try {
            if(empty($encodedId)){
                Session::flash('error', 'Invalid ID');
                return redirect()->back();
            }
            $decodedId = Encryption::decodeId($encodedId);

            $getReturnData = StickerPilgrims::leftJoin('sticker_passport_return', 'sticker_pilgrims.sticker_passport_return_id', '=', 'sticker_passport_return.id')
                ->where('sticker_pilgrims.sticker_passport_return_id', $decodedId)
                ->where('sticker_pilgrims.is_delivery', 1)
                ->get([
                    'sticker_pilgrims.id as pilgrim_id',
                    'sticker_pilgrims.go_serial_no as go_serial_no',
                    'sticker_pilgrims.passport_type as passport_type',
                    'sticker_pilgrims.passport_no as passport_no',
                    'sticker_pilgrims.passport_dob as passport_dob',
                    'sticker_pilgrims.pilgrim_tracking_no as pilgrim_tracking_no',
                ]);
            $voucher_no = StickerPassportReturn::where('id',$decodedId)->value('tracking_no');
            if(count($getReturnData)<1){
                Session::flash('error','Passport Not Found');
                return redirect()->back();
            }

            return view("GoPassport::view-delivery", compact('voucher_no','getReturnData','encoded_process_type_id','encodedId'));

        } catch (\Exception $e) {
            Session::flash('error','Sorry Failed !!');
            return redirect()->back()->withInput();
        }
    }

    public function getDeliveryList(Request $request){
        try {
            if(empty($request->get('process_type_id'))){
                Session::flash('error','Process Type not match !!');
                return redirect()->back();
            }
            $list = StickerPassportReturn::get('sticker_passport_return.*');

            return Datatables::of($list)
                ->addColumn('action', function ($list) use ($request) {
                    $html = '<a href="' . url('go-passport/view/delivery-passport/' . Encryption::encodeId($list->id) . '/' . ($request->get('process_type_id'))) . '"
                              class="btn btn-xs btn-outline-success">
                              <i class="fa fa-box-open"></i> Open
                            </a> &nbsp;';
                    return $html;
                })
                ->editColumn('passport', function ($list) {
                    $getPassports = StickerPilgrims::where('sticker_passport_return_id', $list->id)->pluck('passport_no');

                    $passportNumbers = $getPassports->implode(', ');
                    if(count($getPassports) > 0){
                        $Passport = $passportNumbers;
                    }else{
                        $Passport = 'No Data';
                    }
                    return $Passport;
                })
                ->editColumn('return_date', function ($list) {
                    $time = $list->return_date;
                    $return_date = Carbon::createFromFormat('Y-m-d', $time)->format('d-M-Y');
                    return $return_date;
                }) ->editColumn('updated_at', function ($list) {
                    $time = $list->updated_at;
                    $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $time)->format('d-M-Y');
                    return $updated_at;
                })
                ->addIndexColumn('DT_RowIndex')
                ->rawColumns(['action','DT_RowIndex'])
                ->make(true);
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[DVP-1004]');
            return redirect()->back();
        }
    }

    public function returnPdfGenarate(Request $request, $id,$encoded_process_type_id) {
        try {
            if (empty($encoded_process_type_id) || $encoded_process_type_id == null ) {
                Session::flash('error', 'Sorry! Invalid Process Type');
                return redirect()->back();
            }
            if (empty($id) || $id == null ) {
                Session::flash('error', 'Sorry! Invalid Id');
                return redirect()->back();
            }

            $decoded_process_type_id = Encryption::decodeId($encoded_process_type_id);
            $decoded_id = Encryption::decodeId($id);

            if($request->get('flag') == 'yes'){
                $pilgrimData = StickerPilgrims::where('id', $decoded_id)->where(['is_archived' => 0,'is_delivery' => 1,])->get();
                $StickerPassportReturn = StickerPassportReturn::where('id',$pilgrimData[0]->sticker_passport_return_id)->first();
            }elseif ($request->get('flag') == 'no'){
                $pilgrimData = StickerPilgrims::where('sticker_passport_return_id', $decoded_id)->where(['is_archived' => 0,'is_delivery' => 1,])->get();
                $StickerPassportReturn = StickerPassportReturn::where('id',$pilgrimData[0]->sticker_passport_return_id)->first();
            }

            $current_time = date('d M, Y');

            $pdfFilePath = CommonFunction::directoryFunction('DeliveryPassport', "delivery_passport_");
            $fullPath = request()->root() . "/" . $pdfFilePath;
            $qrCodeUrl = url($fullPath);
            $contents = view("GoPassport::delivery-pdf", compact('current_time', 'qrCodeUrl', 'pilgrimData','StickerPassportReturn'))->render();
            $subject = 'Go Delivery Passport PDF';
            $title = 'GO Delivery Passport PDF';

            $pdfGeneration = CommonFunction::pdfGeneration($title, $subject, '', $contents, $pdfFilePath,'F',true);
            if($fullPath){
                $pdfUrl = url($fullPath);
                return redirect()->to($pdfUrl);
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry!' . $e->getMessage() . '[PCC-1008]');
            return redirect()->back();
        }
    }

    /*   ################### Passport Delivery related function End #####################   */


    /*   ################### External API Request related function Start #####################   */
    public function externalToken(Request $request){
    try{
        if(empty($request->all())){
            $msg = 'Request Data not found';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
        $checkRequest = $this->checkExternalRequest($request);
        if(!$checkRequest){
            $msg = 'Wrong Credential';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }

        $tokenUrl = "$this->api_url/api/getToken";

        $credential = [
            'clientid' => env('CLIENT_ID'),
            'username' => env('CLIENT_USER_NAME'),
            'password' => env('CLIENT_PASSWORD')
        ];
        $token = CommonFunction::getApiToken($tokenUrl, $credential);
        if (!$token) {
            $msg = 'Sorry Invalid token';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
        return response()->json(['responseCode' => 1, 'Token' => $token]);
        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }

    public function checkExternalRequest($request){
        try{
            $username = Encryption::decode($request->get('username'));
            $password = Encryption::decode($request->get('password'));
            $clientid = Encryption::decode($request->get('clientid'));

            $EXTERNAL_USERNAME = config('constant.EXTERNAL_API_USERNAME');
            $EXTERNAL_PASSWORD = config('constant.EXTERNAL_API_PASSWORD');
            $EXTERNAL_CLIENTID = config('constant.EXTERNAL_API_CLIENTID');

            if($EXTERNAL_USERNAME == $username && $EXTERNAL_PASSWORD == $password && $EXTERNAL_CLIENTID == $clientid ){
                return true;
            }else{
                return false;
            }
        }catch (\Exception $e) {
            return false;
        }

    }

    public function storeOcrMedicineData(Request $request){
        try{
            $token = $request->header('APIAuthorization');
            if($token == null || $token == ''){
                $msg = 'Invalid Token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = env('API_URL')."/api/store-ocr-medicine-data";
            $data = $request->all();
            if(empty($data)){
                $msg = 'Data not found';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);

            if($apiResponseDataArr == null){
                $msg = 'Something went wrong !!! [OCR-003]';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            if ($apiResponse['http_code'] !== 200) {
                $msg = 'Something went wrong !!! [OCR-004]';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            if ($apiResponseDataArr->status !== 200 ) {
                return response()->json(['responseCode' => -1, 'msg' => $apiResponseDataArr->msg]);

            }

            return response()->json(['responseCode' => 1, 'msg' =>$apiResponseDataArr->msg]);
        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }

    public function storePilgrimHouseColorData(Request $request){
        try{
            $requestPath = $request->getPathInfo();
            $requestHeaders = $request->headers->all();
            $token = $request->header('APIAuthorization');
            if($token == null || $token == ''){
                $msg = 'Invalid Token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = env('API_URL')."/api/store-pilgrims-house-color-code";
            $data = $request->all();
            if(empty($data)){
                $msg = 'Data not found';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $postData['requestUrl'] = $requestPath;
            $postData['requestData'] = $data;
            $postData['requestHeaders'] = $requestHeaders;
            $postData = json_encode($postData);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);

            if($apiResponseDataArr == null){
                $msg = 'Something went wrong !!! [KSA-003]';
                return response()->json(['responseCode' => -1, 'status' => 400, 'msg' => $msg]);
            }
            if ($apiResponse['http_code'] !== 200) {
                $msg = 'Something went wrong !!! [KSA-004]';
                return response()->json(['responseCode' => -1, 'status' => $apiResponse['http_code'], 'msg' => $msg]);
            }
            if ($apiResponseDataArr->status !== 200 ) {
                return response()->json(['responseCode' => -1, 'status' => $apiResponseDataArr->status, 'msg' => $apiResponseDataArr->msg]);
            }

            return response()->json(['responseCode' => 1, 'response' => 'success', 'status' => $apiResponseDataArr->status, 'msg' =>$apiResponseDataArr->msg]);
        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [KSA-001]';
            return response()->json(['responseCode' => -1, 'status' => 400, 'msg' => $msg]);
        }
    }

    /*   ################### External API Request related function End #####################   */
}
