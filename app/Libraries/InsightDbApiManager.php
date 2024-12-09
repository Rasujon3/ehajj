<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Log;

class InsightDbApiManager
{
    private static function getToken()
    {
        try {
            $access_data = [
                "client_id" => env('INSIGHTDB_API_CLIENT_ID'),
                "client_secret" => env('INSIGHTDB_API_CLIENT_SECRET'),
                "grant_type" => 'client_credentials'
            ];
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_POST, 1);
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($access_data));
            curl_setopt($curl_handle, CURLOPT_URL, env('INSIGHTDB_API_TOKEN_URL'));
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $result = curl_exec($curl_handle);
            if (curl_errno($curl_handle)) {
                $data = ['responseCode' => 0, 'msg' => curl_error($curl_handle), 'data' => ''];
                curl_close($curl_handle);
                return json_encode($data);
            }
            curl_close($curl_handle);

            if (!$result || !property_exists(json_decode($result), 'access_token')) {
                $data = ['responseCode' => 0, 'msg' => 'API connection failed!', 'data' => ''];
                return json_encode($data);
            }

            $decoded_json = json_decode($result, true);
            return $decoded_json['access_token'];
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }

    public static function getAllNews($apiUrl)
    {
        $token = self::getToken();

        $ch = curl_init();
        $headers = ['Authorization: Bearer ' . $token];

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        if (curl_error($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }
        curl_close($ch);

        if ($response) {
            return json_decode($response, true);
        }
        return false;
    }

}
