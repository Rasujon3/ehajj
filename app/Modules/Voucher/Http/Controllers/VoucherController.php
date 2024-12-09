<?php

namespace App\Modules\Voucher\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class VoucherController extends Controller
{


    public function index()
    {
        if(Auth::user()->user_type === '18x415' && !in_array(Auth::user()->working_user_type, ['Pilgrim', 'Guide'])) {
            Session::flash('error', 'আপনার কোন টাইপের ইউজার তা সিলেক্ট করুন। আপনি যদি হজযাত্রী হন তাহলে  Working user type থেকে Pilgrim সিলেক্ট করুন। আর যদি আপনি হজ গাইড হন তাহলে Guide সিলেক্ট করুন।');
            return redirect(url('users/profileinfo'));
        }
        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        return view('Voucher::index');
    }

    public function storeVoucher(Request $request){
        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        $this->validate($request, [
            'payment_type' => 'required|in:online,offline',
            //'voucher_name' => ['required'],
            'address' => ['required'],
            'depositor_name' => ['required'],
            'deposite_date' => ['required'],
            'mobile_number' => ['required','regex:/^(?:\+8801|01)[3-9]\d{8}$/']
        ]);
        if($request->payment_type != 'online'){
            $this->validate($request, [
                'district_id' => ['required'],
                'thana_id' => ['required'],
                'bank_branch_id' => ['required'],
            ]);
        }
        if ($request->isMethod("POST")){
            try {
                $prpUserID = Auth::user()->prp_user_id;
                $base_url = env('API_URL');
                $token = CommonFunction::getTokenData();
                if (!$token) {
                    $msg = 'Sorry Invalid token';
                    return response()->json(['responseCode' => -1, 'msg' => $msg]);
                }
                $apiUrl = "$base_url/api/store-voucher";
                $data = [
                    'prp_user_id' => $prpUserID,
                    'payment_type' => $request->payment_type,
                    'voucher_name' => $request->depositor_name,
                    'depositor_name' => $request->depositor_name,
                    'deposit_date' => date('Y-m-d', strtotime($request->deposite_date)),
                    'bank_branch_id' => $request->bank_branch_id,
                    'depositor_address' => $request->address,
                    'depositor_mobile_no' => $request->mobile_number,
                    'is_govt' =>'Government',
                ];
                $postData = json_encode($data);
                $headers = [
                    'APIAuthorization:' . $token,
                    'Content-Type: application/json',
                ];
                $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
                $apiResponseDataArr = json_decode($apiResponse['data']);
                return response()->json($apiResponseDataArr);
            } catch (\Exception $e) {
                $msg = 'Something went wrong !!! [OCR-001]';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
        }
    }

    public function getVoucher(Request $request) {

        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        try {
            $limit = $request->get('limit');
            $page=$request->get('page');
            $prpUserID = Auth::user()->prp_user_id;
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-voucher-details-data";
            $data = [
                'user_id' => Encryption::encodeId($prpUserID),
                'limit' =>$limit,
                'page'=>$page
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json($apiResponseDataArr);
        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getVoucherDetail($id){
        try {
            $accessMode = ACL::getAccsessRight('pilgrim');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $prpUserID = Auth::user()->prp_user_id;
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-pre-reg-voucher-data";
            $data = [
                'prp_user_id' => Encryption::encodeId($prpUserID),
                'group_payment_id' =>$id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json($apiResponseDataArr);

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }

    public function getDistrict(){

        try {
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-district";
            $data = [];
            $postData = json_encode($data);

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json($apiResponseDataArr);

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }

    public function getPoliceStation($id){
        try {
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-police-stations";
            $data = [
                'districtId'=>$id
            ];
            $postData = json_encode($data);

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json($apiResponseDataArr);

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }

    public function getBankBranch($id){
        try {
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-bank-branch";
            $data = [
                'thanaId'=>$id
            ];
            $postData = json_encode($data);

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json($apiResponseDataArr);

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }

    public function getPilgrim(Request $request){
        try {

            $accessMode = ACL::getAccsessRight('pilgrim');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $prpUserID = Auth::user()->prp_user_id;
            $group_payment_id = $request->get('id');
            $limit = $request->has('limits') ? $request->get('limits') : 10;
            $page = $request->has('page') ? $request->get('page') : 1;
            $search = (!empty($request->get('search')) && $request->get('search') != null) ? $request->get('search') : '';

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-pre-reg-voucher-pilgrim";
            $data = [
                'user_id'=>Encryption::encodeId($prpUserID),
                'group_payment_id'=>$group_payment_id,
                'limit' => $limit,
                'page' => $page,
                'search' => $search,
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
            $pilgrimList = !empty($apiResponseDataArr->data->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $pilgrimList]);

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getPaymentInfo(Request $request) {
        try {
            $prpUserID = Auth::user()->prp_user_id;
            $tracking_no = $request->get('tracking_no');
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-group-payment-info";
            $data = [
                'user_id'=>Encryption::encodeId($prpUserID),
                'tracking_no'=>$tracking_no,
                'source'=> 1,
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);

            if($apiResponse['http_code'] != 200) {
                $decodeResponse = json_decode($apiResponse['data'], true);
                return response()->json(['responseCode' => -1, 'msg' => $decodeResponse['msg']]);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json(['responseCode' => 1, 'data' => $apiResponseDataArr]);

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }

    public function addDeleteAction(Request $request){

        try {
            $accessMode = ACL::getAccsessRight('pilgrim');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $prpUserID = Auth::user()->prp_user_id;

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/pre-reg-voucher-pilgrim-action";
            $data = [
                'user_id'=>Encryption::encodeId($prpUserID),
                'pilgrim_id'=>$request->pilgrimId,
                'group_payment_id'=>$request->groupPaymentId,
                'operationType'=>$request->operationType,
            ];

            $postData = json_encode($data);

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);

            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json($apiResponseDataArr);

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }

    public function lockAndloack(Request $request){
        try {
            $accessMode = ACL::getAccsessRight('pilgrim');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $prpUserID = Auth::user()->prp_user_id;

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/pre-reg-voucher-lock-unlock";
            $data = [
                'user_id'=>Encryption::encodeId($prpUserID),
                'id'=>$request->groupPaymentId,
                'type'=>$request->type,
            ];

            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json($apiResponseDataArr);

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }

    public function deleteAllPilgrim(Request $request){
        try {
            $accessMode = ACL::getAccsessRight('pilgrim');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $prpUserID = Auth::user()->prp_user_id;
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/pre-reg-voucher-remove-all-pilgrim";
            $data = [
                'group_payment_id'=>$request->group_payment_id,
            ];

            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json($apiResponseDataArr);

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function submitPayment(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('pilgrim');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            if(empty($request->get('reg_key')) || empty($request->get('tracking_no')) || empty($request->get('payType'))){
                $msg = 'Invalid Information! [CP-001]';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/online-payment-gateway-request";
            $data = [
                'trackingNo'=>$request->get('tracking_no'),
                'reg_key'=>$request->get('reg_key'),
                'pay_type'=>1,
                'source'=>1,
                'request_by'=> Auth::user()->prp_user_id,
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if(!empty($apiResponseDataArr->data->portal_url)){
                return response()->json(['responseCode' => 1, 'data' => $apiResponseDataArr->data->portal_url]);
            } else {
                //$msg = 'Payment Gateway URL Found not found!';
                $msg = $apiResponseDataArr->msg;
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }

        } catch (\Exception $e) {
            //dd($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [EHP043]');
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [EHP043]');
            $msg = 'Something went wrong !!! [EHP043]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getVoucherEditInfo($id){
        try {
            $accessMode = ACL::getAccsessRight('pilgrim');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $prpUserID = Auth::user()->prp_user_id;
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/edit-voucher";
            $data = [
                'group_payment_id' =>$id,
                'request_by' => $prpUserID,
            ];
            $postData = json_encode($data);

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);

            return response()->json($apiResponseDataArr);

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function updateVoucher(Request $request)
    {
        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        $this->validate($request, [
            'payment_type' => 'required|in:online,offline',
            //'voucher_name' => ['required'],
            'address' => ['required'],
            'depositor_name' => ['required'],
            'deposite_date' => ['required'],
            'mobile_number' => ['required','regex:/^(?:\+8801|01)[3-9]\d{8}$/']
        ]);
        if($request->payment_type != 'online'){
            $this->validate($request, [
                'district_id' => ['required'],
                'thana_id' => ['required'],
                'bank_branch_id' => ['required'],
            ]);
        }

        if ($request->isMethod("POST")) {
            try {
                $prpUserID = Auth::user()->prp_user_id;
                $base_url = env('API_URL');
                $token = CommonFunction::getTokenData();
                if (!$token) {
                    $msg = 'Sorry Invalid token';
                    return response()->json(['responseCode' => -1, 'msg' => $msg]);
                }
                $apiUrl = "$base_url/api/update-voucher";
                $data = [
                    'user_id'=>Encryption::encodeId($prpUserID),
                    'payment_type' => $request->payment_type,
                    'group_payment_id'=>$request->group_payment_id,
                    'voucher_name' => $request->depositor_name,
                    'depositor_name' => $request->depositor_name,
                    'deposit_date' => date('Y-m-d',strtotime($request->deposite_date)),
                    'bank_branch_id' => $request->bank_branch_id,
                    'depositor_address' => $request->address,
                    'depositor_mobile_no' => $request->mobile_number,
                    'is_govt' => 'Government',
                ];
                $postData = json_encode($data);
                $headers = [
                    'APIAuthorization:' . $token,
                    'Content-Type: application/json',
                ];
                $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
                $apiResponseDataArr = json_decode($apiResponse['data']);
                return response()->json($apiResponseDataArr);

            } catch (\Exception $e) {
                $msg = 'Something went wrong !!! [OCR-001]';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
        }
    }
    public function pdfGenerator(Request $request){
        try {
            $accessMode = ACL::getAccsessRight('pilgrim');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $prpUserID = Auth::user()->prp_user_id;
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/generate-pdf-voucher";
            $data = [
                'user_id' => Encryption::encodeId($prpUserID),
                'payment_id'=>$request->group_payment_id,
                'pdf_type'=>$request->pdf_type,
                'pdf_flag'=>$request->pdf_flag,
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json($apiResponseDataArr);

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! PV-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function paymentVerify(Request $request){
        try {
            $accessMode = ACL::getAccsessRight('pilgrim');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/hajj-pg-payment-verify";
            $data = [
                'trackingNo'=>$request->tracking_no,
                'reg_key'=>$request->wallet_reg_key,
                'pay_type'=> 1,
                'source'=>1,
                'pay_unique_id'=>$request->pay_unique_id,
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json($apiResponseDataArr);
        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [PV-002]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function paymentCancel(Request $request){
        try {
            $accessMode = ACL::getAccsessRight('pilgrim');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/hajj-pg-payment-cancel";
            $data = [
                'trackingNo'=> $request->tracking_no,
                'reg_key'=> $request->wallet_reg_key,
                'pay_type'=> 1,
                'source'=>1,
                'pay_unique_id'=>$request->pay_unique_id,
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json($apiResponseDataArr);
        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [PV-003]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function counterPaymentVoucher($tracking_no,$pay_trn_id){
        try {
            if (!ACL::isAllowed(ACL::getAccsessRight('pilgrim'), 'V')) {
                abort(403, 'You have no access right! Please contact the system administrator.');
            }

            $apiResponseDataArr = $this->getOnlinePayDetails($tracking_no, $pay_trn_id);
            if (!$apiResponseDataArr) {
                return redirect()->back()->with('error', 'Data not found [CP-002]');
            }

            $current_time = date('d M, Y');
            $html = view("Voucher::counterPaymentVoucher", compact('current_time', 'apiResponseDataArr'))->render();

            CommonFunction::pdfGeneration(
                'Counter Payment Voucher',
                '',
                '',
                $html,
                "{$tracking_no}_Profile_Info_PDF.pdf"
            );

        } catch (\Exception $e) {
            #dd($e->getMessage());
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [CPS-003]');
            return redirect()->back()->with('error', 'Something went wrong !!! [CP-003]');
        }
    }
    public function counterPaySlip($tracking_no,$pay_trn_id){
        try {
            if (!ACL::isAllowed(ACL::getAccsessRight('pilgrim'), 'V')) {
                abort(403, 'You have no access right! Please contact the system administrator.');
            }

            $apiResponseDataArr = $this->getOnlinePayDetails($tracking_no, $pay_trn_id);
            if (!$apiResponseDataArr) {
                return redirect()->back()->with('error', 'Data not found [CP-004]');
            }

            $current_time = date('d M, Y');
            $html = view("Voucher::counterPaymentSlip", compact('current_time', 'apiResponseDataArr'))->render();

            CommonFunction::pdfGeneration(
                'Counter Payment Slip',
                'Counter Payment Slip PDF',
                '',
                $html,
                "{$tracking_no}_counter_pay_slip.pdf"
            );

        } catch (\Exception $e) {
            #dd($e->getMessage());
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [CPS-001]');
            return redirect()->back()->with('error', 'Something went wrong !!! [CP-005]');
        }
    }
    public function getOnlinePayDetails($tracking_no, $pay_trn_id) {
        try {
            $token = CommonFunction::getTokenData();
            if (!$token) {
                Session::flash('error', 'Invalid token');
                return false;
            }

            $apiUrl = env('API_URL') . "/api/get-counter-payment-info";
            $postData = json_encode([
                'user_id' => Encryption::encodeId(Auth::user()->prp_user_id),
                'tracking_no' => $tracking_no,
                'pay_trn_id' => $pay_trn_id,
                'source' => 1,
            ]);

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);

            if ($apiResponse['http_code'] !== 200) {
                Session::flash('error', 'Data not found');
                return false;
            }

            return json_decode($apiResponse['data'])->data ?? false;

        } catch (\Exception $e) {
            Log::error("Error fetching online payment details [CPS-002]: {$e->getMessage()} in {$e->getFile()} at line {$e->getLine()}");
            return false;
        }
    }
}
