<?php

namespace App\Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Libraries\ImageProcessing;
use App\Libraries\PostApiData;
use App\Models\ActionInformation;
use App\Modules\ProcessPath\Models\UserDesk;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveClinic;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Settings\Models\IndustrialCityList;
use App\Modules\Users\Models\Countries;
use App\Modules\Users\Models\Delegation;
use App\Modules\Users\Models\OsspidLog;
use App\Modules\Users\Models\SecurityProfile;
use App\Modules\Users\Models\UserLogs;
use App\Modules\Users\Models\Users;
use App\Modules\Users\Models\UserTypes;
use App\Modules\Settings\Models\Area;
use App\Modules\Web\Http\Controllers\WebController;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\UtilFunction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Mockery\Exception;
use yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    /*
     * user's list for system admin
     */
    public function lists()
    {
        if (!ACL::getAccsessRight('user', '-V-')) {
            abort('400', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
        }
        return view('Users::user_list');
    }
    public function guideLists()
    {
        $access_users = Configuration::where('caption','Guide_List_Side_Bar')->first();
        $access_users_array = json_decode($access_users->value2);
        if(!in_array(Auth::user()->user_email,$access_users_array)){
            Session::flash('error', "'You have no access right to this menue! " . '[UC-1051]');
            return Redirect::back();
        }
        if (!ACL::getAccsessRight('user', '-V-')) {
            abort('400', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
        }
        return view('Users::guide_list');
    }

    /*
     * user's details information by ajax request
     */
    public function getList()
    {
        $mode = ACL::getAccsessRight('user', 'V');
        try {
            $userList = Users::getUserList();
            return Datatables::of($userList)
                ->addColumn('action', function ($userList) use ($mode) {
                    if ($mode) {
                        $force_log_out_btn = '';
                        $assign_parameters_btn = '';
                        $assign_desk_btn = '';
                        $parkAssign = '';
                        $company_associated = '';
                        $accessLog = '';

                        if (Auth::user()->user_type == '1x101' || Auth::user()->user_type == '8x808') {
                            // if ($userList->user_type == '4x404') {
                            $assign_parameters_btn = '<a href="' . url('/users/assign-parameters/' . Encryption::encodeId($userList->id)) .
                                '" class="btn btn-xs btn-warning btn-flat m-1"><i class="fa fa-check-circle"></i> Assign Perameters</a>';
                            // }

                            $accessLog = '<a href="' . url('/users/access-log/' . Encryption::encodeId($userList->id)) .
                                '" class="btn btn-flat btn-xs btn-success m-1"><i class="fa fa-key"></i> Access Log</a>';

                            if (!empty($userList->login_token)) {
                                $force_log_out_btn = '<a href="' . url('/users/force-logout/' . Encryption::encodeId($userList->id)) .
                                    '" class="btn btn-xs btn-danger btn-flat m-1"><i class="fa fa-sign-out "></i> Force Log out</a>';
                            }
                        }

                        return '<a href="' . url('users/view/' . Encryption::encodeId($userList->id)) . '" class="btn btn-flat btn-primary btn-xs m-1"><i class="fa fa-folder-open-o"></i> Open</a>' . $force_log_out_btn . $assign_desk_btn . $assign_parameters_btn . $parkAssign . $company_associated . $accessLog;
                    } else {
                        return '';
                    }
                })
                ->editColumn('user_status', function ($userList) {
                    if ($userList->user_status == 'inactive') {
                        $activate = 'class="text-danger" ';
                    } else {
                        $activate = 'class="text-success" ';
                    }
                    return '<span ' . $activate . '><b>' . $userList->user_status . '</b></span>';
                })
                ->editColumn('created_at', function ($userList) {
                    return $userList->created_at ? $userList->created_at->format('Y-m-d H:i:s') : null;
                })
                ->removeColumn('id', 'is_sub_admin')
                ->rawColumns(['user_status', 'action'])
                ->make(true);
        } catch(\Exception $e) {
            Log::error($e->getMessage(). ' @@@@@ '. $e->getFile(). ' @@@@@ '. $e->getLine());
            return response()->json(['status' => 'error', 'responseCode' => -1, 'message' => $e->getMessage()]);
        }
    }
    public function getGuideList()
    {
        $mode = ACL::getAccsessRight('user', 'V');
        $userList = Users::getGuideList();
        return Datatables::of($userList)
            ->addColumn('action', function ($userList) use ($mode) {
                if ($mode) {
                    $force_log_out_btn = '';
                    $assign_parameters_btn = '';
                    $assign_desk_btn = '';
                    $parkAssign = '';
                    $company_associated = '';
                    $accessLog = '';

                    if (Auth::user()->user_type == '1x101' || Auth::user()->user_type == '8x808') {
                        if ($userList->user_type == '4x404') {
                            $assign_parameters_btn = '<a href="' . url('/users/assign-parameters/' . Encryption::encodeId($userList->id)) .
                                '" class="btn btn-xs btn-warning btn-flat m-1"><i class="fa fa-check-circle"></i> Assign Perameters</a>';
                        }

                        $accessLog = '<a href="' . url('/users/access-log/' . Encryption::encodeId($userList->id)) .
                            '" class="btn btn-flat btn-xs btn-success m-1"><i class="fa fa-key"></i> Access Log</a>';

                        if (!empty($userList->login_token)) {
                            $force_log_out_btn = '<a href="' . url('/users/force-logout/' . Encryption::encodeId($userList->id)) .
                                '" class="btn btn-xs btn-danger btn-flat m-1"><i class="fa fa-sign-out "></i> Force Log out</a>';
                        }
                    }

                    return '<a href="' . url('users/view/' . Encryption::encodeId($userList->id)) . '?from=guide-view" class="btn btn-flat btn-primary btn-xs m-1"><i class="fa fa-folder-open-o"></i> Open</a>' . $force_log_out_btn . $assign_desk_btn . $assign_parameters_btn . $parkAssign . $company_associated . $accessLog;
                } else {
                    return '';
                }
            })
            ->editColumn('user_status', function ($userList) {
                if ($userList->user_status == 'inactive') {
                    $activate = 'class="text-danger" ';
                } else {
                    $activate = 'class="text-success" ';
                }
                return '<span ' . $activate . '><b>' . $userList->user_status . '</b></span>';
            })
            ->editColumn('created_at', function ($userList) {
                return $userList->created_at->format('Y-m-d H:i:s');
            })
            ->removeColumn('id', 'is_sub_admin')
            ->rawColumns(['user_status', 'action'])
            ->make(true);
    }


    public function assignParameters($id)
    {
        if (!ACL::getAccsessRight('user', 'A'))
            abort(401, 'You have no access right. Contact with system admin for more information. [UC-973]');

        try {
            $user_id = Encryption::decodeId($id);
            $user_info = Users::where('id', $user_id)->first(['id', 'desk_id', 'office_ids', 'user_email', 'user_mobile', 'user_first_name', 'user_middle_name', 'user_last_name']);
            $fullName = '' . $user_info->user_first_name . ' ' . $user_info->user_middle_name . ' ' . $user_info->user_last_name;

            $select_desk = array();
            $select_park = array();
            if ($user_info != null) {
                $select_desk = explode(',', $user_info->desk_id);
                $select_park = explode(',', $user_info->office_ids);
            }
            $park_list = IndustrialCityList::orderBy('name')->where('status', 1)->where('type', 1)->where('is_archive', 0)->get(['name as park_name', 'id']);
            $desk_list = UserDesk::where('status', 1)->get(['desk_name', 'id']);

            return view('Users::assign_users_parameters', compact('park_list', 'desk_list', 'select_desk', 'select_park', 'fullName', 'user_info'));
        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[UC-1051]');
            return Redirect::back();
        }
    }

    public function assignParametersSave(Request $request)
    {

        if (!ACL::getAccsessRight('user', 'E'))
            abort(401, 'You have no access right. Contact with system admin for more information. [UC-974]');

        $rules = [];
        $rules['assign_desk'] = 'required';


        $messages = [];
        $messages['assign_desk'] = 'Please assign at least one desk!!!';


        $this->validate($request, $rules, $messages);

        try {
            $user_id = Encryption::decodeId($request->get('user_id'));

            $assign_desk = 0;
            if ($request->get('assign_desk') != null) {
                $assign_desk = implode(',', $request->get('assign_desk'));
            }


            DB::beginTransaction();
            $deskData = Users::find($user_id);
            $deskData->desk_id = !empty($assign_desk) ? $assign_desk : 0;

            $deskData->save();
            DB::commit();

            $loginController = new LoginController();
            $loginController::killUserSession($user_id);

            Session::flash('success', 'Successfully assigned parameters.');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[UC-1052]');
            return redirect()->back();
        }
    }


    public function failedLoginHist(request $request, $email)
    {
        if (!CommonFunction::isAdmin()) {
            Session::flash('error', 'Permission Denied [UC-1094]');
            return redirect('dashboard');
        }
        $logged_in_user_type = Auth::user()->user_type;
        $decodedUserEmail = Encryption::decodeId($email);
        $user = Users::where('user_email', $decodedUserEmail)->get([
            DB::raw("CONCAT_WS(' ', users.user_first_name, users.user_middle_name, users.user_last_name) as user_full_name"),
            'user_mobile'
        ]);
        return view('Users::failed-loginHistory', compact('logged_in_user_type', 'user', 'decodedUserEmail', 'email'));
    }

    public function accessLogHist($userId)
    {
        $decodedUserId = Encryption::decodeId($userId);
        if (!CommonFunction::isAdmin()) {
            Session::flash('error', 'Permission Denied [UC-11125]');
            return redirect('dashboard');
        }
        $logged_in_user_type = Auth::user()->user_type;
        $user = Users::find($decodedUserId);
        $user_name = $user->user_first_name . ' ' . $user->user_middle_name . ' ' . $user->user_last_name;
        $user_mobile = $user->user_mobile;
        $email = $user->user_email;
        return view('Users::access-log', compact('logged_in_user_type', 'user', 'userId', 'email', 'user_name', 'user_mobile'));
    }

    public function getAccessLogData($userId)
    {
        if (!ACL::getAccsessRight('user', 'E')) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
        try {
            $decodedUserId = Encryption::decodeId($userId);
            $user_logs = UserLogs::JOIN('users', 'users.id', '=', 'user_logs.user_id')
                ->where('user_logs.user_id', '=', $decodedUserId)
                ->orderBy('user_logs.id', 'desc')
                ->limit(10)
                ->get([
                    'users.designation',
                    'users.user_mobile',
                    DB::raw("CONCAT(' ', users.user_first_name, users.user_middle_name, users.user_last_name) as user_full_name"),
                    'user_logs.user_id', 'ip_address', 'login_dt', 'logout_dt', DB::raw('@rownum  := @rownum  + 1 AS rownum')
                ]);
            return Datatables::of($user_logs)
                ->make(true);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage() . ' [UC-1103]');
            return \redirect()->back();
        }
    }

    public function getAccessLogDataForSelf()
    {
        try {
            $osspidLog = new OsspidLog();
            $access_token = $osspidLog->getAuthToken();

            if ($access_token) {
                $user_logs = $osspidLog->getOsspidAccessLogHistory($access_token);
                return Datatables::of(collect($user_logs->osspidLoggerResponse->responseData))
                    ->make(true);
            } else {
                dd('Token not found!!!');
            }
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage() . ' [UC-1102]');
            return \redirect()->back();
        }
    }

    public function getLast50Actions()
    {
        DB::statement(DB::raw('set @rownum=0'));
        $last50Action = ActionInformation::where('user_id', '=', Auth::user()->id)->orderBy('id', 'DESC')->take(50)
            ->get(['action_info.action', 'action_info.ip_address', 'action_info.created_at', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
        return Datatables::of($last50Action)
            ->editColumn('rownum', function ($data) {
                return $data->rownum;
            })
            ->editColumn('created_at', function ($data) {
                $old_date_timestamp = strtotime($data->created_at);
                $new_date = date('Y-m-d H:i:s', $old_date_timestamp);
                return $new_date;
            })
            ->make(true);
    }

    public function getAccessLogFailed()
    {
        try {
            $osspidLog = new OsspidLog();
            $access_token = $osspidLog->getAuthToken();

            if ($access_token) {
                $user_Failed = $osspidLog->getOsspidFailedLoginHistory($access_token);

                return Datatables::of(collect(collect($user_Failed->osspidLoggerResponse->responseData)))
                    ->make(true);
            } else {
                return response()->json(['messagge' => 'Token not found!!!']);
            }
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage() . ' [UC-1101]');
            return \redirect()->back();
        }
    }

    public function getRowFailedData(request $request, Users $email)
    {
        //feedback 26
        try {
            $email = Encryption::decodeId($request->get('email'));
            $mode = ACL::getAccsessRight('user', 'E');
            $failed_login_history = DB::table('failed_login_history')->where('user_email', $email);
            return Datatables::of($failed_login_history)
                ->addColumn('action', function ($failed_login_history) use ($mode) {
                    if ($mode) {
                        return '<a  data-toggle="modal" data-target="#myModal" id="' . $failed_login_history->id . '" onclick="myFunction(' . $failed_login_history->id . ')" class="ss btn btn-xs btn-primary" ><i class="fa fa-retweet"></i> Resolved</a>';
                    }
                })
                ->editColumn('remote_address', function ($failed_login_history) {
                    return '' . $failed_login_history->remote_address . '</span>';
                })
                ->removeColumn('id', 'is_sub_admin')
                ->make(true);
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage() . ' [UC-1100]');
            return \redirect()->back();
        }
    }

    public function FailedDataResolved(request $request)
    {
        if (!ACL::getAccsessRight('user', 'E')) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
        try {
            $date = date('Y-m-d h:i:s a', time());
            $failed_login_history = DB::table('failed_login_history')->where('id', CommonFunction::vulnerabilityCheck($request->get('failed_login_id')))->first();
            DB::beginTransaction();
            DB::table('delete_login_history')->insert(
                [
                    'remote_address' => $failed_login_history->remote_address,
                    'user_email' => $failed_login_history->user_email,
                    'deleted_by' => $logged_in_user_type = Auth::user()->id,
                    'remarks' => $request->get('remarks'),
                    'created_at' => $date,
                    'updated_at' => $date
                ]
            );
            DB::table('failed_login_history')->where('id', CommonFunction::vulnerabilityCheck($request->get('failed_login_id')))->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Successfully Resolved');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage() . ' [UC-1099]');
            return \redirect()->back();
        }
    }

    /*
     * view individual user from admin panel
     */
    public function view($id, Users $Users)
    {

        if (!ACL::getAccsessRight('user', 'V')) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
        try {
            $user_id = Encryption::decodeId($id);
            $user = $Users->getUserRow($user_id);
            $working_office = IndustrialCityList::where('id', $user->office_ids)->value('name');

            $desk_id = explode(',', $user->desk_id);
            $desk = UserDesk::whereIn('id', $desk_id)->get(['desk_name']);

            $user_type_part = explode('x', $user->user_type);
            $delegateInfo = '';
            // get delegation info if user is delegated
            if ($user->delegate_to_user_id != 0) {
                $delegateInfo = Users::leftJoin('user_desk as ud', 'ud.id', '=', 'users.desk_id')
                    ->leftJoin('user_types as ut', 'ut.id', '=', 'users.user_type')
                    ->where('users.id', $user->delegate_to_user_id)
                    ->first([
                        'users.id',
                        DB::raw("CONCAT(' ', users.user_first_name, users.user_middle_name, users.user_last_name) as user_full_name"),
                        'users.desk_id', 'ut.type_name',
                        'user_email', 'user_mobile', 'users.user_type', 'ud.desk_name',
                        'designation'
                    ]);


            }


            if (count($user_type_part) > 1) {
                $user_types = UserTypes::where('status', 'active')
                    ->orderBy('type_name')
                    ->pluck('type_name', 'id')->toArray();

                $delegationInfo = '';

                if ($user->delegate_to_user_id > 0) {
                    $delegationInfo = Users::leftJoin('user_desk as ud', 'ud.id', '=', 'users.desk_id')
                        ->where('users.id', $user->delegate_to_user_id)
                        ->first([
                            'users.id',
                            DB::raw("CONCAT_WS(' ', users.user_first_name, users.user_middle_name, users.user_last_name) as user_full_name"),
                            'user_email', 'user_mobile', 'ud.desk_name'
                        ]);
                }

                return view('Users::user-profile', compact("user", "user_types", 'working_office', 'desk', 'delegationInfo', 'delegateInfo'));
            } else {
                Session::flash('error', 'User Type not defined. [UC-1096]');
                return redirect('users/lists');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . ' [UC-1098]');
            return Redirect::back()->withInput();
        }
    }


    public function verifyNID()
    {
        if (!ACL::getAccsessRight('user', '-E-')) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
        try {
            $passport_types = [
                'ordinary' => 'Ordinary',
                'diplomatic' => 'Diplomatic',
                'official' => 'Official',
            ];
            $passport_nationalities = Countries::orderby('nationality')->where('nationality', '!=', '')->where('nationality', '!=', 'Bangladeshi')
                ->pluck('nationality', 'id')->toArray();
            return view('Users::new-user-nid', compact('passport_types', 'passport_nationalities'));
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . ' [UC-1095]');
            return Redirect::back()->withInput();
        }
    }

    public function identityVerifyConfirm(Request $request)
    {
        if (!ACL::getAccsessRight('user', '-E-')) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
        try {

            $nationality_type = $request->get('nationality_type');
            $identity_type = $request->get('identity_type_bd');

            Session::put('nationality_type', $nationality_type);
            Session::put('identity_type', $identity_type);
            sleep(1);
            return \redirect('users/create-new-user');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage() . ' [UC-1026]');
            return \redirect()->back();
        }
    }

    public function createNewUser()
    {
        if (!ACL::getAccsessRight('user', 'A')) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }

        try {

            $logged_user_type = Auth::user()->user_type;
            $user_type_part = explode('x', $logged_user_type);

            if ($logged_user_type == '1x101') { // 1x101 is Sys Admin
                $user_types = UserTypes::where('is_registrable', '1')
                    ->pluck('type_name', 'id')->toArray();
            } elseif ($logged_user_type == '8x808') { // 8x808 is IT Cell
                $user_types = UserTypes::where('is_registrable', '1')
                    ->where('id', '4x404')
                    ->pluck('type_name', 'id')->toArray();
            } else {
                $user_types = UserTypes::where('id', 'LIKE', "$user_type_part[0]x" . substr($user_type_part[1], 0, 2) . "_")
                    ->where('id', 'NOT LIKE', "$user_type_part[0]_" . substr($user_type_part[1], 0, 2) . "0")
                    ->orderBy('type_name')->pluck('type_name', 'id')->toArray();
            }

            return view("Users::new-user", compact("user_types", "logged_user_type"));
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage() . '[UC-1127]');
            return \redirect()->back();
        }
    }

    public function storeNewUser(Request $request)
    {
        if (!ACL::getAccsessRight('user', 'A')) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }

        $rules = [];
        $rules['user_first_name'] = 'required';
        $rules['user_gender'] = 'required';
        $rules['user_type'] = 'required';

        $rules['user_DOB'] = 'required|date';
        $rules['user_mobile'] = 'required|bd_mobile';
        $rules['user_email'] = 'required|email|unique:users';

        $messages = [];
        $messages['user_first_name.required'] = 'The first name field is required';
        $messages['user_gender.required'] = 'The gender field is required';
        $messages['user_type.required'] = 'The user type field is required';
        $messages['user_mobile.required'] = 'The mobile number field is required';
        $messages['user_email.required'] = 'The email address field is required';
        $messages['user_DOB.required'] = 'The date of birth field is required';
        $messages['user_DOB.date'] = 'The date of birth field is required';

        $this->validate($request, $rules, $messages);
        try {

            DB::beginTransaction();

            $token_no = hash('SHA256', "-" . $request->get('user_email') . "-");
            $encrypted_token = Encryption::encodeId($token_no);


            $nationality_type = 'bangladeshi';
            $identity_type = 'none';


            if (Auth::user()->user_type == '1x101' || Auth::user()->user_type == '8x808') {
                $user_type = $request->get('user_type');
            } else {
                $user_type = Auth::user()->user_type;
            }

            $user_hash_expire_time = new Carbon('+6 hours');

            $user = new Users();
            $user->user_email = $request->get('user_email');
            $user->nationality_type = $nationality_type;
            $user->identity_type = $identity_type;
            $user->user_nid = $request->get('user_nid');
            $user->contact_address = $request->get('contact_address');


            $user->user_first_name = $request->get('user_first_name');
            $user->user_middle_name = $request->get('user_middle_name');
            $user->user_last_name = $request->get('user_last_name');
            $user->user_gender = $request->get('user_gender');

            $user->user_DOB = CommonFunction::changeDateFormat($request->get('user_DOB'), true);
            $user->user_type = $user_type;
            $user->user_mobile = $request->get('user_mobile');
            $user->user_agreement = 0;
            $user->user_verification = 'no';
            $user->first_login = 0;
            $user->last_login_type = 'general';
            $user->user_status = 'active';
            $user->user_hash = $encrypted_token;
            $user->user_hash_expire_time = $user_hash_expire_time->toDateTimeString();
            $user->working_company_id = 0;
            $user->is_approved = 1; // 1 = auto approved
            $user->save();


            // Forget session data
            Session::forget('nationality_type');
            Session::forget('identity_type');
            Session::forget('nid_info');
            Session::forget('imgPath');
            Session::forget('eTin_info');
            Session::forget('passport_info');
            Session::forget('oauth_token');
            Session::forget('oauth_data');

            $receiverInfo[] = [
                'user_email' => $request->get('user_email'),
                'user_mobile' => $request->get('user_mobile')
            ];

            $appInfo = [
                'verification_link' => url('users/verify-created-user/' . ($encrypted_token))
            ];

            CommonFunction::sendEmailSMS('CONFIRM_ACCOUNT', $appInfo, $receiverInfo);
            Session::flash('success', 'User has been created successfully! An email has been sent to the user for account activation.');

            DB::commit();
            return redirect('users/lists');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . ' [UC-1094]');
            return Redirect::back()->withInput();
        }
    }

    public function verification($confirmationCode)
    {
        try {
            $user = Users::where('user_hash', $confirmationCode)->first();
            if (!$user) {
                \Session::flash('error', 'Invalid Token! Please resend email verification link [UC-1085].');
                return redirect('login');
            }
            $currentTime = new Carbon;
            $validateTime = new Carbon($user->created_at . '+6 hours');
            if ($currentTime >= $validateTime) {
                Session::flash('error', 'Verification link is expired (validity period 6 hrs). Please sign up again! [UC-1084]');
                return redirect('/login');
            }

            $user_type = $user->user_type;
            if ($user->user_verification != 'yes') {
                $districts = ['' => 'Select one'] + Area::where('area_type', 2)->orderBy('area_nm', 'asc')->lists('area_nm', 'area_id')->all();
                return view('Users::verification', compact('user_type', 'confirmationCode', 'districts'));
            } else {
                \Session::flash('error', 'Invalid Token! Please sign up again.[UC-1092]');
                return redirect('users/reSendEmail');
            }

        } catch (\Exception $e) {
            \Session::flash('error', 'Invalid Token! Please sign up again.[UC-1093]');
            return redirect('users/reSendEmail');
        }
    }

    //When completing registration, to get thana after selecting district
    public function getThanaByDistrictId(Request $request)
    {
        try {
            $district_id = CommonFunction::vulnerabilityCheck($request->get('districtId'));

            $thanas = Area::select(DB::raw("CONCAT(AREA_ID,' ') AS AREA_ID"), 'area_nm')
                ->where('PARE_ID', $district_id)->orderBy('area_nm_ban', 'ASC')->pluck('area_nm', 'AREA_ID')->toArray();
            $data = ['responseCode' => 1, 'data' => $thanas];
            return response()->json($data);
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong [UC-1091].');
            return Redirect::back()->withInput();
        }
    }

    public function getDistrictByDivision(Request $request)
    {
        $division_id = CommonFunction::vulnerabilityCheck($request->get('divisionId'));

        $districts = Area::select(DB::raw("CONCAT(AREA_ID,' ') AS AREA_ID"), 'area_nm_ban')
            ->where('PARE_ID', $division_id)->orderBy('area_nm_ban')->pluck('area_nm_ban', 'AREA_ID')->toArray();
        $data = ['responseCode' => 1, 'data' => $districts];
        return response()->json($data);
    }

    /*
     * individual User's profile Info view
     */
    public function profileInfo()
    {
        if (!ACL::getAccsessRight('user', '-V-'))
            abort('400', 'You have no access right!. Contact with system admin for more information.');

        try {
            $desk = '';
            $park = '';
            $users = Users::find(Auth::user()->id);
            if ($users->office_ids != '') {
                $office_id = explode(',', $users->office_ids);
                $park = IndustrialCityList::whereIn('id', $office_id)->get(['name as park_name']);
            }
            if ($users->desk_id != '') {
                $desk_id = explode(',', $users->desk_id);
                $desk = UserDesk::whereIn('id', $desk_id)->get(['desk_name']);
            }
            $delegate_to_types = UserTypes::whereIn('id', array_map('trim', ['4x404']))->pluck('type_name', 'id')->toArray();
            $user_type_info = UserTypes::where('id', $users->user_type)->first();
            $id = Encryption::encodeId(Auth::user()->id);
            $pharmacyList = MedicalReceiveClinic::where('status', 1)->pluck('name', 'id')->toArray();

            return view('Users::profile-info', compact(
                'id',
                'users',
                'user_type_info',
                'desk',
                'delegate_to_types',
                'park',
                'pharmacyList'
            ));
        } catch (\Exception $e) {
            Session::flash('error', 'Something went wrong ! [UC-1089]');
            return \redirect('dashboard');
        }
    }

    public function getSinglePilgrim(Request $request, $tracking_no){
        $profileInfo = array();
        $postData = [
            'tracking_no' => Encryption::decodeId($tracking_no),
            'is_child_list' => 0
        ];
        $postdata = http_build_query($postData);
        $base_url = env('API_URL');
        $url = "$base_url/api/get-pilgrim-profile-information";
        $response = PostApiData::getData($url,$postdata);
        $response_data = json_decode($response,true);
        if ($response_data['status'] == 200) {
            $profileInfo = $response_data['data'];
            if(!empty($profileInfo['identity']['picture'])) {
                $profileInfo['identity']['picture'] = CommonFunction::getMinioUploadedFileURL($profileInfo['identity']['picture']);
            }
        }
        $is_owner = 0;
        $is_pilgrim_profile = (Auth::user()->user_type == '21x101');
        $profile = 'Users::pilgrim-portfolio';
        return view($profile,
            compact(
                'profileInfo',
                'is_pilgrim_profile',
                'is_owner',
            )
        );
    }

    public function updatePilgrimProfileBySingleColumn(Request $request){
        $profileInfo = array();
        $postData = [
            'tracking_no' => $request->tracking_no,
            'flag_column' => $request->flag_column,
            'email' => $request->email,
            'ksa_mobile_no' => $request->ksa_mobile_no,
        ];
        $postdata = http_build_query($postData);
        $base_url = env('API_URL');
        $url = "$base_url/api/update-pilgrim-profile-by-single-column";
        $response = PostApiData::getData($url,$postdata);
        $response_data = json_decode($response,true);
        if ($response_data['status'] == 200) {
            return response()->json(['responseCode' => 1]);
        }else{
            return response()->json(['responseCode' => 0, "message"=> $response_data["msg"]]);
        }
    }

    /*
     * user's account activaton
     */
    public function activate($id)
    {
        if (!ACL::getAccsessRight('user', 'E')) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
        $user_id = Encryption::decodeId($id);
        try {
            $user = Users::where('id', $user_id)->first();
            $user_active_status = $user->user_status;

            if ($user_active_status == 'active') {
                Users::where('id', $user_id)->update(['user_status' => 'inactive']);
                \Session::flash('error', "User's Profile has been deactivated Successfully!");
            } else {
                Users::where('id', $user_id)->update(['user_status' => 'active']);
                \Session::flash('success', "User's profile has been activated successfully!");
            }
            LoginController::killUserSession($user_id);
            return redirect('users/lists/');
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage(). '[UC-1129]');
            return Redirect::back()->withInput();
        }
    }

    /*
     * User's password update function
     */
    public function updatePassFromProfile(Request $request)
    {
        $userId = Encryption::decodeId($request->get('Uid'));
        if (!ACL::getAccsessRight('user', 'SPU', $userId)) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
        $dataRule = [
            'user_old_password' => 'required',
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
            return redirect('users/profileinfo#tab_2')->withErrors($validator)->withInput();
        }

        try {
            $old_password = $request->get('user_old_password');
            $new_password = $request->get('user_new_password');

            $password_match = Users::where('id', Auth::user()->id)->value('password');
            $password_chk = Hash::check($old_password, $password_match);
            $refresh_token = Session::get('sso_refresh_token');
            if ($password_chk == true) {
                $email = Auth::user()->user_email;
                $user_mobile = Auth::user()->user_mobile;
                $receiverInfo[] = [
                    'user_email' => $email,
                    'user_mobile' => $user_mobile
                ];
                $appInfo = [
                    'user_name' => Auth::user()->user_first_name
                ];

                DB::beginTransaction();
                Users::where('id', Auth::user()->id)
                    ->update(array('password' => Hash::make($new_password)));


                CommonFunction::sendEmailSMS('PASSWORD_CHANGE', $appInfo, $receiverInfo);
                DB::commit();
                WebController::backChannelLogout($refresh_token);
                Auth::logout();
                UtilFunction::entryAccessLogout();

                return redirect('login')->with('success', 'Your password has been changed successfully! Please login with the new password.');
            } else {
                \Session::flash('error', 'Password do not match [UC-1086]');
                return Redirect('users/profileinfo#tab_2')->with('status', 'error');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'Sorry! Something is Wrong [UC-1087].');
            return Redirect::back()->withInput();
        }
    }

    /*
     * password update from admin panel
     */
    public function resetPassword($id)
    {
        if (!ACL::getAccsessRight('user', 'R')) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
        try {
            $user_id = Encryption::decodeId($id);
            $password = str_random(10);

            $user_active_status = DB::table('users')->where('id', $user_id)->pluck('user_status');
            if ($user_active_status == 'active') {
                Users::where('id', $user_id)->update([
                    'password' => Hash::make($password)
                ]);

                \Session::flash('success', "User's password has been reset successfully! An email has been sent to the user!");
            } else {
                \Session::flash('error', "User profile has not been activated yet! Password can not be changed");
            }
            return redirect('users/lists');
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage().' [UC-1035]');
            return Redirect::back()->withInput();
        }
    }

    // Verifying new users created by admin
    public function verifyCreatedUser($encrypted_token)
    {
        $user = Users::where('user_hash', $encrypted_token)->first();
        if (!$user) {
            Session::flash('error', 'Invalid Token. Please try again...');
            return redirect('login');
        }
        $currentTime = new Carbon;
        $validateTime = new Carbon($user->user_hash_expire_time);
        if ($currentTime >= $validateTime) {
            Session::flash('error', 'Verification link is expired (validity period 6 hrs). Please contact to System Admin!');
            return redirect('/login');
        }

        if ($user->user_verification == 'no') {
            return view('Users::verify-created-user', compact('encrypted_token'));
        } else {
            Session::flash('error', 'Invalid Token! Please sign-up again to continue [UC-1084]');
            return redirect('/');
        }
    }

    function createdUserVerification($encrypted_token, Request $request, Users $usersmodel)
    {
        try {
            $user = Users::where('user_hash', $encrypted_token)->first();

            if (!$user) {
                Session::flash('error', 'Invalid token! Please sign up again to complete the process');
                return redirect('create');
            }

            $this->validate($request, [
                'user_agreement' => 'required',
            ]);
            DB::beginTransaction();
            $createdByInfo = Users::where('id', $user->created_by)->first(['user_mobile', 'user_email']);
            $ossPidRequestData['osspidRequest'] = array(
                'clientId' => env('osspid_client_id'),
                'secretKey' => env('osspid_client_secret_key'),
                'requestType' => 'REGISTRATION',
                'version' => '1.0',
                'requestData' => array(
                    'domain' =>  'https://dev-ehaj.oss.net.bd',
                    'userInfo' => array(
                        'name' => $user->user_first_name,
                        'email' => $user->user_email,
//                        'password' => $user_password,
                        'gender' => $user->user_gender,
                        'mobileNo' => $user->user_mobile,
                        'dob' => $user->user_DOB,
                    ),
                    'createdBy' => array(
                        'email' => (isset($createdByInfo->user_email) ? $createdByInfo->user_email : ''),
                        'contactNo' => (isset($createdByInfo->user_phone) ? $createdByInfo->user_phone : ''),
                    ),
                )
            );

            $jsonEncodeData = json_encode($ossPidRequestData);
            $encodedOssPidRequestData = urlencode($jsonEncodeData);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, env('osspid_base_url_ip') . "/osspid-reg/api?param=" . $encodedOssPidRequestData);
            curl_setopt($ch, CURLOPT_POST, 0);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, "requestData=$requested_url");
            // receive server response ...
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 150);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $response = curl_exec($ch);
            curl_close($ch);

