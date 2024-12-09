<?php

namespace App\Modules\REUSELicenseIssue\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Libraries\PostApiData;
use App\Models\User;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveClinic;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\PharmacyInventory;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\PilgrimMedDetails;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\PilgrimMedIssue;
use App\Modules\REUSELicenseIssue\Services\StickerVisa;
use App\Modules\REUSELicenseIssue\Traits\HmisApiRequest;
use App\Modules\Users\Models\Users;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Traits\Token;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class MedicineReceiveController extends Controller
{
    protected $api_url;
    use Token;
    use HmisApiRequest;
    public function __construct()
    {
        $this->api_url =  env('API_URL');
    }

    public function index(){
//        $pharmacy = MedicalReceiveClinic::where('id', Auth::user()->working_company_id)->first(['id', 'name']);
//        $medicine = PharmacyInventory::where('pharmacy_id', Auth::user()->working_company_id)->get([
//            'id',
//            'med_type',
//            'trade_name',
//            'sku',
//            'quantity'
//        ]);
//        $medicineArr = [];
//        foreach ($medicine as $key => $item){
//            $medicineArr[$item->id] = $item->med_type.'_'.$item->trade_name.'_'.$item->sku;
//        }

        return view('REUSELicenseIssue::MedicineReceive.index');
    }

    public function searchPilgrimReceived(Request $request){

        try {
            $pid = $request->pid;
            $postData = [
                'pid' => $pid
            ];
            $postdata = http_build_query($postData);
            $base_url = env('API_URL');
            $url = "$base_url/api/get-pilgrim-by-pid";
            $response = PostApiData::getData($url,$postdata);
            $responseData = json_decode($response);
            if ($responseData->status == 200) {
                $data['responseData'] = $responseData;
                $data['medData'] = PilgrimMedIssue::leftJoin('pilgrim_medicine_details', 'pilgrim_medicine_details.master_id', '=', 'pilgrim_medicine_issue.id')
                    ->leftJoin('pharmacy_inventory', 'pharmacy_inventory.id', '=', 'pilgrim_medicine_details.med_id')
                    ->leftJoin('medical_receive_clinic', 'medical_receive_clinic.id', '=', 'pilgrim_medicine_issue.pharmacy_id')
                    ->where('pilgrim_medicine_issue.pid', $pid)
                    ->get([
                        'pilgrim_medicine_issue.date',
                        'pilgrim_medicine_details.quantity',
                        'pharmacy_inventory.med_type',
                        'pharmacy_inventory.trade_name',
                        'pharmacy_inventory.sku',
                        'medical_receive_clinic.name as pharmacy_name',
                    ]);
                $public_html = strval(view("REUSELicenseIssue::MedicineReceive.pilgrim-med-data", $data));
                return response()->json(['responseCode' => 1, 'html' => $public_html]);
            }else{
                return response()->json(['responseCode'=>0, 'msg'=>$responseData->msg]);
            }
        }catch (\Exception $e){
            return response()->json(['responseCode'=>0, 'msg'=>$e->getMessage()]);
        }

    }

}
