<?php

namespace App\Modules\RegVoucher\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RegVoucherController extends Controller
{
    public function index(){
        if(Auth::user()->user_type === '18x415' && !in_array(Auth::user()->working_user_type, ['Pilgrim', 'Guide'])) {
            Session::flash('error', 'আপনার কোন টাইপের ইউজার তা সিলেক্ট করুন। আপনি যদি হজযাত্রী হন তাহলে  Working user type থেকে Pilgrim সিলেক্ট করুন। আর যদি আপনি হজ গাইড হন তাহলে Guide সিলেক্ট করুন।');
            return redirect('/users/profileinfo');
        }
        $accessMode = ACL::getAccsessRight('registration');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        return view('RegVoucher::index');
    }
    public function storeVoucher(Request $request){
        $accessMode = ACL::getAccsessRight('registration');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        $this->validate($request, [
            // 'voucher_name'   => ['required'],
            'total_pilgrim'  => ['required'],
            'address'        => ['required'],
            'deposit_date'   => ['required'],
            'depositor_name' => ['required'],
            'hajj_pcz_id'    => ['required'],
            'mobile_number'  => ['required','regex:/^(?:\+8801|01)[3-9]\d{8}$/']
        ]);
        if ($request->isMethod("POST")){
            try {
                $request_by = Auth::user()->prp_user_id;
                $base_url = env('API_URL');
                $token = CommonFunction::getTokenData();
                if (!$token) {
                    $msg = 'Sorry Invalid token';
                    return response()->json(['responseCode' => -1, 'msg' => $msg]);
                }
                $apiUrl = "$base_url/api/create-registration-voucher";
                $data = [
                    // 'voucher_name'        => $request->voucher_name,
                    'deposit_date'        => $request->deposit_date,
                    'hajj_pcz_id'         => $request->hajj_pcz_id,
                    'depositor_name'      => $request->depositor_name,
                    'depositor_address'   => $request->address,
                    'depositor_mobile_no' => $request->mobile_number,
                    'total_pilgrim'       => $request->total_pilgrim,
                    'ref_voucher_no'      => null,
                    'request_by'          => $request_by,
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

        $accessMode = ACL::getAccsessRight('registration');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        try {
            $limit = $request->get('limit');
            $page = $request->get('page');
            $request_by = Auth::user()->prp_user_id;
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/reg-voucher-list";
            $data = [
                'request_by'=>$request_by,
                'limit' => $limit,
                'page' => $page
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            $data = !empty($apiResponseDataArr->data->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $data]);
        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getVoucherDetail($id){
        try {
            $accessMode = ACL::getAccsessRight('registration');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $reg_voucher_id = $id;
            $request_by = Auth::user()->prp_user_id;
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/view-registration-voucher";
            $data = [
                'reg_voucher_id' => $reg_voucher_id,
                'request_by'=>$request_by
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
    public function getPackage(Request $request){
        try {
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            $request_by = Auth::user()->prp_user_id;
            $is_short_package = $request->has('is_short_package') ? $request->get('is_short_package') : '0';
            $package_category_val = $request->has('category') ? $request->get('category') : '';
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/voucher-active-session";
            $data = [
                'request_by'=>$request_by,
                'is_short_package' => $is_short_package,
                'package_category_val' => $package_category_val
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
            $hajjPackageList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $hajjPackageList]);
        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [GP-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getPilgrim(Request $request){
        try {

            $accessMode = ACL::getAccsessRight('registration');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $request_by = Auth::user()->prp_user_id;
            $limit = $request->has('limits') ? $request->get('limits') : 10;
            $page = $request->has('page') ? $request->get('page') : 1;
            $search = (!empty($request->get('search')) && $request->get('search') != null) ? $request->get('search') : '';


            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/expected-reg-pilgrim-list";
            $data = [
                'request_by'=>$request_by,
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
            $apiResponseDataArr = json_decode($apiResponse['data']);
            return response()->json(['responseCode' => 1, 'data' => $apiResponseDataArr]);
        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function addDeleteAction(Request $request){
        try {
            $accessMode = ACL::getAccsessRight('registration');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $request_by = Auth::user()->prp_user_id;

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/registration-voucher-pilgrim-process";
            $data = [
                'request_by'    =>$request_by,
                'request_type'  =>$request->request_type,
                'pilgrim_id'    =>$request->pilgrim_id,
                'pilgrims_id'   =>$request->pilgrims_id,
                'reg_voucher_id'=>$request->reg_voucher_id,
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
            $accessMode = ACL::getAccsessRight('registration');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $request_by = Auth::user()->prp_user_id;

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/reg-voucher-lock-unlock";
            $data = [
                'request_by'=>$request_by,
                'reg_voucher_id'=>$request->reg_voucher_id,
                'request_type'=>$request->type,
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
            $accessMode = ACL::getAccsessRight('registration');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $request_by = Auth::user()->prp_user_id;
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/reg-remove-all-pilgrim";
            $data = [
                'reg_voucher_id'=>$request->reg_voucher_id,
                'request_by'=>$request->request_by,
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
            $accessMode = ACL::getAccsessRight('registration');
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
                $msg = 'Payment Gateway URL Found not found!';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }

        } catch (\Exception $e) {
            $msg = 'Something went wrong !!! [OCR-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getVoucherEditInfo($id){
        try {
            $accessMode = ACL::getAccsessRight('registration');
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
    public function updateVoucher(Request $request){
        $accessMode = ACL::getAccsessRight('registration');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        $this->validate($request, [
            // 'voucher_name'   => ['required'],
            'total_pilgrim'  => ['required'],
            'address'        => ['required'],
            'deposit_date'   => ['required'],
            'depositor_name' => ['required'],
            'hajj_pcz_id'    => ['required'],
            'mobile_number'  => ['required','regex:/^(?:\+8801|01)[3-9]\d{8}$/']
        ]);

        if ($request->isMethod("POST")) {
            try {
                $request_by = Auth::user()->prp_user_id;
                $base_url = env('API_URL');
                $token = CommonFunction::getTokenData();
                if (!$token) {
                    $msg = 'Sorry Invalid token';
                    return response()->json(['responseCode' => -1, 'msg' => $msg]);
                }
                $apiUrl = "$base_url/api/reg-voucher-edit";
                $data = [
                    // 'voucher_name'        => $request->voucher_name,
                    'deposit_date'        => $request->deposit_date,
                    'hajj_pcz_id'         => $request->hajj_pcz_id,
                    'depositor_name'      => $request->depositor_name,
                    'depositor_address'   => $request->address,
                    'depositor_mobile_no' => $request->mobile_number,
                    'total_pilgrim'       => $request->total_pilgrim,
                    'ref_voucher_no'      => null,
                    'request_by'          => $request_by,
                    'reg_voucher_id'      => $request->reg_voucher_id,
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
            $accessMode = ACL::getAccsessRight('registration');
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
            $apiUrl = "$base_url/api/reg-voucher-pdf-generator";
            $data = [
                'reg_voucher_id'=>$request->reg_voucher_id,
                'is_first_call'=>$request->is_first_call,
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
    public function addPilgrimBySerialNo(Request $request){
        try {
            $accessMode = ACL::getAccsessRight('registration');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/check-pilgrim-info-by-tracking-no";
            $data = [
                'tracking_no'=> $request->tracking_no,
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
}
