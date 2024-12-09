<?php

namespace App\Http\Traits;


use App\Libraries\Encryption;
use App\Libraries\PostApiData;
use App\Modules\ProcessPath\Models\FlightRequestPilgrim;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveClinic;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveSource;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveSupplier;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait GenderChangeRequestTrait
{
    public function addGenderChangeRequestForm(){
        $data = array();
        $data['process_type_id'] = $this->process_type_id;
        $data['session_id'] = $this->session_id;
        $data['flight_drop_down_list'] = $this->getFlightDropdonw();
        $processDetails = ProcessType::where('id', $data['process_type_id'])->first();
        $activeMenuArr = explode(',', $processDetails['active_menu_for']);
        $data['active_menu_for'] = $activeMenuArr;

        return strval(view("REUSELicenseIssue::Listing.GenderChangeRequest.master", $data));
    }

    public function viewGenderChangeRequestForm($appId){
        $appmasterId = Encryption::decodeId($appId);
        $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
            ->first();
        $data = array();
        $data['data'] = $pilgrimdata;
        $data['session_id'] = $this->session_id;
        $data['process_type_id'] = $this->process_type_id;
        $data['flight_request_pilgrims_array'] = FlightRequestPilgrim::where('pilgrim_data_list_id', $pilgrimdata->id)->where('session_id', $data['session_id']->session_id)->pluck('pid')->toArray();

        return (string)view("REUSELicenseIssue::Listing.GenderChangeRequest.masterView", $data);
    }


    public function storeGenderChangeRequestContent($request,$app_id,$mode){
//        dd($request->all());
        $validate = Validator::make(
            ['mobile_change' => $request->mobile_change],
            ['mobile_change' => ['regex:/^01(1|3|4|5|6|7|8|9)\d{8}$/']]
        );
        if($validate->fails()){
            Session::flash('error', 'Sorry! Mobile No not valid.');
            return back();
        }
        $pilgrim_tracking_no = $request->tracking_no;
        if (empty($pilgrim_tracking_no)) {
            Session::flash('error', 'Sorry! Tracking No not found.');
            return back();
        }
        $isExistOnProcess = ProcessList::where('process_type_id', $this->process_type_id)
                                    ->where('status_id', '<', 25)
                                    ->whereJsonContains('json_object->Tracking No', $pilgrim_tracking_no)
                                    ->exists();
        if ($isExistOnProcess) {
            Session::flash('error', "$pilgrim_tracking_no is already in progress for ministry approval.");
            return back();
        }

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
            if ($request->full_name_english) {

                $pilgrimData = [
                    'full_name_english'=> $request->full_name_english,
                    'tracking_no'=> $request->tracking_no,
                    'gender'=> $request->gender,
                    'gender_change'=> $request->gender_change,
                    'mobile'=> $request->mobile,
                    'mobile_change'=> $request->mobile_change,
                    'ksa_mobile_change'=> $request->ksa_mobile_change,
                    'ksa_mobile'=> $request->ksa_mobile,
                    'mobile_checked'=> $request->mobile_checked,
                    'ksa_mobile_checked'=> $request->ksa_mobile_checked,
                    'gender_checked'=> $request->gender_checked,
                    'pilgrim_type_id'=> $request->pilgrim_type_id,
                    'is_govt'=> $request->is_govt,
                    'dob'=> $request->dob,
                    'present_address'=> $request->present_address,
                    'father_name'=> $request->father_name,
                    'name_in_english_change'=> $request->name_in_english_change,
                    'name_in_bangla'=> $request->name_in_bangla,
                    'name_in_bangla_change'=> $request->name_in_bangla_change,
                    'father_name_in_bangla_change'=> $request->father_name_in_bangla_change,
                    'mother_name_in_bangla'=> $request->mother_name_in_bangla,
                    'mother_name_in_bangla_change'=> $request->mother_name_in_bangla_change,

                    'village_ward' => $request->village_ward,
                    'district' => $request->district,
                    'district_id' => $request->district_id,
                    'police_station' => $request->police_station,
                    'thana_id' => $request->thana_id,
                    'post_code' => $request->post_code,

                    'post_code_change' => $request->post_code_change,
                    'district_id_change' => $request->district_id_change,
                    'district_change' => $request->district_change,
                    'thana_id_change' => $request->thana_id_change,
                    'police_station_change' => $request->police_station_change,
                    'village_ward_change' => $request->village_ward_change,
                ];
                $jsonData = json_encode($pilgrimData);
            }


            if ($request->full_name_english) {
                $appData->json_object = $jsonData;
            }



            $appData->listing_id = $request->listing_id;
            $appData->process_type = "tracking_no";

            $appData->process_type_id = $this->process_type_id;
            $appData->session_id = !empty($hajjSessionId->id) ? $hajjSessionId->id : 0;
            $appData->request_type = 'Gender Change Request';
            # $appData->save();
            if (!$appData->save()) {
                Session::flash('error', "Failed to save pilgrim data list.");
                return back();
            }
            $existingRecord = ProcessList::where('ref_id', $appData->id)
                                    ->where('process_type_id', $this->process_type_id)
                                    ->first();
            if ($existingRecord) {
                Session::flash('error', "Duplicate ref_id entry. Please try with a different value. [ER-0001]");
                return back();
            }

            $search_paramers = explode(',', $request->request_data);

            $processData->company_id = Auth::user()->working_company_id;
            $processData->locked_at = date('Y-m-d H:i:s');
            $processData->hash_value = '';
            $processData->previous_hash = '';
            $processData->process_desc = 'desc';
            $processData->submitted_at = date('Y-m-d H:i:s', time());
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

                    $processData->previous_hash = $processData->hash_value ?? "";
                    $processData->hash_value = Encryption::encode($resultData);
                } else {
                    $processData->status_id = 1;
                    $processData->desk_id = 5;
                }
            }

            $pilgrim_json = [
                'Tracking No' => $request->tracking_no,
                'Full Name English' => $request->full_name_english,
                'Is Govt' => $request->is_govt
            ];
            $processData->process_type_id = $this->process_type_id;
            $processData->priority = 1;
            $processData->ref_id = $appData->id;
            // $processData->tracking_no       =  'PIL-' . date("Ymd") . '-';
            # $processData->json_object = json_encode($search_paramers);
            $processData->json_object = json_encode($pilgrim_json);
            $processData->created_at = date('Y-m-d H:i:s');

            //tracking no
            $trackingPrefix = 'GCR';
            $processTypeId = $this->process_type_id;


            $tracking_no = $trackingPrefix . strtoupper(dechex($processTypeId . $appData->id));
            $tracking_no_exists = ProcessList::where('tracking_no', $tracking_no)
                                        ->where('process_type_id', $this->process_type_id)
                                        ->exists();
            if ($tracking_no_exists) {
                Session::flash('error', "Sorry! Tracking No already exists.");
                return back();
            }
            $processData->tracking_no = $tracking_no;
            $processData->save();


            // Insert tracking no in process list table
            // DB::statement("update  process_list, process_list as table2  SET process_list.tracking_no=(
            //     select concat('$trackingPrefix',
            //             LPAD( IFNULL(MAX(SUBSTR(table2.tracking_no,-7,7) )+1,1),7,'0')
            //                     ) as tracking_no
            //         from (select * from process_list ) as table2
            //         where table2.process_type_id ='$processTypeId' and table2.id!='$processData->id' and table2.tracking_no like '$trackingPrefix%'
            // )
            // where process_list.id='$processData->id' and table2.id='$processData->id'");
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
