<?php

namespace App\Modules\Bulletin\Services;


use App\Http\Controllers\CommonController;
use App\Libraries\CommonFunction;
use App\Modules\Bulletin\Exceptions\BulletinException;

class BulletinTemplateService
{
    protected $template;
    protected $emailTemplate;
    protected $bulletinData;

    public function __construct($template, $emailTemplate, $bulletinData)
    {
        $this->template = $template;
        $this->emailTemplate = $emailTemplate;
        $this->bulletinData = $bulletinData;
    }

    /**
     * @throws BulletinException
     */
    public function prepareContent(): array
    {
        if (!$deathSummaryData = $this->getDeathSummaryData()) {
            $this->template = str_replace('{$isShowDeathHistory}', 'display:none', $this->template);
            $this->emailTemplate = str_replace('{$isShowDeathHistory}', 'display:none', $this->emailTemplate);
        } else {
            $this->bulletinData[] = $deathSummaryData;
            $this->template = str_replace('{$isShowDeathHistory}', 'display: list-item; list-style: square;', $this->template);
            $this->emailTemplate = str_replace('{$isShowDeathHistory}', 'margin:0;display: list-item; list-style: square;', $this->emailTemplate);
        }

        if ($this->bulletinData['haj_type'] == "PRE_HAJ") {
            if (!$visaStatusData = $this->getVisaStatusData()) {
                $this->template = str_replace('{$isShowVisaStatus}', 'display:none', $this->template);
                $this->emailTemplate = str_replace('{$isShowVisaStatus}', 'display:none', $this->emailTemplate);
            } else {
                $this->bulletinData[] = $visaStatusData;
                $this->template = str_replace('{$isShowVisaStatus}', 'display: list-item; list-style: square;', $this->template);
                $this->emailTemplate = str_replace('{$isShowVisaStatus}', 'margin:0;display: list-item; list-style: square;', $this->emailTemplate);
            }
        }

        if (!$deathPilgrimData = $this->getLastDeathPilgrim()) {
            $this->template = str_replace('{$isShowLastDeathHistory}', 'display:none', $this->template);
            $this->emailTemplate = str_replace('{$isShowLastDeathHistory}', 'display:none', $this->emailTemplate);
        } else {
            $this->bulletinData[] = $deathPilgrimData;
            $this->template = str_replace('{$isShowLastDeathHistory}', 'display:block', $this->template);
            $this->emailTemplate = str_replace('{$isShowLastDeathHistory}', 'display:block', $this->emailTemplate);
        }


        foreach ($this->bulletinData as $index => $value) {
            $key = '{$' . $index . '}'; # Ex: templete key : {$BulletinNo}
            $this->template = str_replace($key,$value, $this->template);
            $this->emailTemplate = str_replace($key,$value, $this->emailTemplate);
        }

        return ['content' => $this->template, 'emailContent' => $this->emailTemplate];
    }

    /**
     * @throws BulletinException
     */
    private function getDeathSummaryData(): bool
    {
        $summaryDataArray = $this->fetchDataFromAPI('HMIS_DEATH_SUMMARY');
        if (empty($summaryDataArray)) {
            return false;
        }

        $summaryData = $summaryDataArray[0];
        if (!property_exists($summaryData, 'Total') || empty($summaryData->Total) || $summaryData->Total == '0') {
            return false;
        }

        $this->bulletinData['total'] = CommonFunction::convert2Bangla($summaryData->Total);
        $this->bulletinData['male'] = !empty($summaryData->male) && $summaryData->male != '0' ? CommonFunction::convert2Bangla($summaryData->male) : '০';
        $this->bulletinData['female'] = !empty($summaryData->Female) && $summaryData->Female != '0' ? CommonFunction::convert2Bangla($summaryData->Female) : '০';
        $this->bulletinData['mokka'] = !empty($summaryData->Makkah) && $summaryData->Makkah != '0' ? CommonFunction::convert2Bangla($summaryData->Makkah) : '০';
        $this->bulletinData['modina'] = !empty($summaryData->Madinah) && $summaryData->Madinah != '0' ? CommonFunction::convert2Bangla($summaryData->Madinah) : '০';
        $this->bulletinData['jedda'] = !empty($summaryData->Jaddah) && $summaryData->Jaddah != '0' ? CommonFunction::convert2Bangla($summaryData->Jaddah) : '০';
        $this->bulletinData['mina'] = !empty($summaryData->Mina) && $summaryData->Mina != '0' ? CommonFunction::convert2Bangla($summaryData->Mina) : '০';
        $this->bulletinData['arafa'] = !empty($summaryData->Arafah) && $summaryData->Arafah != '0' ? CommonFunction::convert2Bangla($summaryData->Arafah) : '০';
        $this->bulletinData['mujdalifa'] = !empty($summaryData->Muzdalifah) && $summaryData->Muzdalifah != '0' ? CommonFunction::convert2Bangla($summaryData->Muzdalifah) : '০';

        return true;
    }

