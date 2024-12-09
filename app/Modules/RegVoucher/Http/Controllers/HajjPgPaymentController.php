<?php

namespace App\Modules\Voucher\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HajjPgPaymentController extends Controller
{
    public function hajjPgReturnURL(Request $request) {
        $token = CommonFunction::getTokenData();
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $base_url = env('API_URL');
        $apiUrl = "$base_url/api/hajj-pg-payment-confirm";
        $postData = $request->all();
        $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
        $apiResponseDataArr = json_decode($apiResponse['data']);
        $exception_id = 1;
        if ($apiResponseDataArr->status != 200){
            $exception_id = -1;
        }
        $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
        if (empty($data->transaction_id)){
            $exception_id = -1;
        }
        return redirect('/hajjpg-payment-confirmation/'.Encryption::encode($data->tracking_no).'/'.Encryption::encode($exception_id).'/'.$data->group_payment_id.'/'.Encryption::encode($data->source));
    }

    public function hajjPgPaymentConfirmation($tracking_no, $exception_id, $group_payment_id, $source) {
        $data['tracking_no'] = Encryption::decode($tracking_no);
        $data['exception_id'] = Encryption::decode($exception_id);
        $data['group_payment_id'] =$group_payment_id;
        $data['source'] = Encryption::decode($source);
        return view('Voucher::paymentConfirmation', $data);
    }
}
