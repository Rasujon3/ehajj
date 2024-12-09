<?php

namespace App\Http\Traits;


use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\PostApiData;
use App\Models\User;
use App\Modules\ProcessPath\Models\FlightRequestPilgrim;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveClinic;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveSource;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveSupplier;
use App\Modules\Settings\Models\Configuration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

trait TravelPlanTrait
{
    public function addTravelPlanForm(){
        $data = array();
        $data['process_type_id'] = $this->process_type_id;
        $data['session_id'] = $this->session_id;
        $data['flight_drop_down_list'] = Auth::user()->flight_id!=0?$this->getFlightDropdonw():['data'=>[]];
        $total_submitted_request_by_guide = FlightRequestPilgrim::where('guide_id',Auth::user()->hmis_guide_id)->whereIn('status',[1,25])->count();
        $assign_limit = Configuration::where('caption','Guide_Pilgrim_Assign_limit')->first();
        $data['remain_assign_limit'] = $assign_limit->value - $total_submitted_request_by_guide;
        return strval(view("REUSELicenseIssue::Listing.TravelPlan.master", $data));
    }

    public function viewTravelPlanForm($appId){
        $appmasterId = Encryption::decodeId($appId);
        $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
            ->first();

        $data = array();
        $data['data'] = $pilgrimdata;
        $data['session_id'] = $this->session_id;
        $data['process_type_id'] = $this->process_type_id;
        $data['flight_request_pilgrims_array'] = FlightRequestPilgrim::where('pilgrim_data_list_id', $pilgrimdata->id)->where('session_id', $data['session_id']->session_id)->pluck('pid')->toArray();
        $flight_request_pilgrims = FlightRequestPilgrim::where('pilgrim_data_list_id',$pilgrimdata->id)->first();

        $data['total_submitted_request_by_guide'] = FlightRequestPilgrim::where('guide_id',$flight_request_pilgrims->guide_id)->where('status',1)->count();
        $data['total_approved_request_for_guide'] = FlightRequestPilgrim::where('guide_id',$flight_request_pilgrims->guide_id)->where('status',25)->count();
        $data['guide_information'] = User::where('hmis_guide_id',$flight_request_pilgrims->guide_id)->select('user_first_name','user_middle_name','user_last_name','user_email','user_mobile','flight_id')->first();
        $remove_permission = 0;
        if(!empty($pilgrimdata->processlist->desk_id) && in_array($pilgrimdata->processlist->desk_id, explode(',',Auth::user()->desk_id))){
            $remove_permission = 1;
        }
        $data['remove_permission'] = $remove_permission;
        return (string)view("REUSELicenseIssue::Listing.TravelPlan.masterView", $data);
    }


    public function storeTravelPlanContent($request,$app_id,$mode){
        $hajjSessionId = HajjSessions::where(['state' => 'active'])->first('id');
        if ($request->get('actionBtn') != "cancel") {
            if($request->flight_id != null) {
                $postData = [
                    'flight_id' => $request->flight_id
                ];
                $postdata = http_build_query($postData);
                $base_url = env('API_URL');
                $url = "$base_url/api/flight-details";
                $response = PostApiData::getData($url, $postdata);
                $responseData = '';
                if ($response) {
                    $responseData = json_decode($response);
                } else {
                    return back()->with('error', 'Flight Not Found!');
                }
                $flight_data = $responseData->data[0];
            }

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

                    $pilgrimData2[$i]['full_name_english'] = $request->full_name_english2[$arrKey];
                    $pilgrimData2[$i]['tracking_no'] = $request->tracking_no2[$arrKey];
                    $pilgrimData2[$i]['pilgrim_id'] = $request->pilgrim_id2[$arrKey];
                    $pilgrimData2[$i]['pid'] = $request->pid2[$arrKey];
                    $pilgrimData2[$i]['flight_id'] = $request->flight_get_code;
                    $pilgrimData2[$i]['mobile'] = $request->mobile2[$arrKey];
                    $i++;
                    array_push($checked_pid,$arrKey);
                }
                $jsonData2 = json_encode($pilgrimData2);
            }

            $already_exists_request = FlightRequestPilgrim::where('session_id',$hajjSessionId->id)->whereIn('pid',$checked_pid)->whereIn('status',[1,25])->pluck('pid')->toArray();

            if(count($already_exists_request)>0){
                return back()->with('error','Can not submit this request');
            }