//        $response = @file_get_contents("http://dev-mongo.eserve.org.bd:8075/osspid/api?param=".$encodedOssPidRequestData, false);

            $decodedResponseData = json_decode($response);

            $responseCode = null;
            $messageCode = null;
            if (isset($decodedResponseData->osspidResponse->responseCode)) {
                $responseCode = $decodedResponseData->osspidResponse->responseCode;
                if (isset($decodedResponseData->osspidResponse->message->code)) {
                    $messageCode = $decodedResponseData->osspidResponse->message->code;
                }
            }
            // Bad request format
            if ($responseCode == 400) {
                DB::rollback();
                Session::flash('error', 'Bad request format for OSSPID [OSSPID-400]');
                return \redirect()->back();
            }

            // Unauthorized user access tried
            // invalid client id and secret key
            elseif ($responseCode == 401) {
                if ($messageCode == 40100) {
                    Session::flash('error', 'Invalid client information, please recheck client id, secret key. [OSSPID-40100]');
                } elseif ($messageCode == 40101) {
                    Session::flash('error', 'Invalid client information, please recheck client id, secret key. [OSSPID-40101]');
                }
                DB::rollback();
                return \redirect()->back();
            } // if email already exists in OSSPID then this is verified user
            elseif ($responseCode == 412) {
                if ($messageCode == 41200) {
                    Session::flash('success', 'Your account created successfully,  You may login using previous OSSPID password. [OSSPID-41200]');
                } elseif ($messageCode == 41201) {
                    Session::flash('error', 'Data validation exception occurred. [OSSPID-41201]');
                    DB::rollback();
                    return \redirect()->back();
                } elseif ($messageCode == 41202) {
                    Session::flash('error', 'Block-Chain Verification error. [OSSPID-41202]');
                    DB::rollback();
                    return \redirect()->back();
                } elseif ($messageCode == 41203) {
                    Session::flash('error', 'Block-Chain server error occurred. [OSSPID-41203]');
                    DB::rollback();
                    return \redirect()->back();
                }
            } elseif ($responseCode == 200) {
                Session::flash('success', "Your account created successfully, Your account information has been sent into <b>(" . $user->user_email . ")</b>, You may login into Hitech-Park-OSS through OSSPID.");
            } else {
                DB::rollback();
                Session::flash('error', 'Something went wrong [OSS-420]');
                return \redirect()->back();
            }

            $user->user_verification = 'yes';
            $user->save();

            DB::commit();
            return redirect('login');
        } catch (Exception $e) {
            DB::rollback();
            Session::flash('error', 'Something went wrong [VER-1520]');
            return \redirect()->back();
        }
    }
    //////

