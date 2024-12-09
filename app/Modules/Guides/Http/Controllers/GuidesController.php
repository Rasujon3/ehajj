<?php

namespace App\Modules\Guides\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\Guides\Models\GuidesInfo;
use App\Modules\Guides\Models\GuidesVoucher;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\Settings\Models\Area;
use App\Services\Minio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;

class GuidesController extends Controller
{

    public function __construct()
    {

    }
    public function index() {
        if(Auth::user()->user_type === '18x415' && !in_array(Auth::user()->working_user_type, ['Pilgrim', 'Guide'])) {
            Session::flash('error', 'আপনার কোন টাইপের ইউজার তা সিলেক্ট করুন। আপনি যদি হজযাত্রী হন তাহলে  Working user type থেকে Pilgrim সিলেক্ট করুন। আর যদি আপনি হজ গাইড হন তাহলে Guide সিলেক্ট করুন।');
            return redirect(url('users/profileinfo'));
        }
        $accessMode = ACL::getAccsessRight('guides');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        return view('Guides::index');
    }
    public function getGuideApplicationList(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $prp_user_id = Auth::user()->prp_user_id;
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/guide-application-list";
            $data = [
                'prp_user_id' => $prp_user_id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $guideApplicationList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $guideApplicationList]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-001]');
            $msg = 'Something went wrong !!! [GC-001]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function storeHajGuideData(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }

            // Validation rules
            $rules = [
                'full_name_bangla' => 'required',
                'birth_date' => 'required',
                'mother_name' => 'required',
                'father_name' => 'required',
                'full_name_english' => 'required',
                'national_id' => 'required',
                'mobile' => 'required',
                'per_district_id' => 'required',
                'per_thana_id' => 'required',
                'post_code' => 'required',
                'district_id' => 'required',
                'thana_id' => 'required',
                'gender' => 'required',
                'profile_pic' => 'required',
                'present_division_id' => 'required',
                'present_division_name' => 'required',
                'permanent_division_id' => 'required',
                'permanent_division_name' => 'required'
            ];
            // Educational validations
            $educationalFields = [
                'ssc' => ['institute_name', 'passing_year', 'board_name', 'grade', 'certificate_link'],
                'hsc' => ['institute_name', 'passing_year', 'board_name', 'grade', 'certificate_link'],
                'honours' => ['institute_name', 'passing_year', 'board_name', 'grade', 'certificate_link'],
                'masters' => ['institute_name', 'passing_year', 'board_name', 'grade', 'certificate_link']
            ];

            foreach ($educationalFields as $level => $fields) {
                $fileCheck = $request->file("{$level}_certificate_link");
                foreach ($fields as $field) {
                    if ($request->get("{$level}_{$field}") !== null || $fileCheck !== null) {
                        $rules["{$level}_{$field}"] = 'required';
                    }
                }
            }

            $this->validate($request, $rules);

            // Get Token for EHajj API
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/store-haj-guide";
            $prp_user_id = Auth::user()->prp_user_id;
            $user_type = Auth::user()->user_type;
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            if($request->has('profile_pic')) {
                /* $profilePic = $request->file('profile_pic');
                $mimeType = explode("/", $profilePic->getMimeType());
                if($mimeType[0] != 'image') {
                    return response()->json(['responseCode' => -1, 'status' => 422, 'msg' => "Profile picture should be image"]);
                }
                $fileDataLink = file_get_contents($profilePic->getRealPath());
                $profilePicBase64 = 'data:' . $profilePic->getMimeType() . ';base64,' . base64_encode($fileDataLink); */
                $profilePicBase64 = $request->get('profile_pic');
                $profilePicData = CommonFunction::putMinioUploadedFileURL($profilePicBase64, env('MINIO_BUCKET_HMIS'));
            }