    /**
     * @throws BulletinException
     */
    private function getVisaStatusData(): bool
    {
        $visaStatusArray = $this->fetchDataFromAPI('HMIS_VISA_STATUS');
        if (empty($visaStatusArray)) {
            return false;
        }

        $visaStatusData = $visaStatusArray[0];

        $totalVisa = '০';
        if (property_exists($visaStatusData,'PilgrimVisa') && !empty($visaStatusData->PilgrimVisa) && $visaStatusData->PilgrimVisa != '0'){
            $totalVisa = CommonFunction::convert2Bangla($visaStatusData->PilgrimVisa);
        }

        $govtVisa = '০';
        if (property_exists($visaStatusData,'GovtVisa') && !empty($visaStatusData->GovtVisa) && $visaStatusData->GovtVisa != '0'){
            $govtVisa = CommonFunction::convert2Bangla($visaStatusData->GovtVisa);
        }

        $privateVisa = '০';
        if (property_exists($visaStatusData,'PrivateVisa') && !empty($visaStatusData->PrivateVisa) && $visaStatusData->PrivateVisa != '0'){
            $privateVisa = CommonFunction::convert2Bangla($visaStatusData->PrivateVisa);
        }

        $this->bulletinData['total_visa'] = $totalVisa;
        $this->bulletinData['govt_visa'] = $govtVisa;
        $this->bulletinData['pvt_visa'] = $privateVisa;
        return true;
    }

    /**
     * @throws BulletinException
     */
    private function getLastDeathPilgrim(): bool
    {
        $pilgrimDataArray = $this->fetchDataFromAPI('HMIS_LAST_DEATH_PILGRIM ');

        if (empty($pilgrimDataArray)) {
            return false;
        }

        $pilgrimData = $pilgrimDataArray[0];
        if (!$this->isValidLastDeathPilgrimData($pilgrimData)) {
            return false;
        }

        $this->bulletinData['name'] = $pilgrimData->PilgrimName ?? '';
        $this->bulletinData['passport_no'] = $pilgrimData->PassportNo ?? '0000';
        $this->bulletinData['date'] = $pilgrimData->DeathDate ?? '' ;
        $this->bulletinData['age'] = $pilgrimData->Age ?? 0 ;
        return true;
    }

    /**
     * @throws BulletinException
     */
    private function fetchDataFromAPI($endpoint)
    {
        $clientID = config('constant.INSIGHTDB_API_CLIENT_ID');
        $clientSecret = config('constant.INSIGHTDB_API_CLIENT_SECRET');
        $url = config('constant.INSIGHTDB_API_TOKEN_URL');

        $token = (new CommonController())->getToken($clientID, $clientSecret, $url);
        if (!$token) {
            throw new BulletinException("এপিআই সার্ভিসের সাথে যোগাযোগ করতে পারছে নাহ। দয়া করে আবার চেষ্টা করুন", 500);
        }

        $apiEndpoint = config('constant.INSIGHTDB_API_BASE_URL') . '/' . $endpoint;
        $headers = ["Authorization: Bearer $token"];

        $response = CommonFunction::curlGetRequest($apiEndpoint, [], $headers);
        if ($response['http_code'] != 200) {
            throw new BulletinException("এপিআই সার্ভিসের সাথে যোগাযোগ করতে পারছে নাহ। দয়া করে আবার চেষ্টা করুন", 500);
        }

        $responseDecodedData = json_decode($response['data']);
        if (!property_exists($responseDecodedData, 'responseCode') || $responseDecodedData->responseCode != 200) {
            throw new BulletinException("এপিআই সার্ভিসের সাথে যোগাযোগ করতে পারছে নাহ। দয়া করে আবার চেষ্টা করুন", 500);
        }

        return $responseDecodedData->data;
    }

    private function isValidLastDeathPilgrimData($data): bool
    {
        return !empty($data->PilgrimName)
            && !empty($data->PassportNo)
            && !empty($data->DeathDate)
            && !empty($data->Age);
    }

}
