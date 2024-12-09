<?php

namespace App\Modules\Registration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\ImageProcessing;
use App\Modules\Guides\Http\Controllers\GuidesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Modules\GoPassport\Http\Controllers\GoPassportController;

class RegistrationController extends Controller
{

    public function __construct(){ }

    public function index() {
        if(Auth::user()->user_type === '18x415' && !in_array(Auth::user()->working_user_type, ['Pilgrim', 'Guide'])) {
            Session::flash('error', 'আপনার কোন টাইপের ইউজার তা সিলেক্ট করুন। আপনি যদি হজযাত্রী হন তাহলে  Working user type থেকে Pilgrim সিলেক্ট করুন। আর যদি আপনি হজ গাইড হন তাহলে Guide সিলেক্ট করুন।');
            return redirect('/users/profileinfo');
        }
        $accessMode = ACL::getAccsessRight('registration');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        return view('Registration::index');
    }

    public function getList(Request $request) {
        $accessMode = ACL::getAccsessRight('registration');
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
            $apiUrl = "$base_url/api/get-eligible-govt-pilgrim-list";
            $data = [
                'request_by' => $prpUserID,
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
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [RC-001]');
            $msg = 'Something went wrong !!! [RC-001]';
            return response()->json(['responseCode' => -1, 'status'=> false , 'msg' => $msg]);
        }

    }

