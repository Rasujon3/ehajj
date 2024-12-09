<?php

namespace App\Libraries;

use App\ActionInformation;
use App\Models\AuditLog;
use App\Models\User;
use App\Modules\CompanyAssociation\Models\CompanyAccessPrivileges;
use App\Modules\CompanyAssociation\Models\CompanyAssociation;
use App\Modules\CompanyAssociation\Models\CompanyAssociationMaster;
use App\Modules\CompanyAssociation\Models\InactiveCompanyUser;
use App\Modules\CompanyProfile\Models\CompanyInfo;
use App\Modules\CompanyProfile\Models\CompanyType;
use App\Modules\CompanyProfile\Models\RegistrationOffice;
use App\Modules\Files\Controllers\FilesController;
use App\Modules\IndustryNew\Controllers\IndustryNewController;
use App\Modules\IndustryNew\Models\IndustryNew;
use App\Modules\ProcessPath\Models\ProcessFavoriteList;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessStatus;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\ProcessPath\Models\UserDesk;
use App\Modules\ReportsV2\Models\ReportRecentActivates;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveClinic;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Settings\Models\EmailQueue;
use App\Modules\Settings\Models\EmailTemplates;
use App\Modules\Settings\Models\IndustrialCityList;
use App\Modules\Settings\Models\Logo;
use App\Modules\Settings\Models\RegulatoryAgency;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\Settings\Models\Area;
use App\Modules\Users\Models\Countries;
use App\Modules\Users\Models\Users;
use App\Modules\VisaAssistance\Models\SponsorsDirectors;
use App\Services\Minio;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CommonFunction
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

    public static function showErrorPublic($param, $msg = 'Sorry! Something went wrong! ')
    {
        $j = strpos($param, '(SQL:');
        if ($j > 15) {
            $param = substr($param, 8, $j - 9);
        } else {
            //
        }
        return $msg . $param;
    }
    public static function updateScriptPara($sql, $data)
    {
        $start = strpos($sql, '{$');
        while ($start > 0) {
            $end = strpos($sql, '}', $start);
            if ($end > 0) {
                $filed = substr($sql, $start + 2, $end - $start - 2);
                $sql = substr($sql, 0, $start) . $data[$filed] . substr($sql, $end + 1);
            }
            $start = strpos($sql, '{$');
        }
        return $sql;
    }

    public static function updatedOn($updated_at = '')
    {
        $update_was = '';
        if ($updated_at && $updated_at > '0') {
            $update_was = Carbon::createFromFormat('Y-m-d H:i:s', $updated_at)->diffForHumans();
        }
        return $update_was;
    }

    public static function getUserId()
    {

        if (Auth::user()) {
            return Auth::user()->id;
        } else {
            return 0;
        }
    }

    public static function getUserType()
    {

        if (Auth::user()) {
            return Auth::user()->user_type;
        } else {
            // return 1;
            dd('Invalid User Type');
        }
    }

    public static function GlobalSettings()
    {
        $logoInfo = Logo::orderBy('id', 'DESC')->first([
            'logo',
            'title',
            'manage_by',
            'help_link'
        ]);

        // Session::put('logo', 'assets/images/OSS_Framework_Logo.webp');
        Session::put('logo', 'assets/images/no_image.png');

        if ($logoInfo != "") {
            if (file_exists(url($logoInfo->logo))) {
                Session::put('logo', $logoInfo->logo);
            }
            Session::put('title', $logoInfo->title);
            Session::put('manage_by', $logoInfo->manage_by);
            Session::put('help_link', $logoInfo->help_link);
        }
    }

    public static function getUserCompanyWithZero()
    {
        if (Auth::user()) {
            return Auth::user()->working_company_id;
        } else {
            return 0;
        }
    }

    public static function getUserAllCompanyIdsWithZero()
    {
        if (Auth::user()) {
            $companyIds = CompanyAssociationMaster::where('request_from_user_id', Auth::user()->id)
                ->where('is_active', 1)
                ->where('status', 25)
                ->where('is_archive', 0)
                ->pluck('company_id')
                ->toArray();
            //            dd($companyIds, Auth::user()->id);
            return $companyIds;
        } else {
            return [-1];
        }
    }

    public static function getUserCompanyByUserId($userId)
    {
        $user = Users::find($userId);
        if ($user) {
            return $user->working_company_id;
        } else {
            return 0;
        }
    }

    public static function redirectToLogin()
    {
        echo "<script>location.replace('users/login');</script>";
    }

    public static function formateDate($date = '')
    {
        return date('d.m.Y', strtotime($date));
    }

    public static function convertUTF8($string)
    {
        //        $string = 'u0986u09a8u09c7u09beu09dfu09beu09b0 u09b9u09c7u09beu09b8u09beu0987u09a8';
        $string = preg_replace('/u([0-9a-fA-F]+)/', '&#x$1;', $string);
        return html_entity_decode($string, ENT_COMPAT, 'UTF-8');
    }

    /* This function determines if an user is an admin or sub-admin
     * Based On User Type
     *  */
    public static function isAdmin()
    {
        $user_type = Auth::user()->user_type;
        /*
         * 1x101 for System Admin
         * 5x501 for Agency Admin
         */
        if ($user_type == '1x101') {
            return true;
        } else {
            return false;
        }
    }

    public static function generateTrackingID($prefix, $id)
    {
        $prefix = strtoupper($prefix);
        $str = $id . date('Y') . mt_rand(0, 9);
        if (strlen($str) > 12) {
            $str = substr($str, strlen($str) - 12);
        }
        return strtoupper($prefix . dechex($str));
    }

    public static function changeDateFormat($datePicker, $mysql = false, $with_time = false)
    {

        try {
            if ($mysql) {
                if ($with_time) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $datePicker)->format('d-M-Y');
                } else {
                    return Carbon::createFromFormat('d-M-Y', $datePicker)->format('Y-m-d');
                }
            } else {
                return Carbon::createFromFormat('Y-m-d', $datePicker)->format('d-M-Y');
            }
        } catch (\Exception $e) {
            if (config('app.debug')) {
                dd($e);
            } else {
                return $datePicker; //'Some errors occurred (code:793)';
            }
        }
    }

    // Get age from birth date
    public static function age($birthDate)
    {
        $year = '';
        if ($birthDate) {
            $year = Carbon::createFromFormat('Y-m-d', $birthDate)->diff(Carbon::now())->format('%y years, %m months and %d days');
        }
        return $year;
    }

    public static function getUserDeskIds()
    {

        if (Auth::user()) {
            $deskIds = Auth::user()->desk_id;
            return explode(',', $deskIds);
        } else {
            // return 1;
            return [];
        }
    }

    public static function getDeskName($desk_id)
    {
        if (Auth::user()) {
            return UserDesk::where('id', $desk_id)->value('desk_name');
        } else {
            return '';
        }
    }

    public static function getUserOfficeIds()
    {

        if (Auth::user()) {
            $officeIds = Auth::user()->office_ids;
            $userOfficeIds = explode(',', $officeIds);
            return $userOfficeIds;
        } else {
            // return 1;
            dd('Invalid User status');
        }
    }

    public static function getDelegatedUserDeskOfficeIds()
    {

        $userId = CommonFunction::getUserId();
        $delegated_usersArr = Users::where('delegate_to_user_id', $userId)
            ->get([
                'id as user_id',
                'desk_id',
                'office_ids'
            ]);
        $delegatedDeskOfficeIds = array();
        foreach ($delegated_usersArr as $value) {

            $userDesk = explode(',', $value->desk_id);
            $userOffice = explode(',', $value->office_ids);
            $tempArr = array();
            $tempArr['user_id'] = $value->user_id;
            $tempArr['desk_ids'] = $userDesk;
            $tempArr['office_ids'] = $userOffice;
            $delegatedDeskOfficeIds[$value->user_id] = $tempArr;
        }
        return $delegatedDeskOfficeIds;
    }

    public static function getSelfAndDelegatedUserDeskOfficeIds()
    {

        $userId = CommonFunction::getUserId();
        $delegated_usersArr = Users::where('delegate_to_user_id', $userId)
            ->orWhere('id', $userId)
            ->get([
                'id as user_id',
                'desk_id',
                'office_ids'
            ]);
        //        dd($delegated_usersArr);
        $delegatedDeskOfficeIds = array();
        foreach ($delegated_usersArr as $value) {

            $userDesk = explode(',', $value->desk_id);
            $userOffice = explode(',', $value->office_ids);
            $tempArr = array();
            $tempArr['user_id'] = $value->user_id;
            $tempArr['desk_ids'] = $userDesk;
            $tempArr['office_ids'] = $userOffice;
            $delegatedDeskOfficeIds[$value->user_id] = $tempArr;
        }
        return $delegatedDeskOfficeIds;
    }

    public static function hasDeskOfficeWisePermission($desk_id, $office_id)
    {

        $getSelfAndDelegatedUserDeskOfficeIds = CommonFunction::getSelfAndDelegatedUserDeskOfficeIds();
        foreach ($getSelfAndDelegatedUserDeskOfficeIds as $selfDeskId => $value) {
            if (in_array($desk_id, $value['desk_ids']) ) {
                return true;
            }
        }
        return false;
    }

    public static function convert2English($ban_number)
    {
        $eng = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $ban = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($ban, $eng, $ban_number);
    }

    public static function getNotice($flag = 0)
    {
        if ($flag == 1) {
            $list = DB::select(DB::raw("SELECT date_format(updated_at,'%d %M, %Y') `Date`,heading,details,importance,id, case when importance='Top' then 1 else 0 end Priority FROM notice where status='public' or status='private' order by Priority desc, updated_at desc LIMIT 10"));
        } else {
            $list = DB::select(DB::raw("SELECT date_format(updated_at,'%d %M, %Y') `Date`,heading,details,importance,id, case when importance='Top' then 1 else 0 end Priority FROM notice where status='public' order by Priority desc, updated_at desc LIMIT 10"));
        }
        return $list;
    }

    public static function getCompanyNameById($id)
    {
        if ($id) {
            $name = CompanyInfo::where('id', $id)->value('org_nm_bn');
            return $name;
        } else {
            return 'N/A';
        }
    }

    public static function getBSCICOfficeName($id)
    {
        if ($id) {
            return IndustrialCityList::where('id', $id)->value('name_en');
        } else {
            return 'N/A';
        }
    }

    public static function getDistrictFirstTwoChar($district_id)
    {
        $districtName = Area::where('area_type', 2)->where('area_id', $district_id)->value('area_nm');
        return strtoupper(substr($districtName, 0, 2));
    }

    public static function getCompanyUserType()
    {
        $type = CompanyAssociationMaster::where('company_id', Auth::user()->working_company_id)
            ->where('request_from_user_id', Auth::user()->id)
            ->where('is_active', 1)
            ->where('status', 25)
            ->where('is_archive', 0)
            ->value('type');
        return $type;
    }

    /**
     * Count all application those are awaiting for processing by desk user.
     * Conditions:
     * 1. The application must be within the desk of the desk officer
     * 2. The application must be within the office of the desk officer
     * 3. The desk officer of the application must be the current desk officer or unassigned
     * 4. The application's desk ID cannot be zero
     * 5. The application Status ID cannot be contained in -1, 5 (Draft, Shortfall)
     *
     * @return mixed
     */
    public static function pendingApplication()
    {
        $userDeskIds = CommonFunction::getUserDeskIds();
        $userOfficeIds = CommonFunction::getUserOfficeIds();
        $user_id = CommonFunction::getUserId();
        return ProcessList::whereIn('desk_id', $userDeskIds)
            ->whereIn('office_id', $userOfficeIds)
            ->where(function ($query2) use ($user_id) {
                $query2->where('user_id', $user_id)
                    ->orWhere('user_id', 0);
            })
            ->where('desk_id', '!=', 0)
            ->whereNotIn('status_id', [-1, 5])
            ->count();
    }

    /**
     * @param string $caption
     * @param array $appInfo
     * @param array $receiverInfo
     * @return mixed
     * @throws \Throwable
     */
    public static function sendEmailSMS($caption = '', $appInfo = [], $receiverInfo = [])
    {

        try {
            $template = EmailTemplates::where('caption', $caption)->first();

            //            if (isset($appInfo['process_type_id']) && in_array($appInfo['process_type_id'], [101, 102, 103, 104, 105, 106])) { //Eliminating service type from email content for these service
            //                $template->email_content = str_replace('Service Type: {$serviceSubName}<br/>', '', $template->email_content);
            //            }

            if (!in_array($caption, [
                'TWO_STEP_VERIFICATION', 'ACCOUNT_ACTIVATION',
                'CONFIRM_ACCOUNT', 'APPROVE_USER', 'REJECT_USER', 'PASSWORD_RESET_REQUEST',
                'APP_APPROVE_PIN_NUMBER', 'ASK_FOR_ADVICE_FROM_ADVISOR', 'USER_VERIFICATION_EMAIL',
                'NEW_PASSWORD', 'PASSWORD_RESET_REQUEST', 'DEVICE_DETECTION', 'ONE_TIME_PASSWORD','PASSWORD_CHANGE'
            ])) {

                $template->email_content = str_replace('{$trackingNumber}', $appInfo['tracking_no'], $template->email_content);
                $template->email_content = str_replace('{$serviceName}', $appInfo['process_type_name'], $template->email_content);
                //                $template->email_content = str_replace('{$serviceSupperName}', $appInfo['process_type_name'], $template->email_content);
                $template->email_content = str_replace('{$serviceSubName}', $appInfo['process_type_name'], $template->email_content);
                $template->email_content = str_replace('{$remarks}', $appInfo['remarks'], $template->email_content);
                $template->sms_content = str_replace('{$serviceName}', $appInfo['process_type_name'], $template->sms_content);
                //                $template->sms_content = str_replace('{$serviceSupperName}', $appInfo['process_supper_name'], $template->sms_content);
                $template->sms_content = str_replace('{$trackingNumber}', $appInfo['tracking_no'], $template->sms_content);
            } elseif ($caption == 'APP_ISSUED_LETTER') {
                $template->email_content = str_replace('{$serviceName}', $appInfo['process_type_name'], $template->email_content);
                $template->email_content = str_replace('{$serviceSubName}', $appInfo['process_type_name'], $template->email_content);
                $template->email_content = str_replace('{$attachment}', $appInfo['attachment_certificate_name'], $template->email_content);
                $template->sms_content = str_replace('{$serviceName}', $appInfo['process_type_name'], $template->sms_content);
                $template->sms_content = str_replace('{$trackingNumber}', $appInfo['tracking_no'], $template->sms_content);
            } elseif ($caption == 'CONFIRM_ACCOUNT') {
                $template->email_content = str_replace('{$verificationLink}', $appInfo['verification_link'], $template->email_content);
            } elseif ($caption == 'DEVICE_DETECTION') {
                $template->email_content = str_replace('{$device}', $appInfo['device'], $template->email_content);
                $template->email_subject = str_replace('{$device}', $appInfo['device'], $template->email_subject);
            } elseif ($caption == 'ONE_TIME_PASSWORD') {
                $template->email_content = str_replace('{$code}', $appInfo['one_time_password'], $template->email_content);
            } else if ($caption == 'TWO_STEP_VERIFICATION') {
                $template->email_content = str_replace('{$code}', $appInfo['code'], $template->email_content);
                $template->sms_content = str_replace('{$code}', $appInfo['code'], $template->sms_content);


                if ($appInfo['verification_type'] == 'mobile_no') {
                    $template->email_active_status = 0;
                    $template->sms_active_status = 1;
                } else {
                    $template->email_active_status = 1;
                    $template->sms_active_status = 0;
                }
            } elseif ($caption == 'REJECT_USER') {
                $template->email_content = str_replace('{$rejectReason}', $appInfo['reject_reason'], $template->email_content);
            } elseif ($caption == 'PASSWORD_RESET_REQUEST') {
                $template->email_content = str_replace('{$reset_password_link}', $appInfo['reset_password_link'], $template->email_content);
            } elseif($caption == 'PASSWORD_CHANGE') {
                $template->email_content = str_replace('{$name}', $appInfo['user_name'], $template->email_content);
                $template->sms_content = str_replace('{$name}', $appInfo['user_name'], $template->sms_content);
            }

            if ($caption == 'PROCEED_TO_MEETING') {
                $template->email_content = str_replace('{$meetingDate}', $appInfo['meeting_date'], $template->email_content);
                $template->email_content = str_replace('{$meetingTime}', $appInfo['meeting_time'], $template->email_content);
            } elseif (in_array($caption, ['APP_APPROVE_AND_PAYMENT', 'MC_APP_APPROVE_AND_PAYMENT'])) {
                $template->email_content = str_replace('{$govtFees}', $appInfo['govt_fees'], $template->email_content);
            } elseif ($caption == 'APP_GOV_PAYMENT_SUBMIT') {
                $template->email_content = str_replace('{$govtFees}', $appInfo['govt_fees'], $template->email_content);
                $template->email_content = str_replace('{$govtFeesOnlyAmount}', $appInfo['govt_fees_amount'], $template->email_content);
                $template->email_content = str_replace('{$paymentDate}', $appInfo['payment_date'], $template->email_content);
                $template->sms_content = str_replace('{$govtFeesOnlyAmount}', $appInfo['govt_fees_amount'], $template->sms_content);
            } elseif ($caption == 'IMMIGRATION') {
                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
                $template->email_content = str_replace('{$visaType}', $appInfo['visa_type'], $template->email_content);
                $template->email_content = str_replace('{$airportName}', $appInfo['airport_name'], $template->email_content);
                $template->email_content = str_replace('{$airportAddress}', $appInfo['airport_address'], $template->email_content);
            } elseif ($caption == 'EMBASSY_HIGH_COMMISSION') {
                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
                $template->email_content = str_replace('{$visaType}', $appInfo['visa_type'], $template->email_content);
                $template->email_content = str_replace('{$highCommissionName}', $appInfo['high_commission_name'], $template->email_content);
                $template->email_content = str_replace('{$highCommissionAddress}', $appInfo['high_commission_address'], $template->email_content);
            } elseif ($caption == 'WP_ISSUED_LETTER_STAKEHOLDER') {
                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
            } elseif ($caption == 'ASK_FOR_ADVICE_FROM_ADVISOR') {
                $business_type = CompanyType::where('id', $appInfo['business_type'])->first(['name_bn']);
                $country_name = Countries::where('id', $appInfo['country_id'])->first(['nicename']);

                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
                $template->email_content = str_replace('{$organization_name}', $appInfo['organization_name'], $template->email_content);
                $template->email_content = str_replace('{$business_type}', $business_type['name_bn'], $template->email_content);
                $template->email_content = str_replace('{$country}', $country_name['nicename'], $template->email_content);
                $template->email_content = str_replace('{$user_mobile}', $appInfo['mobile_no'], $template->email_content);
                $template->email_content = str_replace('{$email_address}', $appInfo['email'], $template->email_content);
                $template->email_content = str_replace('{$questions}', $appInfo['question'], $template->email_content);
            } elseif ($caption == 'OP_ISSUED_LETTER_STAKEHOLDER') {
                $template->email_content = str_replace('{$organizationName}', $appInfo['organization_name'], $template->email_content);
            }
            //            elseif ($caption == 'REJECT_USER') {
            //                $template->email_content = str_replace('{$rejectReason}', $appInfo['reject_reason'], $template->email_content);
            //            }
            elseif ($caption == 'VRN_ISSUED_LETTER_STAKEHOLDER') {
                $template->email_content = str_replace('{$name}', $appInfo['name'], $template->email_content);
                $template->email_content = str_replace('{$nationality}', $appInfo['nationality'], $template->email_content);
                $template->email_content = str_replace('{$passportNumber}', $appInfo['passport_number'], $template->email_content);
                $template->email_content = str_replace('{$designation}', $appInfo['designation'], $template->email_content);
                $template->email_content = str_replace('{$visaType}', $appInfo['visa_type'], $template->email_content);
            } elseif ($caption == 'APP_APPROVE_PIN_NUMBER') {
                $template->email_content = str_replace('{$pinNumber}', $appInfo['code'], $template->email_content);
                $template->sms_content = str_replace('{$pinNumber}', $appInfo['code'], $template->sms_content);
            } elseif ($caption == 'APP_APPROVE') {
                $template->email_content = str_replace('{$trackingNumber}', $appInfo['tracking_no'], $template->email_content);
                $template->email_content = str_replace('{$serviceName}', $appInfo['process_type_name'], $template->email_content);
                $template->sms_content = str_replace('{$serviceName}', $appInfo['process_type_name'], $template->sms_content);
                $template->sms_content = str_replace('{$trackingNumber}', $appInfo['tracking_no'], $template->sms_content);
            }
            $smsBody = $template->sms_content;
            $header = $template->email_subject;
            $param = $template->email_content;
            $caption = $template->caption;

            $email_content = view("Users::message", compact('header', 'param'))->render();
            $ccEmailFromConfiguration = CommonFunction::ccEmail();
            $NotificationWebService = new NotificationWebService();

            if ($template->email_active_status == 1 || $template->sms_active_status == 1) {  // checking whether template status is on/off for email and sms
                $emailQueueData = [];

                foreach ($receiverInfo as $receiver) {
                    $emailQueue = [];
                    $emailQueue['process_type_id'] = isset($appInfo['process_type_id']) ? $appInfo['process_type_id'] : 0;
                    $emailQueue['app_id'] = isset($appInfo['app_id']) ? $appInfo['app_id'] : 0;
                    $emailQueue['status_id'] = isset($appInfo['status_id']) ? $appInfo['status_id'] : 0;
                    $emailQueue['caption'] = $caption;
                    $emailQueue['email_content'] = $email_content;
                    if ($template->email_active_status == 1) {
                        $emailQueue['email_to'] = $receiver['user_email'];
                        $emailQueue['email_cc'] = !empty($template->email_cc) ? $template->email_cc : $ccEmailFromConfiguration;
                    }
                    $emailQueue['email_subject'] = $header;
                    if (!empty(trim($receiver['user_mobile'])) && $template->sms_active_status == 1) {
                        $emailQueue['sms_content'] = $smsBody;
                        $emailQueue['sms_to'] = substr(trim($receiver['user_mobile']), -11);

                        // Instant SMS Sending
                        $sms_sending_response = $NotificationWebService->sendSms($receiver['user_mobile'], $smsBody);
                        $emailQueue['sms_response'] = $sms_sending_response['msg'];
                        if ($sms_sending_response['status'] === 1) {
                            $emailQueue['sms_status'] = 1;
                            $emailQueue['sms_response_id'] = $sms_sending_response['message_id'];
                        }
                        // End of Instant SMS Sending
                    }
                    $emailQueue['attachment'] = isset($appInfo['attachment']) ? $appInfo['attachment'] : '';
                    $emailQueue['attachment_certificate_name'] = isset($appInfo['attachment_certificate_name']) ? $appInfo['attachment_certificate_name'] : '';
                    $emailQueue['created_at'] = date('Y-m-d H:i:s');
                    $emailQueue['updated_at'] = date('Y-m-d H:i:s');

                    // Instant Email sending
                    if (empty($emailQueue['attachment_certificate_name']) && $template->email_active_status == 1) {
                        $email_sending_response = $NotificationWebService->sendEmail([
                            'header_text' => config('app.project_name'),
                            'recipient' => $receiver['user_email'],
                            'subject' => $header,
                            'bodyText' => '',
                            'bodyHtml' => $email_content,
                            'email_cc' => $emailQueue['email_cc']
                        ]);
                        $emailQueue['email_response'] = $email_sending_response['msg'];
                        if ($email_sending_response['status'] === 1) {
                            $emailQueue['email_status'] = 1;
                            $emailQueue['email_response_id'] = $email_sending_response['message_id'];
                        }
                    }
                    // End of Instant Email sending

                    $emailQueueData[] = $emailQueue;
                }

                EmailQueue::insert($emailQueueData);
                $lastId = DB::getPdo()->lastInsertId();
                return $lastId;
            }

            return true;
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine());
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [CM-1005]');
            return Redirect::back()->withInput();
        }
    }

    public static function requestPinNumber($app_id, $process_type_id)
    {
        $user_id = CommonFunction::getUserId();
        $users = Users::where('id', $user_id)->first();
        $code = rand(1000, 9999);
        //        $token = $code .$user_id;
        //        $encrypted_pin = Encryption::encode($token);
        $data = [
            'code' => $code,
            'app_id' => $app_id,
            'process_type_id' => $process_type_id
        ];

        Users::where('user_email', $users->user_email)->update(['pin_number' => $code]);

        $receiverInfo[] = [
            'user_email' => $users->user_email,
            'user_mobile' => $users->user_mobile
        ];

        CommonFunction::sendEmailSMS('APP_APPROVE_PIN_NUMBER', $data, $receiverInfo);
        return true;
    }

    public static function checkFavoriteItem($process_id)
    {
        $result = ProcessFavoriteList::where('process_id', $process_id)
            ->where('user_id', CommonFunction::getUserId())
            ->count();
        return $result;
    }

    public static function ccEmail()
    {
        return Configuration::where('caption', 'CC_EMAIL')->value('value');
    }

    public static function getUserFullName()
    {
        if (Auth::user()) {
            return Auth::user()->user_first_name . ' ' . Auth::user()->user_middle_name . ' ' . Auth::user()->user_last_name;
        } else {
            return 'Invalid Login Id';
        }
    }

    public static function convert_number_to_words($number)
    {
        $common = new CommonFunction;
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'fourty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            1000000 => 'million',
            1000000000 => 'billion',
            1000000000000 => 'trillion',
            1000000000000000 => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $common->convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $common->convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $common->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $common->convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string)$fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    public static function generateRegistrationNumber($ref_id, $registrationPrefix, $table, $process_type_id)
    {

        if ($process_type_id == 1) {
            DB::statement("update  $table, $table as table2  SET $table.regist_no=(
                                                            select concat('$registrationPrefix',
                                                                    LPAD( IFNULL(MAX(SUBSTR(table2.regist_no,-7,7) )+1,1),7,'0')
                                                                          ) as regist_no
                                                             from (select * from $table ) as table2
                                                             where table2.id!='$ref_id'
                                                        )
                                                      where $table.id='$ref_id' and table2.id='$ref_id'");
        } else {
            DB::statement("update  $table, $table as table2  SET $table.regist_no=(
                                                            select concat('$registrationPrefix',
                                                                    LPAD( IFNULL(MAX(SUBSTR(table2.regist_no,-8,8) )+1,1),8,'0')
                                                                          ) as regist_no
                                                             from (select * from $table ) as table2
                                                             where table2.id!='$ref_id'
                                                        )
                                                      where $table.id='$ref_id' and table2.id='$ref_id'");
        }
    }

    public static function getOfficeShortCode($office_id)
    {
        if ($office_id) {
            return IndustrialCityList::where('id', $office_id)->value('office_short_code');
        } else {
            return '';
        }
    }

    public static function checkEligibility()
    {
        //        $companyIds = explode(',', Auth::user()->company_ids);
        //        $inactiveUser = DB::table('inactive_company_users')->where('user_id', Auth::user()->id)
        //            ->whereIn("company_id", $companyIds)->pluck('company_id');
        //        dd($inactiveUser);

        //        $companyIds = explode(',', Auth::user()->company_ids);
        $companyId = Auth::user()->working_company_id;
        $data = 0;
        if ($companyId > 0) {
            //            $data = CompanyInfo::where('id', $companyId)->count();
            $data = CompanyInfo::where('id', $companyId)->where('company_status', 1)->count();
        }
        if ($data > 0)
            return 1;
        else
            return 0;
    }

    public static function geCompanyUsersEmailPhone($company_id)
    {
        return Users::where('working_company_id', $company_id)
            ->whereIn('user_type', ['5x505', '6x606'])
            ->where('user_status', 'active')
            ->get(['user_email', 'user_mobile']);
    }

    public static function storeReportRecentActivates($report_id, $type = '')
    {
        $user_id = Auth::user()->id;
        $insertData = ReportRecentActivates::firstOrNew(['user_id' => $user_id, 'report_id' => $report_id]);
        $insertData->type = $type;
        $insertData->is_active = 1;
        $insertData->updated_at = Carbon::now();
        $insertData->save();
    }

    public static function vulnerabilityCheck($value, $type = 'integer')
    {
        if ($type == 'integer') {
            $intData =  is_numeric($value);
            if ($intData && (!preg_match('/[\'^£$%&*().}{@#~?><>,|=_+¬-]/', $value))) {
                return $value;
            }
        }
        if ($type == 'string') {
            if (!preg_match('/[\'^£$%&*().!}{@#~?><>,|=_+¬-]/', $value)) {
                return $value;
            }
        }
        abort(404);
    }

    public static function getAppRedirectPathByJson($json)
    {
        $openMode = 'edit';
        $form_id = json_decode($json, true);
        $url = (isset($form_id[$openMode]) ? explode('/', trim($form_id[$openMode], "/")) : '');
        $view = ($url[1] == 'edit' ? 'view-app' : 'view'); // view page
        $edit = ($url[1] == 'edit' ? 'edit-app' : 'view'); // edit page
        $array = [
            'view' => $view,
            'edit' => $edit
        ];
        return $array;
    }

    public static function showAuditLog($updated_at = '', $updated_by = '')
    {
        $update_was = 'Unknown';
        if ($updated_at && $updated_at > '0') {
            $update_was = Carbon::createFromFormat('Y-m-d H:i:s', $updated_at)->diffForHumans();
        }

        $user_name = 'Unknown';
        if ($updated_by) {
            $name = User::where('id', $updated_by)->first(['user_first_name', 'user_middle_name', 'user_last_name']);
            if ($name) {
                $user_name = $name->user_first_name . ' ' . $name->user_middle_name . ' ' . $name->user_last_name;
            }
        }
        return '<span class="help-block">সর্বশেষ সংশোধন : <i>' . $update_was . '</i> by <b>' . $user_name . '</b></span>';
    }

    public static function convert2Bangla($eng_number)
    {
        $eng = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $ban = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($eng, $ban, $eng_number);
    }

    public static function DelegateUserInfo($desk_id)
    {
        $userID = CommonFunction::getUserId();

        $delegateUserInfo = Users::where('delegate_to_user_id', $userID)
            ->first([
                'id',
                DB::raw("CONCAT(users.user_first_name,' ',users.user_middle_name, ' ',users.user_last_name) as user_full_name"),
                'user_email',
                'user_pic',
                'designation'
            ]);
        return $delegateUserInfo;
    }



    /*************************************
     * Ending OSS Common functions
     *************************************/


    /*****************************************************************
     *
     * Please, write all project basis function in below
     *
     *****************************************************************/

    public static function getApiToken($tokenUrl,$tokenData)
    {
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );
        $response = self::curlPostRequest($tokenUrl,$tokenData,$headers);

        /*
        Set bearerCode
        */
        $decodedResponseData = json_decode($response['data']);

        return property_exists($decodedResponseData, 'token') ? $decodedResponseData->token : '';
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

    public static function maskedMobile($mobile_number)
    {
        $mobile_number = substr($mobile_number, -11);
        $masked_number = substr($mobile_number,0, 4).'*******' . substr($mobile_number, -2);
        return $masked_number;

    }

    public static function rearrangeMedicineName($medName, $case = ''){
        $name = trim($medName);
        $searchArr = array("  ", "-", " + ", ";", "_", "   ");
        $replaceArr = array(" ", " ", "+", " ", " ", " ");
        $correct_word = str_replace($searchArr, $replaceArr, $name);
        if ($case == 'store'){
            return strtolower($correct_word);
        }else{
            $revised_word = str_replace(' ', '', $correct_word);
            return strtolower($revised_word);
        }

    }

    public static function setFlashMessageByStatusId( $status_id ) {
        $msg_type = [
            1  => 'Successfully Submitted !',
            -1 => 'Successfully Updated !',
            2  => 'Successfully Re-Submitted'
        ];

        if ( empty( $msg_type[ $status_id ] ) ) {
            Session::flash( 'error', 'Failed due to Application Status Conflict. Please try again later! [ISPR-007]' );
            return;
        }
        Session::flash( 'success', $msg_type[ $status_id ] );
    }

    public static function pdfGeneration($title,$subject,$stylesheet='',$html,$pdfFilePath, $saveOrView = 'I',$exit=false){
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults(); // extendable default Configs
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults(); // extendable default Fonts
        $fontData = $defaultFontConfig['fontdata'];
        $mpdf = new \Mpdf\Mpdf([
            'tempDir'       => storage_path(),
            'fontDir' => array_merge($fontDirs, [
                public_path('assets/fonts'), // to find like /public/fonts/SolaimanLipi.ttf
            ]),
            'fontdata' => $fontData + [
                    'kalpurush' => [
                        'R' => 'kalpurush-kalpurush.ttf', 'I' => 'kalpurush-kalpurush.ttf', 'useOTL' => 0xFF, 'useKashida' => 75,

                    ],
                ],
            'default_font' => 'kalpurush',
//                'setAutoTopMargin' => 'pad',
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 11,
            'margin_top' => 7,    	// 15 margin_left
            'margin_left' => 7,    	// 15 margin_left
            'margin_right' => 7,    	// 15 margin right
            'margin_header' => 0,     // 9 margin header
            'margin_footer' => 7,     // 9 margin footer
        ]);


        $mpdf->SetProtection(array('print'));
        $mpdf->SetDefaultBodyCSS('color', '#000');
        $mpdf->SetTitle( $title);
        $mpdf->SetSubject($subject);
        $mpdf->SetAuthor("Business Automation Limited");
        //$mpdf->autoScriptToLang = true;
        $mpdf->baseScript = 1;
        $mpdf->autoVietnamese = true;
        $mpdf->SetDisplayMode('fullwidth');
        $mpdf->SetHTMLHeader();

        $footer = '<table>
                    <tbody>
                    <tr>
                        <td width="10" style="width:10px;padding: 0;">&nbsp;</td>
                        <td>
                            <table width="980" border="0" cellspacing="0" cellpadding="0" class="mobile-shell" style="width: 980px;">
                                <tbody>
                                <tr>
                                    <td style="text-align: left;padding: 5px 10px; font-size:10px;">Generated On: ' . date('d-M-Y h:i A') . '</td>
                                </tr>
                                <tr>
                                    <td style="text-align: left;padding: 0px 10px; font-size:10px;">Haj Management Information System managed by
                                        Business Automation Ltd on behalf of Ministry of Religious Affairs, Bangladesh
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td width="10" style="width:10px;padding: 0;">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>';

        if($subject == 'PDF [PDF-001]') {
            $current_time = date('Y-m-d H:i:s');
            $footer = '
            <table style="width: 100%;">
                <tr>
                    <td style="color: #091E42; width: 50%; text-align: left; font-size: 10px;">For more information, please visit  - <a href="https://hajj.gov.bd/" style="color: #474D49; text-decoration: none">hajj.gov.bd</a></td>
                    <td style="color: #091E42; width: 50%; text-align: right; font-size: 10px;">Generated On : '. date('d-M-Y h:i A', strtotime($current_time)).'</td>
                </tr>
            </table>';
        }
        $mpdf->SetHTMLFooter($footer);
        $mpdf->defaultfooterline = 0;

        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->showWatermarkImage = true;
        if($stylesheet){
            $mpdf->WriteHTML($stylesheet, 1);
        }
        if($subject == 'PDF [PDF-001]'){
            $mpdf->SetWatermarkImage(public_path('assets/images/travel_pdf_watermark.png'),1.0,50);
        }
        if($subject == 'Counter Payment Slip PDF'){
            $mpdf->SetWatermarkImage(public_path('assets/images/paid.png'),1,[35, 35],[60, 60]);
        }
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output($pdfFilePath, $saveOrView); // Saving pdf *** F for Save only, I for view only.
        if($subject == 'PDF [PDF-001]') {
            return true;
        }
        if(!$exit){
            exit();
        }
    }

    public static function getPharmacyName(){
        return MedicalReceiveClinic::where('id', Auth::user()->working_company_id)->value('name');
    }

    public static function get_enum_values( $table, $field )
    {
        $type = DB::select(DB::raw('SHOW COLUMNS FROM '.$table.' WHERE Field = "'.$field.'"'))[0]->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $enum = explode("','", $matches[1]);
        return $enum;
    }

    public static function curlGetRequest($url,$reqData,$headers)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => $reqData,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>false,
        ));

        $response = curl_exec($curl);

        if (!curl_errno($curl)) {
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        } else {
            $http_code = 0;
        }
        curl_close($curl);
        return ['http_code' => intval($http_code), 'data' => $response];

    }
    public static function getToken($client_id, $client_secret,$url)
    {
        $api_url_for_token = $url;
        $postdata['client_id'] = $client_id;
        $postdata['client_secret'] = $client_secret;
        $postdata['grant_type'] = 'client_credentials';

        $headers = ['Content-Type: application/x-www-form-urlencoded'];

        try {
            $respData = CommonFunction::curlPostRequest($api_url_for_token, $postdata, $headers);
            if ($respData['http_code'] != 200) {
                return false;
            }
            $decoded_json = json_decode($respData['data'], true);
            return $decoded_json['access_token'];

        } catch (\Exception $e) {
            return false;
        }
    }

    public static function directoryFunction($directory, $fileNamePrefix){
        $pdfFilePath = 'news/uploads/'.$directory.'/';
        $directoryName = $pdfFilePath.date("Y/m");
        $directoryNameYear = $pdfFilePath.date("Y");
        if (!file_exists($directoryName)) {
            $oldmask = umask(0);
            mkdir($directoryName, 0777, true);
            umask($oldmask);
        }
        $pdfFileName = uniqid($fileNamePrefix,true);
        $pdfFilePath = $directoryName."/".$pdfFileName.'.pdf';
        return $pdfFilePath;
    }

    public static function validateMobileNumber($mobile_no)
    {
        $mobile_validation_err = '';
        $first_digit = substr($mobile_no, 0, 1);
        $first_two_digit = substr($mobile_no, 0, 2);
        $first_four_digit = substr($mobile_no, 0, 5);
        // if first two digit is 01
        if (strlen($mobile_no) < 11) {
            $mobile_validation_err = 'Mobile number should be minimum 11 digit';
        } elseif ($first_two_digit == '01') {
            if (strlen($mobile_no) != 11) {
                $mobile_validation_err = 'Mobile number should be 11 digit';
            }
        } // if first two digit is +880
        else if ($first_four_digit == '+8801') {
            if (strlen($mobile_no) != 14) {
                $mobile_validation_err = 'Mobile number should be 14 digit';
            }
        } // if first digit is only
        else if ($first_digit == '+') {
            // Mobile number will be ok
        } else {
            $mobile_validation_err = 'Please enter valid Mobile number';
        }

        if (strlen($mobile_validation_err) > 0) {
            return $mobile_validation_err;
        } else {
            return 'ok';
        }
    }

    public static function getTokenData(){
        $tokenUrl =  env('API_URL')."/api/getToken";
        $credential = [
            'clientid' => env('CLIENT_ID'),
            'username' => env('CLIENT_USER_NAME'),
            'password' => env('CLIENT_PASSWORD')
        ];
        return self::getApiToken($tokenUrl, $credential);
    }
    public static function getFlightCodeByDate($date)
    {
        $token = self::getTokenData();
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $base_url = env('API_URL');
        $apiUrl = "$base_url/api/reservation-flight-code";
        $postData = [
            'tracking_no'=>explode("_",Auth::user()->user_email)[0],
            'date'=>$date
        ];
        $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
        $apiResponseDataArr = json_decode($apiResponse['data']);
        if ($apiResponseDataArr->status != 200){
            return response()->json([]);
        }
        return $apiResponseDataArr->data;
    }
    public static function getMinioUploadedFileURL($file) {
        try{
            if(strpos($file, 'minio') !== false){
                $parts = explode(':', $file);
                $filename = end($parts);
                $bucket = prev($parts);
                $domain = prev($parts);
                $path = "https:/".$domain."/".$bucket.$filename;
                $mime_type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);

                $mimes = explode('.', $filename);
                $type = "image";
                if($mimes[1] == 'pdf'){
                    $type = "application";
                }
                $file = 'data:'. $type . '/' . $mime_type . ';base64,' . base64_encode($data);
            }
            return $file;
        } catch (\Exception $e) {
            return false;
        }
    }
    public static function putMinioUploadedFileURL($file, $bucket = null) {
        try{
            $imageData = base64_decode(preg_replace('#^data:\w+/\w+;base64,#i', '', $file));
            $finfo = finfo_open();
            $format = finfo_buffer($finfo, $imageData, FILEINFO_MIME_TYPE);
            finfo_close($finfo);
            $mime = explode('/', $format)[1];

            if (!in_array($mime, ['png', 'jpeg', 'jpg', 'webp', 'pdf'])) {
                return false;
            }

            $minio = new Minio($bucket ?? env('MINIO_BUCKET'));
            $now = \DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''));
            $local = $now->setTimeZone(new \DateTimeZone('Asia/Dhaka'));
            $filename = $local->format("Y/m");
            $uuid = uniqid();
            $fileName = $filename . '/' . $uuid . '.' . $mime;
            $ObjectURL = $minio->put($imageData, $fileName);
            $parsedUrl = parse_url(env("MINIO_ENDPOINT"));
            $domain = $parsedUrl['host'];
            $server = $bucket ?? env("MINIO_BUCKET");
            $return_url = "minio:/".$domain.":".$server.":/".$fileName;
            return $return_url;
        }
        catch (\Exception $e) {
            return false;
        }
    }

    public static function checkTokenVerification($requestHeaders){
        $apiUrl = env('API_URL')."/api/verify-access-token";

        $postData['requestHeaders'] = $requestHeaders;
        $postData = json_encode($postData);
        $token = $requestHeaders['apiauthorization'][0];
        $headers = [
            'APIAuthorization:' . $token,
            'Content-Type: application/json',
        ];
        $apiResponse = self::curlPostRequest($apiUrl, $postData, $headers,true);
        if (!empty($apiResponse)){
            $apiResponseDataArr = json_decode($apiResponse['data']);
            $result=json_decode($apiResponse['data']);
            if (!empty($result->data)){
                return response()->json([
                    'data'=>true,
                    'status'=>200
                ],Response::HTTP_OK);
            }else{
                return response()->json([
                    'data'=>false,
                    'status'=>200
                ],Response::HTTP_BAD_GATEWAY);
            }
        }else{
            return response()->json([
                'data'=>"No Response found"
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
    public static function getExternalToken($client_id, $client_secret,$url)
    {
        $api_token_json_filepath = public_path('api_token_json/token.json');
        $directoryPath = pathinfo($api_token_json_filepath, PATHINFO_DIRNAME);
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }
        if(file_exists($api_token_json_filepath)){
            $sms_token_json = file_get_contents($api_token_json_filepath);
            $sms_token_json = json_decode($sms_token_json, true);
            if(!empty($sms_token_json["token_expire_time_str"]) && $sms_token_json["token_expire_time_str"] > strtotime(Carbon::now())){
                return isset($sms_token_json['access_token']) ? $sms_token_json['access_token'] : '';
            }
        }

        $api_url_for_token = $url;
        $postdata['client_id'] = $client_id;
        $postdata['client_secret'] = $client_secret;
        $postdata['grant_type'] = 'client_credentials';

        $headers = ['Content-Type: application/x-www-form-urlencoded'];

        try {
            $respData = CommonFunction::curlPostRequest($api_url_for_token, $postdata, $headers);
            if ($respData['http_code'] != 200) {
                return false;
            }
            $formatResponse = $decoded_json = json_decode($respData['data'], true);
            $formatResponse = $formatResponse + ['token_expire_time_str' => strtotime(Carbon::now()->addMinutes(3))];
            $formatResponse = json_encode($formatResponse, JSON_PRETTY_PRINT);
            file_put_contents($api_token_json_filepath, '{}');
            file_put_contents($api_token_json_filepath, $formatResponse);
            return $decoded_json['access_token'];

        } catch (\Exception $e) {
            return false;
        }
    }
}
