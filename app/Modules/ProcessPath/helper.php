<?php

// use Session;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalDetails;
use App\Modules\REUSELicenseIssue\Http\Controllers\AjaxRequestController;
use App\Libraries\PostApiData;
use App\Modules\REUSELicenseIssue\Models\StickerVisa\StickerPilgrims;
use App\Http\Traits\Token;
use App\Libraries\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Modules\Users\Models\Users;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\PdfServiceInfo;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\Settings\Models\PdfPrintRequestQueue;

use App\Modules\ProcessPath\Models\FlightRequestPilgrim;
use Illuminate\Support\Facades\Session;

/**
 * @param $json
 *
 * @return string
 */
function getDataFromJson($json): string
{
    $jsonDecoded = json_decode( $json );
    $string      = '';
    if(is_array($jsonDecoded)){
        return '';
    }
    foreach ( $jsonDecoded as $key => $data ) {
        $string .= $key . ": " . $data . ', ';
    }

    return $string;
}


/**
 * Here are the extras that work on a specific process type application or a specific status of the application.
 * Such as sending mail / sms to specific status, generating certificates in the final status or
 * updating any data in the specified status
 *
 * @param $process_list_id
 * @param $status_id
 * @param int $approver_desk_id
 * @param $requestData
 *
 * @return bool
 * @throws Throwable
 */
