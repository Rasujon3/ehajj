<?php

namespace App\Modules\REUSELicenseIssue\Models\MedicalReceive;

use App\Libraries\CommonFunction;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\ProcessPath\Models\ProcessList;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MedicalDetails extends Model
{
    protected $table = 'medical_details';
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();
        // Before update
        static::creating(function ($post) {
            $post->created_by = CommonFunction::getUserId();
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function ($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }

    public static function storeData($process_list_id, $type)
    {
        $refId = ProcessList::where('id', $process_list_id)->value('ref_id');
        $appData = PilgrimDataList::where('id', $refId)->first();
        try {
            $decodedJson = json_decode($appData->json_object);
            unset($decodedJson[3][0]);

            DB::beginTransaction();
            foreach ($decodedJson[3] as $data) {

                $medicalDetails = new MedicalDetails();
                if ($type == 'receive') {
                    $medicalDetails->source = $decodedJson[0][0];
                    $medicalDetails->clinic = $decodedJson[1][0];
                    $medicalDetails->supplier = $decodedJson[2][0];
                    $date = '';
                    if ($data[7] != null){
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[7])->format('Y-m-d');
                    }
                    $medicalDetails->expiry_date = $date;

                    $medicalDetails->generic_name = $data[2];
                    $medicalDetails->batch = $data[6];
                    $medicalDetails->med_type = $data[1];
                    $medicalDetails->trade_name = $data[3];
                    $medicalDetails->sku = strtolower(str_replace(' ', '', $data[4]));
                    $medicalDetails->quantity = (int)$data[5];
                    if ($medicalDetails->source == 3) { // 3 is pharmacy
                        $pharmacyInventory = PharmacyInventory::where('pharmacy_id', $medicalDetails->clinic)
                            ->where(['med_type' => $medicalDetails->med_type,
                                'trade_name' => $medicalDetails->trade_name,
                                'sku' => $medicalDetails->sku])
                            ->first();
                        if (empty($pharmacyInventory)) {
                            $pharmacyInventory = new PharmacyInventory();
                        }
                        $pharmacyInventory->pharmacy_id = $medicalDetails->clinic;
                        $pharmacyInventory->med_type = $medicalDetails->med_type;
                        $pharmacyInventory->trade_name = $medicalDetails->trade_name;
                        $pharmacyInventory->sku = $medicalDetails->sku;
                        $pharmacyInventory->quantity = $pharmacyInventory->quantity - $medicalDetails->quantity;
                        $pharmacyInventory->save();
                    }
                }
                if ($type == 'issue') {
                    $medicalDetails->clinic = $decodedJson[0][0];
                    $medicalDetails->requisition_by = $decodedJson[1][0];
                    $medicalDetails->receipt_doc = $decodedJson[2][0];
                    $medicalDetails->med_type = $data[1];
                    $medicalDetails->trade_name = $data[2];
                    $medicalDetails->sku = strtolower(str_replace(' ', '', $data[3]));
                    $medicalDetails->quantity = (int)$data[4];

                    $pharmacyInventory = PharmacyInventory::where('pharmacy_id', $medicalDetails->clinic)
                        ->where(['med_type' => $medicalDetails->med_type,
                            'trade_name' => $medicalDetails->trade_name,
                            'sku' => $medicalDetails->sku])
                        ->first();
                    if (empty($pharmacyInventory)) {
                        $pharmacyInventory = new PharmacyInventory();
                    }
                    $pharmacyInventory->pharmacy_id = $medicalDetails->clinic;
                    $pharmacyInventory->med_type = $medicalDetails->med_type;
                    $pharmacyInventory->trade_name = $medicalDetails->trade_name;
                    $pharmacyInventory->sku = $medicalDetails->sku;
                    $pharmacyInventory->quantity = $pharmacyInventory->quantity + $medicalDetails->quantity;
                    $pharmacyInventory->save();
                }
                $medicalDetails->master_id = $refId;
                $medicalDetails->type = $type;

                $medicalInventory = MedicalInventory::where(['med_type' => $medicalDetails->med_type, 'trade_name' => $medicalDetails->trade_name, 'sku' => $medicalDetails->sku])
                    ->first();

                if (empty($medicalInventory)) {
                    $medicalInventory = new MedicalInventory();
                }
                $medicalInventory->med_type = $medicalDetails->med_type;
                $medicalInventory->trade_name = $medicalDetails->trade_name;
                $medicalInventory->sku = $medicalDetails->sku;
                if ($type == 'receive') {
                    $medicalInventory->quantity = $medicalInventory->quantity + $medicalDetails->quantity;
                } else {
                    $medicalInventory->quantity = $medicalInventory->quantity - $medicalDetails->quantity;
                }
                $medicalInventory->last_updated = date('Y-m-d', strtotime(Carbon::now()));
                $medicalInventory->save();

                $medicalDetails->inventory_id = $medicalInventory->id;
                $medicalDetails->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage(), $e->getLine(), $e->getFile());
        }

    }
}
