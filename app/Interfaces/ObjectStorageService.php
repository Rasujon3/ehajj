<?php

namespace App\Interfaces;

interface ObjectStorageService
{
    public function put($file, $fileName);

    public function get($fileName, $contentType = '');
}
