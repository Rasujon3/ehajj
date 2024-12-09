<?php

namespace App\Libraries;

use App\ActionInformation;
use App\Models\AuditLog;
use App\Modules\CompanyAssociation\Models\CompanyAccessPrivileges;
use App\Modules\CompanyAssociation\Models\InactiveCompanyUser;
use App\Modules\Files\Controllers\FilesController;
use App\Modules\IndustryNew\Controllers\IndustryNewController;
use App\Modules\Settings\Models\RegulatoryAgency;
use App\Modules\VisaAssistance\Models\SponsorsDirectors;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class MinioToken
{
    /*************************************
     * Starting OSS Common functions
     *************************************/

    /**
     * @param Carbon|string $updated_at
     * @param string $updated_by
     * @return string
     * @internal param $Users->id /string $updated_by
     */

    public static function getMinioTokenData(){
        $tokenUrl =  env('MINIO_TOKEN_BASE_URL')."/realms/minio/protocol/openid-connect/token";
        $credential = [
            'client_id' => env('MINIO_CLIENT_ID'),
            'client_secret' => env('MINIO_CLIENT_SECRET'),
            'grant_type' => env('MINIO_GRANT_TYPE')
        ];
        return self::getMinioApiToken($tokenUrl, $credential);
    }

    public static function getMinioApiToken($tokenUrl,$tokenData)
    {
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );
        $response = self::curlPostRequest($tokenUrl,$tokenData,$headers);
        $decodedResponseData = json_decode($response['data']);

        return property_exists($decodedResponseData, 'access_token') ? $decodedResponseData->access_token : '';
    }
    public static function getMinioSecretData(){
        $tokenUrl = env('MINIO_SECRET_URL');
        $minioToken = self::getMinioTokenData();
        $credential = [
            'WebIdentityToken' => $minioToken,
            'Version' => env('MINIO_SECRET_VERSION'),
            'Action' => env('MINIO_SECRET_ACTION')
        ];
        return self::getMinioApiSecret($tokenUrl, $credential);
    }

    public static function getMinioApiSecret($tokenUrl,$tokenData)
    {
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );
        $response = self::curlPostRequest($tokenUrl,$tokenData,$headers);
        $xml = simplexml_load_string($response['data']);
        if ($xml && isset($xml->AssumeRoleWithWebIdentityResult->Credentials->AccessKeyId)) {
            $accessKeyId = (string) $xml->AssumeRoleWithWebIdentityResult->Credentials->AccessKeyId;
            $secretAccessKey = (string) $xml->AssumeRoleWithWebIdentityResult->Credentials->SecretAccessKey;
            $sessionToken = (string) $xml->AssumeRoleWithWebIdentityResult->Credentials->SessionToken;
            $expiration = (string) $xml->AssumeRoleWithWebIdentityResult->Credentials->Expiration;
        }
        $decodedResponseData = [
            "accessKeyId" => $accessKeyId,
            "secretAccessKey" => $secretAccessKey,
            "sessionToken" => $sessionToken,
            "expiration" => $expiration,
            "token_expire_time_str" => Carbon::parse($expiration)->format("Y-m-d H:i:s")
        ];

        $jsonResponse = json_encode($decodedResponseData);
        Session::put('minioTempCredential',serialize($jsonResponse));
    }

    public static function decodedMinioSessionData()
    {
        $minioSession = Session::get('minioTempCredential');
        $unserializeMinioSession = unserialize($minioSession);
        return json_decode($unserializeMinioSession);
    }
    public static function isTokenExpired()
    {
        $decodedMinioSession = self::decodedMinioSessionData();
        $currentDateTime = Carbon::now();
        $tokenExpireTime = Carbon::parse($decodedMinioSession->token_expire_time_str);
        return $tokenExpireTime->gt($currentDateTime);
    }

    public static function curlPostRequest($url,$postdata,$headers,$jsonData=false)
    {
        if(!$jsonData){
            $postdata = http_build_query($postdata);
        }
        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            $result = curl_exec($ch);

            if (!curl_errno($ch)) {
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            } else {
                $http_code = 0;
            }
            curl_close($ch);
            return ['http_code' => intval($http_code), 'data' => $result];

        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }
}
