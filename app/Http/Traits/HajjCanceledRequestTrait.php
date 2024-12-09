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

trait HajjCanceledRequestTrait
{
    public function addHajjCanceledRequestForm(){
        $data = array();
        $data['process_type_id'] = $this->process_type_id;
        $data['session_id'] = $this->session_id;
        return strval(view("REUSELicenseIssue::Listing.HajjCanceledRequest.master", $data));
    }

    public function viewHajjCanceledRequestForm($appId){
        $appmasterId = Encryption::decodeId($appId);
        $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
            ->first();
        $data = array();
        $data['data'] = $pilgrimdata;
        $data['session_id'] = $this->session_id;
        $data['process_type_id'] = $this->process_type_id;

        return (string)view("REUSELicenseIssue::Listing.HajjCanceledRequest.masterView", $data);
    }


    public function storeHajjCanceledRequestContent($request,$app_id,$mode){
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
            //DB::beginTransaction();
            if ($request->full_name_english) {

                $pilgrimData = [
                    'full_name_english'=> $request->full_name_english,
                    'tracking_no'=> $request->tracking_no,
                    'agency'=> $request->agency,
                    'license_no'=> $request->license_no,
                    'father_name'=> $request->father_name,
                    'mother_name'=> $request->mother_name,
                    'nid'=> $request->nid,
                    'reason_for_not_perform_hajj'=> $request->reason_for_not_perform_hajj,
                    'remarks'=> $request->remarks,
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
            $appData->request_type = 'Hajj Canceled Request';
            $appData->save();

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

                    $processData->previous_hash = $processData->hash_value ? $processData->hash_value : "";
                    $processData->hash_value = Encryption::encode($resultData);
                } else {
                    $processData->status_id = 1;
                    $processData->desk_id = 6;
                }
            }
            $processData->process_type_id = $this->process_type_id;
            $processData->priority = 1;
            $processData->ref_id = $appData->id;
            $processData->json_object = json_encode($search_paramers);
            $processData->created_at = date('Y-m-d H:i:s');
            $processData->save();
        } else {
            $processData = ProcessList::where([
                'process_type_id' => $this->process_type_id,
                'ref_id' => $app_id
            ])->first();
            $processData->status_id = -1;
            $processData->desk_id = 0;
            $processData->save();
        }
        //DB::commit();
    }
}
