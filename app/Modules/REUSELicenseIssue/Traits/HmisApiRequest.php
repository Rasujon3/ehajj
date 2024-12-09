<?php

namespace App\Modules\REUSELicenseIssue\Traits;

use App\Libraries\CommonFunction;
use Illuminate\Support\Facades\Session;
trait HmisApiRequest
{
    private function getTokenData(){
        $tokenUrl = "$this->api_url/api/getToken";
        $credential = [
            'clientid' => env('CLIENT_ID'),
            'username' => env('CLIENT_USER_NAME'),
            'password' => env('CLIENT_PASSWORD')
        ];

        return CommonFunction::getApiToken($tokenUrl, $credential);
    }
    private function getTeamData($isAjaxRequest = false)
    {
        $token = $this->getTokenData();
        if (!$token) {
            $msg = 'Failed to generate API Token!!!';
            Session::flash('error', $msg);
            return $isAjaxRequest ? response()->json(['responseCode' => -1, 'msg' => $msg]) : [];
        }
        $apiUrl = "$this->api_url/api/get-hmis-pilgrim-types";
        $headers = [
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        ];

        $apiResponse = CommonFunction::curlPostRequest($apiUrl, [], $headers, true);

        if ($apiResponse['http_code'] !== 200) {
            $msg = 'Something went wrong!!!';
            Session::flash('error', $msg);
            return $isAjaxRequest ? response()->json(['responseCode' => -1, 'msg' => $msg]) : [];
        }

        if ($isAjaxRequest){
            return $apiResponse['data'];
        }

        $apiResponseDataArr = json_decode($apiResponse['data']);
        if ($apiResponseDataArr->status !== 200) {
            $msg = 'Something went wrong from api server!!!';
            Session::flash('error', $msg);
            return $isAjaxRequest ? response()->json(['responseCode' => -1, 'msg' => $msg]) : [];
        }
        return collect($apiResponseDataArr->data)->pluck('type_name', 'pilgrim_type_id')->toArray();
    }

    private function getTeamSubTypeData($isAjaxRequest = false)
    {
        $token = $this->getTokenData();
        if (!$token) {
            $msg = 'Failed to generate API Token!!!';
            Session::flash('error', $msg);
            return $isAjaxRequest ? response()->json(['responseCode' => -1, 'msg' => $msg]) : [];
        }
        $apiUrl = "$this->api_url/api/get-hmis-pilgrims-sub-type";
        $headers = [
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        ];

        $apiResponse = CommonFunction::curlPostRequest($apiUrl, [], $headers, true);

        if ($apiResponse['http_code'] !== 200) {
            $msg = 'Something went wrong!!!';
            Session::flash('error', $msg);
            return $isAjaxRequest ? response()->json(['responseCode' => -1, 'msg' => $msg]) : [];
        }

        if ($isAjaxRequest){
            return $apiResponse['data'];
        }

        $apiResponseDataArr = json_decode($apiResponse['data']);

        if ($apiResponseDataArr->status !== 200) {
            $msg = 'Something went wrong from api server!!!';
            Session::flash('error', $msg);
            return $isAjaxRequest ? response()->json(['responseCode' => -1, 'msg' => $msg]) : [];
        }

        return collect($apiResponseDataArr->data)->pluck('sub_type_name', 'pilgrim_sub_type_id')->toArray();
    }
    private function getSubTeamDataByTeamId($teamId,$isAjaxRequest = false){
        $token = $this->getTokenData();
        if (!$token) {
            $msg = 'Failed to generate API Token!!!';
            Session::flash('error', $msg);
            return $isAjaxRequest ? response()->json(['responseCode' => -1, 'msg' => $msg]) : [];
        }
        $apiUrl = "$this->api_url/api/get-hmis-pilgrim-sub-types";
        $post_data['pilgrim_type_id'] = $teamId;
        $post_data = json_encode($post_data);

        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $apiResponse = CommonFunction::curlPostRequest($apiUrl, $post_data, $headers, true);

        if ($apiResponse['http_code'] != 200) {
            $msg = 'Something went wrong!!!';
            Session::flash('error', $msg);
            return $isAjaxRequest ? response()->json(['responseCode' => -1, 'msg' => $msg]) : [];
        }

        if ($isAjaxRequest){
            return $apiResponse['data'];
        }
        $apiResponseDataArr = json_decode($apiResponse['data']);
        if ($apiResponseDataArr->status != 200){
            $msg = 'Something went wrong from api server!!!';
            Session::flash('error', $msg);
            return $isAjaxRequest ? response()->json(['responseCode' => -1, 'msg' => $msg]) : [];
        }
        return collect($apiResponseDataArr->data)->pluck('name', 'id')->toArray();
    }
}
