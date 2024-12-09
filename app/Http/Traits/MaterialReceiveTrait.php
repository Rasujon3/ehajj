<?php

namespace App\Http\Traits;


use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalDetails;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalInventory;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveClinic;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveSource;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveSupplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

trait MaterialReceiveTrait
{
    public function addMaterialReceiveForm()
    {

        $data = array();
        $data['process_type_id'] = $this->process_type_id;
        $data['session_id'] = $this->session_id;
        $data['sources'] = MedicalReceiveSource::where('status', 1)->pluck('name', 'id')->toArray();
        $data['suppliers'] = MedicalReceiveSupplier::where('status', 1)->pluck('name', 'id')->toArray();
        $data['clinics'] = MedicalReceiveClinic::where('status', 1)->pluck('name', 'id')->toArray();
        return strval(view("REUSELicenseIssue::MaterialReceive.master", $data));
    }

    public function viewMaterialReceiveForm($appId)
    {
        $appmasterId = Encryption::decodeId($appId);
        $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
            ->first();
        $bulkArray = json_decode($pilgrimdata->json_object);
        $data = array();
        $data['app_id'] = $appmasterId;
        $data['source'] = $bulkArray[0][0];
        $data['clinic'] = $bulkArray[1][0];
        $data['supplier'] = $bulkArray[2][0];
        $data['materials'] = $bulkArray[3];

        $materials = $data['materials'];
        foreach ($materials as $keys => $item) {
            if ($keys > 0) {
                $oldMaterial = MedicalInventory::where('med_type', $item[1])
                    ->where('trade_name', $item[3])
                    ->where('sku', $item[4])
                    ->first();

                if (!empty($oldMaterial)) {
                    $text = $oldMaterial->quantity;
                    $materials[$keys][5] = $text;
                } else {
                    $text = "";
                    $materials[$keys][5] = $text;
                }


                $materials[$keys][6] = $item[5];
                $materials[$keys][7] = $item[6];
                $materials[$keys][8] = $item[7];
                $materials[$keys][9] = !empty($oldMaterial->last_updated) ? date('d-M-Y', strtotime($oldMaterial->last_updated)) : 'N/A';
            }
        }
        $data['materials'] = $materials;
        $data['session_id'] = $this->session_id;
        $data['process_type_id'] = $this->process_type_id;
        $data['sources'] = MedicalReceiveSource::where('status', 1)->pluck('name', 'id')->toArray();
        $data['suppliers'] = MedicalReceiveSupplier::where('status', 1)->pluck('name', 'id')->toArray();
        $data['clinics'] = MedicalReceiveClinic::where('status', 1)->pluck('name', 'id')->toArray();

        return (string)view("REUSELicenseIssue::MaterialReceive.masterView", $data);
    }

    public function storeMaterialReceive($request, $excel)
    {
        $hajjSessionId = HajjSessions::where(['state' => 'active'])->first('id');
        $appId = $request->app_id;
        if (!isset($appId)) {
            $appData = new PilgrimDataList();
            $processData = new ProcessList();
        } else {
            $appId = Encryption::decodeId($appId);
            $appData = PilgrimDataList::find($appId);
            $processData = ProcessList::where([
                'process_type_id' => $this->process_type_id,
                'ref_id' => $appId
            ])->first();
        }
        DB::beginTransaction();
        $data['supplier'] = $request->supplier;
        $data['clinic'] = $request->clinic == '' ? 'null' : $request->clinic;
        $data['source'] = $request->source == '' ? 'null' : $request->source;
        $otherDataArr = [[$data['source']], [$data['clinic']], [$data['supplier']]];
        $file = $request->file('medical_products');
        if ($request->file('medical_products')) {
            $file_name = $file->getClientOriginalName();
            $destinationPath = 'uploads';
            $file->move($destinationPath, $file_name);
            $data['bulkData'] = $excel->toArray(new MedicalDetails(), 'uploads/' . $file_name);
            foreach ($data['bulkData'][0] as $key => $item) {
                if ($item[1] == null && $item[2] == null && $item[3] == null) {
                    unset($data['bulkData'][0][$key]);
                } else {
                    if ($key > 0) {

                        if ($item[1] != null && strtolower($item[1]) == 'tab'){
                            $item[1] = "Tablet";
                        }
                        if ($item[1] != null && strtolower($item[1]) == 'inj'){
                            $item[1] = "Injection";
                        }
                        if ($item[1] != null && strtolower($item[1]) == 'cap'){
                            $item[1] = "Capsule";
                        }
                        if ($item[1] == null){
                            $item[1] = "-";
                        }
                        if ($item[4] != null){
                            $item[4] = strtolower(str_replace(' ', '', $item[4]));
                        }else{
                            $item[4] = '-';
                        }
                        if ($item[6] == null){
                            $item[6] = "Hajj-".date('Y');
                        }

                        $data['bulkData'][0][$key][1] = trim($item[1]);
                        $data['bulkData'][0][$key][2] = CommonFunction::rearrangeMedicineName($item[2], 'store');
                        $data['bulkData'][0][$key][3] = CommonFunction::rearrangeMedicineName($item[3], 'store');
                        $data['bulkData'][0][$key][4] = strtolower(str_replace(' ', '', trim($item[4])));
                        $data['bulkData'][0][$key][5] = trim($item[5]);
                        $data['bulkData'][0][$key][6] = trim($item[6]);
                    }
                }
            }
            $mergedData = array_merge($otherDataArr, $data['bulkData']);

            $jsonData = json_encode($mergedData);
            $appData->json_object = $jsonData;
        }

        $appData->process_type_id = 4;
        $appData->request_type = 'Material_Receive';
        $appData->session_id = !empty($hajjSessionId->id) ? $hajjSessionId->id : 0;
        $appData->save();

        $search_paramers = explode(',', $request->request_data);

        $processData->process_type_id = $this->process_type_id;
        $processData->priority = 1;
        $processData->ref_id = $appData->id;
        $processData->json_object = json_encode($search_paramers);
        $processData->process_desc = 'desc';
        $processData->created_at = date('Y-m-d H:i:s');
        $processData->pid = Str::uuid()->toString();

        if ($request->get('actionBtn') == "draft") {
            $processData->status_id = -1;
            $processData->desk_id = 5;
        } else {

            $processData->status_id = 1;
            $processData->desk_id = 6;
            $processData->process_desc = '';
            $processData->resubmitted_at = ''; // application resubmission Date
            $processData->submitted_at = date('Y-m-d H:i:s', time());

            $resultData = $processData->id . '-' . $processData->tracking_no .
                $processData->desk_id . '-' . $processData->status_id . '-' . $processData->user_id . '-' .
                $processData->updated_by;

            $processData->previous_hash = $processData->hash_value ?? "";
            $processData->hash_value = Encryption::encode($resultData);
            //tracking no
            $trackingPrefix = 'MM-R-';
            $processTypeId = $this->process_type_id;
            $tracking_no = $trackingPrefix . strtoupper(dechex($processTypeId . $appData->id));
            $processData->tracking_no = $tracking_no;

        }
        $processData->save();

        DB::commit();

        if ($processData->status_id == 1) {
            Session::flash('success', 'Data Submitted successfully.');
        } elseif ($processData->status_id == -1) {
            Session::flash('success', 'Data updated successfully.');
        }
    }

}
