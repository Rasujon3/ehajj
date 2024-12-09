<?php

namespace App\Modules\SonaliPayment\Services;

use App\Libraries\CommonFunction;
use App\Modules\ProcessPath\Models\ProcessList;
use Illuminate\Support\Facades\Auth;
use App\Modules\Users\Models\Users;

trait OnlinePaymentPostProcessing
{
    private function applicationRelatedTasks($process_name, $process_info, $paymentInfo)
    {
        $applicantEmailPhone = CommonFunction::geCompanyUsersEmailPhone($process_info->company_id);

        $appInfo = [
            'app_id' => $process_info->ref_id,
            'status_id' => $process_info->status_id,
            'process_type_id' => $process_info->process_type_id,
            'tracking_no' => $process_info->tracking_no,
            'process_type_name' => $process_name,
            'remarks' => ''
        ];

        switch ($process_info->process_type_id) {
            case 1: // New Registration
                if ($process_info->status_id === 1) {
                    CommonFunction::sendEmailSMS('APP_SUBMIT', $appInfo, $applicantEmailPhone);
                }
                break;
                // The functionality for new process type will go here
        }
    }
}