    public function getVenue( $districtId) {
        try {
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-venue-list";
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

    public function passportVerification( Request $request){
        try {
            $rules = [
                'dob' => 'required',
                'passport_no' => 'required',
                'passport_type' => 'required',
                'pilgrim_id' => 'required',
                'tracking_no' => 'required',
            ];
            $messages = [
                'dob.required' => 'অনুগ্রহ করে জন্মতারিখ প্রদান করুন। [PV:001]',
                'passport_no.required' => 'হজযাত্রীর পাসপোর্ট নম্বর পাওয়া যায়নি। [PV:002]',
                'passport_type.required' => 'হজযাত্রীর পাসপোর্টের ধরণ পাওয়া যায়নি। [PV:003]',
                'pilgrim_id.required' => 'হজযাত্রীর id  পাওয়া যায়নি। [PV:004]',
                'tracking_no.required' => 'হজযাত্রীর tracking number পাওয়া যায়নি। [PV:005]',
            ];
            $validationResponse = $this->validateRequest($request, $rules, $messages);
            if ($validationResponse) {
                return $validationResponse;
            }
            # Here the passport verify request data check api call process
            $checkedPassportReq = $this->checkPassportProcessData($request);
            if($checkedPassportReq['responseCode'] == 0){
                $data = ['responseCode' => -5,'status' => false,'message'=> $checkedPassportReq['message'], 'data' => ''];
                return response()->json($data);
            }
            $checkedPassportResponse = $checkedPassportReq['data'];
            if(!$checkedPassportResponse->isUniquePassport) {
                $data = [
                    'responseCode' => -5,
                    'status' => false,
                    'message'=> 'আপনার এই পাসপোর্ট নম্বরটি ইতিমধ্যে ব্যবহৃত হয়েছে, দয়াকরে সঠিক পাসপোর্ট নম্বর দিন। অথবা কল সেন্টার যোগাযোগ করুন। [EPV-011]',
                    'data' => ''
                ];
                return response()->json($data);
            }

            if($checkedPassportResponse->responseCode == 1 && $checkedPassportResponse->status == 1) {
                $data = [
                    'responseCode' => -5,
                    'status' => false,
                    'message'=> 'প্রাক-নিবন্ধিত হজযাত্রীর তথ্য মন্ত্রনালয়ে অপেক্ষমাণ রয়েছে [RPV:099]',
                    'data' => ''
                ];
                return response()->json($data);
            }

            # Here Verify Passport Information
            $goController = new GoPassportController();
            $response =$goController->passportVerifyRequest($request);
            $passportData = json_decode($response->content());
            if($passportData->responseCode == 1){
                $pData = $passportData->data->passportData;
                # Here Check data and previous passport verify request status
                #$nidMismatch = $request->nid_number != $pData->national_id;
                $processStatusInvalid = $checkedPassportResponse->status !== 2;
                # $passportDataMismatch = strtoupper($request->name_en) != strtoupper($pData->pass_name ) && date('Y-m-d', strtotime($request->dob)) != date('Y-m-d', strtotime($pData->pass_dob));

                if(!$processStatusInvalid) {
                    $data = [
                        'responseCode' => 1,
                        'status' => true,
                        'message'=>'Data Found ',
                        'data' => $passportData->data
                    ];
                    return response()->json($data);
                } else {
                    $requestData = new Request();
                    $requestData->replace([
                        'given_name' => $pData->given_name,
                        'surname' => $pData->surname,
                        'birth_date' => $pData->pass_dob,
                        'nid_number' => $pData->national_id,
                        'birth_certificate' => $pData->birth_certificate,
                        'pilgrim_id' => $request->pilgrim_id,
                        'tracking_no' => $request->tracking_no
                    ]);
                    # Here check passport mismatch data scoring
                    $checkedPassportScoring = $this->checkPassportScoring($requestData);
                    if($checkedPassportScoring['responseCode'] == 0){
                        $data = ['responseCode' => 0,'status' => false,'message'=> $checkedPassportScoring['message'], 'data' => ''];
                        return response()->json($data);
                    }
                    if($checkedPassportScoring['responseCode'] == 1) {
                        $data = [
                            'responseCode' => 1,
                            'status' => true,
                            'message'=> $checkedPassportScoring['message'],
                            'data' => $passportData->data
                        ];
                        return response()->json($data);
                    } else {
                        return response()->json([
                            'responseCode' => 5,
                            'status' => false,
                            'dob' => Encryption::encode($request->dob),
                            'type' => Encryption::encode($request->passport_type),
                            'passportNo' => Encryption::encode($request->passport_no),
                            'message' => $checkedPassportScoring['message'],
                            'data' => ''
                        ]);
                    }
                }
            } else {
                $data = ['responseCode' => 0,'status' => false,'message'=> $passportData->msg, 'data' => ''];
            }
            return response()->json($data);
        } catch (\Exception $e) {
            #dd($e->getMessage());
            Log::error('Error in Passport verify Request: ' . $e->getMessage());
            return response()->json(['responseCode' => 0, 'status'=> false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PV:002]']);
        }
    }

    public function updateRegPilgrim(Request $request) {
        $accessMode = ACL::getAccsessRight('registration');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }

        try {

            $validator = [
                'tracking_no' => 'required|string|max:255',
                'pilgrim_id' => 'required|string|max:255',
                'passport_no' => 'required|string|max:20',
                'passport_type' => 'required|string|max:20 | in:E-PASSPORT,MRP',
                'pass_issue_date' => 'required',
                'pass_exp_date' => 'required',
                'pass_type' => 'required',
                'pass_name' => 'required',

                'father_name_en' => 'required|string|max:255',
                'mother_name_en' => 'required|string|max:255',

                'gender' => 'required|string',
                'birth_date' => 'required',

                'occupation' => 'required|string|max:100',
                'mobile' => 'required|string|max:15',
                'marital_status' => 'required|string|max:20',

                'pass_per_post_code' => 'required|string|max:10',
                'pass_per_district' => 'required',
                'pass_per_thana' => 'required',
                'pass_per_village' => 'required|string|max:255',

                'pass_post_code' => 'required',
                'pass_district' => 'required',
                'pass_thana' => 'required',
                'pass_village' => 'required|string|max:255',

                'training_district' => 'required',
                'training_venue' => 'required',
                'vaccine_district' => 'required',

                'refund_account_type' => 'required',
                'refund_account_name' => 'required|string|max:255',
                'refund_account_number' => 'required|string|max:30',
                'refund_bank_id' => 'required|integer',
                'refund_branch_district' => 'required|integer',
                'refund_routing_no' => 'required|string|max:20',
            ];

            $is_govt_job = $request->has('is_govt_job') ? filter_var($request->get('is_govt_job'), FILTER_VALIDATE_BOOLEAN) : false;

            if($is_govt_job) {
                $validator['designation'] = 'required';
                $validator['serviceGrade'] = 'required';
            }

            if($request->get('marital_status') === 'Married') {
                $validator['spouse_name'] = 'required';
            }

            $this->validate($request, $validator);

            if(!empty($request->file('pilgrim_img'))) {
                $image = $request->file('pilgrim_img');
                // Get the image's contents
                $imageContents = file_get_contents($image->getRealPath());
                // Convert the image to base64
                $base64Image = base64_encode($imageContents);
                $base64ResizeImage = base64_encode(ImageProcessing::resizeBase64Image($base64Image, 200, 200));
            }else{
                $base64Image = $request->get('passport_img');
                if (strpos($base64Image, 'base64,') !== false) {
                    $base64ResizeImage = explode('base64,', $base64Image)[1]; // Split and take only the encoded part
                }else{
                    $base64ResizeImage = '';
                }
            }

            $request->merge([
                "father_name" => $request->father_name_en,
                "full_name_bangla" => $request->name_bn,
                "mother_name" => $request->mother_name_en,
                "govt_service_grade" => $request->serviceGrade,
                "account_number" => $request->refund_account_number,
                "account_holder_name" => $request->refund_account_name,
                "refund_branch_id" => $request->refund_routing_no,
                "payment_receive_type" => $request->refund_account_type,
                "prp_user_id" => Encryption::encodeId(Auth::user()->prp_user_id),
                "request_by" => Auth::user()->prp_user_id,
                'is_govt' => 'Government',
                'photo' => isset($base64ResizeImage) ? $base64ResizeImage : null,
            ]);

            $postData = $request->all();

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/store-registered-pilgrim";

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode($postData), $headers,true);

            if($apiResponse['http_code'] !== 200) {
                $decodeResponse = json_decode($apiResponse['data'], true);
                return response()->json(['responseCode' => -1, 'msg' => $decodeResponse['msg'], 'response' => $decodeResponse['response']]);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);
            $decodeResponse = $apiResponseDataArr->data;

            return response()->json(['responseCode' => 1, 'status'=> true , 'msg' => $apiResponseDataArr->msg, 'data' => $decodeResponse, 'response' => $apiResponseDataArr->response]);

        } catch(\Exception $e) {
            return response()->json(['responseCode' => -1, 'msg' => $e->getMessage()]);
        }

    }

    public function searchPreRegPilgrim(Request $request) {
        $accessMode = ACL::getAccsessRight('registration');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        try {
            $validator = [
                'tracking_no' => 'required|string|max:255',
            ];
            $this->validate($request, $validator);

            $postData = [
                'tracking_no' => $request->input('tracking_no')
            ];

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg, 'response' => 'error']);
            }
            $apiUrl = "$base_url/api/check-pilgrim-info-by-tracking-no";

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode($postData), $headers,true);
            if($apiResponse['http_code'] !== 200) {
                $decodeResponse = json_decode($apiResponse['data'], true);
                return response()->json(['responseCode' => -1, 'msg' => $decodeResponse['msg'], 'response' => $decodeResponse['response']]);
            }
            $apiResponseDataArr = json_decode($apiResponse['data']);
            $decodeResponse = $apiResponseDataArr->data;
            return response()->json(['responseCode' => 1, 'status'=> true , 'msg' => $apiResponseDataArr->msg, 'data' => $decodeResponse, 'response' => $apiResponseDataArr->response]);
        } catch(\Exception $e) {
            return response()->json(['responseCode' => -1, 'msg' => $e->getMessage(), 'response' => 'error']);
        }
    }

    public function getPilgrimData(Request $request) {
        $accessMode = ACL::getAccsessRight('registration');
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

            // pre reg pilgrim info for registration
            $apiUrl = "$base_url/api/get-reg-pilgrim-info";

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

    public function getEditRegPilgrimData(Request $request) {
        $accessMode = ACL::getAccsessRight('registration');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }

        try {
            $pilgrimId = $request->has('pilgrimId') ? $request->input('pilgrimId') : '';
            if(!$pilgrimId) {
                return response()->json(['responseCode' => -1, 'msg' => 'PilgrimId is required']);
            }
            $trackingNo =$request->has('tracking_no') ? $request->input('tracking_no') : '';
            if(!$trackingNo) {
                return response()->json(['responseCode' => -1, 'msg' => 'Pilgrim Tracking no is required']);
            }
            $postData = [
                'pilgrim_id' => $pilgrimId,
                'tracking_no' => $trackingNo,
                'prp_user_id' => Auth::user()->prp_user_id,
            ];

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }

            // pre reg pilgrim info for registration
            $apiUrl = "$base_url/api/get-edit-reg-pilgrim-info";

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

    public function imgStoreRequest(Request $request){
        # Check access right
        $this->checkAccessRights();
        try {
            $rules = [
                'image_file' => 'required|file|mimes:jpeg,png,jpg|max:2048',
                'pilgrim_id' => 'required',
            ];
            $messages = [
                'image_file.required' => 'পাসপোর্ট এর ছবি টি দিয়ে রিকুয়েস্ট করুন [PIMG:001]',
                'pilgrim_id.required' => 'Pilgrim Id not found. [PIMG:002]',
            ];

            $validationResponse = $this->validateRequest($request, $rules, $messages);
            if ($validationResponse) {
                return $validationResponse;
            }
            # Operation Start here
            $obj = new GuidesController();
            $image = $request->file('image_file');
            $base64Image = $obj->convertImageToBase64($image);
            $token = $this->getApiToken();
            if (!$token) {
                return $this->sendErrorResponse('ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [TN:003]');
            }
            # API Calling
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/passport-verify-files-store";
            $postData =[
                'ref_id' => $request->get('pilgrim_id'),
                'base64_img' => $base64Image,
                'type' => 'reg_passport_verify',
                'prp_user_id' => Auth::user()->prp_user_id,
            ];
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = $this->sendApiRequest($apiUrl,$postData,$headers);
            $decodeApiResponseData = json_decode($apiResponse['data']);
            if($apiResponse['http_code'] !== 200) {
                return $this->sendErrorResponse($decodeApiResponseData->msg);
            }
            return response()->json(['responseCode' => 1, 'message' => $decodeApiResponseData->msg, 'status' => true]);

        } catch (\Exception $e) {
            Log::error('Error in passport imgStoreRequest: ' . $e->getMessage());
            return $this->sendErrorResponse('ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PV:077]');
        }
    }
    public function submitPassportVerifyRequest(Request $request){
        # Check access rights
        $this->checkAccessRights();
        try {
            if (!$request->hasFile('image_file')) {
                return $this->sendErrorResponse('অনুগ্রহ করে পাসপোর্টের ছবি আপলোড করুন। [PIMG:001]');
            }
            $rules = [
                'pilgrimTrackingNo' => 'required',
                'passportNo' => 'required',
                'passportType' => 'required',
                'passportDob' => 'required',
            ];
            $messages = [
                'pilgrimTrackingNo.required' => 'হজযাত্রীর ট্র্যাকিং নম্বর পাওয়া যায়নি। [PIMG:003]',
                'passportNo.required' => 'পাসপোর্ট নম্বর পাওয়া যায়নি। [PIMG:004]',
                'passportType.required' => 'পাসপোর্টের ধরণ উল্লেখ করা হয়নি। [PIMG:005]',
                'passportDob.required' => 'হজযাত্রীর জন্মতারিখ পাওয়া যায়নি। [PIMG:006]',
            ];
            $validationResponse = $this->validateRequest($request, $rules, $messages);
            if ($validationResponse) {
                return $validationResponse;
            }
            # Handle E-Passport image upload and conversion
            $base64Image = $this->handleEPassportImage($request);
            $token = $this->getApiToken();
            if (!$token) {
                return $this->sendErrorResponse('ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [TN:004]');
            }
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/store-passport-verification-process";
            $postData =[
                "tracking_no"=> $request->get('pilgrimTrackingNo'),
                "passport_no"=> Encryption::decode($request->get('passportNo')),
                "passport_type"=> Encryption::decode($request->get('passportType')),
                "dob"=> Encryption::decode($request->get('passportDob')),
                "prp_user_id"=> Auth::user()->prp_user_id,
                "e_passport_scan_copy_base64"=> $base64Image
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
            return response()->json(['responseCode' => 1, 'message' => $decodeApiResponseData->msg, 'status' => true]);

        } catch (\Exception $e) {
            #dd($e->getMessage());
            Log::error('Error in save passport request: ' . $e->getMessage());
            return $this->sendErrorResponse('ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [PV:078]');
        }
    }

    private function checkPassportProcessData($request){
        $token = $this->getApiToken();
        if (!$token) {
            $data = ['responseCode' => 0, 'status' => false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [TN:001]', 'data' => ''];
            return $data;
        }
        $base_url = env('API_URL');
        $apiUrl = "$base_url/api/check-passport-verify-request";
        $postData =[
            'tracking_no' => $request->get('tracking_no'),
            'passport_no' => $request->get('passport_no')
        ];
        $headers = [
            'APIAuthorization:' . $token,
            'Content-Type: application/json',
        ];
        $apiResponse = $this->sendApiRequest($apiUrl,$postData,$headers);
        $decodeApiResponseData = json_decode($apiResponse['data']);
        if($apiResponse['http_code'] !== 200) {
            $data = ['responseCode' => 0, 'status' => false, 'message' => $decodeApiResponseData->msg, 'data' => ''];
        } else {
            $data = ['responseCode' => 1, 'status' => true, 'data' => $decodeApiResponseData->data, 'message' => ''];
        }
        return $data;

    }
    private function checkPassportScoring($request){
        try{
            $token = $this->getApiToken();
            if (!$token) {
                $data = [
                    'responseCode' => 0,
                    'status' => false,
                    'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [TN:004]'
                ];
                return $data;
            }
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/generate-passport-score";
            $postData =[
                'given_name' => $request->get('given_name'),
                'surname' => $request->get('surname'),
                'birth_date' => $request->get('birth_date'),
                'nid_number' => $request->get('nid_number'),
                'birth_certificate' => $request->get('birth_certificate'),
                'pilgrim_id' => $request->get('pilgrim_id'),
                'tracking_no' => $request->get('tracking_no')
            ];
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = $this->sendApiRequest($apiUrl,$postData,$headers);
            $decodeApiResponseData = json_decode($apiResponse['data']);
            if($apiResponse['http_code'] !== 200) {
                $data = ['responseCode' => 0, 'status' => false, 'message' => $decodeApiResponseData->msg, 'data' => ''];
            } else {
                if($decodeApiResponseData->data->responseCode == 1){
                    $data = ['responseCode' => 1, 'status' => true, 'data' => $decodeApiResponseData->data, 'message' => ''];
                } else {
                    $data = ['responseCode' => 2, 'status' => false, 'data' => '', 'message' => $decodeApiResponseData->msg];
                }
                //$data = ['responseCode' => 1, 'status' => true, 'data' => $decodeApiResponseData->data, 'message' => ''];
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Error ' . $e->getMessage());
            $data = ['responseCode' => 0, 'status' => false, 'message' => 'ডাটা লোড করতে ব্যর্থ হয়েছে। দয়া করে পুনরায় চেষ্টা করুন [CH:001]'];
            return $data;
        }
    }
    /* Handle E-Passport image method */
    private function handleEPassportImage($request){
        $base64Image = "";
        if (Encryption::decode($request->get('passportType')) === "E-PASSPORT") {
            if (!$request->hasFile('image_file')) {
                return $this->sendErrorResponse('পাসপোর্ট এর ছবি টি দিয়ে রিকুয়েস্ট করুন [PIMG:002]');
            }
            $obj = new GuidesController();
            $image = $request->file('image_file');
            $base64Image = $obj->convertImageToBase64($image);
        }
        return $base64Image;
    }
    private function sendApiRequest($apiUrl, array $postData, array $headers){
        return CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
    }
    private function getApiToken(){
        return CommonFunction::getTokenData();
    }
    private function checkAccessRights(){
        $accessMode = ACL::getAccsessRight('registration');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
    }
    private function validateRequest($request, array $rules, array $messages){
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 0,
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }
        return false;
    }
    private function sendErrorResponse($message){
        return response()->json([
            'responseCode' => 0,
            'status' => false,
            'message' => $message
        ]);
    }

}