function CertificateMailOtherData($process_list_id, $status_id, $requestData, $approver_desk_id = 0): bool
{

    $processInfo  = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
        ->where('process_list.id', $process_list_id)
        ->first([
            'process_list.tracking_no',
            'process_type.id',
            'process_type.name as process_type_name',
            'process_list.ref_id',
            'process_list.company_id',
            'process_list.process_type_id',
            'process_list.office_id',
            'process_list.process_desc',
        ]);
    $receiverInfo = Users::where('working_company_id', $processInfo->company_id)
        ->where('user_status', 'active')
        ->get(['user_email', 'user_mobile']);


    $appInfo = [
        'app_id' => $processInfo->ref_id,
        'status_id' => $status_id,
        'process_type_id' => $processInfo->process_type_id,
        'tracking_no' => $processInfo->tracking_no,
        'process_type_name' => $processInfo->process_type_name,
        'remarks' => $requestData['remarks']
    ];
    $hajjSession = HajjSessions::where(['state' => 'active'])->first('id');
    if ($status_id == 5) {
        //CommonFunction::sendEmailSMS('APP_SHORTFALL', $appInfo, $receiverInfo);
    } elseif ($status_id == 6) {

        //CommonFunction::sendEmailSMS('APP_REJECT', $appInfo, $receiverInfo);
        switch ($processInfo->process_type_id){
            case 3:
                if (in_array($status_id, ['6'])) {

                    $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $processInfo->ref_id)
                        ->first();
                   $flight_data =  FlightRequestPilgrim::where('pilgrim_data_list_id',$pilgrimdata->id)->first();
                    $guide_id = $flight_data->guide_id;

                    $selected_pilgrim_data = FlightRequestPilgrim::where('pilgrim_data_list_id',$pilgrimdata->id)->pluck('pid')->toArray();
                    FlightRequestPilgrim::whereIn('pid', $selected_pilgrim_data)->where('session_id',$hajjSession->id)->update(['status'=>6,'updated_by'=>Auth::user()->id]);

                    $json_check_data = json_encode($selected_pilgrim_data);
                    $postData = [
                        'request_data' => $json_check_data,
                        'guide_id' => $guide_id
                    ];

                    $postdata = http_build_query($postData);
                    $base_url = env('API_URL');
                    //need to done for pilgrim data update;
                    $url = "$base_url/api/send-flight-reject-sms";
                    $response = PostApiData::getData($url,$postdata);
                }
                return true;
                break;

            case 7:
                if (in_array($status_id, ['6'])) {
                    $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $processInfo->ref_id)
                        ->first();
                    $json_data = json_decode($pilgrimdata->json_object);
                    $postData = [
                        'tracking_no' => $json_data->tracking_no,
                    ];
//
                    $postdata = http_build_query($postData);
                    $base_url = env('API_URL');
                    $url = "$base_url/api/cancel-will-not-perform-hajj";
                    $response = PostApiData::getData($url,$postdata);
                    $response_data = json_decode($response,true);
                    if ($response_data['status'] == 200) {
//                        ProcessList::where('id', $process_list_id)
//                            ->update(['completed_date' => date('Y-m-d H:i:s')]);
                        return  true;
                        break;
                    }else{
                        Session::flash('error', $response_data['msg'].'. [PPC-1200]');
                        return false;
                    }
                }
                return true;
                break;

            case 8:
                return true;
            case 9:
                if (in_array($status_id, ['6'])) {

                }
                return true;
                break;
            case 10:
                if (in_array($status_id, ['6'])) {
                    $complain = \App\Modules\Web\Models\Complain::where('pilgrim_data_list_id',$processInfo->ref_id)->first();
                    \App\Modules\Web\Models\Complain::where('id',$complain->id)->update(['status' => 6]);

                }
                return true;
                break;
            case 11:
                \App\Modules\News\Models\News::where('id',$processInfo->ref_id)->update(['status' => 6]);
                return true;
                break;
            case 12:
                return true;
                break;

            default:
                Session::flash('error', 'Unknown process type for Certificate and Others. [PPC-1200]');
                return false;
                break;
        }
    }
    switch ($processInfo->process_type_id) {
        case 1: // Government Pilgrim Listing
            if (in_array($status_id, ['25'])) {
                $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $processInfo->ref_id)
                    ->first();
                $process_type_id = $processInfo->process_type_id;

                $approve_info = getPilgrimApproved($pilgrimdata, $process_type_id);

                if ($approve_info->status) {
                    ProcessList::where('id', $process_list_id)->update(['completed_date' => date('Y-m-d H:i:s')]);
                }
            }

            return true;
            break;

        case 2: // Government Pilgrim Listing
            if (in_array($status_id, ['25'])) {

                $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $processInfo->ref_id)
                    ->first();
                $process_type_id = $processInfo->process_type_id;
                $approve_info = getPilgrimApproved($pilgrimdata, $process_type_id);

                if ($approve_info->status) {
                    ProcessList::where('id', $process_list_id)->update(['completed_date' => date('Y-m-d H:i:s')]);
                }
            }

            return true;
            break;
        case 3:
            if (in_array($status_id, ['25'])) {
                $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $processInfo->ref_id)
                    ->first();
                $selected_pilgrim_data = FlightRequestPilgrim::where('pilgrim_data_list_id',$pilgrimdata->id)
                ->whereNotIn('status', [6])
                ->pluck('pid')->toArray();
                $pilgrim_json_id_array = json_encode($selected_pilgrim_data);
                $trip_id = $requestData['trip_id'];
                $guide_data = FlightRequestPilgrim::where('pilgrim_data_list_id',$pilgrimdata->id)
                                                    ->first();
                $guide_id = $guide_data->guide_id;
                $assign_limit_config = \App\Modules\Settings\Models\Configuration::where('caption','Guide_Pilgrim_Assign_limit')->first();

                $guide_already_assigned_count = FlightRequestPilgrim::where('guide_id',$guide_id)->where('session_id',$hajjSession->id)->whereIn('status',[1,25])->count();
                $guide_already_approved_count = FlightRequestPilgrim::where('guide_id',$guide_id)->where('session_id',$hajjSession->id)->where('status',25)->count();
                if($assign_limit_config->value < $guide_already_assigned_count){
//                    return redirect()->back()->with('error','আবেদনটি অনুমোদন করা সম্ভব হচ্ছে না। একজন হজ গাইডের অধিনে সর্বোচ্চ '.$assign_limit_config->value.' হজ হজযাত্রী এড করতে পরবেন। বর্তমানে '.$guide_already_approved_count.' জনের অনুমোদন করা হয়েছে ।');
                    Session::put('error','আবেদনটি অনুমোদন করা সম্ভব হচ্ছে না। একজন হজ গাইডের অধিনে সর্বোচ্চ '.$assign_limit_config->value.' হজ হজযাত্রী এড করতে পরবেন। বর্তমানে '.$guide_already_approved_count.' জনের অনুমোদন করা হয়েছে ।');
                    return 0;
                }

