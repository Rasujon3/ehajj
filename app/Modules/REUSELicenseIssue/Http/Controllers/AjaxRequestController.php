<?php

namespace App\Modules\REUSELicenseIssue\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\PostApiData;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\REUSELicenseIssue\Services\StickerVisa;
use App\Modules\REUSELicenseIssue\Traits\HmisApiRequest;
use App\Modules\Settings\Models\Area;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Traits\Token;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class AjaxRequestController extends Controller
{
    protected $api_url;
    use Token;
    use HmisApiRequest;
    public function __construct()
    {
        $this->api_url =  env('API_URL');
    }
    public function dropDownList(Request $request)
    {
        $process_type = $request['pilgrim_type'];
        $tokenData = $this->getToken();
        $token = json_decode($tokenData)->token;

        $ch = curl_init();

        // Set the API endpoint URL
        $base_url = $this->api_url;
        $url = "$base_url/api/get-pilgrim_listing_dropdown?pilgrim_type=$process_type";


        // $pilgrim_type = "Government";
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/x-www-form-urlencoded',
        );

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


        $response = curl_exec($ch);

        if (curl_error($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }

        curl_close($ch);

        if ($response) {
            $responseData = json_decode($response, true);
        }

        return $responseData;
        // return response()->json(['responseCode' => 1, 'data' => $responseData]);

    }
    public function getSubTeamData(Request $request){
        $teamId = $request->get('team_id');
        return $this->getSubTeamDataByTeamId($teamId,true);;
    }
    public function getStickerVisaGoMember(Request $request){
        return  StickerVisa::getStickerVisaGoMemberInfo($request,true);
    }
    public function getHmisPilgrimListing($encoded_process_type_id, Request $request)
    {
        $data = explode(',', $request->request_data);

        // Set the POST data
        $postData = [
            'request_data' => json_encode($data),
            'is_crm_guide' => $request->is_crm_guide
        ];
//        dd($postData);
        $postdata = http_build_query($postData);

        $base_url = env('API_URL');
        $url = "$base_url/api/hmis-pilgrims-for-guide";

        $response = PostApiData::getData($url,$postdata);

        $responseData= '';
        if ($response) {
            $responseData = json_decode($response);
        }
        return response()->json(['responseCode' => 1, 'data' => $responseData]);
    }
    public function getGuideDetails($encoded_process_type_id, Request $request)
    {
        $data = explode(',', $request->request_data);
        // Set the POST data
        $postData = [
            'guide_id' => $request->guide_id
        ];
        $postdata = http_build_query($postData);
        $base_url = env('API_URL');
        $url = "$base_url/api/guide-details";
        $response = PostApiData::getData($url,$postdata);
        $responseData= '';
        if ($response) {
            $responseData = json_decode($response);
        }
        return response()->json(['responseCode' => 1, 'data' => $responseData]);
    }

    public function searchAgencyLicence(Request $request, $flag=false) {
        try {
            $processTypeId = Encryption::decodeId($request->get('process_type_id'));
            if(empty($processTypeId) || $processTypeId == null ){
                return $this->sendErrorResponse('Process Type Invalid [PT:001]');
            }

            $licenseNo = $request->get('licence_no');
            if(!$flag){
                $checkProcessList = ProcessList::where(['process_type_id' => $processTypeId, 'status_id' => 1])
                    ->where('json_object', 'like', '%' . $licenseNo . '%')
                    ->count();

                if($checkProcessList > 0){
                    return $this->sendErrorResponse('Already in Process [TN:123]');
                }
            }

            $token = $this->getApiToken();
            if (!$token) {
                return $this->sendErrorResponse('ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [TN:022]');
            }
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/get-agency-info-by-licence-no";
            $postData =[
                "licence_no"=> $request->get('licence_no'),
            ];
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = $this->sendApiRequest($apiUrl, $postData, $headers);
            $decodeApiResponseData = json_decode($apiResponse['data']);
            if ($apiResponse['http_code'] !== 200) {
                return $this->sendErrorResponse($decodeApiResponseData->msg);
            }
            return response()->json(['responseCode' => 1, 'message' => $decodeApiResponseData->msg, 'data' => $decodeApiResponseData->data, 'status' => true]);

        } catch (\Exception $e) {
            #dd($e->getMessage());
            Log::error('Error in save passport request: ' . $e->getMessage());
            return $this->sendErrorResponse('ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PV:078]');
        }
    }

    public function agencyInfoStoreForm(Request $request) {
        try {
            $processTypeId = Encryption::decodeId($request->get('process_type_id'));
            if(empty($processTypeId) ){
                Session::flash('error',' Process Type Invalid [AG:001]');
                return Redirect::back();
            }
            if(empty($request->agency_section) && empty($request->pre_reg_section) && empty($request->agency_user_section) ){
                Session::flash('error',' আপনি কোন চেক বক্স সিলেক্ট করেন নি [AG:005]');
                return Redirect::back();
            }
            if(empty($request->get('haj_license_no')) ){
                Session::flash('error',' আপনার এজেন্সির Licence নাম্বার পাওয়া যায়নি [AG:002]');
                return Redirect::back();
            }
            if($request->get('agency_status') === "0" ){
                if(empty($request->inactive_reason) && empty($request->inactive_date)){
                    Session::flash('error',' আপনি inactive reason অ্যান্ড inactive deadline প্রদান করেন নি [AG:077]');
                    return Redirect::back();
                }
            }
            $hajjSessionId = HajjSessions::where(['state' => 'active'])->first('id');
            $pilgrimData = [
                'process_type_id'=> $processTypeId,
                'haj_license_no'=> $request->haj_license_no,
                'user_name'=> $request->user_name,
                'agency_name'=> $request->agency_name,
                'user_email'=> $request->user_email,
                'agency_id'=> $request->agency_id,
                'user_status'=> $request->user_status,
                'agency_status'=> $request->agency_status,
                'inactive_reason'=> $request->inactive_reason,
                'inactive_date'=> $request->inactive_date,
                'pre_reg_status'=> $request->pre_reg_status,
                'active_user_id'=> $request->active_user_id,
                'user_phone'=> $request->user_phone,
                'chk_current_agency_status'=> $request->chk_current_agency_status,
                'chk_current_pre_reg_agency_status'=> $request->chk_current_pre_reg_agency_status,
                'chk_current_user_status'=> $request->chk_current_user_status,
                'chk_current_user_email'=> $request->chk_current_user_email,
                'agency_section'=> !empty($request->agency_section) ? $request->agency_section : 0,
                'pre_reg_section'=> !empty($request->pre_reg_section) ? $request->pre_reg_section : 0,
                'agency_user_section'=> !empty($request->agency_user_section) ? $request->agency_user_section : 0,
            ];

            DB::beginTransaction();

            $appData = new PilgrimDataList();
            $jsonData = json_encode($pilgrimData);
            $appData->json_object = $jsonData;
            $appData->process_type = "agency_info_update";
            $appData->process_type_id = $processTypeId;
            $appData->session_id = !empty($hajjSessionId->id) ? $hajjSessionId->id : 0;
            $appData->request_type = 'agency_info_update';

            if (!$appData->save()) {
                DB::rollBack();
                Session::flash('error','Failed to save updated agency information. Please try again. [AG:111]');
                return Redirect::back();
            }

            # Here send the data for process
            $processJsonData = [
                'haj_license_no'=> $request->haj_license_no,
                'user_name'=> $request->user_name,
                'agency_name'=> $request->agency_name,
                'user_email'=> $request->user_email,
                'user_phone'=> $request->user_phone,
            ];
            $processJsonData = json_encode($processJsonData);
            $processData = new ProcessList();
            $trackingPrefix = 'AIU';
            $tracking_no = $trackingPrefix . strtoupper(dechex($processTypeId . $appData->id));
            $processData->tracking_no =$tracking_no;
            $processData->locked_at = date('Y-m-d H:i:s');
            $processData->submitted_at = date('Y-m-d H:i:s', time());
            $processData->process_type_id = $processTypeId;
            $processData->ref_id = $appData->id;
            $processData->desk_id = 7; // Aproval desk
            $processData->status_id = 1; // submitted status
            $processData->json_object = $processJsonData;
            $processData->created_at = date('Y-m-d H:i:s');
            $processData->created_by = Auth::user()->id;

            if (!$processData->save()) {
                DB::rollBack();
                Session::flash('error','Failed to save process data. Please try again. [AG:112]');
                return Redirect::back();
            }
            DB::commit();

            Session::flash('success','  এজেন্সির তথ্য আপডেটের রিকোয়েস্টটি সফলভাবে পাঠানো হয়েছে [AG-1011]');
            return redirect('/process/list');
        } catch (\Exception $e) {
            #dd($e->getMessage(). '@@@' . $e->getFile() .'@@@'. $e->getLine());
            DB::rollBack();
            Log::error('Error 208: ' . $e->getMessage());
            Session::flash('error', 'দুঃখিত! অনুগ্রহ করে আবার চেষ্টা করুন। [AG-1012]');
            return redirect('/process/list');
        }
    }

    public function viewAgencyInfo($processTypeId, $applicationId): JsonResponse
    {
        try {
            $data = array();
            $appmasterId = Encryption::decodeId($applicationId);
            $getProcessData = PilgrimDataList::where('id', $appmasterId)->where('process_type_id',$processTypeId)->first();
            $json_object = json_decode($getProcessData->json_object);
            $data['json_object'] = $json_object;
            $data['ref_id'] = $applicationId;
            $data['process_type_id'] = Encryption::encodeId($processTypeId);

            // Create a new request instance from an array
            $request = new Request();
            $request->replace([
                'licence_no' => $json_object->haj_license_no,
                'process_type_id' => Encryption::encodeId($processTypeId),
            ]);

            $response = $this->searchAgencyLicence($request, true);
            $currentData = json_decode($response->content());
            if($currentData->responseCode == 1){
                $data['currentData'] = $currentData->data;
            }
            $public_html = strval(view("REUSELicenseIssue::Agency.agencyInfoView", $data));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        }
        catch (\Exception $e){
            Log::error('Error : ' . $e->getMessage(). $e->getline());
            echo 'something went wrong.';
            exit();
        }
    }
    public function updateAgencyInfo($processInfoRefId)
    {
        try {
            $getData = PilgrimDataList::where('id', $processInfoRefId)->first();
            $request = json_decode($getData->json_object);
            if(empty($getData)){
                Session::flash('error', 'Agency information not found. [AG-1200]');
                return false;
            }
            $token = $this->getApiToken();
            if (!$token) {
                Session::flash('error',' ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [TN:022]');
                return false;
            }
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/update-agency-info";
            $postData = [
                'haj_license_no'=> $request->haj_license_no,
                'user_email'=> $request->user_email,
                'agency_id'=> $request->agency_id,
                'user_status'=> $request->user_status,
                'agency_status'=> $request->agency_status,
                'inactive_reason'=> $request->inactive_reason,
                'inactive_date'=> $request->inactive_date,
                'pre_reg_status'=> $request->pre_reg_status,
                'active_user_id'=> $request->active_user_id,
                'agency_section'=> !empty($request->agency_section) ? $request->agency_section : 0,
                'pre_reg_section'=> !empty($request->pre_reg_section) ? $request->pre_reg_section : 0,
                'agency_user_section'=> !empty($request->agency_user_section) ? $request->agency_user_section : 0,
            ];
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = $this->sendApiRequest($apiUrl, $postData, $headers);
            //dd($apiResponse);
            $decodeApiResponseData = json_decode($apiResponse['data']);
            if ($apiResponse['http_code'] !== 200) {
                Session::flash('error', $decodeApiResponseData->msg.'[AG-1220]');
                return false;
            }
            Session::flash('success', $decodeApiResponseData->msg.'[AG-1210]');
            return true;

        } catch (\Exception $e) {
            #dd($e->getMessage());
            Log::error('Error' . $e->getMessage(). $e->getLine());
            return false;
        }
    }
    public function getThanaByDistrictId(Request $request)
    {
        try {
            $district_id = CommonFunction::vulnerabilityCheck($request->get('districtId'));

            $thanas = Area::select(DB::raw("CONCAT(AREA_ID,' ') AS AREA_ID"), 'area_nm')
                ->where('PARE_ID', $district_id)->orderBy('area_nm_ban', 'ASC')->pluck('area_nm', 'AREA_ID')->toArray();
            $data = ['responseCode' => 1, 'data' => $thanas];
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error' . $e->getMessage(). $e->getLine() . '[ARC-100]');
            $msg = 'Something went wrong !!! [ARC-100]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getAllDistrictListData(Request $request) {
        try {
            $allDistrictData = Area::where('area_type', 2)->get();
            return response()->json(['responseCode' => 1, 'data' => $allDistrictData]);

        } catch (\Exception $e) {
            Log::error('Error' . $e->getMessage(). $e->getLine().' [ARC-101]');
            $msg = 'Something went wrong !!! [ARC-101]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }

    private function sendApiRequest($apiUrl, array $postData, array $headers){
        return CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
    }
    private function getApiToken(){
        return CommonFunction::getTokenData();
    }
    private function sendErrorResponse($message){
        return response()->json([
            'responseCode' => 0,
            'status' => false,
            'message' => $message
        ]);
    }

}
