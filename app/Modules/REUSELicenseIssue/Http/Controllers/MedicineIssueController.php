<?php

namespace App\Modules\REUSELicenseIssue\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\MinioToken;
use App\Libraries\Encryption;
use App\Libraries\PostApiData;
use App\Models\User;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\DraftMedicine;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveClinic;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\PharmacyInventory;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\PilgrimMedDetails;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\PilgrimMedIssue;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\OcrMedicineResponse;
use App\Modules\REUSELicenseIssue\Services\StickerVisa;
use App\Modules\REUSELicenseIssue\Traits\HmisApiRequest;
use App\Modules\Users\Models\Users;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Traits\Token;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use App\Services\Minio;
use Yajra\DataTables\DataTables;

class MedicineIssueController extends Controller
{
    protected $api_url;
    use Token;
    use HmisApiRequest;
    public function __construct()
    {
        $this->api_url =  env('API_URL');
    }

    public function index(){
        $pharmacy = MedicalReceiveClinic::where('id', Auth::user()->working_company_id)->first(['id', 'name']);
        $medicine = PharmacyInventory::where('pharmacy_id', Auth::user()->working_company_id)->get([
            'id',
            'med_type',
            'trade_name',
            'sku',
            'quantity'
        ]);
        $medicineArr = [];
        foreach ($medicine as $key => $item){
            $medicineArr[$item->id] = $item->med_type.' '.$item->trade_name.' '.$item->sku;
        }

        return view('REUSELicenseIssue::MedicineIssue.index', compact('pharmacy', 'medicineArr'));
    }

    public function changePharmacy(Request $request){
        try {
            $pharmacyId = $request->pharmacyId;
            Users::where('id', Auth::user()->id)->update(['working_company_id' => $pharmacyId]);
            return response()->json(['responseCode'=>1, 'msg'=> 'Pharmacy changed successfully!']);
        }catch (\Exception $e){
            return response()->json(['responseCode'=>0, 'msg'=> $e->getMessage()]);
        }

    }

    public function savePharmacy(Request $request){
        try {
            $pharmacyId = $request->pharmacy_id;
            Users::where('id', Auth::user()->id)->update(['working_company_id' => $pharmacyId, 'first_login' => 1]);
            return redirect('/dashboard');
        }catch (\Exception $e){
            Session::flash('error', $e->getMessage());
            return redirect('/dashboard');
        }

    }