//                dd($requestData['flight_id']);

                // Set the POST data
                $postData = [
                    'request_data' => $pilgrim_json_id_array,
                    'trip_id' => $trip_id,
                    'flight_id' => $requestData['flight_id'],
                    'guide_id'=>$guide_id
                ];

                $postdata = http_build_query($postData);
                $base_url = env('API_URL');
                $url = "$base_url/api/add-trip-to-pilgrim";
                $response = PostApiData::getData($url,$postdata);
                $response_data = json_decode($response,true);

                if($response_data['status']==200){
                    ProcessList::where('id', $process_list_id)->update(['completed_date' => date('Y-m-d H:i:s')]);
                    FlightRequestPilgrim::whereIn('pid', $selected_pilgrim_data)
                    ->where('session_id',$hajjSession->id)
                    ->whereNotIn('status', [6])
                    ->update(['status'=>25,'updated_by'=>Auth::user()->id]);
                    if($pilgrimdata->listing_id != $requestData['flight_id']){
                        $getUpdatedOtherData = getUpdateOtherData($selected_pilgrim_data,$requestData['flight_id'],$pilgrimdata->id,$hajjSession->id);
                        if($getUpdatedOtherData){
                            return true;
                        }else{
                            return false;
                        }
                    }
                    return true;


                }else{
                    Session::flash('error', $response_data['msg'].'. [PPC-1200]');
                    return false;
                }
                break;

            }
            return true;
        case 4: // Material Receive
            if (in_array($status_id, ['25'])) {
                ProcessList::where('id', $process_list_id)
                    ->update(['completed_date' => date('Y-m-d H:i:s')]);
                MedicalDetails::storeData($process_list_id, 'receive');
            }
            return true;
            break;
        case 5: // Material Issue
            if (in_array($status_id, ['25'])) {
                ProcessList::where('id', $process_list_id)
                    ->update(['completed_date' => date('Y-m-d H:i:s')]);
                MedicalDetails::storeData($process_list_id, 'issue');
            }
            return true;
            break;
        case 6:
            if (in_array($status_id, ['25'])) {
                $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $processInfo->ref_id)
                    ->first();
                $postData = [
                    'request_data' => $pilgrimdata->json_object,
                ];

                $postdata = http_build_query($postData);
                $base_url = env('API_URL');
                $url = "$base_url/api/update-pilgrim-gender";
                $response = PostApiData::getData($url,$postdata);
                $response_data = json_decode($response,true);
                if ($response_data['status'] == 200) {
                    ProcessList::where('id', $process_list_id)
                        ->update(['completed_date' => date('Y-m-d H:i:s')]);
                    return  true;
                    break;
                }else{
                    Session::flash('error', $response_data['msg'].'. [PPC-1200]');
                    return false;
                }
            }
            return true;
            break;
        case 7:
            if (in_array($status_id, ['25'])) {
                $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $processInfo->ref_id)
                    ->first();
                $json_data = json_decode($pilgrimdata->json_object);
                $postData = [
                    'tracking_no' => $json_data->tracking_no,
                    'reason' => $json_data->reason_for_not_perform_hajj,
                    'remark' => $json_data->remarks,
                ];