            if($request->hasFile('ssc_certificate_link')) {
                $sscCertLink = $request->file('ssc_certificate_link');
                $sscMimeType = $sscCertLink->getMimeType();
                if (!in_array($sscMimeType, ['image/jpeg', 'image/png', 'application/pdf'])) {
                    return response()->json(['responseCode' => -1, 'status' => 422, 'msg' => "SSC Certificate should be image or pdf"]);
                }
                $sscFileDataLink = file_get_contents($sscCertLink->getRealPath());
                $sscFileBase64 = 'data:' . $sscCertLink->getMimeType() . ';base64,' . base64_encode($sscFileDataLink);
                $sscCertificateData = CommonFunction::putMinioUploadedFileURL($sscFileBase64, env('MINIO_BUCKET_HMIS'));
            }
            if($request->hasFile('hsc_certificate_link')) {
                $hscCertLink = $request->file('hsc_certificate_link');
                $hscMimeType = $hscCertLink->getMimeType();
                if (!in_array($hscMimeType, ['image/jpeg', 'image/png', 'application/pdf'])) {
                    return response()->json(['responseCode' => -1, 'status' => 422, 'msg' => "HSC Certificate should be image or pdf"]);
                }
                $hscFileDataLink = file_get_contents($hscCertLink->getRealPath());
                $hscFileBase64 = 'data:' . $hscCertLink->getMimeType() . ';base64,' . base64_encode($hscFileDataLink);
                $hscCertificateData = CommonFunction::putMinioUploadedFileURL($hscFileBase64, env('MINIO_BUCKET_HMIS'));
            }
            if($request->hasFile('honours_certificate_link')) {
                $honoursCertLink = $request->file('honours_certificate_link');
                $honoursMimeType = $honoursCertLink->getMimeType();
                if (!in_array($honoursMimeType, ['image/jpeg', 'image/png', 'application/pdf'])) {
                    return response()->json(['responseCode' => -1, 'status' => 422, 'msg' => "Honours Certificate should be image or pdf"]);
                }
                $honoursFileDataLink = file_get_contents($honoursCertLink->getRealPath());
                $honoursFileBase64 = 'data:' . $honoursCertLink->getMimeType() . ';base64,' . base64_encode($honoursFileDataLink);
                $honoursCertificateData = CommonFunction::putMinioUploadedFileURL($honoursFileBase64, env('MINIO_BUCKET_HMIS'));
            }
            if($request->hasFile('masters_certificate_link')) {
                $mastersCertLink = $request->file('masters_certificate_link');
                $mastersMimeType = $mastersCertLink->getMimeType();
                if (!in_array($mastersMimeType, ['image/jpeg', 'image/png', 'application/pdf'])) {
                    return response()->json(['responseCode' => -1, 'status' => 422, 'msg' => "Masters Certificate should be image or pdf"]);
                }
                $mastersFileDataLink = file_get_contents($mastersCertLink->getRealPath());
                $mastersFileBase64 = 'data:' . $mastersCertLink->getMimeType() . ';base64,' . base64_encode($mastersFileDataLink);
                $mastersCertificateData = CommonFunction::putMinioUploadedFileURL($mastersFileBase64, env('MINIO_BUCKET_HMIS'));
            }