            if ($request->full_name_english) {
                foreach ($request->full_name_english as $arrKey => $data) {
                    $pilgrimData[$arrKey]['full_name_english'] = $data;
                    $pilgrimData[$arrKey]['tracking_no'] = $request->tracking_no[$arrKey];
                    $pilgrimData[$arrKey]['pilgrim_id'] = $request->pilgrim_id[$arrKey];
                    $pilgrimData[$arrKey]['pid'] = $request->pid[$arrKey];
                    $pilgrimData[$arrKey]['flight_id'] = $request->flight_get_code;
                    // $pilgrimData[$arrKey]['flight_code'] = $request->flight_code[$arrKey];
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
            // if ($request->get('actionBtn') == "submit")
            $appData->listing_id = $request->flight_id;
            $appData->process_type = $request->process_type;
            $appData->process_type_id = $this->process_type_id;
            $appData->session_id = !empty($hajjSessionId->id) ? $hajjSessionId->id : 0;
            $appData->request_type = 'Travel_Plan';
            $appData->save();
            $search_paramers = explode(',', $request->request_data);
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
                if ($processData->status_id == 5) { // For shortfall
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

                    $processData->previous_hash = $processData->hash_value ? $processData->hash_value : "";
                    $processData->hash_value = Encryption::encode($resultData);
                } else {
                    $processData->status_id = 1;

                    $processData->desk_id   = 6;

                }
            }
            $processData->process_type_id = $this->process_type_id;
            $processData->priority = 1;
            $processData->ref_id = $appData->id;
            $guide_tracking_no = explode("@",Auth::user()->user_email)[0];
            // $processData->tracking_no       =  'PIL-' . date("Ymd") . '-'
            if($request->flight_id != null) {
                $ref_data = [
                    'flight_code' => $flight_data->flight_code,
                    'departure_date' => $flight_data->departure_time,
                    'Guide Tracking No' => $guide_tracking_no
                ];
                $processData->json_object = json_encode($ref_data);
            }elseif($request->flight_id == null){
                $ref_data = [
                    'flight_code' => 0,
                    'possible_flight_date' => Carbon::createFromFormat('d-M-Y', $request->possible_flight_date)->format('Y-m-d H:i:s'),
                    'Guide Tracking No' => $guide_tracking_no
                ];
                $processData->json_object = json_encode($ref_data);
            }
            $processData->created_at = date('Y-m-d H:i:s');
            //tracking no
            $trackingPrefix = 'TP';
            $processTypeId = $this->process_type_id;
            $tracking_no = CommonFunction::generateTrackingID($trackingPrefix, $processTypeId.$appData->id);
            //$tracking_no = $trackingPrefix . strtoupper(dechex($processTypeId . $appData->id));
            $processData->tracking_no = $tracking_no;
            $processData->save();
            foreach ($pilgrimData2 as $item) {
                $flight_request_pilgrims = FlightRequestPilgrim::where('tracking_no', $item['tracking_no'])
                    ->where('session_id', $hajjSessionId->id)
                    ->first();
                if ($flight_request_pilgrims == null) {
                    $flight_request_pilgrims = new FlightRequestPilgrim();
                    $flight_request_pilgrims->pilgrim_data_list_id = $appData->id;
                    $flight_request_pilgrims->tracking_no = $item['tracking_no'];
                    $flight_request_pilgrims->process_list_id = $processData->id;
                    $flight_request_pilgrims->flight_id = $request->flight_id;
                    $flight_request_pilgrims->session_id = $hajjSessionId->id;
                    $flight_request_pilgrims->pid = $item['pid'];
                    $flight_request_pilgrims->guide_id = Auth::user()->hmis_guide_id;
                    $flight_request_pilgrims->pilgrim_name = $item['full_name_english'];
                    $flight_request_pilgrims->pilgrim_mobile = $item['mobile'];
                    if($request->flight_id != null) {
                        $flight_request_pilgrims->flight_code = $flight_data->flight_code;
                        $flight_request_pilgrims->flight_date = $flight_data->departure_time;
                    }
                    $flight_request_pilgrims->status = 1;
                    $flight_request_pilgrims->created_by = Auth::user()->id;
                    $flight_request_pilgrims->updated_by = Auth::user()->id;
                    if($request->flight_id == null) {
                        $flight_request_pilgrims->possible_flight_date = Carbon::createFromFormat('d-M-Y', $request->possible_flight_date)->format('Y-m-d H:i:s');
                    }
                    } else {
                    $flight_request_pilgrims->pilgrim_data_list_id = $appData->id;
                    $flight_request_pilgrims->tracking_no = $item['tracking_no'];
                    $flight_request_pilgrims->process_list_id = $processData->id;
                    $flight_request_pilgrims->flight_id = $request->flight_id;
                    $flight_request_pilgrims->session_id = $hajjSessionId->id;
                    $flight_request_pilgrims->pid = $item['pid'];
                    $flight_request_pilgrims->guide_id = Auth::user()->hmis_guide_id;
                    $flight_request_pilgrims->pilgrim_name = $item['full_name_english'];
                    $flight_request_pilgrims->pilgrim_mobile = $item['mobile'];
                    if($request->flight_id != null) {
                        $flight_request_pilgrims->flight_code = $flight_data->flight_code;
                        $flight_request_pilgrims->flight_date = $flight_data->departure_time;
                    }
                    $flight_request_pilgrims->status = 1;
                    $flight_request_pilgrims->created_by = Auth::user()->id;
                    $flight_request_pilgrims->updated_by = Auth::user()->id;
                    if($request->flight_id == null) {
                        $flight_request_pilgrims->possible_flight_date = Carbon::createFromFormat('d-M-Y', $request->possible_flight_date)->format('Y-m-d H:i:s');
                    }
                }
               $flight_request_pilgrims->save();
            }
            //send sms
            // Set the POST data
            $json_check_data = json_encode($checked_pid);

            $postData = [
                'request_data' => $json_check_data,
                'flight_id' => $request->flight_id,
                'guide_id' => Auth::user()->hmis_guide_id
            ];


            $postdata = http_build_query($postData);

            $base_url = env('API_URL');
            //need to done for pilgrim data update;
            $url = "$base_url/api/send-flight-submit-sms";
            $response = PostApiData::getData($url,$postdata);
            $response_data = json_decode($response,true);
        } else {
            $processData = ProcessList::where([
                'process_type_id' => $this->process_type_id,
                'ref_id' => $app_id
            ])->first();
            $processData->status_id = -1;
            $processData->desk_id = 0;
            $processData->save();
        }
    }

}