//
                $postdata = http_build_query($postData);
                $base_url = env('API_URL');
                $url = "$base_url/api/will-not-perform-hajj";
                $response = PostApiData::getData($url,$postdata);

                $response_data = json_decode($response,true);
                if ($response_data['status'] == 200) {
                    ProcessList::where('id', $process_list_id)
                        ->update(['completed_date' => date('Y-m-d H:i:s')]);
                    return  true;
                    break;
                }else{
                    Session::flash('error', $response_data['msg'].'. [PPC-1200]');
                    return false;
                }
            }
            return true;
            break;
        case 8:
            if (in_array($status_id, ['25'])) {
                $masterData = PilgrimDataList::with('processlist')->where('id', $processInfo->ref_id)
                    ->first();
                $stickerPilgrimsData = StickerPilgrims::where('ref_id', $processInfo->ref_id)->get();
                $json_data = json_decode($masterData->json_object);
                $pilgrimsData = [];
                foreach ($stickerPilgrimsData as $item){
                    $pilgrimsData[] = [
                        'team_type' => $json_data->team_type,
                        'team_sub_type' => $json_data->sub_team_type,
                        'go_number' => $json_data->go_number,
                        'ehajj_tracking_no' => $processInfo->tracking_no,
                        'go_serial_no' => $item->go_serial_no,
                        'identity_type' => $item->identity_type,
                        'identity_no' => $item->identity_no,
                        'dob' => $item->dob,
                        'gender' => $item->gender,
                        'passport_type' => $item->passport_type,
                        'passport_no' => $item->passport_no,
                        'passport_dob' => $item->passport_dob,
                        'mobile_no' => $item->mobile_no,
                        'session_id' => $masterData->session_id,
                    ];
                }
                return stickerPilgrimApiRequest($pilgrimsData);
            }
            return true;
        case 9:
            if (in_array($status_id, ['25'])) {
                $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $processInfo->ref_id)
                    ->first();
                $selected_pilgrim_data = \App\Modules\ProcessPath\Models\GuideRequestPilgrim::where('pilgrim_data_list_id',$pilgrimdata->id)->pluck('pid')->toArray();
                $pilgrim_json_id_array = json_encode($selected_pilgrim_data);
                $guide_id = $pilgrimdata->listing_id;
                // Set the POST data
                $postData = [
                    'request_data' => $pilgrim_json_id_array,
                    'guide_id' => $guide_id,
                ];
                $postdata = http_build_query($postData);
                $base_url = env('API_URL');
                $url = "$base_url/api/add-guide-to-pilgrim";
                $response = PostApiData::getData($url,$postdata);
                $response_data = json_decode($response,true);

                if ($response_data['status'] == 200) {
                    $process_type_id = $processInfo->process_type_id;
                    $approve_info = getPilgrimApproved($pilgrimdata,$process_type_id);
                    if ($approve_info->status) {
                        ProcessList::where('id', $process_list_id)->update(['completed_date' => date('Y-m-d H:i:s')]);
                    }
                    \App\Modules\ProcessPath\Models\GuideRequestPilgrim::whereIn('pid', $selected_pilgrim_data)->delete();
                    return true;
                    break;
                }else{
                    Session::flash('error', $response_data['msg'].'. [PPC-1200]');
                    return false;
                }
            }
            return true;
        case 10:
            if (in_array($status_id, ['25'])) {
                    $process_type_id = $processInfo->process_type_id;
                    ProcessList::where('id', $process_list_id)->update(['completed_date' => date('Y-m-d H:i:s')]);
                $complain = \App\Modules\Web\Models\Complain::where('pilgrim_data_list_id',$processInfo->ref_id)->first();
                \App\Modules\Web\Models\Complain::where('id',$complain->id)->update(['status' => 25]);

            return true;
            break;
            }
            return true;
        case 11:
            if ($status_id == 25) {
                \App\Modules\News\Models\News::where('id',$processInfo->ref_id)->update(['status' => 1, 'post_status' => 'publish']);
            }
            if ($status_id == 6) {
                \App\Modules\News\Models\News::where('id',$processInfo->ref_id)->update(['status' => 6]);
            }
            return true;
            break;

        case 12:
            if ($status_id == 25) {
                $obj = new AjaxRequestController();
                $processInfoRefId = $processInfo->ref_id;
                $status = $obj->updateAgencyInfo($processInfoRefId);
                return $status;
                break;
            }
            return true;
            break;

        default:
            Session::flash('error', 'Unknown process type for Certificate and Others. [PPC-1200]');
            return false;
            break;
    }
}

/**
 * @param $app_id
 * @param $process_type_id
 * @param int $approver_desk_id
 * @param string $certificate_type
 * @param string $certificate_name
 * @param $master_table
 *
 * @return bool
 */