    public function searchPilgrim(Request $request){

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
                return response()->json(['responseCode'=>1, 'data'=>$responseData]);
            }else{
                return response()->json(['responseCode'=>0, 'msg'=>$responseData->msg]);
            }
        }catch (\Exception $e){
            return response()->json(['responseCode'=>0, 'msg'=>$e->getMessage()]);
        }

    }
    public function searchPilgrimByPassport(Request $request){
        try {
            $passport_no = $request->passport_no;
            $postData = [
                'passport_no' => $passport_no
            ];
            $postdata = http_build_query($postData);
            $base_url = env('API_URL');
            $url = "$base_url/api/get-pilgrim-by-passportNo";
            $response = PostApiData::getData($url,$postdata);
            $responseData = json_decode($response);
            if ($responseData->status == 200) {
                return response()->json(['responseCode'=>1, 'data'=>$responseData]);
            }else{
                return response()->json(['responseCode'=>0, 'msg'=>$responseData->msg]);
            }
        }catch (\Exception $e){
            return response()->json(['responseCode'=>0, 'msg'=>$e->getMessage()]);
        }

    }
    public function searchPilgrimByPidOrPassport(Request $request){
        try {
            $pid = $request->pid;
            $postData = [
                'pid' => $pid
            ];
            $postdata = http_build_query($postData);
            $base_url = env('API_URL');
            $url = "$base_url/api/get-pilgrim-by-passport-or-pid";
            $response = PostApiData::getData($url,$postdata);
            $responseData = json_decode($response);
            if ($responseData->status == 200) {
                return response()->json(['responseCode'=>1, 'data'=>$responseData]);
            }else{
                return response()->json(['responseCode'=>0, 'msg'=>$responseData->msg]);
            }
        }catch (\Exception $e){
            return response()->json(['responseCode'=>0, 'msg'=>$e->getMessage()]);
        }

    }

    public function store(Request $request){
        try {
            DB::beginTransaction();
            $medIssue = new PilgrimMedIssue();
            $medIssue->pharmacy_id = $request->pharmacy_id;
            $medIssue->pid = $request->pid;
            $medIssue->date = date('Y-m-d', strtotime($request->date));
            $medIssue->save();
            foreach ($request->medicine as $key => $item){
                $details = new PilgrimMedDetails();
                $details->master_id = $medIssue->id;
                $details->med_id = $request->medicine[$key];
                $details->quantity = $request->quantity[$key];
                $details->save();
                if (str_contains($details->quantity, '-')){
                    $details->quantity = str_replace('-', '', $details->quantity);
                    PharmacyInventory::where('id', $details->med_id)->update(['quantity' => DB::raw("quantity + $details->quantity")]);
                }else{
                    PharmacyInventory::where('id', $details->med_id)->update(['quantity' => DB::raw("quantity - $details->quantity")]);
                }
            }
            DB::commit();
            Session::flash('success', 'Medicine delivered successfully');
            return redirect('/medicine-issue');
        }catch (\Exception $e){
            DB::rollBack();
            Session::flash('error', $e->getMessage());
            return redirect()->back();
        }
    }
    public function imageUploadApi(Request $request, $id = "")
    {
        try{
            if (empty($id)) {
                $isDraftedImageUpload = $this->draftedImageUpload($request, 'upload');
                $encodeId =  $isDraftedImageUpload['id'];
                if ($encodeId == "") {
                    Session::flash('error', 'Data Not Saved. [DFT-001]');
                    return redirect()->back();
                }
                $decodedId = Encryption::decodeId($encodeId);
            } else {
                $decodedId = Encryption::decodeId($id);
                $encodeId = $id;
            }
            $draftMedicineData = DraftMedicine::where('id', $decodedId)->first();
            if(!$draftMedicineData){
                Session::flash('error', 'Data missing [DFT-002]');
                return redirect()->back();
            }
            $ObjectURL = $draftMedicineData['image_url'];
            $draftPid = $draftMedicineData['pid'];
            $minioImgFormattedURL = basename($ObjectURL);

            $minio = new Minio('hajj-dev');
            $imageData = $minio->get($minioImgFormattedURL, "image/png");
            if (!$imageData) {
                Session::flash('error', 'Image Not found, Please Try Again !!! [MNO-004]');
                return redirect()->back();
            }
            $base64Image = explode(",", $imageData)[1];
            $apiUrl = env('ML_API_URL')."/medi/render_prescriptionbase64";
            $postData = [
                'base64_image' => $base64Image,
            ];
            $postData = json_encode($postData);
            $headers = [
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);

            DB::beginTransaction();
            $ocrResponse = new OcrMedicineResponse ();
            $ocrResponse->json_response = json_encode($apiResponse);
            $ocrResponse->pharmacy_id =  Auth::user()->working_company_id ;
            $ocrResponse->user_id = Auth::user()->id;
            $ocrResponse->draft_medicine_id = $decodedId;
            $ocrResponse->image_url = $ObjectURL;
            $ocrResponse->save();
            DB::commit();

            if ($apiResponse['http_code'] !== 200) {
                Session::flash('error', 'Scan prescription API response failed!! [OCR-001]');
                $apiResponseDataArr = ['medicines' => [], 'passport_no' => 'NAN'];
            } else {
                $apiResponseDataArr = json_decode($apiResponse['data'],true);
            }

            if($apiResponseDataArr == null){
                Session::flash('error', 'Data Not found !!! [OCR-002]');
            }
            if (empty($apiResponseDataArr['medicines']) || $apiResponseDataArr['medicines'] == null || $apiResponseDataArr['medicines'] == '' ) {
                Session::flash('error', 'Medicine Not found !!! [OCR-003]');
            }
                $pharmacy = MedicalReceiveClinic::where('id', Auth::user()->working_company_id)->first(['id', 'name']);
                $medicines = PharmacyInventory::where('pharmacy_id', Auth::user()->working_company_id)->get([
                    'id',
                    'med_type',
                    'trade_name',
                    'sku',
                    'quantity'
                ]);
                $medicineArr = [];
                $medicineTradeArr = [];
                foreach ($medicines as $key => $item){
                    $medicineArr[$item->id] = $item->med_type.' '.$item->trade_name.' '.$item->sku;
                    $medicineTradeArr[$item->trade_name] = $item->id;
                }

                $returnData['medicines'] = $apiResponseDataArr && $apiResponseDataArr['medicines'] ? $apiResponseDataArr['medicines'] : [];
                $passportNo = $apiResponseDataArr['passport_no'] !== "NAN" && strlen($apiResponseDataArr['passport_no']) === 9 ? $apiResponseDataArr['passport_no'] : null;
                return view('REUSELicenseIssue::MedicineIssue.scan-medicine-data', compact('imageData','encodeId','passportNo','returnData','pharmacy', 'medicineTradeArr', 'medicineArr', 'draftPid'));

        } catch (\Exception $e) {
        Session::flash('error', 'Sorry!' . $e->getMessage() . '[OCR-1019]');
        return redirect()->back();
        }
    }

    public function scanMedicineStore(Request $request)
    {
        if(empty($request->get('pid')) || $request->get('pid') == null || $request->get('pid') == ""){
            Session::flash('error', 'Please Provide PID');
            return redirect('/medicine-issue');
        }
        if(empty($request->get('hidden_draft_id')) || $request->get('hidden_draft_id') == null || $request->get('hidden_draft_id') == ""){
            Session::flash('error', 'Image not found.');
            return redirect('/medicine-issue');
        }
        try {
            $pid = $request->pid;
            $hajjSession = HajjSessions::where('state', 'active')->value('id');
            if(empty($hajjSession)) {
                Session::flash('error', 'Active Session not Found');
                return redirect('/medicine-draft');
            }
            $postData = [
                'pid' => $pid
            ];
            $postdata = http_build_query($postData);
            $base_url = env('API_URL');
            $url = "$base_url/api/get-pilgrim-by-pid";
            $response = PostApiData::getData($url,$postdata);
            $responseData = json_decode($response);
            if ($responseData->status !== 200) {
                Session::flash('error', 'Please Provide Valid PID');
                return redirect('/medicine-draft');
            }else{
                DB::beginTransaction();
                $medIssue = new PilgrimMedIssue();
                $medIssue->pharmacy_id = $request->pharmacy_id;
                $medIssue->pid = $request->pid;
                $medIssue->date = date('Y-m-d', strtotime($request->date));
                $medIssue->save();
                foreach ($request->medicine as $key => $item) {
                    $details = new PilgrimMedDetails();
                    $details->master_id = $medIssue->id;
                    $details->med_id = $request->medicine[$key];
                    $details->quantity = $request->quantity[$key];
                    $details->selected_med_name = PharmacyInventory::where('id', $request->medicine[$key])->value('trade_name');
                    $details->scan_med_name = isset($request->scan_med_nm) && $request->scan_med_nm[$key] ? $request->scan_med_nm[$key] : '';
                    $details->save();
                    if (str_contains($details->quantity, '-')) {
                        $details->quantity = str_replace('-', '', $details->quantity);
                        PharmacyInventory::where('id', $details->med_id)->update(['quantity' => DB::raw("quantity + $details->quantity")]);
                    } else {
                        PharmacyInventory::where('id', $details->med_id)->update(['quantity' => DB::raw("quantity - $details->quantity")]);
                    }
                }
                $decodedDraftId = Encryption::decodeId($request->get('hidden_draft_id'));
                DraftMedicine::where('id', $decodedDraftId)->update(['is_draft' => 3,'issue_checker' => 2, 'pid' => trim($request->pid)]);
                DB::commit();

                $nextDraftId = DraftMedicine::where('id', '<', $decodedDraftId)
                    ->where([
                        'pharmacy_id' => Auth::user()->working_company_id,
                        'session_id' => $hajjSession,
                    ])
                    ->where('is_draft', '!=', 3)
                    ->where('issue_checker', '!=', 1)
                    ->orderBy('id', 'desc')
                    ->value('id');

                Session::flash('success', 'Medicine delivered successfully');
                if (!empty($nextDraftId)) {
                    return redirect('/medicine-draft/edit/' . Encryption::encodeId($nextDraftId) );
                } else {
                    return redirect('/medicine-draft');
                }

            }

        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', $e->getMessage());
            return redirect('/medicine-draft');
        }
    }

    public function draftMedicineList(){
        try{
            $pharmacy = MedicalReceiveClinic::where('id', Auth::user()->working_company_id)->first(['id', 'name']);
            return view("REUSELicenseIssue::MedicineIssue.list", compact('pharmacy' ));
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function getDraftMedicineList(Request $request)
    {
        try {
            $hajjSession = HajjSessions::where('state', 'active')->value('id');
            $list = DraftMedicine::where([
                'pharmacy_id' => Auth::user()->working_company_id,
                'session_id' => $hajjSession,
            ])
                ->where('is_draft', '!=', 3)
                ->orderBy('id','desc') // Corrected orderBy function
                ->get();
            return Datatables::of($list)
                ->addColumn('action', function ($list) use ($request) {
                    $html = '<a href="/medicine-draft/edit/' . Encryption::encodeId($list->id) . '"
                              class="btn btn-xs btn-outline-success open-btn"  data-row-id="' . $list->id . '" target="_blank">
                              <i class="fa fa-box-open"></i> Open
                            </a> &nbsp;';
                    return $html;
                })
                ->editColumn('updated_at', function ($list) {
                    $time = $list->updated_at;
                    if($time){
                        $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $time)->format('d-M-Y H:i:s');
                    }else{
                        $updated_at = "N/A";
                    }
                    return $updated_at;
                })
                ->editColumn('image_url', function ($list) {
                    $img = $list->image_url;
                    if(!empty($img)){
                        $returnImg = $img;
                    }else{
                        $returnImg = "N/A";
                    }
                    return $returnImg;
                })
                ->editColumn('issue_checker', function ($list) {
                    $issue_checker = $list->issue_checker;
                    if(!empty($issue_checker) && $issue_checker == 1){
                        $returnissue_checker = "Reject";
                    } else {
                        $returnissue_checker = "Pending";
                    }
                    return $returnissue_checker;
                })
                ->addIndexColumn('DT_RowIndex')
                ->rawColumns(['action','DT_RowIndex'])
                ->make(true);
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[PCC-1002]');
            return redirect()->back();
        }
    }

    public function draftedImageUpload(Request $request, $flag = "")
    {
        if ($request->has('flag')) {
            $flag = $request->input('flag');
        }
        try{
            $base64Image = $request->input('image');
            $ObjectURL = CommonFunction::getMinioUploadedFileURL($base64Image);
            if (!$ObjectURL) {
                return false;
            }

            $hajjSessionId = HajjSessions::where(['state' => 'active'])->value('id');
            if ($flag == "upload") {
                $is_draft = 2;
            }else {
                $is_draft = 1;
            }

            $insert = DraftMedicine::create([
                'pharmacy_id' => Auth::user()->working_company_id,
                'is_draft' => $is_draft,
                'session_id' => $hajjSessionId,
                'image_url' => $ObjectURL,
                'created_at' => Carbon::now(),
                'created_by' => CommonFunction::getUserId(),
                'updated_at' => Carbon::now(),
                'updated_by' => CommonFunction::getUserId()
            ]);

            if ($insert) {
                if ($flag == "upload") {
                    $Id = Encryption::encodeId($insert->id);
                    return ['id' => $Id, 'ObjectURL' => $ObjectURL];
                } else {
                    return response()->json(['responseCode' => 1, "message"=> "Prescription drafted successfully."]);
                }
            } else {
                if ($flag == "upload") {
                    return ['id' => "", 'ObjectURL' => ""];
                } else {
                    return response()->json(['responseCode' => -1, "message"=> "Prescription drafted falied."]);
                }
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry!' . $e->getMessage() . '[OCR-1009]');
            return redirect()->back();
        }
    }

    public function draftRejectAction($id) {
        try{
            if(!empty($id)) {
                $decodedDraftId = Encryption::decodeId($id);
                DraftMedicine::where('id', $decodedDraftId)->update(['issue_checker' => 1]);
                return response()->json(['responseCode'=>1, 'msg'=>"Rejected", 'url'=>'/medicine-draft']);
            } else {
                return response()->json(['responseCode'=>0, 'msg'=>'Draft Information Missing']);
            }

        } catch (\Exception $e){
            return response()->json(['responseCode'=>0, 'msg'=>$e->getMessage()]);
        }
    }

}
