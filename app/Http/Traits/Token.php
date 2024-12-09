<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;

trait Token
{
    public static function getToken()
    {
        $base_url = env('API_URL');
        $api_url_for_token = "$base_url/api/getToken";
        $username          = env('CLIENT_USER_NAME');
        $password          = env('CLIENT_PASSWORD');
        $clientid          = env('CLIENT_ID');

        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
                'clientid'     => $clientid,
                'username'      => $username,
                'password'      => $password
            )));
            curl_setopt($curl, CURLOPT_URL, $api_url_for_token);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($curl);

            if (!$result || !property_exists(json_decode($result), 'token')) {
                $data = ['responseCode' => 0, 'msg' => 'API connection failed!', 'data' => ''];

                return json_encode($data);
            }

            curl_close($curl);
            $decoded_json = json_decode($result, true);

            $data         = [
                'responseCode' => 1,
                'msg'          => 'Success',
                'token'         => $decoded_json['token'],
                'expires_in'   => $decoded_json['expire_on']
            ];

            return json_encode($data);
        } catch (Exception $e) {
            Log::error($e);
            return false;
        }
    }
}