function certificateGenerationRequest($app_id, $process_type_id, $master_table, $certificate_name, $approver_desk_id = 0, $certificate_type = 'generate'): bool
{
    try {
        // Generating service wise new license
        $processInfo = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->where([
                'process_list.ref_id' => $app_id,
                'process_list.process_type_id' => $process_type_id
            ])->first([
                'process_list.company_id',
                'process_type.service_code'
            ]);
        $service_code = $processInfo->service_code;
        $company_id = $processInfo->company_id;
        $licenseInfo = DB::table("$master_table as apps")->where('apps.company_id', $company_id)
            ->first([
                DB::raw("CONCAT('14.32.0000.702.',LPAD('$service_code',2,'0'),'.',LPAD($company_id,3,'0'),'.',DATE_FORMAT(NOW(), '%y'),'.',COUNT(apps.id)+1) AS license_no")
            ]);
        switch ($process_type_id) {

            case 1: // Government Pilgrim Listing

                break;
            default:
                return false;
        }

        $url_store = PdfPrintRequestQueue::firstOrNew([
            'process_type_id' => $process_type_id,
            'app_id' => $app_id
        ]);

        $pdf_info = PdfServiceInfo::where('certificate_name', $certificate_name)->first([
            'pdf_server_url',
            'reg_key',
            'pdf_type',
            'certificate_name',
            'table_name',
            'field_name'
        ]);
        if (empty($pdf_info)) {
            return false;
        }
        $tableName = $pdf_info->table_name;
        $fieldName = $pdf_info->field_name;

        $url_store->process_type_id = $process_type_id;
        $url_store->app_id = $app_id;
        $url_store->pdf_server_url = $pdf_info->pdf_server_url;
        $url_store->reg_key = $pdf_info->reg_key;
        $url_store->pdf_type = $pdf_info->pdf_type;
        $url_store->certificate_name = $pdf_info->certificate_name;
        $url_store->prepared_json = 0;
        $url_store->table_name = $tableName;
        $url_store->field_name = $fieldName;
        $url_store->url_requests = '';
        //        $url_store->status = 0;
        $url_store->job_sending_status = 0;
        $url_store->no_of_try_job_sending = 0;
        $url_store->job_receiving_status = 0;
        $url_store->no_of_try_job_receving = 0;

        if ($certificate_type == 'generate') {
            $url_store->signatory = Auth::user()->id;

            // Store approve information
            $signature_store_status = storeSignatureQRCode($process_type_id, $app_id, $approver_desk_id, 0, 'final');
            if ($signature_store_status === false) {
                return false;
            }
        }
        $url_store->updated_at = date('Y-m-d H:i:s');
        $url_store->save();

        return true;
    } catch (Exception $e) {
        dd($e->getLine(), $e->getMessage());

        return false;
    }
}

/**
 * @param $process_type_id
 * @param $app_id
 * @param int $user_id
 * @param $approver_desk_id
 * @param string $signature_type
 *
 * @return bool
 */
function storeSignatureQRCode($process_type_id, $app_id, $approver_desk_id, $user_id = 0, $signature_type = 'final'): bool
{
    $pdf_sign = new \App\Modules\Settings\Models\PdfSignatureQrcode();
    $pdf_sign->signature_type = $signature_type;
    $pdf_sign->app_id = $app_id;
    $pdf_sign->process_type_id = $process_type_id;
    if ($user_id == 0) {

        if (empty(Auth::user()->signature_encode)) {
            return false;
        }

        $pdf_sign->signer_user_id = Auth::user()->id;
        $pdf_sign->signer_desk = CommonFunction::getDeskName($approver_desk_id);
        $pdf_sign->signer_name = CommonFunction::getUserFullName();
        $pdf_sign->signer_designation = Auth::user()->designation;
        $pdf_sign->signer_mobile = Auth::user()->user_mobile;
        $pdf_sign->signer_email = Auth::user()->user_email;
        $pdf_sign->signature_encode = Auth::user()->signature_encode;
    } else {
        $user_info = Users::where('id', $user_id)->first([
            DB::raw("CONCAT(user_first_name,' ',user_middle_name, ' ',user_last_name) as user_full_name"),
            'designation',
            'user_phone',
            'user_mobile',
            'user_email',
            'signature_encode',
        ]);

        if (empty($user_info->signature_encode)) {
            return false;
        }

        $pdf_sign->signer_user_id = $user_id;
        $pdf_sign->signer_desk = CommonFunction::getDeskName($approver_desk_id);
        $pdf_sign->signer_name = $user_info->user_full_name;
        $pdf_sign->signer_designation = $user_info->designation;
        $pdf_sign->signer_mobile = $user_info->user_mobile;
        $pdf_sign->signer_email = $user_info->user_email;
        $pdf_sign->signature_encode = $user_info->signature_encode;
    }
    $pdf_sign->save();

    return true;
}


