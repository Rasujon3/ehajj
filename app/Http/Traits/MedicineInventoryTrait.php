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
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\PharmacyInventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

trait MedicineInventoryTrait
{
    public function getMedicineTotalInventory()
    {
        $data = array();
        $data['title'][0] = 'Type';
        $data['title'][1] = 'Generic Name';
        $data['title'][2] = 'Trade Name';
        $data['title'][3] = 'SKU';
        $data['title'][4] = 'Main Store Quantity';
        $pharmacy = MedicalReceiveClinic::where('status', 1)->get();
        $data['pharmacyCount'] = MedicalReceiveClinic::where('status', 1)->get(['id']);
        $data['mainInventory'] = MedicalInventory::leftJoin('medical_details', 'medical_inventory.id', '=', 'medical_details.inventory_id')
            ->select('medical_inventory.*', DB::raw('MAX(medical_details.generic_name) as generic_name'))
            ->groupBy('medical_details.inventory_id')
            ->get()
            ->toArray();
        $i = 5;
        foreach ($pharmacy as $item) {
            $data['title'][$i] = $item->name . " Qty";
            $i++;
        }

        foreach ($data['mainInventory'] as $key => $item) {
            $pharmacyItems = PharmacyInventory::where('med_type', $item['med_type'])
                ->where('trade_name', $item['trade_name'])
                ->where('sku', $item['sku'])
                ->get(['pharmacy_id', 'quantity']);
            $data['mainInventory'][$key]['total'] = 0;
            $quantityAll = 0;
            foreach ($pharmacyItems as $pItem) {
                $quantity = $pItem->quantity;
                $data['mainInventory'][$key][$pItem->pharmacy_id] = $quantity;
                $quantityAll = $quantityAll + $quantity;
            }
            $data['mainInventory'][$key]['total'] = $quantityAll + $item['quantity'];
        }
        return (string)view("REUSELicenseIssue::MedicineStore.totalInventory", $data);
    }

    public function getPharmacyInventory($pharmacyId)
    {
        $data['pharmacyInventory'] = PharmacyInventory::where('pharmacy_id', $pharmacyId)->get()->toArray();
        if (count($data['pharmacyInventory']) > 0){
            $data['pharmacyInventory'] = $this->getInRequistionQty($data['pharmacyInventory'], $pharmacyId);
            $data['pharmacyInventory'] = $this->getOutRequistionQty($data['pharmacyInventory'], $pharmacyId);
        }
        return (string)view("REUSELicenseIssue::MedicineStore.pharmacyInventory", $data);
    }

    public function getInRequistionQty($pharmacyInventory, $pharmacyId){
        $inRequisition = PilgrimDataList::leftJoin('process_list', 'process_list.ref_id', '=', 'pilgrim_data_list.id')
            ->where('process_list.process_type_id', 5)
            ->where('process_list.status_id', 1)
            ->get(['pilgrim_data_list.json_object']);
        $inRequisitionArr = [];
        foreach ($inRequisition as $key => $item){
            $decodedJson = json_decode($item->json_object);
            if ($decodedJson[0][0] == $pharmacyId){
                $inRequisitionArr[$key] = json_decode($item->json_object);
            }
        }

        foreach ($pharmacyInventory as $pKey => $pharmacyItem) {
            foreach ($inRequisitionArr as $value) {
                foreach ($value[3] as $k => $medItem) {
                    if ($k > 0) {
                        if ($medItem[1] == $pharmacyItem['med_type'] && $medItem[2] == $pharmacyItem['trade_name'] && $medItem[3] == $pharmacyItem['sku']) {
                            $pharmacyInventory[$pKey]['inRequisition'] = $medItem[4];
                        }
                    }
                }
            }
        }
        return $pharmacyInventory;
    }

    public function getOutRequistionQty($pharmacyInventory, $pharmacyId){
        $outRequisition = PilgrimDataList::leftJoin('process_list', 'process_list.ref_id', '=', 'pilgrim_data_list.id')
            ->where('process_list.process_type_id', 4)
            ->where('process_list.status_id', 1)
            ->get(['pilgrim_data_list.json_object']);
        $outRequisitionArr = [];
        foreach ($outRequisition as $key => $item){
            $decodedJson = json_decode($item->json_object);
            if ($decodedJson[1][0] != 'null' && $decodedJson[1][0] == $pharmacyId){
                $outRequisitionArr[$key] = json_decode($item->json_object);
            }
        }

        foreach ($pharmacyInventory as $pKey => $pharmacyItem) {
            foreach ($outRequisitionArr as $value) {
                foreach ($value[3] as $k => $medItem) {
                    if ($k > 0) {
                        if ($medItem[1] == $pharmacyItem['med_type'] && $medItem[3] == $pharmacyItem['trade_name'] && $medItem[4] == $pharmacyItem['sku']) {
                            $pharmacyInventory[$pKey]['outRequisition'] = $medItem[5];
                        }
                    }
                }
            }
        }
        return $pharmacyInventory;
    }

}
