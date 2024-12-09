<?php

namespace App\Modules\Pilgrims\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\ImageProcessing;
use App\Modules\CompanyProfile\Models\CompanyInfo;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessStatus;
use App\Modules\ProcessPath\Models\UserDesk;
use App\Modules\Settings\Models\ApplicationRollback;
use App\Modules\Settings\Models\Area;
use App\Modules\Settings\Models\ForcefullyDataUpdate;
use App\Modules\Settings\Models\MaintenanceModeUser;
use App\Modules\Users\Models\Users;
use App\Modules\Users\Models\UserTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use App\Modules\GoPassport\Http\Controllers\GoPassportController;

class PilgrimController extends Controller
{

    public function __construct(){ }
    public function index() {
        if(Auth::user()->user_type === '18x415' && !in_array(Auth::user()->working_user_type, ['Pilgrim', 'Guide'])) {
            Session::flash('error', 'আপনার কোন টাইপের ইউজার তা সিলেক্ট করুন। আপনি যদি হজযাত্রী হন তাহলে  Working user type থেকে Pilgrim সিলেক্ট করুন। আর যদি আপনি হজ গাইড হন তাহলে Guide সিলেক্ট করুন।');
            return redirect(url('/users/profileinfo'));
        }
        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        return view('Pilgrims::index');
    }
    public function getList(Request $request) {
        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        try {
            $search_input = $request->has('search') ? $request->get('search') : '';
            $limit = $request->get('limit');
            $page = $request->get('page');

            $prpUserID = Auth::user()->prp_user_id;
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-pre-reg-pilgrims";
            $data = [
                'user_id' => Encryption::encodeId($prpUserID),
                'limit' => $limit,
                'search' => $search_input,
                'page' => $page
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $data = !empty($apiResponseDataArr->data->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1, 'msg' => $apiResponseDataArr->msg ,'data' => $data]);
        } catch (\Exception $e) {
            #dd($e->getMessage(),$e->getLine());
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }

    }
    public function configNidPassport() {
        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        try {
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-nid-passport-config";
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl, [], $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            $verification_flag = $apiResponseDataArr->data;
            $data = [];
            foreach ($verification_flag as $key => $value) {
                $data[$value->caption] = $value->value;
            }

            return response()->json(['responseCode' => 1, 'status'=> true ,'data' => $data]);

        } catch (\Exception $e) {
            return response()->json(['responseCode' => 0, 'status'=> false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [CNP:001]']);
        }
    }
    public function getIndexData() {
        try {

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-index-data";
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl, [], $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            $decodeResponse = $apiResponseDataArr->data;
            $ocupationArr = [];
            $is_govt = [];
            foreach ($decodeResponse->occupation as $item) {
                $ocupationArr[$item->id] = $item->name;
                if($item->is_govt == 1) {
                    $is_govt[$item->id] = $item->is_govt;
                }
            }
            $indexData = [
                'district' =>$decodeResponse->district,
                'occupation' =>$ocupationArr,
                'bank_list' =>$decodeResponse->bankDistrictList->bank_list,
                'district_list' =>$decodeResponse->bankDistrictList->district_list,
                'account_owner_type' =>$decodeResponse->accountOwnerType,
                'is_govt' => $is_govt,
                'govtServiceGrade' => $decodeResponse->govtServiceGrade,
                'nationalitys' => $decodeResponse->nationalitys,
            ];
            return response()->json(['responseCode' => 1, 'status'=> true ,'data' => $indexData]);

        }catch (\Exception $e) {
            #dd($e->getMessage());
            return response()->json(['responseCode' => 0, 'status'=> false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [CNP:001]']);
        }
    }
    public function getBankBranch( $bankId,$bankDistrictId) {
        try {
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-bank-branches";
            $postData = [
                'bank_id' => $bankId,
                'bank_district_id' => $bankDistrictId,
            ];
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode($postData), $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            $decodeResponse = $apiResponseDataArr->data;
          return response()->json(['responseCode' => 1, 'status'=> true ,'data' => $decodeResponse]);

        } catch (\Exception $e) {
        #dd($e->getMessage());
        return response()->json(['responseCode' => 0, 'status'=> false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [CNP:001]']);
        }
    }
    public function getPoliceStation( $districtId) {
        try {
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-police-stations";
            $postData = [
                'districtId' => $districtId,
            ];
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode($postData), $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            $decodeResponse = $apiResponseDataArr->data;
          return response()->json(['responseCode' => 1, 'status'=> true ,'data' => $decodeResponse]);

        } catch (\Exception $e) {
        #dd($e->getMessage());
        return response()->json(['responseCode' => 0, 'status'=> false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [CNP:001]']);
        }
    }
    public function getDistrictPoliceStationbyPostCode($postCode) {
        try{
            $clientID = config('constant.EBS_CLIENT_ID');
            $clientSecret = config('constant.EBS_CLIENT_SECRET');
            $url =config('constant.IDP_TOKEN_API_URL');
            $token = CommonFunction::getExternalToken($clientID, $clientSecret,$url);

            if (!$token) {
                return response()->json(['responseCode' => 0, 'message' => 'Server Error.']);
            }

            $postdata = [];
            $Url = config('constant.INSIGHTDB_POST_CODE_LIST_URL').$postCode;
            $headers = array(
                'Content-Type: application/json',
                "Authorization: Bearer $token",
            );

            $Response = CommonFunction::curlGetRequest($Url,$postdata, $headers);

            if ($Response['http_code'] == 200) {
                $ResponseArr = json_decode($Response['data'], true);
                if ($ResponseArr['responseCode'] == 200) {
                    if(!empty($ResponseArr['data'])){
                        $data = ['responseCode' => 1, 'status' => true, 'data' => $ResponseArr['data'][0]];
                    }else{
                        $data = ['responseCode' => 0,'status' => false,'message' => 'পোস্ট কোড এর কোন তথ্য পাওয়া যায়নি [PDD:001]', 'data' => ''];
                    }
                }
            } else {
                $data = ['responseCode' => 0,'status' => false,'message'=>'InsightDb ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PDD:001]', 'data' => ''];
            }
            return response()->json($data);
        }catch (\Exception $e){
            return response()->json(['responseCode' => 0, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PDD:002]']);
        }
    }
    public function passportVerification( Request $request) {
        try {
          $goController = new GoPassportController();
          $response =$goController->passportVerifyRequest($request);
          $passportData = json_decode($response->content());
          if($passportData->responseCode == 1){
              $data = ['responseCode' => 1,'status' => true,'message'=>'Data Found ', 'data' => $passportData->data];
          }else{
              $data = ['responseCode' => 0,'status' => false,'message'=> $passportData->msg, 'data' => ''];
          }
          return response()->json($data);
        } catch (\Exception $e) {
            #dd($e->getMessage());
            return response()->json(['responseCode' => 0, 'status'=> false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PV:002]']);
        }
    }
    public function store(Request $request) {
        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        try{
            $validator = [
                'resident' => 'required|string',
                'gender' => 'required|string',
                'identity' => 'required|string',
                'birth_date' => 'required',

                'name_en' => 'required|string|max:255',

                'occupation' => 'required|string|max:100',
                'mobile' => 'required|string|max:15',
                'marital_status' => 'required|string|max:20',

                'permanent_post_code' => 'required|string|max:10',
                'permanent_district_id' => 'required',
                'permanent_district_name' => 'required',
                'permanent_police_station_id' => 'required',
                'permanent_police_station_name' => 'required',
                'permanent_address' => 'required|string|max:255',

                'present_post_code' => 'required',
                'present_district_id' => 'required',
                'present_district_name' => 'required',
                'present_police_station_id' => 'required',
                'present_police_station_name' => 'required',
                'present_address' => 'required|string|max:255',

                'refund_account_type' => 'required',
                'refund_account_name' => 'required|string|max:255',
                'refund_account_number' => 'required|string|max:30',
                'refund_bank_id' => 'required|integer',
                'refund_branch_district' => 'required|integer',
                'refund_routing_no' => 'required|string|max:20',
            ];

            if($request->get('resident') !== 'Bangladeshi') {
                $validator['nationality2'] = 'required';
            }
            $is_govt_job = $request->has('is_govt_job') ? filter_var($request->get('is_govt_job'), FILTER_VALIDATE_BOOLEAN) : false;
            if($is_govt_job) {
                $validator['designation'] = 'required';
                $validator['serviceGrade'] = 'required';
            }
            if($request->get('marital_status') === 'Married') {
                $validator['spouse_name'] = 'required';
            }
            $identity = $request->has('identity') ? $request->get('identity') : '';
            if($identity !== 'PASSPORT') {
                $validator['name_bn'] = 'required|string|max:255';
                $validator['father_name_bn'] = 'required|string|max:255';
                $validator['mother_name_bn'] = 'required|string|max:255';
                $validator['pilgrim_img'] = 'required';
            }
            if($identity === 'NID') {
                $validator['nid_number'] = 'required|string|max:20';
            } elseif ($identity === 'PASSPORT') {
                $validator['father_name_en'] = 'required|string|max:255';
                $validator['mother_name_en'] = 'required|string|max:255';
                $validator['passport_number'] = 'required|string|max:20';
                $validator['passport_type'] = 'required|string|max:20';
            }elseif ($identity === 'DOB') {
                $validator['brn'] = 'required';
                $validator['dob_img'] = 'required';
            } else {
                return response()->json(['responseCode' => -1, 'msg' => 'Identity is required']);
            }
            $this->validate($request, $validator);

            if ($identity === 'DOB') {
                $dob_image = $request->file('dob_img');
                // Get the image's contents
                $dob_imageContents = file_get_contents($dob_image->getRealPath());
                // Convert the image to base64
                $dob_base64Image = base64_encode($dob_imageContents);
                $dob_base64ResizeImage = base64_encode(ImageProcessing::resizeBase64Image($dob_base64Image, 350, 600));
            }

            $postData = [
                'resident' => $request->input('resident'),
                'gender' => $request->input('gender'),
                'identity' => $request->input('identity'),
                'birth_date' => date("Y-m-d", strtotime($request->input('birth_date'))),
                'passport_type' => $request->input('passport_type'),
                'passport_number' => $request->input('passport_number'),
                'nid_number' => $request->input('nid_number'),

                'name_bn' => $request->input('name_bn'),
                'name_en' => $request->input('name_en'),

                'occupation' => $request->input('occupation'),
                'mobile' => $request->input('mobile'),
                'marital_status' => $request->input('marital_status'),

                'permanent_post_code' => $request->input('permanent_post_code'),
                'permanent_district_id' => $request->input('permanent_district_id'),
                'permanent_district_name' => $request->input('permanent_district_name'),
                'permanent_police_station_id' => $request->input('permanent_police_station_id'),
                'permanent_police_station_name' => $request->input('permanent_police_station_name'),
                'permanent_address' => $request->input('permanent_address'),

                'present_post_code' => $request->input('present_post_code'),
                'present_district_id' => $request->input('present_district_id'),
                'present_district_name' => $request->input('present_district_name'),
                'present_police_station_id' => $request->input('present_police_station_id'),
                'present_police_station_name' => $request->input('present_police_station_name'),
                'present_address' => $request->input('present_address'),

                'refund_account_type' => $request->input('refund_account_type'),
                'refund_account_name' => $request->input('refund_account_name'),
                'refund_account_number' => $request->input('refund_account_number'),
                'refund_bank_id' => $request->input('refund_bank_id'),
                'refund_branch_district' => $request->input('refund_branch_district'),
                'refund_routing_no' => $request->input('refund_routing_no'),

                'prp_user_id' => Encryption::encodeId(Auth::user()->prp_user_id),
                'is_group' => $request->has('is_group') ? $request->input('is_group') : 0,
                'leader_id' => $request->has('leader_id') ? $request->input('leader_id') : 0,
                'nationalId_img' => $request->has('nationalId_img') ? $request->input('nationalId_img') : '',
                'pilgrim_img' => $request->has('pilgrim_img') ? $request->input('pilgrim_img') : '',
                'is_govt' => 'Government',
                'is_govt_job' => $is_govt_job,
            ];

            if($request->get('resident') !== 'Bangladeshi') {
                $postData['nationality2'] =$request->has('nationality2') ? $request->get('nationality2') : '';
            }
            if($is_govt_job) {
                $postData['designation'] = $request->has('designation') ? $request->get('designation') : '';
                $postData['serviceGrade'] = $request->has('serviceGrade') ? $request->get('serviceGrade') : '';
            }
            if($request->get('marital_status') === 'Married') {
                $postData['spouse_name'] = $request->has('spouse_name') ? $request->get('spouse_name') : '';
            }

            if($identity !== 'PASSPORT') {
                $postData['father_name_bn'] = $request->input('father_name_bn');
                $postData['mother_name_bn'] = $request->input('mother_name_bn');
                $postData['photo'] = $request->get('pilgrim_img');
            } else {
                $postData['father_name_en'] = $request->input('father_name_en');
                $postData['mother_name_en'] = $request->input('mother_name_en');
            }

            if ($identity === 'DOB') {
                $postData['birth_id'] = $request->has('brn') ? $request->get('brn') : '';
                $postData['dob_img'] = $dob_base64ResizeImage;
            }

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/store-pilgrim";

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode($postData), $headers,true);

            if($apiResponse['http_code'] !== 200) {
                $decodeResponse = json_decode($apiResponse['data'], true);
                return response()->json(['responseCode' => -1, 'msg' => $decodeResponse['msg']]);
            }
            $apiResponseDataArr = json_decode($apiResponse['data']);
            $decodeResponse = $apiResponseDataArr->data;

            return response()->json(['responseCode' => 1, 'status'=> true ,'data' => $decodeResponse]);
        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [PRE-001]';
            //dd($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [PRE-001]');
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [PRE-001]');
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }

    }
    public function updatePreRegPilgrim(Request $request) {
        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }

        try {
            $validator = [
                'tracking_no' => 'required|string|max:255',
                'pilgrim_id' => 'required|string|max:255',

                'resident' => 'required|string',
                'gender' => 'required|string',
                'identity' => 'required|string',
                'birth_date' => 'required',

                'name_en' => 'required|string|max:255',

                'occupation' => 'required|string|max:100',
                'mobile' => 'required|string|max:15',
                'marital_status' => 'required|string|max:20',

                'permanent_post_code' => 'required|string|max:10',
                'permanent_district_id' => 'required',
                'permanent_district_name' => 'required',
                'permanent_police_station_id' => 'required',
                'permanent_police_station_name' => 'required',
                'permanent_address' => 'required|string|max:255',

                'present_post_code' => 'required',
                'present_district_id' => 'required',
                'present_district_name' => 'required',
                'present_police_station_id' => 'required',
                'present_police_station_name' => 'required',
                'present_address' => 'required|string|max:255',

                'refund_account_type' => 'required',
                'refund_account_name' => 'required|string|max:255',
                'refund_account_number' => 'required|string|max:30',
                'refund_bank_id' => 'required|integer',
                'refund_branch_district' => 'required|integer',
                'refund_routing_no' => 'required|string|max:20',
            ];
            if($request->get('resident') !== 'Bangladeshi') {
                $validator['nationality2'] = 'required';
            }
            $is_govt_job = $request->has('is_govt_job') ? filter_var($request->get('is_govt_job'), FILTER_VALIDATE_BOOLEAN) : false;

            if($is_govt_job) {
                $validator['designation'] = 'required';
                $validator['serviceGrade'] = 'required';
            }
            if($request->get('marital_status') === 'Married') {
                $validator['spouse_name'] = 'required';
            }

            $identity = $request->has('identity') ? $request->get('identity') : '';
            if($identity !== 'PASSPORT') {
                $validator['father_name_bn'] = 'required|string|max:255';
                $validator['mother_name_bn'] = 'required|string|max:255';
                $validator['name_bn'] = 'required|string|max:255';
            }
            if($identity === 'NID') {
                $validator['nid_number'] = 'required|string|max:20';
            } elseif ($identity === 'PASSPORT') {
                $validator['father_name_en'] = 'required|string|max:255';
                $validator['mother_name_en'] = 'required|string|max:255';
                $validator['passport_number'] = 'required|string|max:20';
                $validator['passport_type'] = 'required|string|max:20';
            }elseif ($identity === 'DOB') {
                $validator['brn'] = 'required';
            } else {
                return response()->json(['responseCode' => -1, 'msg' => 'Identity is required']);
            }

            $this->validate($request, $validator);

            if(!empty($request->has('pilgrim_img'))) {
                $base64ResizeImage = $request->get('pilgrim_img');
            }
            $postData = [
                'tracking_no' => $request->input('tracking_no'),
                'pilgrim_id' => $request->input('pilgrim_id'),

                'resident' => $request->input('resident'),
                'gender' => $request->input('gender'),
                'identity' => $request->input('identity'),
                'birth_date' => date("Y-m-d", strtotime($request->input('birth_date'))),
                'passport_type' => $request->input('passport_type'),
                'passport_number' => $request->input('passport_number'),
                'nid_number' => $request->input('nid_number'),
                'brn' => $request->input('brn'),

                'name_en' => $request->input('name_en'),

                'occupation' => $request->input('occupation'),
                'mobile' => $request->input('mobile'),
                'marital_status' => $request->input('marital_status'),

                'permanent_post_code' => $request->input('permanent_post_code'),
                'permanent_district_id' => $request->input('permanent_district_id'),
                'permanent_district_name' => $request->input('permanent_district_name'),
                'permanent_police_station_id' => $request->input('permanent_police_station_id'),
                'permanent_police_station_name' => $request->input('permanent_police_station_name'),
                'permanent_address' => $request->input('permanent_address'),

                'present_post_code' => $request->input('present_post_code'),
                'present_district_id' => $request->input('present_district_id'),
                'present_district_name' => $request->input('present_district_name'),
                'present_police_station_id' => $request->input('present_police_station_id'),
                'present_police_station_name' => $request->input('present_police_station_name'),
                'present_address' => $request->input('present_address'),

                'refund_account_type' => $request->input('refund_account_type'),
                'refund_account_name' => $request->input('refund_account_name'),
                'refund_account_number' => $request->input('refund_account_number'),
                'refund_bank_id' => $request->input('refund_bank_id'),
                'refund_branch_district' => $request->input('refund_branch_district'),
                'refund_routing_no' => $request->input('refund_routing_no'),

                'prp_user_id' => Encryption::encodeId(Auth::user()->prp_user_id),
                'is_group' => $request->has('is_group') ? $request->input('is_group') : 0,
                'leader_id' => $request->has('leader_id') ? $request->input('leader_id') : 0,
                // 'nationalId_img' => $request->has('nationalId_img') ? $request->input('nationalId_img') : '',
                'photo' => isset($base64ResizeImage) ? $base64ResizeImage : null,
                'is_govt' => 'Government',
                'is_govt_job' => $is_govt_job,
            ];
            if( $request->get('identity') === 'DOB' && !empty($request->file('dob_img'))) {
                $dob_image = $request->file('dob_img');
                // Get the image's contents
                $dob_imageContents = file_get_contents($dob_image->getRealPath());
                // Convert the image to base64
                $dob_base64Image = base64_encode($dob_imageContents);
                $dob_base64ResizeImage = base64_encode(ImageProcessing::resizeBase64Image($dob_base64Image, 400, 600));

                $postData['dob_img'] = $dob_base64ResizeImage;
            }

            if($is_govt_job) {
                $postData['designation'] = $request->has('designation') ? $request->get('designation') : '';
                $postData['serviceGrade'] = $request->has('serviceGrade') ? $request->get('serviceGrade') : '';
            }

            if($request->get('marital_status') === 'Married') {
                $postData['spouse_name'] = $request->has('spouse_name') ? $request->get('spouse_name') : '';
            }
            if($identity !== 'PASSPORT') {
                $postData['father_name_bn'] = $request->input('father_name_bn');
                $postData['mother_name_bn'] = $request->input('mother_name_bn');
                $postData['name_bn'] = $request->input('name_bn');
            } else {
                $postData['father_name_en'] = $request->input('father_name_en');
                $postData['mother_name_en'] = $request->input('mother_name_en');
            }

            if($request->get('resident') !== 'Bangladeshi') {
                $postData['nationality2'] =$request->has('nationality2') ? $request->get('nationality2') : '';
            }

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/update-pre-reg-pilgrim";

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode($postData), $headers,true);

            if($apiResponse['http_code'] !== 200) {
                $decodeResponse = json_decode($apiResponse['data'], true);
                return response()->json(['responseCode' => -1, 'msg' => $decodeResponse['msg']]);
            }
            $apiResponseDataArr = json_decode($apiResponse['data']);
            $decodeResponse = $apiResponseDataArr->data;

            return response()->json(['responseCode' => 1, 'status'=> true ,'data' => $decodeResponse]);
        } catch(\Exception $e) {
            return response()->json(['responseCode' => -1, 'msg' => $e->getMessage()]);
        }


    }
    public function getPilgrimData(Request $request) {
        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }

        try {
            $pilgrimId = $request->has('pilgrimId') ? $request->input('pilgrimId') : '';
            if(!$pilgrimId) {
                return response()->json(['responseCode' => -1, 'msg' => 'PilgrimId is required']);
            }
            $postData = [
                'pilgrim_id' => $pilgrimId,
                'prp_user_id' => Encryption::encodeId(Auth::user()->prp_user_id),
            ];

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-pilgrim-info";

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode($postData), $headers,true);
            if($apiResponse['http_code'] !== 200) {
                $decodeResponse = json_decode($apiResponse['data'], true);
                return response()->json(['responseCode' => -1, 'msg' => $decodeResponse['msg']]);
            }

            $decodeResponseArr = json_decode($apiResponse['data'], true);
            $data = $decodeResponseArr['data'];
            $data['responseCode'] = 1;
            $data['user_code'] = Auth::user()->prp_user_id;
            return response()->json($data);
        } catch(\Exception $e) {
            return response()->json(['responseCode' => -1, 'msg' => $e->getMessage()]);
        }
    }

    public function getEditPilgrimData(Request $request) {
        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }

        try {
            $pilgrimId = $request->has('pilgrimId') ? $request->input('pilgrimId') : '';
            if(!$pilgrimId) {
                return response()->json(['responseCode' => -1, 'msg' => 'PilgrimId is required']);
            }
            $postData = [
                'pilgrim_id' => $pilgrimId,
                'prp_user_id' => Encryption::encodeId(Auth::user()->prp_user_id),
            ];

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-edit-pilgrim-info";

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode($postData), $headers,true);
            if($apiResponse['http_code'] !== 200) {
                $decodeResponse = json_decode($apiResponse['data'], true);
                return response()->json(['responseCode' => -1, 'msg' => $decodeResponse['msg']]);
            }

            $decodeResponseArr = json_decode($apiResponse['data'], true);
            $data = $decodeResponseArr['data'];
            $data['responseCode'] = 1;
            $data['user_code'] = Auth::user()->prp_user_id;
            return response()->json($data);
        } catch(\Exception $e) {
            return response()->json(['responseCode' => -1, 'msg' => $e->getMessage()]);
        }
    }

    public function checkDuplicatePilgrim( Request $request) {
        try {
            $validator = [
                'identity' => 'required|string',
                'dob' => 'required',
            ];
            if($request->get('identity') === 'NID') {
                $validator['nid_number'] = 'required';
            }
            if($request->get('identity') === 'PASSPORT') {
                $validator['passport_no'] = 'required';
                $validator['passport_type'] = 'required';
            }
            if($request->get('identity') === 'DOB') {
                $validator['brn'] = 'required';
            }

            $this->validate($request, $validator);

            $postData = [
                "identity" => $request->get('identity'),
                "birth_date" => $request->get('dob'),
                "passport_type" => $request->has('passport_type') ? $request->get('passport_type') : '',
                "passport_number" => $request->has('passport_no') ? $request->get('passport_no') : '',
                "nid_number" => $request->has('nid_number') ? $request->get('nid_number') : '',
                "birth_id" => $request->has('brn') ? $request->get('brn') : '',
                "prp_user_id" => Encryption::encodeId(Auth::user()->prp_user_id),
                "is_govt" => "Government"
            ];
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/check-duplicate-pilgrim";

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode($postData), $headers,true);
            $apiResponseArr = json_decode($apiResponse['data']);
            if($apiResponseArr->status == 200){
                $data = ['responseCode' => 1,'status' => true,'message'=>$apiResponseArr->msg, 'data' => $apiResponseArr->data];
            }else{
                $data = ['responseCode' => 0,'status' => false,'message'=>$apiResponseArr->msg, 'data' => ''];
            }
            return response()->json($data);
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return response()->json(['responseCode' => 0, 'status'=> false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PV:005]']);
        }
    }

    public function checkDuplicatePilgrimEdit( Request $request) {
        try {
            $validator = [
                'identity' => 'required|string',
                'dob' => 'required',
                'id' => 'required',
            ];
            if($request->get('identity') === 'NID') {
                $validator['nid_number'] = 'required';
            }
            if($request->get('identity') === 'PASSPORT') {
                $validator['passport_no'] = 'required';
                $validator['passport_type'] = 'required';
            }
            if($request->get('identity') === 'DOB') {
                $validator['brn'] = 'required';
            }

            $this->validate($request, $validator);

            $postData = [
                "pilgrim_id" => $request->get('id'),
                "identity" => $request->get('identity'),
                "birth_date" => $request->get('dob'),
                "passport_type" => $request->has('passport_type') ? $request->get('passport_type') : '',
                "passport_number" => $request->has('passport_no') ? $request->get('passport_no') : '',
                "nid_number" => $request->has('nid_number') ? $request->get('nid_number') : '',
                "brn" => $request->has('brn') ? $request->get('brn') : '',
                "prp_user_id" => Encryption::encodeId(Auth::user()->prp_user_id),
                "is_govt" => "Government"
            ];
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/check-duplicate-pilgrim-edit";

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode($postData), $headers,true);
            $apiResponseArr = json_decode($apiResponse['data']);

            if($apiResponseArr->status == 200){
                $data = ['responseCode' => 1,'status' => true,'message'=>$apiResponseArr->msg, 'data' => $apiResponseArr->data];
            }else{
                $data = ['responseCode' => 0,'status' => false,'message'=>$apiResponseArr->msg, 'data' => ''];
            }
            return response()->json($data);
        } catch (\Exception $e) {
             // dd($e->getMessage(), $e->getLine());
            return response()->json(['responseCode' => 0, 'status'=> false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PV:006]']);
        }
    }
    public function getRegIndexData() {
        try {

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-reg-index-data";
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl, [], $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            $decodeResponse = $apiResponseDataArr->data;
            $ocupationArr = [];
            $is_govt = [];
            foreach ($decodeResponse->occupation as $item) {
                $ocupationArr[$item->id] = $item->name;
                if($item->is_govt == 1) {
                    $is_govt[$item->id] = $item->is_govt;
                }
            }
            $indexData = [
                'district' => $decodeResponse->district,
                'occupation' => $ocupationArr,
                'bank_list' => $decodeResponse->bankDistrictList->bank_list,
                'district_list' => $decodeResponse->bankDistrictList->district_list,
                'account_owner_type' => $decodeResponse->accountOwnerType,
                'is_govt' => $is_govt,
                'govtServiceGrade' => $decodeResponse->govtServiceGrade,
                'nationalitys' => $decodeResponse->nationalitys,
                'thana' => $decodeResponse->thana,
            ];
            return response()->json(['responseCode' => 1, 'status'=> true ,'data' => $indexData]);

        } catch (\Exception $e) {
            #dd($e->getMessage());
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [PC:007]');
            return response()->json(['responseCode' => 0, 'status'=> false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PC:007]']);
        }
    }
    public function getMaharamRelation() {
        try {
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-maharam-relation";
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl, [], $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            $data = $apiResponseDataArr->data;
            return response()->json(['responseCode' => 1, 'status'=> true ,'data' => $data]);

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [PC:007]');
            return response()->json(['responseCode' => 0, 'status'=> false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PC:007]']);
        }
    }
    public function searchMaharamByTrackingNo(Request $request) {
        try {
            $maharam_tracking_no = trim($request->get('maharam_tracking_no'));
            $self_tracking_no = trim($request->get('self_tracking_no'));
            $maharam_tracking_no_initial = substr($maharam_tracking_no, 0, 1);
            if ($maharam_tracking_no == 'undefined' || $maharam_tracking_no == null || $maharam_tracking_no == '') {
                return response()->json(['responseCode' => -1, 'status'=> false, 'message' => 'Please add maharam tracking number.']);
            }
            if ($self_tracking_no == 'undefined' || $self_tracking_no == null || $self_tracking_no == '') {
                return response()->json(['responseCode' => -1, 'status'=> false, 'message' => 'Please add self tracking number.']);
            }
            if ($maharam_tracking_no == $self_tracking_no) {
                return response()->json(['responseCode' => -1, 'status'=> false, 'message' => 'You can not be maharam of yourself.']);
            }
            if ($maharam_tracking_no_initial != 'M' && $maharam_tracking_no_initial != 'N') {
                return response()->json(['responseCode' => -1, 'status'=> false, 'message' => 'Please enter valid Tracking no.']);
            }
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/search-maharam-by-tracking-no";
            $postData = [
                'maharam_tracking_no' => $maharam_tracking_no,
                'self_tracking_no' => $self_tracking_no
            ];
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status == 200) {
                $data = $apiResponseDataArr->data;
                $responseCode = $data->responseCode;
                if ($responseCode == 0) {
                    return response()->json(['responseCode' => -1, 'status'=> false, 'message' => $data->data]);
                } elseif ($responseCode == 1) {
                    return response()->json(['responseCode' => 1, 'status'=> true ,'data' => $data->data]);
                } else {
                    return response()->json(['responseCode' => -1, 'status'=> false, 'message' => 'Something went wrong from API Server [PC:008]']);
                }
            } else {
                return response()->json(['responseCode' => -1, 'status'=> false, 'message' => $apiResponseDataArr->msg]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [PC:007]');
            return response()->json(['responseCode' => -1, 'status'=> false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PC:007]']);
        }
    }

}