function cancellationRequest($process_type_id)
{

    if ($process_type_id == 3) {
        $industryInfo = IndRRCommonPool::where('company_id', Auth::user()->working_company_id)
            ->where('ind_can_tracking_no', null)
            ->get(['id', 'tracking_no', 'project_nm']);

        return $industryInfo;
    }
}

function getUpdateOtherData($pilgrim_array,$flight_id,$pilgrim_data_list_id,$hajj_session_id){
    $postData = [
        'flight_id' => $flight_id
    ];
    $postdata = http_build_query($postData);
    $base_url = env('API_URL');
    $url = "$base_url/api/flight-details";
    $response = PostApiData::getData($url,$postdata);
    $responseData= '';
    if ($response) {
        $responseData = json_decode($response);
    }else{
        return 0;
    }
    $flight_data = $responseData->data[0];
    $update_data = [
        'flight_id' => $flight_id,
        'flight_code'=>$flight_data->flight_code,
        'flight_date'=>$flight_data->departure_time,
    ];
//    dd($update_data,$hajj_session_id,$pilgrim_data_list_id,$pilgrim_array);

    foreach ($pilgrim_array as $pid){
      $flight_request_pilgrim =   FlightRequestPilgrim::where('pid',$pid)->where('session_id',$hajj_session_id)->where('pilgrim_data_list_id',$pilgrim_data_list_id)->first();
      if(isset($flight_request_pilgrim)){
            $flight_request_pilgrim->flight_id = $flight_id;
            $flight_request_pilgrim->flight_code = $flight_data->flight_code;
            $flight_request_pilgrim->flight_date = $flight_data->departure_time;
            $flight_request_pilgrim->save();
        }
    }
    PilgrimDataList::where('id',$pilgrim_data_list_id)->update(['listing_id'=>$flight_id]);
    return true;
}


function getPilgrimApproved($process_data, $process_type_id)
{
    $tokenData = Token::getToken();
    $token = json_decode($tokenData)->token;

    $ch = curl_init();

    // Set the API endpoint URL
    $base_url = env('API_URL');
    $url = "$base_url/api/operational-api";

    $pilgriminoss = json_decode($process_data->json_object);
    $data = collect($pilgriminoss)->pluck('pilgrim_id')->toArray();


    // Set the POST data
    $postData = [
        'request_data' => json_encode($data),
        'listing_id' => $process_data->listing_id,
        'status' => 'Approved',
        'process_type_id' => $process_type_id
    ];

    $postdata = http_build_query($postData);

    $headers = array(
        'APIAuthorization: bearer ' . $token,
        'Content-Type: application/x-www-form-urlencoded',
    );

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);

    $response = curl_exec($ch);

    if (curl_error($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    }

    curl_close($ch);

    if ($response) {
        $responseData = json_decode($response);
    }

    return $responseData;
}

function stickerPilgrimApiRequest($pilgrimsData){
    $tokenUrl = env('API_URL')."/api/getToken";
    $credential = [
        'clientid' => env('CLIENT_ID'),
        'username' => env('CLIENT_USER_NAME'),
        'password' => env('CLIENT_PASSWORD')
    ];

    $token = CommonFunction::getApiToken($tokenUrl, $credential);

    if (!$token) {
        return false;
    }
    $apiUrl = env('API_URL')."/api/store-sticker-pilgrim-data";
    $headers = [
        'APIAuthorization: bearer ' . $token,
        'Content-Type: application/json',
    ];

    $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode(['json_object' => $pilgrimsData ]) , $headers, true);


    if ($apiResponse['http_code'] !== 200) {
        return false;
    }

    $apiResponseDataArr = json_decode($apiResponse['data']);
    if ($apiResponseDataArr->status !== 200) {
        return false;
    }
    return true;
}