//    function createdUserVerification($encrypted_token, Request $request, Users $Users)
//    {
//        try {
//            $user = Users::where('user_hash', $encrypted_token)->first();
//
//            if (!$user) {
//                Session::flash('error', 'Invalid token! Please sign up again to complete the process [UC-1083]');
//                return redirect('create');
//            }
//
//            $this->validate($request, [
//                'user_agreement' => 'required',
//            ]);
//
//            $dataRule = [
//                'user_new_password' => [
//                    'required',
//                    'min:6',
//                    'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{6,}$/'
//                ],
//                'user_confirm_password' => [
//                    'required',
//                    'same:user_new_password',
//                ]
//            ];
//
//            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $dataRule);
//            if ($validator->fails()) {
//                return redirect()->back()->withErrors($validator)->withInput();
//            }
//
//            DB::beginTransaction();
//
//            /**
//             * if there is a need to create the user in OSSPID then the following code should be uncommented
//             */
//
//            $new_password = $request->get('user_new_password');
//            $user->password = Hash::make($new_password);
//            $user->user_status = 'active';
//            $user->user_verification = 'yes';
//            $user->is_approved = 1;
//            $user->save();
//
//            DB::commit();
//            Session::flash('success', 'Your password set has been successfully');
//            return redirect('login');
//        } catch (Exception $e) {
//            DB::rollback();
//            Session::flash('error', 'Something went wrong [UC-1082]');
//            return \redirect()->back();
//        }
//    }

    /*
     * edit individual user from admin panel
     */
    public function edit($id)
    {
        try {
            $user_id = Encryption::decodeId($id);
            // ACL must be modified for IT admin edit permission
            $access_users = \App\Modules\Settings\Models\Configuration::where('caption','Guide_List_Side_Bar')->first();
            $access_users_array = json_decode($access_users->value2);
            if(!in_array(\Illuminate\Support\Facades\Auth::user()->user_email,$access_users_array)){
                if (!ACL::getAccsessRight('user', 'E', $user_id)) {
                    abort('400', 'You have no access right!. Contact with system admin for more information.');
                }
            }

            $users = Users::where('id', $user_id)->first();

            $logged_in_user_type = CommonFunction::getUserType();

            $user_type_part = explode('x', $logged_in_user_type);
            $edit_user_type = UserTypes::where('id', $users->user_type)->value('type_name');

            $IT_users = array('2x201', '2x202', '2x203', '2x205');
            if ($logged_in_user_type == '2x202') { // 2x202 for IT admin
                if (in_array($users->user_type, $IT_users)) {
                    $user_types = [$users->user_type => $edit_user_type] + UserTypes::where('id', 'LIKE', "$user_type_part[0]x" . substr($user_type_part[1], 0, 2) . "_")
                            ->orderBy('type_name')->pluck('type_name', 'id')
                            ->all();
                } else {
                    $user_types = [$users->user_type => $edit_user_type];
                }
            } else {

                if($users->working_user_type == 'Guide'){
                    $user_types = [$users->user_type => $edit_user_type];

                }else{
                    $user_types = UserTypes::where('id', '!=', '1X101')
                        ->where('status','active')
                        ->orderBy('type_name')->pluck('type_name', 'id')
                        ->all();
                }

            }
            $flightArray = [];

            if($users->hmis_guide_id != 0){
                $base_url = env('API_URL');
                $url = "$base_url/api/active-flight-list";
                $response = PostApiData::getData($url, []);
                $flight_data = json_decode($response);
                $flightArray = [];
                $flightArray = [''=>'Select One'];
                if($flight_data != null){
                    if(count($flight_data->data)>0){
                        $flight_list = $flight_data->data;
                        foreach($flight_list as $flight){
                            $flightArray[$flight->id] = $flight->Flight;
                        }
                    }
                }

            }



            $working_office = IndustrialCityList::where('status', 1)->where('type', 1)->orderBy('name')->pluck('name', 'id')->toArray();
            $securityProfile = [0 => 'select one'] + SecurityProfile::where('active_status', 'yes')->pluck('profile_name', 'id')->toArray();
            return view('Users::edit', compact("users", "user_types", "working_office", "securityProfile","flightArray"));

        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . ' [UC-1072]');
            return Redirect::back()->withInput();
        }
    }

    public function update($id, Request $request)
    {
        $rules = [];
        $rules['user_first_name'] = 'required';
        $rules['user_gender'] = 'required';
        $rules['user_mobile'] = 'required';

        $messages = [];
        $messages['user_first_name.required'] = 'The first name field is required';
        $messages['user_gender.required'] = 'The gender field is required';
        $messages['user_mobile.required'] = 'The mobile number field is required';
        $this->validate($request, $rules, $messages);

        $user_id = Encryption::decodeId($id);
        //          ACL must be modified for IT admin update permission
        $access_users = \App\Modules\Settings\Models\Configuration::where('caption','Guide_List_Side_Bar')->first();
        $access_users_array = json_decode($access_users->value2);
        if(!in_array(\Illuminate\Support\Facades\Auth::user()->user_email,$access_users_array)) {
            if (!ACL::getAccsessRight('user', 'E', $user_id))
                abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
        $assign_office = 0;
        $working_office_id = $request->get('working_office_id');
        if ($working_office_id != null) {
            $assign_office = implode(',', $working_office_id);
        }

        try {
            UtilFunction::entryAccessLog();
            $postData = [
                'flight_id' => $request->flight_id
            ];
            if($request->flight_id!=''){
                $postdata = http_build_query($postData);
                $base_url = env('API_URL');
                $url = "$base_url/api/flight-details";
                $response = PostApiData::getData($url,$postdata);
                $responseData= '';
                if ($response) {
                    $responseData = json_decode($response);
                }else{
                    return back()->with('error','Flight Not Found!');
                }
                $flight_data = $responseData->data[0];
            }

            Users::find($user_id)->update([
                'user_first_name' => $request->get('user_first_name'),
                'designation' => $request->get('designation'),
                'security_profile_id' => $request->get('security_profile_id'),
                'user_type' => $request->get('user_type'),
                'office_ids' => $assign_office,
                'user_gender' => $request->get('user_gender'),
                'user_mobile' => $request->get('user_mobile'),
                'updated_by' => CommonFunction::getUserId(),
                'flight_id' => $request->has('flight_id')?$request->get('flight_id'):0,
                'flight_code' => $request->flight_id!=''?$flight_data->flight_code:null,
                'departure_time' => $request->flight_id!=''?$flight_data->departure_time:null,
            ]);

            \Session::flash('success', "User's profile has been updated successfully!");
            return redirect('users/edit/' . $id);
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . ' [UC-1080]');
            return Redirect::back()->withInput();
        }
    }

    /*
     * function for approve a user
     */
    public function approveUser($id, Request $request)
    {

        $user_id = Encryption::decodeId($id);

        if (!ACL::getAccsessRight('user', '-APV-', $user_id)) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');

        }
        try {
            DB::beginTransaction();
            $user = Users::find($user_id);

            // if user don't verify his email then sysadmin can't approve this user
            if (($user->user_agreement == 0) || ($user->user_verification == 'no')) {
                DB::rollback();
                Session::flash('error', "Sorry ! This user has not verified his email yet");
                return redirect('users/lists');
            }

            // without specific user no one can take approval action
            if (!in_array(Auth::user()->user_type, ['1x101', '5x505', '6x606', '2x202'])) {
                DB::rollback();
                Session::flash('error', "Your have no right to approve it. [UC-1110]");
                return redirect('users/lists');
            }


            $user->user_status = 'active';
            $user->is_approved = 1;


            if ($request->get('user_type')) {
                $user->user_type = $request->get('user_type');
                if($user->user_type=='5x505'){
                    $user->working_user_type = 'Employee';
                }elseif ($user->user_type=='4x404'){
                    $user->working_user_type = 'Desk user';
                }
            }
            $user->save();

            $receiverInfo[] = [
                'user_email' => $user->user_email,
                'user_mobile' => $user->user_mobile
            ];

            $appInfo = [];

            CommonFunction::sendEmailSMS('APPROVE_USER', $appInfo, $receiverInfo);
            DB::commit();
            \Session::flash('success', "The user has been approved successfully!");
            return redirect('users/lists');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', 'Sorry! Something is Wrong [UC-1112].');
            return Redirect::back()->withInput();
        }
    }

    /*
     * function for reject a user
     */
    public function rejectUser($id, Request $request)
    {
        if (!ACL::getAccsessRight('user', 'E')) {
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
        try {
            DB::beginTransaction();
            $user_id = Encryption::decodeId($id);
            $reject_reason = $request->get('reject_reason');
            $userData = Users::find($user_id);

            // without specific user no one can take reject action
            if (!(Auth::user()->user_type == '1x101')) {
                DB::rollback();
                Session::flash('error', "Your have no right to approve it.");
                return redirect('users/lists');
            }

            // user reject
            Users::where('id', $user_id)->update([
                'user_status' => 'rejected',
                'user_status_comment' => $reject_reason,
                'is_approved' => 0
            ]);

            \Session::flash('error', "User's Profile has been Rejected Successfully!");

            $receiverInfo[] = [
                'user_email' => $userData->user_email,
                'user_mobile' => $userData->user_mobile
            ];
            $appInfo = [
                'reject_reason' => $reject_reason
            ];
            CommonFunction::sendEmailSMS('REJECT_USER', $appInfo, $receiverInfo);

            DB::commit();
            \Session::flash('error', "User's Profile has been Rejected Successfully!");
            return redirect('users/lists');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', 'Sorry! Something went wrong. [UC-1151]');
            return Redirect::back();
        }
    }

    public function profile_update(Request $request)
    {
        $userId = Encryption::decodeId($request->get('Uid'));
        if (!ACL::getAccsessRight('user', 'SPU', $userId))
            abort('400', 'You have no access right!. Contact with system admin for more information.');

        try {
            $rules = [];
            $rules['user_first_name'] = 'required';
            $rules['user_mobile'] = 'required';
            if (Auth::user()->user_type == '4x404') {

                $rules['designation'] = 'required';
            }
            $rules['user_DOB'] = 'required|date';
            $rules['passport_no'] = 'required_if:identity_type,1';
            $rules['user_nid'] = 'required_if:identity_type,2';
            $messages['user_first_name'] = 'First name field is required.';
            $messages['designation'] = 'Designation field is required.';
            $messages['user_mobile'] = 'Mobile number field is required.';

            $this->validate($request, $rules, $messages);
            $auth_token_allow = 0;
            if ($request->get('auth_token_allow') == '1') {
                $auth_token_allow = 1;
            }

            if (substr($request->get('user_mobile'), 0, 2) == '01') {
                $mobile_no = '+88' . $request->get('user_mobile');
            } else {
                $mobile_no = $request->get('user_mobile');
            }

            $data = [
                'user_middle_name' => $request->get('user_middle_name'),
                'user_last_name' => $request->get('user_last_name'),
                'auth_token_allow' => $auth_token_allow,
                'user_mobile' => $mobile_no,
                'contact_address' => $request->get('contact_address'),
                'designation' => $request->get('designation'),
                'updated_by' => CommonFunction::getUserId()
            ];

            //The code for face detection
            if (!empty($request->user_pic_base64)) {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = $yearMonth;
                $path_with_dir = 'news/uploads/profile-pic/' . $path;
                if (!file_exists('news/uploads/profile-pic/' . $path)) {
                    mkdir($path_with_dir, 0777, true);
                }
                $splited = explode(',', substr($request->get('user_pic_base64'), 5), 2);
                $imageData = $splited[1];
                $base64ResizeImage = base64_encode(ImageProcessing::resizeBase64Image($imageData, 300, 300));
                $base64ResizeImage = base64_decode($base64ResizeImage);
                $user_picture_name = trim(uniqid('profile_pic-' . $userId . '-', true) . '.' . 'jpeg');
                file_put_contents($path_with_dir . $user_picture_name, $base64ResizeImage);
                $data['user_pic'] = $path_with_dir . $user_picture_name;

                /*
                 * Update the session data for profile image
                 * it is required to show profile image in sidebar and topbar,
                 * otherwise, updated image not shown without re-login.
                 */
                Session::put('user_pic', $data['user_pic']);
            }
            if (!empty($request->image)) {
                $img = $request->image;
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'users/profile-pic/' . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $user_picture_name = trim(uniqid('profile_pic-' . $userId . '-', true) . '.' . 'jpeg');

                $image_parts = explode(";base64,", $img);

                $image_base64 = base64_decode($image_parts[1]);

                $data['user_pic'] = $path . $user_picture_name;

                file_put_contents($data['user_pic'], $image_base64);

                $data['user_pic'] = $yearMonth . $user_picture_name;


            }
            // users signature profile-pic
            if (!empty($request->signature_base64)) {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'users/signature/' . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $splited = explode(',', substr($request->get('signature_base64'), 5), 2);
                $imageData = $splited[1];
                $base64ResizeImageEncode = base64_encode(ImageProcessing::resizeBase64Image($imageData, 300, 80));
                $base64ResizeImage = base64_decode($base64ResizeImageEncode);
                $user_signature_name = trim(uniqid('SIGNATURE-' . $userId . '-', true) . '.' . 'jpeg');
                file_put_contents($path . $user_signature_name, $base64ResizeImage);
                $data['signature'] = $yearMonth . $user_signature_name;
                $data['signature_encode'] = $base64ResizeImageEncode;
            }

            if(Auth::user()->user_type == '18x415') {
                $data['tracking_no'] = $request->get('tracking_no') ? strtoupper(trim($request->get('tracking_no'))) : null;
            }
            Users::find($userId)->update($data);
            \Session::flash('success', 'Your profile has been updated successfully.');
            return redirect('users/profileinfo');
        } catch (\Exception $e) {
            Log::error($e->getMessage().' @@@@@ '.$e->getFile().' @@@@@ '.$e->getLine());
            Session::flash('error', 'Sorry! Something is Wrong.' . ' [UC-1072]');
            return Redirect::back()->withInput();
        }

    }

    /*
     * forcefully logout a user by admin
     */

    public function forceLogout($user_id)
    {
        if (!ACL::getAccsessRight('user', 'E'))
            abort('400', 'You have no access right!. Contact with system admin for more information.');

        $id = Encryption::decodeId($user_id);
        $loginController = new LoginController();
        $loginController::killUserSession($id);
        Session::flash('success', "User has been successfully logged out by force!");
        return redirect('users/lists');
    }

    /*
     * user support
     */

    public function getUserSession(Request $request)
    {
        if (Auth::user()) {
            $refresh_token = Session::get('sso_refresh_token');
            $encoded_login_token = Users::where('id', Auth::user()->id)->first('login_token');

            if (Encryption::decode($encoded_login_token->login_token) == Session::getId()) {
                $data = ['responseCode' => 1, 'data' => 'matched'];
            } else {
                WebController::backChannelLogout($refresh_token);
                Auth::logout();
                $data = ['responseCode' => -1, 'data' => 'not matched'];
            }
        } else {
            Auth::logout();
            $data = ['responseCode' => -1, 'data' => 'closed'];
        }
        return response()->json($data);
    }


    public function getDelegatedUserInfo(Request $request)
    {
        $userOfficeId = CommonFunction::getUserOfficeIds();
        $userType = $request->get('designation');
        $result = Users::where('user_type', '=', $userType)
            ->Where('user_status', '=', 'active')
            ->where(function ($query) use ($userOfficeId) {
                $i = 0;
                foreach ($userOfficeId as $office_id) {
                    if ($i == 0) {
                        $query->whereRaw("FIND_IN_SET('$office_id',office_ids)");
                    } else {
                        $query->orWhereRaw("FIND_IN_SET('$office_id',office_ids)");
                    }
                    $i++;
                }
            })
            ->Where(function ($result) {
                return $result->where('delegate_to_user_id', '=', null)
                    ->orWhere('delegate_to_user_id', '=', 0);
            })
            ->Where('id', '!=', Auth::user()->id)
            ->get([
                DB::raw("concat(CONCAT_WS(' ', users.user_first_name, users.user_middle_name, users.user_last_name), ' - ', user_email) as user_full_name "),
                'id'
            ]);
        echo json_encode($result);
    }

    function processDelegation(Request $request)
    {
        if (!ACL::getAccsessRight('user', '-A-')) {
            abort('400', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
        }

        try {
            $delegate_by_user_id = Auth::user()->id;
            $delegate_to_user_id = $request->get('delegated_user');
            $dependend_on_from_userid = Users::where('delegate_to_user_id', '=', $delegate_by_user_id)->get(['id', 'delegate_to_user_id']);

            DB::beginTransaction();
            foreach ($dependend_on_from_userid as $dependentUser) {
                $updateDependent = Users::findOrFail($dependentUser->id);
                $updateDependent->delegate_to_user_id = $delegate_to_user_id;
                $updateDependent->delegate_by_user_id = $delegate_by_user_id;
                $updateDependent->save();

                $delegation = new Delegation();
                $delegation->delegate_form_user = $dependentUser->id;
                $delegation->delegate_by_user_id = $delegate_by_user_id;
                $delegation->delegate_to_user_id = $delegate_to_user_id;
                $delegation->remarks = $request->get('remarks');
                $delegation->status = 1;
                $delegation->save();
            }


            $UData = array(
                'delegate_to_user_id' => $delegate_to_user_id,
                'delegate_by_user_id' => $delegate_by_user_id,
                'updated_at' => Carbon::now(),
                'updated_by' => CommonFunction::getUserId()
            );

            $isUpdated = Users::where('id', $delegate_by_user_id)
                ->orWhere('delegate_to_user_id', $delegate_by_user_id)
                ->update($UData);

            Users::where('id', $delegate_to_user_id)
                ->update(['delegated_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'updated_by' => CommonFunction::getUserId()

                ]);
            DB::commit();

            $type = Auth::user()->user_type;

            $user_type = explode('x', $type)[0];

            if ($user_type != 1 || $user_type != 2) {
                Session::put('sess_delegated_user_id', $delegate_by_user_id);
            }

            if ($isUpdated) {
                Delegation::create([
                    'delegate_form_user' => $delegate_by_user_id,
                    'delegate_by_user_id' => $delegate_by_user_id,
                    'delegate_to_user_id' => $delegate_to_user_id,
                    'remarks' => $request->get('remarks'),
                    'status' => 1,
                    'created_at' => Carbon::now(),
                    'created_by' => CommonFunction::getUserId(),
                    'updated_at' => Carbon::now(),
                    'updated_by' => CommonFunction::getUserId()
                ]);
                return back();
            } else {
                Session::flash('error', 'Delegation Not completed [UC-1087]');
                return redirect('users/profileinfo/#tab_3');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . ' [UC-1071]');
            return Redirect::back()->withInput();
        }

    }

    public function delegate()
    {
        if (!ACL::getAccsessRight('user', '-V-')) {
            abort('400', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
        }
        $delegate_to_user_id = Auth::user()->delegate_to_user_id;
        $delegate_by_user_id = Auth::user()->delegate_by_user_id;
        if ($delegate_to_user_id == 0) {
            return \redirect()->to('dashboard');
        }
        $delegate_to_info = Users::where('users.id', $delegate_to_user_id)
            ->first([
                DB::raw("CONCAT_WS(' ', users.user_first_name, users.user_middle_name, users.user_last_name) as user_full_name"),
                'user_email', 'user_mobile', 'designation', 'delegated_at'
            ]);

        $delegate_by_info = Users::where('users.id', $delegate_by_user_id)
            ->first([
                DB::raw("CONCAT_WS(' ', users.user_first_name, users.user_middle_name, users.user_last_name) as user_full_name"),
                'user_email', 'user_mobile', 'designation'
            ]);
        return view("Dashboard::delegated", compact('delegate_to_info', 'delegate_by_info'));
    }

    public function removeDelegation($DelegateId = '')
    {

        if (!ACL::getAccsessRight('user', '-E-')) {
            abort('400', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
        }

        try {
            if ($DelegateId == '') {
                $sess_user_id = Auth::user()->id;
            } else {
                $sess_user_id = Encryption::decodeId($DelegateId);
            }

            //USER INFO DELEGATION REMOVE
            Users::where('id', $sess_user_id)
                ->update(['delegate_to_user_id' => 0, 'delegate_by_user_id' => 0,

                    'updated_at' => Carbon::now(),
                    'updated_by' => CommonFunction::getUserId()
                ]);


            Users::where('delegate_by_user_id', $sess_user_id)
                ->where('id', '!=', $sess_user_id)
                ->update([
                    'delegate_to_user_id' => $sess_user_id,
                    'delegate_by_user_id' => $sess_user_id,
                    'updated_at' => Carbon::now(),
                    'updated_by' => CommonFunction::getUserId()
                ]);

            //DELEGATION HISTORY UPDATE
            Delegation::where('delegate_by_user_id', $sess_user_id)
                ->where('delegate_to_user_id', Auth::user()->delegate_to_user_id)
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->update(['remarks' => '',
                    'status' => 0,
                    'updated_at' => Carbon::now(),
                    'updated_by' => CommonFunction::getUserId(),
                ]);

            //REMOVE DELEGATION HISTORY ENTRY
            Delegation::where('delegate_by_user_id', $sess_user_id)
                ->where('delegate_to_user_id', Auth::user()->delegate_to_user_id)
                ->orderBy('created_at', 'DESC')->first();

            Session::flash('success', 'Remove Delegation Successfully');
            Session::forget('sess_delegated_user_id');

            if ($DelegateId == '') {
                return redirect("dashboard");
            } else {
                return redirect("users/delegations/" . Encryption::encodeId($sess_user_id));
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . ' [UC-1069]');
            return Redirect::back()->withInput();
        }

    }

    //System admin delegation process
    public function delegations($id)
    {
        if (!ACL::getAccsessRight('user', '-E-')) {
            abort('400', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
        }
        $delegate_to_user_id = Encryption::decodeId($id);

        // check this user is delegated or not ???
        $DelegateData = Users::where('id', $delegate_to_user_id)->first(['delegate_to_user_id', 'delegate_by_user_id']);

        $delegate_by_info = Users::where('users.id', $DelegateData->delegate_by_user_id)
            ->first([
                DB::raw("CONCAT_WS(' ', users.user_first_name, users.user_middle_name, users.user_last_name) as user_full_name"),
                'user_email', 'user_mobile', 'designation', 'delegated_at'
            ]);

        $isDelegate = $DelegateData->delegate_to_user_id;

        if ($isDelegate != 0) {

            $info = Users::leftJoin('user_desk as ud', 'ud.id', '=', 'users.desk_id')
                ->leftJoin('user_types as ut', 'ut.id', '=', 'users.user_type')
                ->where('users.id', $isDelegate)
                ->first([
                    'users.id',
                    DB::raw("CONCAT_WS(' ', users.user_first_name, users.user_middle_name, users.user_last_name) as user_full_name"),
                    'users.desk_id', 'ut.type_name',
                    'user_email', 'user_mobile', 'users.user_type', 'ud.desk_name',
                    'designation', 'delegated_at'
                ]);

            Session::put('sess_delegated_user_id', $isDelegate);
        } else {

            $info = Users::leftJoin('user_desk as ud', 'ud.id', '=', 'users.desk_id')
                ->leftJoin('user_types as ut', 'ut.id', '=', 'users.user_type')
                ->where('users.id', $delegate_to_user_id)
                ->first([
                    'users.id',
                    DB::raw("CONCAT_WS(' ', users.user_first_name, users.user_middle_name, users.user_last_name) as user_full_name"),
                    'users.desk_id', 'ut.type_name',
                    'user_email', 'user_mobile', 'users.user_type', 'ud.desk_name',
                    'designation', 'delegated_at'
                ]);
        }
        $desk_id = $info->desk_id;
        $user_type = $info->user_type;


        if ($desk_id == '' || $desk_id == 0) {
            Session::flash('error', 'Desk id is empty!');
            return redirect("users/view/" . Encryption::encodeId($delegate_to_user_id));
        }
        $deligate_to_desk_data = UserTypes::where('id', $user_type)->first(['delegate_to_types']);
        $deligate_to_type = explode(',', $deligate_to_desk_data->delegate_to_types);
        $designation = UserTypes::whereIn('id', $deligate_to_type)->pluck('type_name', 'id')->toArray();


        return view("Users::delegation", compact('isDelegate', 'delegate_to_user_id', 'info', 'designation', 'delegate_by_info'));
    }

    function storeDelegation(Request $request)
    {
        if (!ACL::getAccsessRight('user', '-E-')) {
            abort('400', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
        }
        try {
            $delegate_by_user_id = Auth::user()->id;
            $delegate_to_user_id = $request->get('delegated_user');
            $delegate_from_user_id = $request->get('user_id');

            // Get all users who are already delegated to the user who is delegating now
            $dependend_on_from_userid = Users::where('delegate_to_user_id', '=', $delegate_from_user_id)->get(['id', 'delegate_to_user_id']);

            DB::beginTransaction();

            // at first delegate all users to the new user, who are already delegated to the user who is delegating now
            foreach ($dependend_on_from_userid as $dependentUser) {

                // Update user delegation data
                $updateDependent = Users::findOrFail($dependentUser->id);
                $updateDependent->delegate_to_user_id = $delegate_to_user_id;
                $updateDependent->delegate_by_user_id = $delegate_by_user_id;
                $updateDependent->save();

                // Store delegation history
                $delegation = new Delegation();
                $delegation->delegate_form_user = $dependentUser->id;
                $delegation->delegate_by_user_id = $delegate_by_user_id;
                $delegation->delegate_to_user_id = $delegate_to_user_id;
                $delegation->remarks = $request->get('remarks');
                $delegation->status = 1;
                $delegation->save();
            }

            $data = [
                'delegate_form_user' => $delegate_from_user_id,
                'delegate_by_user_id' => $delegate_by_user_id,
                'delegate_to_user_id' => $delegate_to_user_id,
                'remarks' => $request->get('remarks'),
                'status' => 1
            ];
            Delegation::create($data);

            $udata = array(
                'delegate_to_user_id' => $delegate_to_user_id,
                'delegate_by_user_id' => $delegate_by_user_id,
            );

            $complt = Users::where('id', $delegate_from_user_id)
                ->orWhere('delegate_to_user_id', $delegate_from_user_id)
                ->update($udata);

            Users::where('id', $delegate_to_user_id)
                ->update(['delegated_at' => Carbon::now()]);

            DB::commit();

            if ($complt) {
                Session::flash('success', 'Delegation process completed Successfully');
                return redirect("users/delegations/" . Encryption::encodeId($delegate_from_user_id));
            } else {
                Session::flash('error', 'Delegation Not completed');
                return redirect("users/delegations/" . Encryption::encodeId($delegate_from_user_id));
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . ' [UC-1066]');
            return Redirect::back()->withInput();
        }
    }

    public function getDelegateUserListForAdmin(Request $request)
    {
        if (!ACL::getAccsessRight('user', '-E-')) {
            abort('400', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
        }
        try {
            $user_type = $request->get('designation');
            $delegate_form_user_id = $request->get('delegate_form_user_id');
            $userInfo = Users::where('id', $delegate_form_user_id)->first();
            $userOfficeIds = explode(',', $userInfo->office_ids);

            $result = Users::where('user_type', '=', $user_type)
                ->Where('user_status', '=', 'active')
                ->where(function ($query) use ($userOfficeIds) {
                    $i = 0;
                    foreach ($userOfficeIds as $office_id) {
                        if ($i == 0) {
                            $query->whereRaw("FIND_IN_SET('$office_id',office_ids)");
                        } else {
                            $query->orWhereRaw("FIND_IN_SET('$office_id',office_ids)");
                        }
                        $i++;
                    }
                })
                ->Where(function ($result) {
                    return $result->where('delegate_to_user_id', '=', null)
                        ->orWhere('delegate_to_user_id', '=', 0);
                })
                ->where('id', '!=', $delegate_form_user_id)
                ->get([
                    DB::raw("CONCAT_WS(' ', users.user_first_name, users.user_middle_name, users.user_last_name) as user_full_name"),
                    'id'
                ]);
            echo json_encode($result);
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . ' [UC-1065]');
            return Redirect::back()->withInput();
        }
    }

    public function twoStep()
    {
        try {
            return view("Users::two-step");
        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()).'[UC-1139]');
            return Redirect::back()->withInput();
        }
    }

    public function checkTwoStep(Request $request)
    {
        try {
            $steps = $request->get('steps');
            $code = rand(1000, 9999);
            $user_email = Auth::user()->user_email;
            $user_phone = Auth::user()->user_mobile;
            $token = $code;
            Users::where('user_email', $user_email)->update(['auth_token' => $token]);

            $receiverInfo[] = [
                'user_email' => $user_email,
                'user_mobile' => $user_phone,
            ];

            $appInfo = [
                'code' => $code,
                'verification_type' => $request->steps,
            ];
            CommonFunction::sendEmailSMS('TWO_STEP_VERIFICATION', $appInfo, $receiverInfo);

            if ($request->get('req_dta') != null) {
                $req_dta = $request->get('req_dta');
                return view("Users::check-two-step", compact('steps', 'user_email', 'user_phone', 'req_dta'));
            } else {
                return view("Users::check-two-step", compact('steps', 'user_email', 'user_phone'));
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . ' [UC-1064]');
            return Redirect::back()->withInput();
        }
    }

    public function verifyTwoStep(Request $request)
    {
        $this->validate($request, [
            'security_code' => 'required',
        ]);

        try {
            $security_code = trim($request->get('security_code'));
            $user_id = Auth::user()->id;
            $count = Users::where('id', $user_id)->where(['auth_token' => $security_code])->count();

            Users::where('id', $user_id)->update(['auth_token' => '']);

            // Profile updated related
            if ($count > 0) {
                UtilFunction::entryAccessLog();
                Session::flash('success', "Security match successfully! Welcome to OSSP platform");
                return redirect('dashboard');
            } else {
                Session::flash('error', "Security Code doesn't match." . ' [UC-1061]');
                return redirect('users/two-step');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong.' . ' [UC-1062]');
            return Redirect::back()->withInput();
        }
    }


    public function resendVerification(Request $request, $user_email = '')
    {
        if (empty($user_email) or $user_email == '') {
            $rules = [];
            $rules['email'] = 'required|email';
            $rules['g-recaptcha-response'] = 'required';

            $messages = [];
            $messages['g-recaptcha-response.required'] = 'Please check the captcha.';

            $this->validate($request, $rules, $messages);
        }

        if ($user_email != '') {
            $decoded_user_email = Encryption::decode($user_email);
        }
        try {
            $mailId = $request->get('email');

            if ($user_email != '') {
                $mailId = $decoded_user_email;
            }
            $check = Users::where('user_email', $mailId)->first();
            if (empty($check)) {
                Session::flash('error', 'Invalid email.' . ' [UC-1064]');
                return \redirect()->back();
            }

            if ($check->user_verification == 'yes') {
                Session::flash('error', 'This user already verified.' . ' [UC-1064]');
                return \redirect()->back();
            }

            $token_no = hash('SHA256', "-" . $mailId . "-");
            $encrypted_token = Encryption::encodeId($token_no);
            $data = array(
                'user_hash' => $encrypted_token,
                'user_hash_expire_time' => new Carbon('+6 hours')
            );

            $receiverInfo[] = [
                'user_email' => $mailId,
                'user_mobile' => $check->user_mobile
            ];

            $appInfo = [
                'verification_link' => url('users/verify-created-user/' . ($encrypted_token))
            ];

            CommonFunction::sendEmailSMS('CONFIRM_ACCOUNT', $appInfo, $receiverInfo);

            Users::where('user_email', $mailId)
                ->update($data);

            Session::flash('success', 'Verification email resent successfully.');
            return \redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something is Wrong. ' . $e->getMessage() . ' [UC-1060]');
            return Redirect::back()->withInput();
        }
    }

    public function resendVerificationFromAdmin(Request $request)
    {
        if (!ACL::getAccsessRight('user', '-E-')) {
            abort('400', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
        }

        $user = Users::where('id', Encryption::decodeId(last(request()->segments())))->first([
            'user_email',
            'user_verification',
            'user_mobile',
        ]);

        try {
            if (empty($user->user_email)) {
                Session::flash('error', 'Invalid email.');
                return \redirect()->back();
            }
            if ($user->user_verification == 'yes') {
                Session::flash('error', 'This user already verified.');
                return \redirect()->back();
            }
            $token_no = hash('SHA256', "-" . $user->user_email . "-");
            $encrypted_token = Encryption::encodeId($token_no);
            $data = array(
                'user_hash' => $encrypted_token,
                'user_hash_expire_time' => new Carbon('+6 hours')
            );

            $receiverInfo[] = [
                'user_email' => $user->user_email,
                'user_mobile' => $user->user_mobile
            ];

            $appInfo = [
                'verification_link' => url('users/verify-created-user/' . ($encrypted_token))
            ];

            CommonFunction::sendEmailSMS('CONFIRM_ACCOUNT', $appInfo, $receiverInfo);

            Users::where('user_email', $user->user_email)
                ->update($data);
            Session::flash('success', 'Verification email resent successfully.');
            return \redirect()->back();
        } catch (\Exception $e) {
            return Redirect::back()->withInput();
        }
    }

    public function getPilgrimRefundBankBranch(Request $request) {
        $profileInfo = array();
        $postData = [
            'districtId' => $request->districtId,
            'bankId' => $request->bankId,

        ];
        $postdata = http_build_query($postData);
        $base_url = env('API_URL');
        $url = "$base_url/api/get-pilgrim-refund-bank-branch";
        $response = PostApiData::getData($url,$postdata);
        $response_data = json_decode($response,true);

        if (!empty($response_data) && isset($response_data['status']) &&  $response_data['status']== 200) {
            return response()->json(['responseCode' => 1, 'data' => $response_data['data']]);
        }else{
            return response()->json(['responseCode' => 0]);
        }
    }

    public function getPilgrimMaharamRefundBankInfo(Request $request) {
        $postData = [
            'maharamId' => $request->maharamId,
            'dependentId' => $request->dependentId,
        ];
        $postdata = http_build_query($postData);
        $base_url = env('API_URL');
        $url = "$base_url/api/get-pilgrim-maharam-bank-information";
        $response = PostApiData::getData($url,$postdata);
        $response_data = json_decode($response,true);

        if (!empty($response_data) && isset($response_data['status']) &&  $response_data['status']== 200) {
            return response()->json(['responseCode' => 1, 'data' => $response_data['data']]);
        }else{
            return response()->json(['responseCode' => 0]);
        }
    }

    public function storePilgrimRefundBankAccount(Request $request) {
        $is_pilgrim_profile = (Auth::user()->user_type == '21x101' || Auth::user()->user_type == '18x415');
        if($is_pilgrim_profile) {
            if(!empty(Auth::user()->user_email)) {
                $trackingNO = isset($request->tracking_no) ? Encryption::decodeId($request->tracking_no) : '';
                if($trackingNO != Auth::user()->tracking_no) {
                    Session::flash('error', 'User tracking no not match');
                    return redirect()->back()->withInput();
                }
                if(isset($request->paymentReceiveType) && $request->paymentReceiveType == 'Nearest Relative') {
                    $validated = $request->validate([
                        'paymentReceiveType' => 'required|string',
                        'accountHolderName' => 'required|string|regex:/^[a-zA-Z0-9\s.()]+$/',
                        'accountNumber' => 'required|numeric|min:13',
                        'refund_district_id' => 'required',
                        'relation' => 'required|regex:/^[a-zA-Z0-9\s.()]+$/',
                        'refund_bank_id' => 'required',
                        'refund_branch_id' => 'required',
                        'tracking_no' => 'required',
                    ]);
                    if(strlen($request->accountNumber) < 13) {
                        return redirect()->back()->withInput();
                    }
                } elseif(isset($request->paymentReceiveType) && $request->paymentReceiveType != 'Nearest Relative') {
                    $validated = $request->validate([
                        'paymentReceiveType' => 'required|string',
                        'accountHolderName' => 'required|string|regex:/^[a-zA-Z0-9\s.()]+$/',
                        'accountNumber' => 'required|numeric|min:13',
                        'refund_district_id' => 'required',
                        'refund_bank_id' => 'required',
                        'refund_branch_id' => 'required',
                        'tracking_no' => 'required',
                    ]);
                    if(strlen($request->accountNumber) < 13) {
                        return redirect()->back()->withInput();
                    }
                } else {
                    return redirect()->back()->withInput();
                }
                $trackingNO = isset($request->tracking_no) ? Encryption::decodeId($request->tracking_no) : '';
                $postData = [
                    'tracking_no' => Auth::user()->tracking_no,
                    'is_child_list' => 1
                ];
                $postdata = http_build_query($postData);
                $base_url = env('API_URL');
                $url = "$base_url/api/get-pilgrim-refund-bank-information";
                $response = PostApiData::getData($url,$postdata);
                $response_data = json_decode($response,true);

                if(count($response_data['data']['existingProcess']) > 0) {
                    Session::flash('error', 'আপনার একটি আবেদন মন্ত্রনালয়ের অনুমোদন এর অপেক্ষায় আছে');
                    return redirect()->back()->withInput();
                }

                $pilgrimOldBankInfo = [
                    'trackingNo' => $trackingNO,
                    'ownerType' => $response_data['data']['owner_type'],
                    'accountName' => $response_data['data']['account_name'],
                    'accountNumber' => $response_data['data']['account_number'],
                    'guardianId' => $response_data['data']['guardian_id'],
                    'relation' => $response_data['data']['relation'],
                    'routing_no' => $response_data['data']['routing_no'],
                    'routing_no_id' => $response_data['data']['id'],
                    'bankName' => $response_data['data']['bank_name'],
                    'distName' => $response_data['data']['dist_name'],
                    'branchName' => $response_data['data']['branch_Name'],
                ];
                $pilgrimOldBankInfo_json = json_encode($pilgrimOldBankInfo);

                $pilgrimNewBankInfo = [
                    'trackingNo' => $trackingNO,
                    'ownerType' => $request->paymentReceiveType,
                    'accountName' => $request->accountHolderName,
                    'accountNumber' => $request->accountNumber,
                    'guardianId' => isset($request->guardianId) ? $request->guardianId : 0,
                    'relation' => isset($request->relation) ? $request->relation : 0,
                    'bankId' => $request->refund_bank_id,
                    'distCode' => $request->refund_district_id,
                    'branchRoutingNo' => $request->refund_branch_id,
                ];
                $pilgrimNewBankInfo_json = json_encode($pilgrimNewBankInfo);

                $postData2 = [
                    'prevData' => $pilgrimOldBankInfo_json,
                    'newData' => $pilgrimNewBankInfo_json,
                    'pilgrimId' => $response_data['data']['pilgrim_id'],
                    'pilgrim_bank_account_info_id' => $response_data['data']['pilgrim_bank_account_info_id'],
                    'userId' => Auth::user()->id,
                    'tracking_no' => $trackingNO
                ];
                $postdata2 = http_build_query($postData2);
                $bankAccReqUrl = "$base_url/api/store-pilgrim-refund-bank-request";
                $response2 = PostApiData::getData($bankAccReqUrl,$postdata2);
                $response_data2 = json_decode($response2, true);

                if($response_data2['status'] == 200) {
                    Session::flash('success', $response_data2['data']);
                    return redirect()->back();
                } else {
                    Session::flash('error', $response_data2['msg']);
                    return redirect()->back()->withInput();
                }

            } else {
                Session::flash('error', 'User not authenticate');
                return redirect()->back()->withInput();
            }
        } else {
            return redirect()->back()->withInput();
        }
    }

    public function updateWorkingUserType(Request $request) {
        try {
            if(empty($request->get('value'))) {
                return response()->json(['responseCode' => -1, 'message' => 'Working User Type required']);
            } else if(!in_array(strtolower($request->get('value')), ['pilgrim', 'guide'])) {
                return response()->json(['responseCode' => -1, 'message' => 'Working User Type should be Pilgrim or Guide']);
            }
            $working_user_type = ucfirst($request->get('value'));

            if($working_user_type == Auth::user()->working_user_type) {
                return response()->json(['responseCode' => -1, 'message' => 'You are select same working user type']);
            }

            $base_url = env('API_URL');
            $token = CommonFunction::getTokenData();

            if (!$token) {
                $msg = 'Sorry Invalid token';
                return response()->json(['responseCode' => -1, 'msg' => $msg]);
            }
            $apiUrl = "$base_url/api/update-user-working-user-type";

            $headers = [
                'APIAuthorization:' . $token,
                'Content-Type: application/json',
            ];

            $postData = [
                'working_user_type' => $working_user_type,
                'prp_user_id' => Encryption::encodeId(Auth::user()->prp_user_id),
            ];

            $apiResponse = CommonFunction::curlPostRequest($apiUrl,json_encode($postData), $headers,true);

            if($apiResponse['http_code'] !== 200) {
                $decodeResponse = json_decode($apiResponse['data'], true);
                return response()->json(['responseCode' => -1, 'message' => $decodeResponse['msg']]);
            }

            $result = Users::where('id', Auth::user()->id)->update(['working_user_type' => $working_user_type]);
            if($result) {
                return response()->json([
                    'responseCode' => 1,
                    'message' => 'Working User Type Updated Successfully',
                    'data' => [
                        'value' => $working_user_type
                    ]
                ]);
            }
            return response()->json(['responseCode' => -1, 'message' => 'Working User Type not updated']);

        } catch(\Exception $e) {
            return response()->json(['responseCode' => -1, 'message' => 'Working User Type not updated']);
        }
    }
}
