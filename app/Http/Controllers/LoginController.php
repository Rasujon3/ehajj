<?php

namespace App\Http\Controllers;

use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\Osspid;
use App\Libraries\UtilFunction;
use App\Models\User;
use App\Modules\API\Http\Controllers\Traits\Notification;
use App\Modules\API\Models\OssAppUser;
use App\Modules\Dashboard\Http\Controllers\DashboardController;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Settings\Models\EmailQueue;
use App\Modules\Settings\Models\MaintenanceModeUser;
use App\Modules\Users\Models\FailedLogin;
use App\Modules\Users\Models\SecurityProfile;
use App\Modules\Users\Models\UserDevice;
use App\Modules\Users\Models\UserLogs;
use App\Modules\Users\Models\UserTypes;
use App\Modules\Users\Models\Users;
use App\Modules\Web\Http\Controllers\WebController;
use Carbon\Carbon;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

class  LoginController extends Controller
{
    use Notification;
    /*
     * Login process check function
     */
    public function reCaptcha()
    {
        return Captcha::img();
    }
    public function check(Request $request, Users $usersModel)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|max:30',
        ];

        if (Session::get('hit') >= 3) {

            $rules['g_recaptcha_response'] = 'required';

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $data = ['responseCode' => 0, 'msg' => 'Please check the captcha.'];
                return response()->json($data);
            }
        } else {
            $this->validate($request, $rules);
        }

        if (!$this->_checkAttack($request)) {
            $msg = Session::get("error");
            Session::flash('error', 'Invalid login information!![HIT3TIMES]');
            $data = ['responseCode' => 0, 'msg' => $msg, 'redirect_to' => ''];
        } else {
            $response = $this->commonLoginCheck($request, $usersModel, 1, '', true);

            if ($response['result']) {
                Session::flash('success', $response['msg']);
                $this->sendPushNotification($request->email);
                $data = ['responseCode' => 1, 'msg' => $response['msg'], 'redirect_to' => $response['redirect_to']];
            } else {
                Session::flash('error', $response['msg']);
                $data = ['responseCode' => 0, 'msg' => $response['msg'], 'redirect_to' => $response['redirect_to'],
                    'hit' => Session::get('hit')];
            }
        }
        return response()->json($data);
    }

    /*
     * check for attack
     */
    private function _checkAttack($request)
    {
        try {
            $ip_address = UtilFunction::getVisitorRealIP();
            $user_email = $request->get('email');
            $count = FailedLogin::where('remote_address', "$ip_address")
                ->where('is_archive', 0)
                ->where('created_at', '>', DB::raw('DATE_ADD(now(),INTERVAL -20 MINUTE)'))
                ->count();
            if ($count > 20) {
                Session::flash('error', 'Invalid Login session. Please try after 10 to 20 minute [LC6091],
                Please contact with system admin.');
                return false;
            } else {
                $count = FailedLogin::where('remote_address', "$ip_address")
                    ->where('is_archive', 0)
                    ->where('created_at', '>', DB::raw('DATE_ADD(now(),INTERVAL -60 MINUTE)'))
                    ->count();
                if ($count > 40) {
                    Session::flash('error', 'Invalid Login session. Please try after 30 to 60 minute [LC6092],
                    Please contact with system admin.');
                    return false;
                } else {
                    $count = FailedLogin::where('user_email', $user_email)
                        ->where('is_archive', 0)
                        ->where('created_at', '>', DB::raw('DATE_ADD(now(),INTERVAL -10 MINUTE)'))
                        ->count();
                }
            }

        } catch (\Exception $e) {
            dd($e->getMessage());
            Session::flash('error', 'Login session exception. Please try after 5 to 10 minute 1003,
            Please contact with system admin.');
            return false;
        }
        return true;
    }

    public static function killUserSession($user_id, $loginType = 0)
    {
        try {
            $sessionID = Users::where('id', $user_id)->value('login_token');
            if (!empty($sessionID)) {
                if ($loginType == 2) { // OTP login
                    $sessionID = $sessionID;
                    Session::getHandler()->destroy($sessionID);
                } else {
                    $sessionID = Encryption::decode($sessionID);
                    Session::getHandler()->destroy($sessionID);
                }

            }
            Users::where('id', $user_id)->update(['login_token' => '']);
        } catch (\Exception $e) {
            Users::where('id', $user_id)->update(['login_token' => '']);
        }
    }

    public function _checkSecurityProfile($request = [], $ip_param = '')
    {
        $security_id = Auth::user()->security_profile_id;
        if (empty($security_id)) {
            $security_id = UserTypes::where('id', Auth::user()->user_type)->value('security_profile_id');
        }


        if ($security_id) {
            $security = SecurityProfile::where(['id' => $security_id])
                ->where('active_status', 'yes')
                ->first([
                    'allowed_remote_ip',
                    'week_off_days',
                    'work_hour_start',
                    'work_hour_end',
                    'alert_message',
                    'active_status',
                ]);
            if (empty($security)) {
                return true;
            } else {
                if ($ip_param) {
                    $ip = $ip_param;
                } else {
                    $ip = UtilFunction::getVisitorRealIP();
                }
                if ($ip == '127.0.0.1' || $ip == '::1') {
                    $ip = '0.0.0.0';
                }
                $net = '0.0.0.0';
                $nets = explode('.', $ip);
                $today = strtoupper(date('D'));
                if (count($nets) == 4) {
                    $net = $nets[0] . '.' . $nets[1] . '.' . $nets[2] . '.0';
                }
                /*
                 * if IP address is equal to '' or '0.0.0.0.' or IP address is in allowed ip
                 */
                if ($security->allowed_remote_ip == ''
                    || $security->allowed_remote_ip == '0.0.0.0'
                    || !(strpos($security->allowed_remote_ip, $net) === false)
                    || !(strpos($security->allowed_remote_ip, $ip) === false)) {

                    /*
                     * It today is not weekly off day
                     */
                    if (strpos(strtoupper($security->week_off_days), $today) === false) {

                        /*
                         * if current time is greater than work_hour_start and less than work_hour_end
                         */
                        date_default_timezone_set('Asia/Dhaka');
                        if (time() >= strtotime($security->work_hour_start) && time() <= strtotime($security->work_hour_end)) {
                            return true;
                        }
                    }
                }
            }
        }
        Session::flash('error', $security->alert_message);
        return false;
    }

    /*
     * protect login for special types users
     */
    public function _protectLogin($type = false)
    {
        if ($type == '10x414') {// For UDC users
            Auth::logout();
            Session::flash('error', 'You are not allowed to login using this type of login method');
            return false;
        } else {
            return true;
        }
    }
    /*
     * Caption set up
     */
    private function _setCaption($usersModel)
    {
        /*
         * for user caption (like: Bank name/agency name/udc name etc)
         */

        $caption_name = '';
        $userAdditionalInfo = $usersModel->getUserSpecialFields(Auth::user());
        if (count($userAdditionalInfo) >= 1 && $userAdditionalInfo[0]['value']) {
            $caption_name .= ' - ';
            if (Auth::user()->user_type == '7x711'
                || Auth::user()->user_type == '7x712'
                || Auth::user()->user_type == '7x713') {
                $caption_name .= UserTypes::where('id', Auth::user()->user_type)->pluck('type_name') . ', ';
            }

            $caption_name .= $userAdditionalInfo[0]['value']; //$userAdditionalInfo[0]['caption'] . ': ' .
            if (strlen($caption_name) > 45) {
                $caption_name = substr($caption_name, 0, 43) . '..';
            }
        } else {
            $caption_name .= ' - ' . Auth::user()->user_email;
        }
        Session::put('caption_name', $caption_name);
    }

    /*
     * User's session set up
     */
    public function _setSession()
    {
        try {
            if (Auth::user()->is_approved == 1
                && Auth::user()->user_status == 'active') {
                Session::put('lang', Auth::user()->user_language);
                App::setLocale(Session::get('lang'));
                Session::put('user_pic', Auth::user()->user_pic);
                Session::put('hit', 0);

                //Set last login time in session
                $last_login_time = UserLogs::leftJoin('users', 'users.id', '=', 'user_logs.user_id')
                    ->where('user_logs.user_id', '=', Auth::user()->id)
                    ->orderBy('user_logs.id', 'desc')
                    ->skip(1)->take(1)
                    ->first(['user_logs.login_dt']);
                $lastLogin = date("d-M-Y h:i:s");
                if ($last_login_time) {
                    $lastLogin = date("d-M-Y h:i:s", strtotime($last_login_time->login_dt));
                }
                Session::put('last_login_time', $lastLogin);

                // for checkAdmin middleware checking
                $security_check_time = Carbon::now();
                Session::put('security_check_time', $security_check_time);
                Session::put('is_first_security_check', 0);

                // for company association selection
                Session::put('is_working_company_selected', 0);

                // for user report module
                Session::put('sess_user_id', Auth::user()->id);
                Session::put('sess_user_type', Auth::user()->user_type);
                Session::put('sess_district', Auth::user()->district);
                Session::put('sess_thana', Auth::user()->thana);

                // To set user desk
                $my_desk_ids = Users::where('id', Auth::user()->id)->value('desk_id');
                $my_desk_ids_exploded = explode(',', $my_desk_ids);
                $all_desk_to_me = implode(',', array_unique($my_desk_ids_exploded));
                Session::put('user_desk_ids', $all_desk_to_me);
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Invalid session ID!');
            return false;
        }
        return true;
    }
    public function logout()
    {
        if (Auth::user()) {
            Users::where('id', Auth::user()->id)->update(['login_token' => '']);
        }
        UtilFunction::entryAccessLogout();
        Session::getHandler()->destroy(Session::getId());
        Session::flush();
        Auth::logout();
        return redirect('/login');
    }
    public function loadLoginOtpForm()
    {
        return strval(view('public_home.otp'));
    }

    public function otpLoginEmailValidationWithTokenProvide(Request $request)
    {

        try {
            $email = trim($request->get('email_address'));

            /*
             * User given data is OK
             */
            if ($email) {
                $user = Users::where('user_email', $email)
                    ->where('is_approved', '=', 1)
                    ->where('user_status', '=', 'active')->first();

                /*
                 * User is valid
                 */
                if ($user) {

                    $login_token = rand(1111, 9999);
                    $expire_time_config = Configuration::where('caption', 'otp_expire_after')->value('value') ? Configuration::where('caption', 'otp_expire_after')->value('value') : "+3 min";
                    $otp_expire_time = date('Y-m-d H:i:s', strtotime($expire_time_config));
                    Users::where('id', $user->id)->update(['login_token' => $login_token, 'otp_expire_time' => $otp_expire_time]);


                    $receiverInfo[] = [
                        'user_email' => $email,
                        'user_mobile' => $user->user_mobile
                    ];

                    $appInfo = [
                        'one_time_password' => $login_token
                    ];

                    $id = CommonFunction::sendEmailSMS('ONE_TIME_PASSWORD', $appInfo, $receiverInfo);
                    $data = ['responseCode' => 1, 'msg' => 'Valid email', 'user_email' => $email, 'queue_id' => Encryption::encodeId($id)];
                    return response()->json($data);
                } else {
                    $data = ['responseCode' => 0, 'msg' => 'Invalid email'];
                    return response()->json($data);
                }
            } else {
                $data = ['responseCode' => 0, 'msg' => 'Invalid email'];
                return response()->json($data);
            }
        } catch (Exception $exception) {
            $data = ['responseCode' => 0, 'msg' => 'Sorry! Something is Wrong.' . $exception->getMessage()];
            return response()->json($data);
        }
    }

    public function checkOtpLogin(Request $request, Users $usersModel)
    {
        $rules = [
            'email' => 'required|email',
            'login_token' => 'required',
        ];
        if (!$this->_checkAttack($request)) {
            Session::flash('error', 'Invalid login information!![HIT3TIMES]');
            $data = ['responseCode' => 0, 'msg' => '', 'redirect_to' => ''];
        } else {

            $response = $this->commonLoginCheck($request, $usersModel, 2,
                trim($request->get('login_token')), true);
            if ($response['msg'] == 'OTP Time Expired!.Please Try again') {
                Session::flash('error', $response['msg']);
                $data = ['responseCode' => 0, 'msg' => "OTP Time Expired!.Please Try again", 'redirect_to' => $response['redirect_to']];
            } elseif ($response['result']) {
                Session::flash('success', $response['msg']);
                $data = ['responseCode' => 1, 'msg' => $response['msg'], 'redirect_to' => $response['redirect_to']];
            } else {
                Session::flash('error', $response['msg']);
                // $data = ['responseCode' => 0, 'msg' => $response['msg'],'redirect_to' => $response['redirect_to']];
                $data = ['responseCode' => 0, 'msg' => "Invalid OTP", 'redirect_to' => $response['redirect_to']];
            }
        }
        return response()->json($data);
    }

    /*
     * loginType (1) = Login By Credential
     * loginType (2) = Login By OTP
     */
    private function commonLoginCheck($request, $usersModel, $loginType = 0, $otp = '', $is_ajax_request = false)
    {
        try {
            $data = [
                'user_email' => $request->get('email')
            ];

            // General login
            if ($loginType == 1) {
                $remember_me = $request->has('remember_me') ? true : false;
                $loggedin = Auth::attempt(
                    ['user_email' => $request->get('email'),
                        'password' => $request->get('password')
                    ], $remember_me);
            } // Login with OTP
            else if ($loginType == 2) {
                $currentTime = new Carbon;
                $user = $usersModel::where('user_email', $request->get('email_address'))->where('login_token', $request->login_token)->first();
                if (empty($user)) {
                    $response = array('result' => false, 'msg' => 'Invalid login information',
                        'redirect_to' => '', 'is_ajax_request' => $is_ajax_request);
                    return $response;
                } elseif ($currentTime >= $user->otp_expire_time) {
                    $response = array('result' => false, 'msg' => 'OTP Time Expired!.Please Try again',
                        'redirect_to' => '', 'is_ajax_request' => $is_ajax_request);
                    return $response;
                }

                $loggedin = Auth::loginUsingId($user->id);

                if (!$loggedin) {
                    $response = array('result' => false, 'msg' => 'Login failed. Please reload the page and
                    try again.', 'redirect_to' => '', 'is_ajax_request' => $is_ajax_request);
                    return $response;
                }

            }

            // if user mail && password is true
            if ($loggedin) {
                // Check Maintenance Mode
                if ($this->checkMaintenanceModeForUser() === true) {
                    $error_msg = session()->get('error');
                    Auth::logout();
                    return array('result' => false, 'msg' => $error_msg, 'redirect_to' => '',
                        'is_ajax_request' => $is_ajax_request);
                    return redirect()->to('/login');
                }

                $userTypeRootStatus = $this->_checkUserTypeRootActivation(Auth::user()->user_type, $is_ajax_request);

                if ($userTypeRootStatus['result'] == false) {
                    Auth::logout();
                    UtilFunction::_failedLogin($data);
                    return array('result' => false, 'msg' => $userTypeRootStatus['msg'], 'redirect_to' => '',
                        'is_ajax_request' => $is_ajax_request);
                }

                if (Auth::user()->is_approved != 1) {
                    Auth::logout();
                    UtilFunction::_failedLogin($data);
                    return array('result' => false, 'msg' => 'The user is not approved, please contact with system admin/ <a href="/articles/support" target="_blank">Help line.</a>',
                        'redirect_to' => '', 'is_ajax_request' => $is_ajax_request);
                }
                if (Auth::user()->is_approved == 1 && Auth::user()->user_status != 'active') {
                    Auth::logout();
                    UtilFunction::_failedLogin($data);
                    return array('result' => false, 'msg' => 'The user is not active, please contact with system admin/ <a href="/articles/support" target="_blank">Help line.</a>',
                        'redirect_to' => '', 'is_ajax_request' => $is_ajax_request);
                }

                // if this user is not verified in system then go back
                if (Auth::user()->user_verification == 'no') {
                    Auth::logout();
                    UtilFunction::_failedLogin($data);
                    return array('result' => false, 'msg' => 'The user is not verified in ' . config('app.project_name') . ', please contact with system admin/ <a href="/articles/support" target="_blank">Help line.</a>',
                        'redirect_to' => '', 'is_ajax_request' => $is_ajax_request);
                }


                if (!$this->_checkSecurityProfile($request)) {
                    Auth::logout();
                    $error = (Session::has('error')) ? Session('error') : 'Security profile does not support login from this network';
                    return array('result' => false, 'msg' => $error, 'redirect_to' => '', 'is_ajax_request' => $is_ajax_request);
                }


                $loginAccess = $this->_protectLogin(Auth::user()->user_type); //login protected for UDC
                if ($loginAccess == false) {
                    //For any user type we can protect login from here
                    $error = (Session::has('error')) ? Session('error') : 'You are not allowed to login using this type of login method';
                    return array('result' => false, 'msg' => $error, 'redirect_to' => '/login', 'is_ajax_request' => $is_ajax_request);
                }


                if ($this->_setSession() == false) {
                    return array('result' => false, 'msg' => 'Session expired', 'redirect_to' => '/login',
                        'is_ajax_request' => $is_ajax_request);
                }

                if (Auth::user()->first_login == 0) {
                    Users::where('id', Auth::user()->id)->update(['first_login' => 1]);
                }

                if (Auth::user()->is_approved == 1) {
                    // Kill previous session and set a new session.
                    $this->killUserSession(Auth::user()->id, $loginType);
                    Users::where('id', Auth::user()->id)->update(['login_token' => Encryption::encode(Session::getId())]);

                    // Set delegated user id in session && redirect to delegation remove page
                    if (in_array(Auth::user()->user_type, ['4x404'])) {
                        if (Auth::user()->delegate_to_user_id != 0) {
                            Session::put('sess_delegated_user_id', Auth::user()->delegate_to_user_id);
                            return array('result' => true, 'msg' => 'Logged in successfully, Welcome to ' . config('app.project_name'), 'redirect_to' => '/users/delegate', 'is_ajax_request' => $is_ajax_request);
                        }
                    }

                    CommonFunction::GlobalSettings();

                    $user_type = UserTypes::where('id', Auth::user()->user_type)->first();
                    if (($user_type->auth_token_type == 'mandatory') || ($user_type->auth_token_type == 'optional' && Auth::user()->auth_token_allow == 1)) {
                        Users::where('id', Auth::user()->id)->update(['auth_token' => 'will get a code soon']);
                        return array('result' => true, 'msg' => 'Logged in successfully, Please verify the 2nd steps.', 'redirect_to' => '/users/two-step', 'is_ajax_request' => $is_ajax_request);
                    } else {
                        UtilFunction::entryAccessLog();
                        $this->newDeviceDetection();
                        // $this->_setCaption($usersModel);
                        // CommonFunction::setPermittedMenuInSession();

                        $redirect_url = '/dashboard';
                        if (in_array(Auth::user()->user_type, ['5x505'])) {
                            $companyIds = CommonFunction::getUserAllCompanyIdsWithZero();
                            // If the user have only one company then set all automatically
                            if (count($companyIds) > 1) {
                                $redirect_url = '/company-association/select-company';
//                                return \redirect()->to('company-association/select-company');
                            }
                        }
                        return array('result' => true, 'msg' => 'Logged in successfully, Welcome to ' . config('app.project_name'), 'redirect_to' => $redirect_url, 'is_ajax_request' => $is_ajax_request);
                    }
                }
            } else {
                if (Session::has('hit')) {
                    Session::put('hit', Session::get('hit') + 1);
                } else {
                    Session::put('hit', 1);
                }

                UtilFunction::_failedLogin($data);
                return array('result' => false, 'msg' => 'Invalid email or password', 'redirect_to' => '1',
                    'is_ajax_request' => $is_ajax_request);
            }
        } catch (\Exception $e) {
            Auth::logout();
            return array('result' => false, 'msg' => $e->getMessage(), '', $e->getLine(),
                'redirect_to' => '/', 'is_ajax_request' => $is_ajax_request);
        }
    }

    public function _checkUserTypeRootActivation($userType = null, $is_ajax_request)
    {
        // for checking user type status
        $userTypeInfo = UserTypes::where('id', $userType)->first();
        if (empty($userTypeInfo->status) || $userTypeInfo->status != "active" ) {
            return array('result' => false, 'msg' => 'The user type is not active, please contact with system admin.', 'redirect_to' => '', 'is_ajax_request' => $is_ajax_request);
        }
        return array('result' => true);
    }

    public function newDeviceDetection()
    {

        try {
            $agent = new Agent();
            $os = $agent->platform();
            $ip = $_SERVER['REMOTE_ADDR'];
            $browser = $agent->browser();

            $userDevice = UserDevice::
            where([
                'user_id' => Auth::user()->id,
                'os' => $os,
                'browser' => $browser,
                'ip' => $ip
            ])->count();

            if ($userDevice == 0) {

                $deviceData = new UserDevice();
                $deviceData->user_id = Auth::user()->id;
                $deviceData->os = $os;
                $deviceData->ip = $ip;
                $deviceData->browser = $browser;
                $deviceData->save();

                $receiverInfo[] = [
                    'user_email' => Auth::user()->user_email,
                    'user_mobile' => Auth::user()->user_mobile
                ];

                $appInfo = [
                    'device' => $os
                ];
                CommonFunction::sendEmailSMS('DEVICE_DETECTION', $appInfo, $receiverInfo);
            }

            return true;

        } catch (\Exception $e) {
            Session::flash('error', 'Device detection error!');
            return false;
        }


    }

    /*
    * forget-password
    */
    public function forgetPassword()
    {
        return view('public_home.forget-password');
    }

    //For Forget Password functionality
    //For Forget Password functionality
    public function resetForgottenPass(Request $request)
    {

        $rules['user_email'] = 'required|email';
        $rules['g-recaptcha-response'] = 'required';
        $messages['g-recaptcha-response.required'] = 'Please check the captcha.';
        $this->validate($request, $rules, $messages);

        try {
            $email = $request->get('user_email');
            $users = DB::table('users')
                ->where('user_email', $email)
                ->first();

            if (empty($users)) {
                \Session::flash('error', 'No user with this email is existed in our current database. Please sign-up first');
                return Redirect('forget-password')->with('status', 'error');

            }

            if ($users->user_status == 'inactive'
                && $users->user_verification == 'no') {
                \Session::flash('error', 'This user is not active and email is not verified yet. Please contact with system admin');
                return Redirect('forget-password')->with('status', 'error');
            }

            DB::beginTransaction();

            $token_no = hash('SHA256', "-" . $email . "-");

            $update_token_in_db = array(
                'user_hash' => $token_no,
            );

            DB::table('users')
                ->where('user_email', $email)
                ->update($update_token_in_db);

            $encrytped_token = Encryption::encode($token_no);
            $verify_link = 'verify-forgotten-pass/' . ($encrytped_token);

            $receiverInfo[] = [
                'user_email' => $users->user_email,
                'user_mobile' => $users->user_mobile
            ];

            $appInfo = [
                'reset_password_link' => url($verify_link)
            ];

            CommonFunction::sendEmailSMS('PASSWORD_RESET_REQUEST', $appInfo, $receiverInfo);

            DB::commit();

            \Session::flash('success', 'Please check your email to verify Password Change');
            return redirect('/')->withInput();
        } catch (Exception $exception) {
            DB::rollback();
            Session::flash('error', 'Sorry! Something is Wrong.' . $exception->getMessage());
            return Redirect::back()->withInput();
        }

    }

    // Forgotten Password reset after verification
    // Forgotten Password reset after verification
    function verifyForgottenPass($token_no)
    {
        $TOKEN_NO = Encryption::decode($token_no);
        $user = Users::where('user_hash', $TOKEN_NO)->first();
        if (empty($user)) {
            \Session::flash('error', 'Invalid token! No such user is found. Please sign up first.');
            return redirect('login');
        }
        return view('public_home.verify-new-password', compact('token_no'));


    }

    public function checkMaintenanceModeForUser()
    {
        $maintenance_data = MaintenanceModeUser::where('id', 1)->first([
            'id',
            'allowed_user_types',
            'allowed_user_ids',
            'alert_message',
            'operation_mode'
        ]);

        // 2 is maintenance mode
        if ($maintenance_data->operation_mode == 2) {
            $allowed_user_types = explode(',', $maintenance_data->allowed_user_types);
            $allowed_user_ids = explode(',', $maintenance_data->allowed_user_ids);
            if (in_array(Auth::user()->user_type, $allowed_user_types)
                or in_array(Auth::user()->id, $allowed_user_ids)) {
                return false;
            }

            Session::flash('error', $maintenance_data->alert_message);
            return true;
        }
        return false;
    }

    public function sendPushNotification($email)
    {
        $ossAppUser = OssAppUser::where('user_id', $email)->first(['token']);
        $agent = new Agent();
        $os = $agent->platform();
        $ip = $_SERVER['REMOTE_ADDR'];
        $browser = $agent->browser();
        $time = Carbon::now();
        if ($ossAppUser) {
            $this->apiSendNotification($ossAppUser->token, 'Logged in ', 'You are logged in from ' . $os .
                ', IP :' . $ip . ', Browser : ' . $browser . ' at ' . $time);
        }
    }

    public function StoreForgottenPass(Request $request)
    {
        $dataRule = [
            'user_new_password' => [
                'required',
                'min:6',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{6,}$/'
            ],
            'user_confirm_password' => [
                'required',
                'same:user_new_password',
            ]
        ];

        $validator = Validator::make($request->all(), $dataRule);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $new_password = $request->get('user_new_password');
        $user = Users::where('user_hash', Encryption::decode($request->token))->first();
        $user->password = Hash::make($new_password);
        $user->user_hash = '';
        $user->save();

        \Session::flash('success', 'Your password has been changed successfully! Please login with the new password.');
        return redirect('login');
    }

    public function OtpResent(Request $request)
    {
        try {
            $email = trim($request->get('email_address'));
            $otpBy = trim($request->get('otp'));

            /*
             * User given data is OK
             */
            if ($email) {
                $user = Users::where('user_email', $email)
                    ->where('is_approved', '=', 1)
                    ->where('user_status', '=', 'active')->first();

                /*
                 * User is valid
                 */
                if ($user) {

                    $login_token = rand(1111, 9999);
                    $expire_time_config = Configuration::where('caption', 'otp_expire_after')->value('value') ? Configuration::where('caption', 'otp_expire_after')->value('value') : "+3 min";
                    $otp_expire_time = date('Y-m-d H:i:s', strtotime($expire_time_config));
                    Users::where('id', $user->id)->update(['login_token' => $login_token, 'otp_expire_time' => $otp_expire_time]);


                    $receiverInfo[] = [
                        'user_email' => $email,
                        'user_mobile' => $user->user_mobile
                    ];

                    $appInfo = [
                        'one_time_password' => $login_token
                    ];

                    $id = CommonFunction::sendEmailSMS('ONE_TIME_PASSWORD', $appInfo, $receiverInfo);
                    $data = ['responseCode' => 1, 'msg' => 'Valid email', 'user_email' => $email, 'queue_id' => Encryption::encodeId($id)];
                    return response()->json($data);
                } else {
                    $data = ['responseCode' => 0, 'msg' => 'Invalid email'];
                    return response()->json($data);
                }
            } else {
                $data = ['responseCode' => 0, 'msg' => 'Invalid email'];
                return response()->json($data);
            }
        } catch (Exception $exception) {
            $data = ['responseCode' => 0, 'msg' => 'Sorry! Something is Wrong.' . $exception->getMessage()];
            return response()->json($data);
        }
    }

    public function checkSMSstatus(Request $request)
    {
        $email_sms_queue = EmailQueue::where('id', Encryption::decodeId($request->email_id))->first();
        if ($email_sms_queue->email_status == 1) {
            $data = ['responseCode' => 1, 'sms_status' => $email_sms_queue->email_status, 'msg' => 'Your OTP has been sent please check your device'];
            return response()->json($data);
        } else {
            $data = ['responseCode' => 1, 'sms_status' => $email_sms_queue->email_status, 'msg' => 'Sending Please wait.'];
            return response()->json($data);
        }


    }

    public function searchPilgrimByTrackingNo(Request $request)
    {
        date_default_timezone_set('Asia/Dhaka');
        // responseCode = -1 // Invalid
        // responseCode = 1 // Pilgrim exist but not exist in ehajj -> go for confirmation by OTP
        // responseCode = 2 // Collect access token from pilgrim
        // responseCode = 3 // Login successfully

        try{
            $tracking_no = !empty($request['tracking_no']) ? $request['tracking_no'] : '';
            $searchData = ['INSERT','UPDATE','DELETE','ALTER','DROP'];
            $replacedData = ['','','','',''];
            $tracking_no = str_replace($searchData,$replacedData,strtoupper($tracking_no));



            if (!$tracking_no) {
                $returnData = ['responseCode' => -1, 'msg' => 'Please provide valid tracking no.'];
                return response()->json($returnData);
            }

            // token generation starts
            $base_url = env('API_URL');
            $tokenUrl = "$base_url/api/getToken";
            $tokenData = [
                'clientid' => env('CLIENT_ID'),
                'username' => env('CLIENT_USER_NAME'),
                'password' => env('CLIENT_PASSWORD')
            ];

            $token = CommonFunction::getApiToken($tokenUrl, $tokenData);
            if (!$token) {
                $returnData = ['responseCode' => -1, 'msg' => 'Failed to generate API Token!!!'];
                return response()->json($returnData);
            }
            // token generation ends

            $apiUrl = "$base_url/api/get-pilgrim-by-tracking-no";
            $postdata = [];
            $postdata['tracking_no'] = $tracking_no;
            $postdata = json_encode($postdata);

            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );

            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postdata, $headers, true);

            if ($apiResponse['http_code'] != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!'];
                return response()->json($returnData);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);

            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!'];
                return response()->json($returnData);
            }

            /*
             * Find this pilgrim in e-haj table
             */
            $prp_user_email  = $tracking_no.'@pilgrimdb';
            $mobile = $apiResponseDataArr->data->mobile;

            $userData = Users::join('user_types','user_types.id', '=', 'users.user_type')
                ->where(['user_email'=>$prp_user_email,'user_type'=>'21x101'])
                ->where(DB::raw('RIGHT(user_mobile,11)'), '=', $mobile)
                ->where('user_types.status', '=', 'active')
                ->first(
                    [
                        'users.id',
                        'users.working_user_type',
                        'users.hmis_guide_id',
                        'users.is_crm_guide',
                        'users.pilgrim_access_token',
                        'users.send_access_token',
                        'users.user_mobile',
                    ]
                );

            $buttonResendShow = false;
            if(!empty($userData) && $userData->pilgrim_access_token){
                $expiredTime = strtotime($userData->send_access_token. ' +5 minutes');
                if(time() > $expiredTime ){
                    $buttonResendShow = true;
                }
            }

            $mobile = $apiResponseDataArr->data->mobile;
            $maskedMobile = "আপনার সম্ভাব্য মোবাইল নম্বরটি : ". CommonFunction::maskedMobile($mobile);

            if($userData == null){
                $tracking_no = $tracking_no;
                $msg = 'Pilgrim exist but not listed in ehajjdb. Please confirm the showing mobile number is yours';
                $returnData = ['responseCode' => 1, 'msg' => $msg, 'mobile'=>$mobile,'maskedMobile' => $maskedMobile, 'tracking_no' => $tracking_no];
                return response()->json($returnData);
            }
            if($apiResponseDataArr->data->pilgrim_type_id == 6 && $userData->hmis_guide_id == 0){
                $userData->working_user_type = "Guide";
                $userData->hmis_guide_id = $apiResponseDataArr->data->hmis_guide_id;
                $userData->is_crm_guide = $apiResponseDataArr->data->is_crm_guide;
                $userData->save();
            }else if($apiResponseDataArr->data->pilgrim_type_id != 6 && $userData->hmis_guide_id > 0){
                $userData->working_user_type = "Pilgrim";
                $userData->hmis_guide_id = 0;
                $userData->is_crm_guide = 0;
                $userData->save();
            }

            $msg = 'Pilgrim exist in ehajjdb. Please enter your access token for entering ehajj system';
            $returnData = ['responseCode' => 2, 'msg' => $msg, 'buttonResendShow'=>$buttonResendShow,'maskedMobile' => $maskedMobile];
            return response()->json($returnData);
        }catch(Exception $e){
            $returnData = ['responseCode' => -1, 'msg' => $e->getMessage().'-'.$e->getFile().'-'.$e->getLine()];
            return response()->json($returnData);
        }
    }

    public function searchPilgrimByAccessToken(Request $request){
    //public function searchPilgrimByAccessToken($tracking_no,$token){
        $tracking_no = !empty($request['tracking_no']) ? $request['tracking_no'] : '';
        $token = !empty($request['token']) ? $request['token'] : '';

        $searchData = ['INSERT','UPDATE','DELETE','ALTER','DROP'];
        $replacedData = ['','','','',''];
        $tracking_no = str_replace($searchData,$replacedData,strtoupper($tracking_no));
        $token = str_replace($searchData,$replacedData,strtoupper($token));

        $prp_user_email  = $data['user_email'] = $tracking_no.'@pilgrimdb';

        $user = Users::join('user_types','user_types.id', '=', 'users.user_type')
            ->where(['user_email'=>$prp_user_email,'user_type'=>'21x101'])
            ->where('pilgrim_access_token', '=', $token)
            ->where('user_types.status', '=', 'active')
            ->first(
                [
                    'users.id'
                ]
            );
        //check if user token or
        if(!isset($user)){
            $msg = "Invalid login information!!";
            Session::flash('error', 'Invalid Access Code Or Tracking No!![HIT3TIMESV2]');
            $data = ['responseCode' => -1, 'msg' => $msg];
            return response()->json($data);
        }

        // responseCode = -1 // Invalid
        // responseCode = 1 // Pilgrim exist but not exist in ehajj -> go for confirmation by OTP
        // responseCode = 2 // Collect access token from pilgrim
        // responseCode = 3 // Login successfully

        $msg = 'Unknown exception';
        $data = ['responseCode' => -1, 'msg' => $msg];

        if (!$this->_checkAttack($request)) {
            $msg = Session::get("error");
            Session::flash('error', 'Invalid login information!![HIT3TIMESV2]');
            $data = ['responseCode' => -1, 'msg' => $msg];
            return response()->json($data);
        } else {
            $loggedin = Auth::loginUsingId($user->id);
            if ($loggedin) {
                // Check Maintenance Mode
                if ($this->checkMaintenanceModeForUser() === true) {
                    Auth::logout();
                    $msg = 'Maintenance mode for user';
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }

                if (Auth::user()->is_approved != 1) {
                    Auth::logout();
                    UtilFunction::_failedLogin($data);
                    $msg = 'User not approved';
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }
                if (Auth::user()->is_approved == 1 && Auth::user()->user_status != 'active') {
                    Auth::logout();
                    UtilFunction::_failedLogin($data);
                    $msg = 'User not active';
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }

                // if this user is not verified in system then go back
                if (Auth::user()->user_verification == 'no') {
                    Auth::logout();
                    UtilFunction::_failedLogin($data);
                    $msg = 'The user is not verified in ' . config('app.project_name') . ', please contact with system admin/ <a href="/articles/support" target="_blank">Help line.</a>';
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }


                if (!$this->_checkSecurityProfile($request)) {
                    Auth::logout();
                    $msg = 'Security profile does not support login from this network';
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }


                $loginAccess = $this->_protectLogin(Auth::user()->user_type); //login protected for UDC
                if ($loginAccess == false) {
                    //For any user type we can protect login from here
                    $msg = 'You are not allowed to login using this type of login method';
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }

                if ($this->_setSession() == false) {
                    $msg = 'Session expired';
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }

                if (Auth::user()->first_login == 0) {
                    Users::where('id', Auth::user()->id)->update(['first_login' => 1]);
                }

                if (Auth::user()->is_approved == 1) {
                    // Kill previous session and set a new session.
                    $this->killUserSession(Auth::user()->id, 0);
                    Users::where('id', Auth::user()->id)->update(['login_token' => Encryption::encode(Session::getId())]);

                    // Set delegated user id in session && redirect to delegation remove page
                    if (in_array(Auth::user()->user_type, ['4x404'])) {
                        if (Auth::user()->delegate_to_user_id != 0) {
                            Session::put('sess_delegated_user_id', Auth::user()->delegate_to_user_id);
                            $msg = 'Logged in successfully, Welcome to ' . config('app.project_name');
                            Session::flash('success', $msg);
                            $returnData = ['responseCode' => 3, 'msg' => $msg];
                            return response()->json($returnData);
                        }
                    }

                    CommonFunction::GlobalSettings();

                    $user_type = UserTypes::where('id', Auth::user()->user_type)->first();
                    if (($user_type->auth_token_type == 'mandatory') || ($user_type->auth_token_type == 'optional' && Auth::user()->auth_token_allow == 1)) {
                        Users::where('id', Auth::user()->id)->update(['auth_token' => 'will get a code soon']);
                        //return array('result' => true, 'msg' => 'Logged in successfully, Please verify the 2nd steps.', 'redirect_to' => '/users/two-step', 'is_ajax_request' => $is_ajax_request);
                        $msg = 'Logged in successfully, Please verify the 2nd steps.';
                        Session::flash('success', $msg);
                        $returnData = ['responseCode' => 3, 'msg' => $msg];
                        return response()->json($returnData);
                    } else {
                        UtilFunction::entryAccessLog();
                        $this->newDeviceDetection();
                        //$redirect_url = '/dashboard';

                        $msg = 'Logged in successfully, Welcome to ' . config('app.project_name');
                        Session::flash('success', $msg);
                        $returnData = ['responseCode' => 3, 'msg' => $msg];
                        return response()->json($returnData);
                    }
                }
            }
        }
        return response()->json($data);
    }

    public function saveNsearchPilgrim(Request $request)
    {
        // responseCode = -1 // Invalid
        // responseCode = 1 // Pilgrim exist but not exist in ehajj -> go for confirmation by OTP
        // responseCode = 2 // Collect access token from pilgrim
        // responseCode = 3 // Login successfully

        try{
            $tracking_no = !empty($request['tracking_no']) ? $request['tracking_no'] : '';
            $req_mobile_no = !empty($request['mobile_no']) ? $request['mobile_no'] : '';

            $searchData = ['INSERT','UPDATE','DELETE','ALTER','DROP'];
            $replacedData = ['','','','',''];
            $tracking_no = str_replace($searchData,$replacedData,strtoupper($tracking_no));

            if (!$tracking_no) {
                $msg= 'Please provide valid tracking no.';
                Session::flash('error', $msg);
                $returnData = ['responseCode' => -1, 'msg' => $msg];
                return response()->json($returnData);
            }
            if (!$req_mobile_no) {
                $msg= 'Please provide valid mobile no.';
                Session::flash('error', $msg);
                $returnData = ['responseCode' => -1, 'msg' => $msg];
                return response()->json($returnData);
            }

            // token generation starts
            $base_url = env('API_URL');
            $tokenUrl = "$base_url/api/getToken";
            $tokenData = [
                'clientid' => env('CLIENT_ID'),
                'username' => env('CLIENT_USER_NAME'),
                'password' => env('CLIENT_PASSWORD')
            ];

            $token = CommonFunction::getApiToken($tokenUrl, $tokenData);
            if (!$token) {
                $msg= 'Failed to generate API Token!!!';
                Session::flash('error', $msg);
                $returnData = ['responseCode' => -1, 'msg' => $msg];
                return response()->json($returnData);
            }
            // token generation ends

            $apiUrl = "$base_url/api/get-pilgrim-by-tracking-no";

            $postdata = [];
            $postdata['tracking_no'] = $tracking_no;
            $postdata = json_encode($postdata);

            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );

            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postdata, $headers, true);
            if ($apiResponse['http_code'] != 200) {
                $msg= 'Something went wrong!!!';
                Session::flash('error', $msg);
                $returnData = ['responseCode' => -1, 'msg' => $msg];
                return response()->json($returnData);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $msg= 'Something went wrong from api server!!!';
                Session::flash('error', $msg);
                $returnData = ['responseCode' => -1, 'msg' => $msg];
                return response()->json($returnData);
            }

            /*
             * Find this pilgrim in e-haj table
             */
            $prp_user_email  = $tracking_no.'@pilgrimdb';
            $mobile = $apiResponseDataArr->data->mobile;

            if(substr($req_mobile_no, -11) != substr($mobile, -11)){
                $msg= 'আপনার প্রদত্ত মোবাইল নম্বরটি সঠিক নয়। অনুগ্রহ করে সঠিক মোবাইল নম্বরটি দিয়ে চেষ্টা করুন।';
                Session::flash('error', $msg);
                $returnData = ['responseCode' => -2, 'msg' => $msg];
                return response()->json($returnData);
            }

            $userData = Users::join('user_types','user_types.id', '=', 'users.user_type')
                ->where(['user_email'=>$prp_user_email,'user_type'=>'21x101'])
                ->where(DB::raw('RIGHT(user_mobile,11)'), '=', $mobile)
                ->where('user_types.status', '=', 'active')
                ->first(
                    [
                        'users.id'
                    ]
                );
            if($userData == null){
                $this->createNewUser($apiResponseDataArr);
                $msg = 'আপনার এক্সেস কোডটি আপনার মোবাইলে পাঠানো হয়েছে। সেই কোড দিয়ে লগইন করার চেষ্টা করুন। ধন্যবাদ।';
                Session::flash('success', $msg);
                $returnData = ['responseCode' => 2, 'msg' => $msg];
                return response()->json($returnData);
            }

            $msg = 'Invalid request';
            $returnData = ['responseCode' => -1, 'msg' => $msg];
            return response()->json($returnData);
        }catch(Exception $e){
            $returnData = ['responseCode' => -1, 'msg' => $e->getMessage().'-'.$e->getFile().'-'.$e->getLine()];
            return response()->json($returnData);
        }
    }
    private function createNewUser($apiResponseDataArr){
        $prp_user_email  = $apiResponseDataArr->data->tracking_no.'@pilgrimdb';
        $mobile = $apiResponseDataArr->data->mobile;
        $accessToken = rand(1111,9999);
        //$userObj = new Users();

        $userObj = Users::firstOrNew([
            'user_email' => $prp_user_email
        ]);

        $userObj->user_type = '21x101';
        $userObj->working_company_id = 0;
        if(isset($apiResponseDataArr->data->pilgrim_type_id) && $apiResponseDataArr->data->pilgrim_type_id==6 ){
            $userObj->working_user_type = 'Guide';
        }else{
            $userObj->working_user_type = 'Pilgrim';
        }
        if(isset($apiResponseDataArr->data->hmis_guide_id) && $apiResponseDataArr->data->pilgrim_type_id==6 ){
            $userObj->hmis_guide_id = $apiResponseDataArr->data->hmis_guide_id;
        }
        if(isset($apiResponseDataArr->data->is_crm_guide) && $apiResponseDataArr->data->pilgrim_type_id==6 ){
            $userObj->is_crm_guide = $apiResponseDataArr->data->is_crm_guide;
        }
        $userObj->desk_id = 0;
        $userObj->office_ids = '';
        $userObj->last_login_type = 'otp';
        $userObj->nationality_type = 'bangladeshi';
        $userObj->identity_type = 'none';
        $userObj->user_first_name = $apiResponseDataArr->data->user_first_name;
        $userObj->user_middle_name = '';
        $userObj->user_last_name = '';
        $userObj->designation = '';
        $userObj->user_email = $prp_user_email;
        $userObj->password = Hash::make(rand(1111111111, 9999999999));
        $userObj->delegate_to_user_id = 0;
        $userObj->delegate_by_user_id = 0;
        $userObj->user_hash = '';
        $userObj->user_status = 'active';
        $userObj->user_verification = 'yes';
        $userObj->pin_number = '';
        $userObj->user_pic = '';
        $userObj->user_nid = '';
        $userObj->user_tin = '';
        $userObj->user_DOB = $apiResponseDataArr->data->birth_date;
        $userObj->user_gender = $apiResponseDataArr->data->gender;
        $userObj->user_mobile = $mobile;
        $userObj->passport_nid_file = '';
        $userObj->signature = '';
        $userObj->signature_encode = '';
        $userObj->is_approved = 1;
        $userObj->pilgrim_access_token = $accessToken;
        $userObj->send_access_token = date('Y-m-d H:i:s');
        $userObj->save();

        // token generation starts
        $base_url = env('API_URL');
        $tokenUrl = "$base_url/api/getToken";
        $tokenData = [
            'clientid' => env('CLIENT_ID'),
            'username' => env('CLIENT_USER_NAME'),
            'password' => env('CLIENT_PASSWORD')
        ];

        $token = CommonFunction::getApiToken($tokenUrl, $tokenData);
        if (!$token) {
            $returnData = ['responseCode' => -1, 'msg' => 'Failed to generate API Token!!!'];
            return response()->json($returnData);
        }
        // token generation ends

        $apiUrl = "$base_url/api/send-quick-sms";

        $postdata = [];
        $postdata['mobile'] = $mobile;
        $postdata['tracking_no'] = $apiResponseDataArr->data->tracking_no;
        $postdata['name'] = $apiResponseDataArr->data->user_first_name;
        $postdata['token'] = $accessToken;
        $postdata = json_encode($postdata);

        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );

        $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postdata, $headers, true);
        if ($apiResponse['http_code'] != 200) {
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!'];
            return response()->json($returnData);
        }

        $apiResponseDataArr = json_decode($apiResponse['data']);
        if ($apiResponseDataArr->status != 200){
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!'];
            return response()->json($returnData);
        }
        return true;
    }

    public function prpLogin(Request $request)
    {
        try{
            $prp_email = !empty($request['prp_email']) ? $request['prp_email'] : '';
            $prp_password = !empty($request['prp_password']) ? $request['prp_password'] : '';

            if (!$prp_email || !$prp_password) {
                $msg = 'আপনার সঠিক লগইন তথ্য প্রদান করুন';
                Session::flash('error', $msg);
                $returnData = ['responseCode' => -1, 'msg' => $msg];
                return response()->json($returnData);
            }

            // token generation starts
            $base_url = env('API_URL');
            $tokenUrl = "$base_url/api/getToken";
            $tokenData = [
                'clientid' => env('CLIENT_ID'),
                'username' => env('CLIENT_USER_NAME'),
                'password' => env('CLIENT_PASSWORD')
            ];

            $token = CommonFunction::getApiToken($tokenUrl, $tokenData);
            if (!$token) {
                $msg = 'Failed to generate API Token!!!';
                Session::flash('error', $msg);
                $returnData = ['responseCode' => -1, 'msg' => $msg];
                return response()->json($returnData);
            }
            // token generation ends

            $apiUrl = "$base_url/api/general-user-login";
            $postdata = [];
            $postdata['email'] = $prp_email;
            $postdata['password'] = $prp_password;
            $postdata = json_encode($postdata);

            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );

            $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postdata, $headers, true);

            if ($apiResponse['http_code'] != 200) {
                $msg = 'Something went wrong!!!';
                Session::flash('error', $msg);
                $returnData = ['responseCode' => -1, 'msg' => $msg];
                return response()->json($returnData);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $msg = 'Something went wrong from api server!!!';
                Session::flash('error', $msg);
                $returnData = ['responseCode' => -1, 'msg' => $msg];
                return response()->json($returnData);
            }

            $userData = $apiResponseDataArr->data;
            $userData->from_prp = 1;

            $userDataResp = $this->checkUserExist($userData);

            if($userDataResp['responseCode'] == -1){    // user not found in local db
                Session::put('oauth_data', $userData);
                $returnData = ['responseCode' => -2, 'msg' => $userDataResp['msg'], 'userData'=>$userData];
                return response()->json($returnData);
            }

            // existed user data in local db
            $existUserData = $userDataResp['data'];

            // this user passed all condition

            $loggedin = Auth::loginUsingId($existUserData->id);
            if ($loggedin) {
                // Check Maintenance Mode
                if ($this->checkMaintenanceModeForUser() === true) {
                    Auth::logout();
                    $msg = 'Maintenance mode for user';
                    Session::flash('error', $msg);
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }

                if (Auth::user()->is_approved != 1) {
                    Auth::logout();
                    $msg = 'User not approved';
                    UtilFunction::_failedLogin(['user_email' => $prp_email]);
                    Session::flash('error', $msg);
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }
                if (Auth::user()->is_approved == 1 && Auth::user()->user_status != 'active') {
                    Auth::logout();
                    $msg = 'User not active';
                    UtilFunction::_failedLogin(['user_email' => $prp_email]);
                    Session::flash('error', $msg);
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }
                // if this user is not verified in system then go back
                if (Auth::user()->user_verification == 'no') {
                    Auth::logout();
                    $msg = 'The user is not verified in ' . config('app.project_name') . ', please contact with system admin/ <a href="/articles/support" target="_blank">Help line.</a>';
                    UtilFunction::_failedLogin(['user_email' => $prp_email]);
                    Session::flash('error', $msg);
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }
                if (!$this->_checkSecurityProfile($request)) {
                    Auth::logout();
                    $msg = 'Security profile does not support login from this network';
                    Session::flash('error', $msg);
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }
                $loginAccess = $this->_protectLogin(Auth::user()->user_type); //login protected for UDC
                if ($loginAccess == false) {
                    //For any user type we can protect login from here
                    $msg = 'You are not allowed to login using this type of login method';
                    Session::flash('error', $msg);
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }

                if ($this->_setSession() == false) {
                    $msg = 'Session expired';
                    Session::flash('error', $msg);
                    $returnData = ['responseCode' => -1, 'msg' => $msg];
                    return response()->json($returnData);
                }

                if (Auth::user()->first_login == 0) {
                    Users::where('id', Auth::user()->id)->update(['first_login' => 1]);
                }

                if (Auth::user()->is_approved == 1) {
                    // Kill previous session and set a new session.
                    $this->killUserSession(Auth::user()->id, 0);
                    Users::where('id', Auth::user()->id)->update(['login_token' => Encryption::encode(Session::getId())]);

                    // Set delegated user id in session && redirect to delegation remove page
                    if (in_array(Auth::user()->user_type, ['4x404'])) {
                        if (Auth::user()->delegate_to_user_id != 0) {
                            Session::put('sess_delegated_user_id', Auth::user()->delegate_to_user_id);
                            $msg = 'Logged in successfully, Welcome to ' . config('app.project_name');
                            Session::flash('success', $msg);
                            $returnData = ['responseCode' => 3, 'msg' => $msg];
                            return response()->json($returnData);
                        }
                    }

                    CommonFunction::GlobalSettings();

                    $user_type = UserTypes::where('id', Auth::user()->user_type)->first();
                    if (($user_type->auth_token_type == 'mandatory') || ($user_type->auth_token_type == 'optional' && Auth::user()->auth_token_allow == 1)) {
                        Users::where('id', Auth::user()->id)->update(['auth_token' => 'will get a code soon']);
                        //return array('result' => true, 'msg' => 'Logged in successfully, Please verify the 2nd steps.', 'redirect_to' => '/users/two-step', 'is_ajax_request' => $s_ajax_request);
                        $msg = 'Logged in successfully, Please verify the 2nd steps.';
                        Session::flash('success', $msg);
                        $returnData = ['responseCode' => 3, 'msg' => $msg];
                        return response()->json($returnData);
                    } else {
                        UtilFunction::entryAccessLog();
                        $this->newDeviceDetection();
                        //$redirect_url = '/dashboard';
                        Users::where('id', Auth::user()->id)->update(['user_sub_type' => $userData->user_sub_type, 'prp_user_id' => $userData->prp_user_id, 'app_mode' => $userData->app_mode]);

                        $msg = 'Logged in successfully, Welcome to ' . config('app.project_name');
                        Session::flash('success', $msg);
                        $returnData = ['responseCode' => 3, 'msg' => $msg];
                        return response()->json($returnData);
                    }
                }
            }
        }
        catch(\Exception $e){
            #dd($e->getMessage(), $e->getLine(), $e->getFile());
            $msg = 'Something went wrong : [exception]!!!';
            Session::flash('error', $msg);
            $returnData = ['responseCode' => -1, 'msg' => $msg];
            return response()->json($returnData);
        }


    }

    public function checkUserExist($userData)
    {
        $app_user = Users::where(['user_email'=>$userData->user_email.'_prp'])->first();
        if(!$app_user){
            return ['responseCode' => -1,'msg'=>'User not found'];
        }
        return ['responseCode' => 1,'msg'=>'User found', 'data'=>$app_user];
    }

    public function resendOtpToUser(Request $request)
    {

        $tracking_no =  $request['tracking_no'];
        $resend_mobile_no =  $request['resend_mobile_no'];

        if(empty($tracking_no) || empty($resend_mobile_no)){
            $returnData = ['responseCode' => -1,'msg'=>'Please provide valid information'];
            return response()->json($returnData);
        }


        $prp_user_email  = $tracking_no.'@pilgrimdb';

        $userData = Users::join('user_types','user_types.id', '=', 'users.user_type')
            ->where(['user_email'=>$prp_user_email,'user_type'=>'21x101'])
            ->where('user_types.status', '=', 'active')
            ->first(
                [
                    'users.id',
                    'users.working_user_type',
                    'users.hmis_guide_id',
                    'users.is_crm_guide',
                    'users.pilgrim_access_token',
                    'users.send_access_token',
                    'users.user_mobile',
                    'users.user_first_name',
                ]
            );

        if(!$userData){
            $returnData = ['responseCode' => -1,'msg'=>'User Not found'];
            return response()->json($returnData);
        }
        if($userData['user_mobile'] != $resend_mobile_no){
            $returnData = ['responseCode' => -1,'msg'=>'Invalid mobile'];
            return response()->json($returnData);
        }


        $userObj = Users::find($userData['id']);

        $accessToken = rand(1111,9999);
        $userObj->pilgrim_access_token = $accessToken;
        $userObj->send_access_token = date('Y-m-d H:i:s');
        $userObj->save();

        // token generation starts
        $base_url = env('API_URL');
        $tokenUrl = "$base_url/api/getToken";
        $tokenData = [
            'clientid' => env('CLIENT_ID'),
            'username' => env('CLIENT_USER_NAME'),
            'password' => env('CLIENT_PASSWORD')
        ];

        $token = CommonFunction::getApiToken($tokenUrl, $tokenData);
        if (!$token) {
            $returnData = ['responseCode' => -1, 'msg' => 'Failed to generate API Token!!!'];
            return response()->json($returnData);
        }
        // token generation ends

        $apiUrl = "$base_url/api/send-quick-sms";

        $postdata = [];
        $postdata['mobile'] = $resend_mobile_no;
        $postdata['tracking_no'] = $tracking_no;
        $postdata['name'] = $userData['user_first_name'];
        $postdata['token'] = $accessToken;
        $postdata = json_encode($postdata);

        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );

        $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postdata, $headers, true);
        if ($apiResponse['http_code'] != 200) {
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!'];
            return response()->json($returnData);
        }

        $apiResponseDataArr = json_decode($apiResponse['data']);
        if ($apiResponseDataArr->status != 200){
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!'];
            return response()->json($returnData);
        }


        $msg = 'আপনার এক্সেস কোডটি আপনার মোবাইলে পাঠানো হয়েছে। সেই কোড দিয়ে লগইন করার চেষ্টা করুন। ধন্যবাদ।';
        Session::flash('success', $msg);
        $returnData = ['responseCode' => 1, 'msg' => $msg];
        return response()->json($returnData);


    }
    public function contact()
    {
        return view("contact");
    }
    public function privacyPolicy()
    {
        return view("privacyPolicy");
    }
    public function ehajApps()
    {
        return view("ehajApps");
    }
    public function getPilgrimdbPath() {
        // token generation starts
        $base_url = env('API_URL');
        $tokenUrl = "$base_url/api/getToken";
        $tokenData = [
            'clientid' => env('CLIENT_ID'),
            'username' => env('CLIENT_USER_NAME'),
            'password' => env('CLIENT_PASSWORD')
        ];

        $token = CommonFunction::getApiToken($tokenUrl, $tokenData);
        if (!$token) {
            $returnData = ['responseCode' => -1, 'msg' => 'Failed to generate API Token!!!'];
            return response()->json($returnData);
        }
        // token generation ends

        $apiUrl = "$base_url/api/get-pilgrimdb-path";

        $postdata = [];
        $postdata = json_encode($postdata);

        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );

        $apiResponse = CommonFunction::curlPostRequest($apiUrl, $postdata, $headers, true);
        if ($apiResponse['http_code'] != 200) {
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!'];
            return response()->json($returnData);
        }

        $apiResponseDataArr = json_decode($apiResponse['data']);
        $pilgrimdbPath = $apiResponseDataArr->data;
        if ($apiResponseDataArr->status != 200){
            $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!'];
            return response()->json($returnData);
        }
        if ($apiResponseDataArr->status == 200){
            $returnData = ['responseCode' => 1, 'msg' => "$apiResponseDataArr->msg", 'data' => "$pilgrimdbPath"];
            return response()->json($returnData);
        }
    }

    public function kecloakCallback(\Illuminate\Http\Request $req) {
        try {
            if (isset($_GET['code'])) {
                // Get the authorization code from the query parameters
                $authorizationCode = $_GET['code'];

                // Make a POST request to the token endpoint to exchange the authorization code for an access token
                $tokenEndpoint = env('KEYCLOAK_TOKEN_END_POINT');
                $clientSecret = env('KEYCLOAK_CLIENT_SECRET');

                $postData = [
                    'grant_type' => 'authorization_code',
                    'code' => $authorizationCode,
                    'client_id' => env('KEYCLOAK_CLIENT_ID'),
                    'client_secret' => $clientSecret,
                    'redirect_uri' => env('KEYCLOAK_REDIRECT_URI')
                ];

                $ch = curl_init($tokenEndpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

                $response = curl_exec($ch);
                curl_close($ch);

                // Handle the response from the token endpoint
                $responseData = json_decode($response, true);
                if (isset($responseData['access_token'])) {
                    // Access token obtained successfully
                    $accessToken = $responseData['access_token'];
                    Session::put('sso_refresh_token', $responseData['refresh_token']);

                    // You can use the access token to make API requests on behalf of the user
                    // For example, retrieve user information from the userinfo endpoint
                    $userinfoEndpoint = env('KEYCLOAK_USER_INFO_END_POINT');

                    $ch = curl_init($userinfoEndpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Bearer ' . $accessToken
                    ]);

                    $userinfoResponse = curl_exec($ch);

                    curl_close($ch);

                    $decodedResponse = json_decode($userinfoResponse);

                    if (!$decodedResponse->user_id) {
                        $refresh_token = $responseData['refresh_token'];
                        if ($refresh_token) {
                            WebController::backChannelLogout($refresh_token);
                        }
                        Session::flash('error', 'Access denied: You do not have permission to login.');
                        return redirect('/login');
                    }
                    if (auth()->loginUsingId($decodedResponse->user_id)) {
                        // Kill previous session and set a new session.
                        $this->killUserSession(Auth::user()->id, 0);
                        Users::where('id', Auth::user()->id)->update(['login_token' => Encryption::encode(Session::getId())]);
                        CommonFunction::GlobalSettings();

                        if (Auth::user()->user_type === '18x415' && !in_array(Auth::user()->working_user_type, ['Pilgrim', 'Guide'])) {
                            Session::flash('error', 'আপনার কোন টাইপের ইউজার তা সিলেক্ট করুন। আপনি যদি হজযাত্রী হন তাহলে  Working user type থেকে Pilgrim সিলেক্ট করুন। আর যদি আপনি হজ গাইড হন তাহলে Guide সিলেক্ট করুন।');
                            return redirect('/users/profileinfo');
                        }

                        // Redirect in prp or prps based on configuiration
                        $dashbaord = new DashboardController();
                        $isOnlyPrps = $dashbaord->canGoInPrpsAndHmis();
                        if($isOnlyPrps['prp_prps_direct_login'] === true && $isOnlyPrps['prp_prps_access'] === 2){
                            $keycloakAuthUrl = env('KEYCLOAK_AUTH_URL');
                            $prps_credentials = [
                                'client_id' =>  env('KEYCLOAK_PRPS_CLIENT_ID'),
                                'redirect_uri' =>  env('KEYCLOAK_PRPS_REDIRECT_URI'),
                                'response_type' => 'code',
                                'scope' => 'openid',
                            ];
                            $prps_httpQueryString = http_build_query($prps_credentials);
                            $redirect_uri = $keycloakAuthUrl . '?' . $prps_httpQueryString;
                            return redirect($redirect_uri);
                        } else if($isOnlyPrps['prp_prps_direct_login'] === true && $isOnlyPrps['prp_prps_access'] === 1) {
                            $keycloakAuthUrl = env('KEYCLOAK_AUTH_URL');
                            $prp_credentials = [
                                'client_id' =>  env('KEYCLOAK_PRP_CLIENT_ID'),
                                'redirect_uri' =>  env('KEYCLOAK_PRP_REDIRECT_URI'),
                                'response_type' => 'code',
                                'scope' => 'openid',
                            ];
                            $prp_httpQueryString = http_build_query($prp_credentials);
                            $redirect_uri = $keycloakAuthUrl . '?' . $prp_httpQueryString;
                            return redirect($redirect_uri);
                        }
                        return redirect('/dashboard');
                    }

                } else {
                    Session::flash('error', 'Something went wrong. Please try again later');
                    return redirect('/');
                }
            } else {
                Session::flash('error', 'Something went wrong. Please try again later');
                return redirect('/');
            }
        } catch(\Exception $e) {
            Session::flash('error', 'Something went wrong. Please try again later');
                return redirect('/');
        }
    }
}
