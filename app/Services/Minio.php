<?php

namespace App\Services;

use App\Libraries\MinioToken;
use Aws\S3\S3Client;
use App\Interfaces\ObjectStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;

class Minio implements ObjectStorageService
{
    private $storageInstance;
    private $bucket;

    /**
     * @throws \Exception
     */
    public function __construct($bucket = '')
    {
        $diskConfig = config('filesystems.disks');
        if ((Session::has('minioTempCredential') && !MinioToken::isTokenExpired()) || !Session::has('minioTempCredential')) {
            MinioToken::getMinioSecretData();
        }

        if (empty($diskConfig['minio']) || !count($diskConfig['minio'])) {
            throw new \Exception("Minio object storage is not properly configured");
        }

        $minioConfig = $diskConfig['minio'];
        $decodedMinioSession = MinioToken::decodedMinioSessionData();

        $this->storageInstance = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-1',
            'endpoint' => $minioConfig['endpoint'],
            'use_path_style_endpoint' => $minioConfig['use_path_style_endpoint'],
            'credentials' => [
                'key' => $decodedMinioSession->accessKeyId,
                'secret' => $decodedMinioSession->secretAccessKey,
                'token' => $decodedMinioSession->sessionToken,
            ]
        ]);

        $this->bucket = !empty($bucket) ? $bucket : $minioConfig['bucket'];
    }

    public function put($file, $fileName = '')
    {
        try {
            $response = $this->storageInstance->putObject([
                'Bucket' => $this->bucket,
                'Key' => $fileName,
                'Body' => $file
            ]);
            return $response['ObjectURL'];
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function get($fileName, $contentType = '')
    {
        try {
            $object = $this->storageInstance->getObject([
                'Bucket' => $this->bucket,
                'Key' => $fileName,
                'saveAs' => 'test'
            ]);
            $contentType = !empty($contentType) ? $contentType : "image/png";
            return "data:$contentType;base64," . base64_encode($object['Body']);

        } catch (\Exception $exception) {
            return false;
        }
    }
}