            $guideData = [
                'full_name_bangla' => $request->get('full_name_bangla'),
                'birth_date' => $request->get('birth_date'),
                'mother_name' => $request->get('mother_name'),
                'father_name' => $request->get('father_name'),
                'full_name_english' => $request->get('full_name_english'),
                'national_id' => $request->get('national_id'),
                'mobile' => $request->get('mobile'),
                'per_district' => $request->get('per_district'),
                'per_district_id' => $request->get('per_district_id'),
                'per_thana_id' => $request->get('per_thana_id'),
                'per_post_code' => $request->get('per_post_code'),
                'per_police_station' => $request->get('per_police_station'),
                'post_code' => $request->get('post_code'),
                'thana_id' => $request->get('thana_id'),
                'district' => $request->get('district'),
                'district_id' => $request->get('district_id'),
                'police_station' => $request->get('police_station'),
                // 'address' => $request->get('address'),
                'occupation' => $request->get('occupation'),
                'designation' => $request->get('designation'),
                'office_name' => $request->get('office_name'),
                'office_address' => $request->get('office_address'),

                'ssc_institute_name' => $request->get('ssc_institute_name'),
                'ssc_board_name' => $request->get('ssc_board_name'),
                'ssc_grade' => $request->get('ssc_grade'),
                'ssc_passing_year' => $request->get('ssc_passing_year'),
                'ssc_certificate_link' => $sscCertificateData ?? null,

                'hsc_institute_name' => $request->get('hsc_institute_name'),
                'hsc_board_name' => $request->get('hsc_board_name'),
                'hsc_grade' => $request->get('hsc_grade'),
                'hsc_passing_year' => $request->get('hsc_passing_year'),
                'hsc_certificate_link' => $hscCertificateData ?? null,

                'honours_institute_name' => $request->get('honours_institute_name'),
                'honours_board_name' => $request->get('honours_board_name'),
                'honours_grade' => $request->get('honours_grade'),
                'honours_passing_year' => $request->get('honours_passing_year'),
                'honours_certificate_link' => $honoursCertificateData ?? null,

                'masters_institute_name' => $request->get('masters_institute_name'),
                'masters_board_name' => $request->get('masters_board_name'),
                'masters_grade' => $request->get('masters_grade'),
                'masters_passing_year' => $request->get('masters_passing_year'),
                'masters_certificate_link' => $mastersCertificateData ?? null,
                'profile_pic' => $profilePicData ?? null,
                'present_address' => $request->get('present_address'),
                'permanent_address' => $request->get('permanent_address'),

                'present_division_id' => $request->get('present_division_id'),
                'present_division_name' => $request->get('present_division_name'),
                'permanent_division_id' => $request->get('permanent_division_id'),
                'permanent_division_name' => $request->get('permanent_division_name'),
                'experience' => $request->get('experience'),
                'additional_experience' => $request->get('additional_experience'),
                'prp_user_id' => $prp_user_id,
                'is_employed' => $request->get('isJobHolder'),
                'is_experience' => $request->get('previous_tracking_no') == 'undefined' ? 'No' : 'Yes',
                'birth_place_id' => $request->get('birth_place_id'),
                'birth_place' => $request->get('birth_place'),
                'previous_tracking_no' => $request->get('previous_tracking_no') != 'undefined' ? $request->get('previous_tracking_no') : '',

                'pass_name' => $request->get('pass_name'),
                'pass_dob' => $request->get('pass_dob'),
                'passport_no' => $request->get('passport_no'),
                'pass_issue_date' => $request->get('pass_issue_date'),
                'pass_exp_date' => $request->get('pass_exp_date'),
                'pass_issue_place' => $request->get('pass_issue_place'),
                'pass_type' => $request->get('pass_type'),
                'pass_post_code' => $request->get('pass_post_code'),
                'pass_village' => $request->get('pass_village'),
                'pass_thana' => $request->get('pass_thana'),
                'pass_district' => $request->get('pass_district'),
                'passport_master_id' => $request->get('passport_master_id'),
                'passport_last_status' => $request->get('passport_last_status'),
                'pass_issue_place_id' => $request->get('pass_issue_place_id'),
                'pass_per_thana' => $request->get('pass_per_thana'),
                'pass_per_village' => $request->get('pass_per_village'),
                'pass_per_district' => $request->get('pass_per_district'),
                'pass_per_post_code' => $request->get('pass_per_post_code'),
                //'user_type' => $request->get('user_type'),
                'user_type' => $user_type,
                'identity' => $request->get('identity'),
                'village_ward' => $request->get('village_ward'),
                'per_village_ward' => $request->get('per_village_ward'),
                'mother_name_english' => $request->get('mother_name_english'),
                'father_name_english' => $request->get('father_name_english'),
                'spouse_name' => $request->get('spouse_name'),
                'gender' => $request->get('gender'),
                'is_govt' => 'Government',
                'pp_global_type' => $request->get('pp_global_type'),
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($guideData), $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);

            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'status' => false, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $guideApplicationList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];

            return response()->json(['status' => 200, 'responseCode' => 1, 'message' => 'Data inserted successfully', 'data' => $guideApplicationList]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 422, 'msg' => $e->validator->errors()]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-001]');
            $msg = 'Something went wrong !!! [GC-001]';
            return response()->json(['status' => false, 'responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function updateApplication(Request $request, $id) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }

            // Validation rules
            $rules = [
                'full_name_bangla' => 'required',
                'birth_date' => 'required',
                'mother_name' => 'required',
                'father_name' => 'required',
                'full_name_english' => 'required',
                'national_id' => 'required',
                'mobile' => 'required',
                'per_district' => 'required',
                'per_district_id' => 'required',
                'per_thana_id' => 'required',
                'per_post_code' => 'required',
//                'per_police_station' => 'required',
                'post_code' => 'required',
                'thana_id' => 'required',
                'district' => 'required',
                'district_id' => 'required',
//                'police_station' => 'required',
                'present_division_id' => 'required',
//                'present_division_name' => 'required',
                'permanent_division_id' => 'required',
//                'permanent_division_name' => 'required',
                'profile_pic' => 'required',
                'present_address' => 'required',
//                'birth_place' => 'required',
                'birth_place_id' => 'required',
                'permanent_address' => 'required',
                'gender' => 'required'
            ];

            // job holder validation
            if ($request->get('isJobHolder') === "Yes") {
                $rules['occupation'] = 'required';
                $rules['designation'] = 'required';
                $rules['office_name'] = 'required';
                $rules['office_address'] = 'required';
            }

            // Educational validations
            $educationalFields = [
                'ssc' => ['institute_name', 'passing_year', 'board_name', 'grade', 'certificate_link'],
                'hsc' => ['institute_name', 'passing_year', 'board_name', 'grade', 'certificate_link'],
                'honours' => ['institute_name', 'passing_year', 'board_name', 'grade', 'certificate_link'],
                'masters' => ['institute_name', 'passing_year', 'board_name', 'grade', 'certificate_link']
            ];

            foreach ($educationalFields as $level => $fields) {
                $fileCheck = $request->file("{$level}_certificate_link");
                foreach ($fields as $field) {
                    if ($request->get("{$level}_{$field}") !== null || $fileCheck !== null) {
                        $rules["{$level}_{$field}"] = 'required';
                    }
                }
            }

            $this->validate($request, $rules);

            // nid validation start
            if (strlen($request->get('national_id')) !== 10 && strlen($request->get('national_id')) !== 13
                && strlen($request->get('national_id')) !== 17) {
                $msg = 'National ID No. must be either 10, 13, or 17 characters long.';
                return response()->json(['status' => false, 'msg' => $msg]);
            }
            // nid validation end
            // mobile validation start
            $mobile_no_validate = CommonFunction::validateMobileNumber($request->get('mobile'));
            if ($mobile_no_validate != 'ok') {
                $msg = 'Invalid mobile number format.';
                return response()->json(['status' => false, 'msg' => $msg]);
            }
            // mobile validation end
            if ($request->get('per_district_id') == "0") {
                $msg = 'Permanent district cannot be empty.';
                return response()->json(['status' => false, 'msg' => $msg]);
            }
            if ($request->get('district_id') == "0") {
                $msg = 'Present district cannot be empty.';
                return response()->json(['status' => false, 'msg' => $msg]);
            }
            if ($request->get('per_thana_id') == "0") {
                $msg = 'Permanent thana cannot be empty.';
                return response()->json(['status' => false, 'msg' => $msg]);
            }
            if ($request->get('thana_id') == "0") {
                $msg = 'Present thana cannot be empty.';
                return response()->json(['status' => false, 'msg' => $msg]);
            }


            // Get Token for EHajj API
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/update-haj-guide";
            $prp_user_id = Auth::user()->prp_user_id;
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $guideData = [
                'full_name_bangla' => $request->get('full_name_bangla'),
                'birth_date' => $request->get('birth_date'),
                'mother_name' => $request->get('mother_name'),
                'father_name' => $request->get('father_name'),
                'full_name_english' => $request->get('full_name_english'),
                'national_id' => $request->get('national_id'),
                'mobile' => $request->get('mobile'),
                'per_district' => $request->get('per_district'),
                'per_district_id' => $request->get('per_district_id'),
                'per_thana_id' => $request->get('per_thana_id'),
                'per_post_code' => $request->get('per_post_code'),
                'per_police_station' => $request->get('per_police_station'),
                'post_code' => $request->get('post_code'),
                'thana_id' => $request->get('thana_id'),
                'district' => $request->get('district'),
                'district_id' => $request->get('district_id'),
                'police_station' => $request->get('police_station'),
                'occupation' => $request->get('occupation'),
                'designation' => $request->get('designation'),
                'office_name' => $request->get('office_name'),
                'office_address' => $request->get('office_address'),

                'ssc_institute_name' => $request->get('ssc_institute_name'),
                'ssc_board_name' => $request->get('ssc_board_name'),
                'ssc_grade' => $request->get('ssc_grade'),
                'ssc_passing_year' => $request->get('ssc_passing_year'),

                'hsc_institute_name' => $request->get('hsc_institute_name'),
                'hsc_board_name' => $request->get('hsc_board_name'),
                'hsc_grade' => $request->get('hsc_grade'),
                'hsc_passing_year' => $request->get('hsc_passing_year'),

                'honours_institute_name' => $request->get('honours_institute_name'),
                'honours_board_name' => $request->get('honours_board_name'),
                'honours_grade' => $request->get('honours_grade'),
                'honours_passing_year' => $request->get('honours_passing_year'),

                'masters_institute_name' => $request->get('masters_institute_name'),
                'masters_board_name' => $request->get('masters_board_name'),
                'masters_grade' => $request->get('masters_grade'),
                'masters_passing_year' => $request->get('masters_passing_year'),

                'present_address' => $request->get('present_address'),
                'permanent_address' => $request->get('permanent_address'),
                'present_division_id' => $request->get('present_division_id'),
                'present_division_name' => $request->get('present_division_name'),
                'permanent_division_id' => $request->get('permanent_division_id'),
                'permanent_division_name' => $request->get('permanent_division_name'),
                'experience' => !empty($request->get('experience')) ? $request->get('experience') : '',
                'additional_experience' => !empty($request->get('additional_experience')) ? $request->get('additional_experience'): '',
                'prp_user_id' => $prp_user_id,
                'created_by' => $prp_user_id,
                'is_employed' => $request->get('isJobHolder'),
                //'is_experience' => $request->get('previous_tracking_no') == 'undefined' ? 'No' : 'Yes',
                'birth_place_id' => $request->get('birth_place_id'),
                'birth_place' => $request->get('birth_place') ?? "",
                'previous_tracking_no' => $request->get('previous_tracking_no') != 'undefined' ? $request->get('previous_tracking_no') : '',

                'pass_name' => $request->get('pass_name'),
                'pass_dob' => $request->get('pass_dob'),
                'passport_no' => $request->get('passport_no'),
                'pass_issue_date' => $request->get('pass_issue_date'),
                'pass_exp_date' => $request->get('pass_exp_date'),
                'pass_issue_place' => $request->get('pass_issue_place'),
                'pass_type' => $request->get('pass_type'),
                'pass_post_code' => $request->get('pass_post_code'),
                'pass_village' => $request->get('pass_village'),
                'pass_thana' => $request->get('pass_thana'),
                'pass_district' => $request->get('pass_district'),
                'passport_master_id' => $request->get('passport_master_id'),
                'passport_last_status' => $request->get('passport_last_status'),
                'pass_issue_place_id' => $request->get('pass_issue_place_id'),
                'pass_per_thana' => $request->get('pass_per_thana'),
                'pass_per_village' => $request->get('pass_per_village'),
                'pass_per_district' => $request->get('pass_per_district'),
                'pass_per_post_code' => $request->get('pass_per_post_code'),
                'mother_name_english' => $request->get('mother_name_english'),
                'father_name_english' => $request->get('father_name_english'),
                'spouse_name' => $request->get('spouse_name'),
                'gender' => $request->get('gender'),
                'pp_global_type' => $request->get('pp_global_type'),
                'guide__id' => $id
            ];

            if($request->has('profile_pic')) {
                /* $profilePic = $request->file('profile_pic');
                $mimeType = explode("/", $profilePic->getMimeType());
                if($mimeType[0] != 'image') {
                    return response()->json(['responseCode' => -1, 'status' => 422, 'msg' => "Profile picture should be image"]);
                }
                $fileDataLink = file_get_contents($profilePic->getRealPath());
                $profilePicBase64 = 'data:' . $profilePic->getMimeType() . ';base64,' . base64_encode($fileDataLink); */
                $profilePicBase64 = $request->get('profile_pic');
                $profilePicData = CommonFunction::putMinioUploadedFileURL($profilePicBase64, env('MINIO_BUCKET_HMIS'));
                $guideData['profile_pic'] = $profilePicData;
            }

            if($request->hasFile('ssc_certificate_link')) {
                $sscCertLink = $request->file('ssc_certificate_link');
                $sscMimeType = $sscCertLink->getMimeType();
                if (!in_array($sscMimeType, ['image/jpeg', 'image/png', 'application/pdf'])) {
                    return response()->json(['responseCode' => -1, 'status' => 422, 'msg' => "SSC Certificate should be image or pdf"]);
                }
                $sscFileDataLink = file_get_contents($sscCertLink->getRealPath());
                $sscFileBase64 = 'data:' . $sscCertLink->getMimeType() . ';base64,' . base64_encode($sscFileDataLink);
                $sscCertificateData = CommonFunction::putMinioUploadedFileURL($sscFileBase64, env('MINIO_BUCKET_HMIS'));
                $guideData['ssc_certificate_link'] = $sscCertificateData;
            }

            if($request->hasFile('hsc_certificate_link')) {
                $hscCertLink = $request->file('hsc_certificate_link');
                $hscMimeType = $hscCertLink->getMimeType();
                if (!in_array($hscMimeType, ['image/jpeg', 'image/png', 'application/pdf'])) {
                    return response()->json(['responseCode' => -1, 'status' => 422, 'msg' => "HSC Certificate should be image or pdf"]);
                }
                $hscFileDataLink = file_get_contents($hscCertLink->getRealPath());
                $hscFileBase64 = 'data:' . $hscCertLink->getMimeType() . ';base64,' . base64_encode($hscFileDataLink);
                $hscCertificateData = CommonFunction::putMinioUploadedFileURL($hscFileBase64, env('MINIO_BUCKET_HMIS'));
                $guideData['hsc_certificate_link'] = $hscCertificateData;
            }

            if($request->hasFile('honours_certificate_link')) {
                $honoursCertLink = $request->file('honours_certificate_link');
                $honoursMimeType = $honoursCertLink->getMimeType();
                if (!in_array($honoursMimeType, ['image/jpeg', 'image/png', 'application/pdf'])) {
                    return response()->json(['responseCode' => -1, 'status' => 422, 'msg' => "Honours Certificate should be image or pdf"]);
                }
                $honoursFileDataLink = file_get_contents($honoursCertLink->getRealPath());
                $honoursFileBase64 = 'data:' . $honoursCertLink->getMimeType() . ';base64,' . base64_encode($honoursFileDataLink);
                $honoursCertificateData = CommonFunction::putMinioUploadedFileURL($honoursFileBase64, env('MINIO_BUCKET_HMIS'));
                $guideData['honours_certificate_link'] = $honoursCertificateData;
            }

            if($request->hasFile('masters_certificate_link')) {
                $mastersCertLink = $request->file('masters_certificate_link');
                $mastersMimeType = $mastersCertLink->getMimeType();
                if (!in_array($mastersMimeType, ['image/jpeg', 'image/png', 'application/pdf'])) {
                    return response()->json(['responseCode' => -1, 'status' => 422, 'msg' => "Masters Certificate should be image or pdf"]);
                }
                $mastersFileDataLink = file_get_contents($mastersCertLink->getRealPath());
                $mastersFileBase64 = 'data:' . $mastersCertLink->getMimeType() . ';base64,' . base64_encode($mastersFileDataLink);
                $mastersCertificateData = CommonFunction::putMinioUploadedFileURL($mastersFileBase64, env('MINIO_BUCKET_HMIS'));
                $guideData['masters_certificate_link'] = $mastersCertificateData;
            }

            $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($guideData), $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr || $apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'status' => $apiResponseDataArr->status, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $guideApplicationList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];

            return response()->json(['status' => 200, 'responseCode' => 1, 'message' => 'Data Updated successfully', 'data' => $guideApplicationList]);
        } catch (ValidationException $e) {
            return response()->json(['responseCode' => -1, 'status' => 422, 'errors' => $e->errors()]);
        }catch (\Exception $e) {
            #dd($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GCE-001]');
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GCE-001]');
            $msg = 'Something went wrong !!! [GCE-001]';
            return response()->json(['responseCode' => -1, 'status' => false, 'msg' => $msg]);
        }
    }
    public function getGuideData(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $trackingNo = $request->get('trackingNo');
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-guide-data";
            $data = [
                'tracking_no' => $trackingNo
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $guideData = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1, 'data' => $guideData]);

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GCE-002]');
            $msg = 'Something went wrong !!! [GCE-002]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getDivisionListData(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $divisionData = Area::where('area_type', 1)->get();
            return response()->json(['responseCode' => 1, 'data' => $divisionData]);

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-003]');
            $msg = 'Something went wrong !!! [GC-003]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getAllDistrictListData(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $allDistrictData = Area::where('area_type', 2)->get();
            return response()->json(['responseCode' => 1, 'data' => $allDistrictData]);

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-004]');
            $msg = 'Something went wrong !!! [GC-004]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getPoliceStationListData(Request $request)
    {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $districtId = $request->get('districtId');
            $get_data = Area::where('pare_id', DB::raw($districtId))
                ->where('area_type', 3)
                ->get();

            $data = ['responseCode' => 1, 'data' => $get_data];
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-005]');
            $msg = 'Something went wrong !!! [GC-005]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getOccupationListData(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }

            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-occupation-list-data";
            $data = [];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!'];
                return response()->json($returnData);
            }
            $occupationData = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1, 'data' => $occupationData]);

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-006]');
            $msg = 'Something went wrong !!! [GC-006]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function nidFileUpload(Request $request)
    {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            if ($request->hasFile('nid_img')) {
                $file = $request->file('nid_img');
                if ($file->isValid() && in_array($file->getMimeType(), ['image/jpeg', 'image/png','image/jpg'])) {
                    if ($file->getSize() <= 2048000) { // 2 MB (in bytes)
                        $apiUrl = env('ML_API_URL')."/identity/upload_nid";
                        $postData = [
                            'image' => new \CURLFile($file->getRealPath(), $file->getMimeType(), $file->getClientOriginalName())
                        ];
                        $headers = [
                            'Content-Type: multipart/form-data',
                        ];
                        $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
                        if(!empty($apiResponse) && $apiResponse['http_code'] == 200) {
                            $apiResponseDataArr = isset($apiResponse['data']) ? json_decode($apiResponse['data'],true) : null;
                            if(!empty($apiResponseDataArr)) {
                                $imgResponse = $apiResponseDataArr['response'] ?? '';
                                $isImageValid = preg_match('/^(\d{1,2})\s+([A-Za-z]{3})\s+(\d{4})$/', $imgResponse['dob']);
                                $formattedDate = "";
                                if ($isImageValid) {
                                    $date = Carbon::createFromFormat('d M Y', $imgResponse['dob']);
                                    $formattedDate = $date->format('Y-m-d');
                                }
                                $transformedResponse = [
                                    "full_name_bangla" => $imgResponse['b_name'] ?: "",
                                    "birth_date" => $formattedDate,
                                    "full_name_english" => $imgResponse['e_name'] ?: "",
                                    "father_name" => $imgResponse['f_name'] ?: "",
                                    "mother_name" => $imgResponse['m_name'] ?: "",
                                    "national_id" => $imgResponse['nid'] ?: ""
                                ];
                                $transformedObject = (object) $transformedResponse;
                                $msg = 'Data Found.';
                                return response()->json(['responseCode' => 1, 'msg' => $msg, 'data' => $transformedObject]);
                            }
                        } else {
                            $imgResponse = [
                                "full_name_bangla" => "",
                                "birth_date" => "",
                                "full_name_english" => "",
                                "father_name" => "",
                                "mother_name" => "",
                                "national_id" => ""
                            ];
                            $transformedObject = (object) $imgResponse;
                            $msg = 'Data Found.';
                            return response()->json(['responseCode' => 1, 'msg' => $msg, 'data' => $transformedObject]);
                        }
                    } else {
                        $msg = 'File size exceeds the allowed limit';
                        return response()->json(['responseCode' => -1, 'msg' => $msg]);
                    }
                } else {
                    $msg = 'Invalid file format';
                    return response()->json(['responseCode' => -1, 'msg' => $msg]);
                }
            } else {
                $msg = 'No file uploaded';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
        }  catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-008]');
            $msg = 'Something went wrong !!! [GC-008]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }

    }
    public function convertImageToBase64($image)
    {
        $file = $image;
        $filePath = $file->getRealPath();
        $fileData = file_get_contents($filePath);
        $base64 = base64_encode($fileData);
        $fileMimeType = $file->getMimeType();
        $base64WithMimeType = 'data:' . $fileMimeType . ';base64,' . $base64;
        return $base64WithMimeType;
    }
    public function addVoucherModalView(Request $request)
    {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-voucher-modal-data";
            $data = [];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers,true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $hajj_packages = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1, 'data' => $hajj_packages]);

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-009]');
            $msg = 'Something went wrong !!! [GC-009]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getVoucherRowDetailsData(Request $request)
    {
        try {
            if (!ACL::getAccsessRight('guides', 'V'))
                die('no access right!');
            $tracking_no_list = trim($request->get('tracking_no_list'));
            $package_id = trim($request->get('package_id'));
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-vouchers-list-by-tracking-no-data";
            $data = [
                'tracking_no_list' => $tracking_no_list,
                'package_id' => $package_id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $vouchers_list = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1, 'data' => $vouchers_list]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-010]');
            $msg = 'Something went wrong !!! [GC-010]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getAlreadyVoucherListData(Request $request)
    {
        try {
            if (!ACL::getAccsessRight('guides', 'V'))
                die('no access right!');
            $tracking_no_list = trim($request->get('tracking_no_list'));
            $package_id = trim($request->get('package_id'));
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-already-added-voucher-list-data";
            $data = [
                'tracking_no_list' => $tracking_no_list,
                'package_id' => $package_id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!'];
                return response()->json($returnData);
            }
            $voucher_info = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1, 'data' => $voucher_info]);
        } catch (\Exception $e) {
            #dd($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GCE-011]');
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GCE-011]');
            $msg = 'Something went wrong !!! [GCE-011]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function addVoucherToGuide(Request $request){
        try {
            if (!ACL::getAccsessRight('guides', 'V'))
                die('no access right!');
            $vouchers_id = $request->get('vouchers_id');
            if (empty($vouchers_id)) {
                $msg = 'Voucher not found.';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/add-voucher-to-guide";
            $data = [
                'vouchers_id' => $vouchers_id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            } else {
                $session_id = $apiResponseDataArr->data;
                DB::beginTransaction();
                if (count($vouchers_id) > 0) {
                    foreach ($vouchers_id as $key => $voucher_id) {
                        GuidesVoucher::create([
                            'guide_id' => $request->get('guidId'),
                            'reg_voucher_id' => $voucher_id,
                            'session_id' => $session_id
                        ]);
                    }
                }
                DB::commit();
                $returnData = ['responseCode' => 1, 'msg' => 'Vouchers Successfully added.'];
                return response()->json($returnData);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-012]');
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!! [GC-012]'];
            return response()->json($returnData);
        }

    }
    public function submitGuideRequest(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $guide_id = $request->get('guideId');
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/submit-guide-request";
            $data = [
                'guide_id' => $guide_id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $data]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-013]');
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!! [GC-013]'];
            return response()->json($returnData);
        }
    }
    public function cancelGuideRequest(Request $request, $guide_id){
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/cancel-guide-request";
            $data = [
                'guide_id' => $guide_id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $data]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-014]');
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!! [GC-014]'];
            return response()->json($returnData);
        }
    }
    public function getVoucherAddedPilgrims(Request $request) {
        try {
            if (!ACL::getAccsessRight('guides', 'V'))
                die('no access right!');
            $guidId = trim($request->get('guidId'));
            // $hajjSession = HajjSessions::where('state', 'active')->value('id');
            $hajjSessionResponse = $this->getActiveSession();
            $decodedResponse = json_decode($hajjSessionResponse->getContent(), true);
            $decodedResponseCode = $decodedResponse['responseCode'];
            if ($decodedResponseCode === -1) {
                $msg = $decodedResponse['msg'];
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            if ($decodedResponseCode === 1) {
                $hajjSession = $decodedResponse['data'];
            }

            $voucherIds = [];
            $voucherIds = GuidesVoucher::where('guide_id', $guidId)
                ->where('session_id', $hajjSession)
                ->pluck('reg_voucher_id')
                ->toArray();
            if( empty($voucherIds) ) {
                $msg = 'Voucher Info Not found !!! [VP-002]';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-guid-voucher-info";
            $data = [
                'vouchers_ids' => $voucherIds
            ];
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($data), $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }else{
                return response()->json(['responseCode' => 1, 'data' => $apiResponseDataArr->data]);
            }

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-015]');
            $msg = 'Something went wrong !!! [GC-015]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function lockPilgrims(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $guidId = $request->get('guidId');
            $flag = $request->get('flag');
            // $hajjSession = HajjSessions::where('state', 'active')->value('id');
            $hajjSessionResponse = $this->getActiveSession();
            $decodedResponse = json_decode($hajjSessionResponse->getContent(), true);
            $decodedResponseCode = $decodedResponse['responseCode'];
            if ($decodedResponseCode === -1) {
                $msg = $decodedResponse['msg'];
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            if ($decodedResponseCode === 1) {
                $hajjSession = $decodedResponse['data'];
            }

            $voucherIds = GuidesVoucher::where('guide_id', $guidId)
                ->where('session_id', $hajjSession)
                ->get();
            if(count($voucherIds) == 0) {
                $msg = 'Please Add pilgrim in Voucher.';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }

            if($flag == 1){
                GuidesInfo::where('id', $guidId)->update(['is_locked' => 1]);
            } else {
                GuidesInfo::where('id', $guidId)->update(['is_locked' => 0]);
            }

            $guidInfo = GuidesInfo::find($guidId);

            return response()->json(['responseCode' => 1, 'lockStatus' => $guidInfo->is_locked]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-016]');
            $msg = 'Something went wrong !!! [GC-016]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function removePilgrim(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $voucherId = $request->get('id');
            $guideId = $request->get('guidId');
            if(empty($voucherId) || empty($guideId)) {
                $msg = 'Invalid Guide.';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/delete-pilgrim-from-guide-voucher";
            $data = [
                'guideId' => $guideId,
                'voucherId' => $voucherId
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $data]);

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-017]');
            $msg = 'Something went wrong !!! [GC-017]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function pdfGenerate(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $guide_id = $request->get('guidId');

            $pdfurl = env('GUIDE_PDF_API_BASE_URL');
            $trainingCertApiResponse = $this->trainingCertApiRequest($guide_id, 'new-job', $pdfurl);
            if (!$trainingCertApiResponse) {
                $data = ['responseCode' => -1, 'msg' => 'Voucher not generate.'];
            } else {
                $response = $this->trainingCertApiRequest($guide_id, 'job-status', $pdfurl);
                if (!empty($response->response) && $response->response->status == 0) {
                    $data = ['responseCode' => -1, 'msg' => 'Voucher not generate.'];
                } elseif (!empty($response->response) && $response->response->status == 1) {
                    GuidesInfo::where('id', $guide_id)->update(['guide_form_link' => $response->response->download_link]);
                    $data = ['responseCode' => 1, 'msg' => 'Guide form generate on process!!!', 'guide_form_link' => $response->response->download_link];
                } else {
                    $data = ['responseCode' => -1, 'msg' => 'Voucher not generate.'];
                }
            }

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-018]');
            $msg = 'Something went wrong !!! [GC-018]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getApplicationList(Request $request, $id){
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }

            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/guide-application-details";
            $data = [
                'id' => $id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $guideApplicationList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $guideApplicationList]);


        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-019]');
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!! [GC-019]'];
            return response()->json($returnData);
        }
    }

    private function trainingCertApiRequest($app_id, $action = '', $pdfurl = '')
    {
        $pdf_type = env('GUIDE_PDF_TYPE');
        $reg_key = env('GUIDE_PDF_REG_KEY');

        $data = array();
        $data['data'] = array(
            'reg_key' => $reg_key,       // Authentication key
            'pdf_type' => $pdf_type,         // letter type
            'ref_id' => $app_id,          //app_id
            'param' => array(
                'guide_id' => $app_id  // app_id
            )
        );

        $data1 = json_encode($data);

        $url = '';

        if ($action == "job-status") {
            $url = "{$pdfurl}api/job-status?requestData=$data1";
        } else if ($action == "new-job") {
            $url = "{$pdfurl}api/new-job?requestData=$data1";
        } else {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 150);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $response = '';
        } else {
            curl_close($ch);
        }
        $dataResponse = json_decode($response);
        return $dataResponse;
    }
    public function getActiveSession() {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-active-session";
            $data = [];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $data]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-021]');
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!! [GC-021]'];
            return response()->json($returnData);
        }
    }
    public function isGuideApplicationExist() {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $prp_user_id = Auth::user()->prp_user_id;
            if (empty($prp_user_id)) {
                $msg = 'User not found.';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/is-guide-application-exist";
            $data = [
                'prp_user_id' => $prp_user_id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $data]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-022]');
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!! [GC-022]'];
            return response()->json($returnData);
        }
    }
    public function getGuideProfileDetails(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $guide_id = $request->get('guide_id');
            if (empty($guide_id)) {
                return response()->json(['responseCode' => -1, 'msg' => 'Guide ID is required.']);
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-guide-profile-details";
            $data = [
                'guide_id' => $guide_id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }

            $guideProfileDetails = !empty($apiResponseDataArr->data->guide_data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $guideProfileDetails]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GPD-022]');
            $msg = 'Something went wrong !!! [GPD-022]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getOwnPilgrims(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $request_by = Auth::user()->prp_user_id;
            if (empty($request_by)) {
                return response()->json(['responseCode' => -1, 'msg' => 'User not found.']);
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-own-registered-pilgrim-list";
            $data = [
                'request_by' => $request_by
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $ownPilgrimList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $ownPilgrimList]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GPD-023]');
            $msg = 'Something went wrong !!! [GPD-023]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function getRegisteredPilgrimByTrackingNo(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $tracking_no = $request->get('trackingNo');
            if (empty($tracking_no)) {
                return response()->json(['responseCode' => -1, 'msg' => 'Tracking no not found.']);
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/get-registered-pilgrim-by-tracking-no";
            $data = [
                'tracking_no' => $tracking_no
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);

            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $pilgrimList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $pilgrimList]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GPD-024]');
            $msg = 'Something went wrong !!! [GPD-024]';
            return response()->json(['responseCode' => -1, 'msg' => $msg]);
        }
    }
    public function addPilgrimToGuide(Request $request) {
        try {
            if (!ACL::getAccsessRight('guides', 'V'))
                die('no access right!');
            $guideId = $request->get('guideId');
            $pilgrims = json_decode($request->get('pilgrims'), true);
            $prp_user_id = Auth::user()->prp_user_id;
            if (empty($guideId)) {
                $msg = 'Guide not found.';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            if (empty($prp_user_id)) {
                $msg = 'User not found.';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($pilgrims) || count($pilgrims) === 0) {
                $msg = 'Invalid Pilgrim data.';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/add-pilgrim-to-guide";
            $data = [
                'guideId' => $guideId,
                'pilgrims' => $pilgrims,
                'prp_user_id' => $prp_user_id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            } else {
                $returnData = ['responseCode' => 1, 'msg' => 'Pilgrim added successfully.'];
                return response()->json($returnData);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-025]');
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!! [GC-025]'];
            return response()->json($returnData);
        }
    }
    public function reSubmitGuideRequest(Request $request) {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $guide_id = $request->get('guideId');
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/re-submit-guide-request";
            $data = [
                'guide_id' => $guide_id
            ];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $data]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-026]');
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!! [GC-026]'];
            return response()->json($returnData);
        }
    }
    public function checkGuideApplicationLastDate() {
        try {
            $accessMode = ACL::getAccsessRight('guides');
            if (!ACL::isAllowed($accessMode, 'V')) {
                die('You have no access right! Please contact with system admin for more information.');
            }
            $base_url = env('API_URL');
            $token = $this->collectToken($base_url);
            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/check-guide-application-last-date";
            $data = [];
            $postData = json_encode($data);
            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postData, $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200) {
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1,'msg' => $apiResponseDataArr->msg ,'data' => $data]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [GC-027]');
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!! [GC-027]'];
            return response()->json($returnData);
        }
    }
    public function collectToken($base_url)
    {
        $api_base_url = $base_url;
        $tokenUrl = "$api_base_url/api/getToken";
        $tokenData = [
            'clientid' => env('CLIENT_ID'),
            'username' => env('CLIENT_USER_NAME'),
            'password' => env('CLIENT_PASSWORD')
        ];

        $token = CommonFunction::getApiToken($tokenUrl, $tokenData);
        return $token;
    }
}
