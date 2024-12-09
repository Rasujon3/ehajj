<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\CompanyProfile\Models\CompanyInfo;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessStatus;
use App\Modules\ProcessPath\Models\UserDesk;
use App\Modules\Settings\Models\ApplicationRollback;
use App\Modules\Settings\Models\Area;
use App\Modules\Settings\Models\ForcefullyDataUpdate;
use App\Modules\Settings\Models\MaintenanceModeUser;
use App\Modules\Users\Models\Users;
use App\Modules\Users\Models\UserTypes;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Session;

class SettingsController extends Controller
{

    public function __construct()
    {
        if (Session::has('lang'))
            \App::setLocale(Session::get('lang'));
        ACL::db_reconnect();
    }




    /**************** Ending of Security related Functions *********/

    /**************** Starting of Maintenance mode related Functions *********/
    public function maintenanceMode()
    {
        $user_types = UserTypes::all([
            'id as type', 'type_name'
        ]);

        $maintenance_data = MaintenanceModeUser::findOrNew(1);

        $allowed_user_array = (empty($maintenance_data->allowed_user_ids) ? [] : explode(',',
            $maintenance_data->allowed_user_ids));

        $users = Users::leftjoin('user_types', 'user_types.id', '=', 'users.user_type')
            ->whereIn('users.id', $allowed_user_array)
            ->get([
                'users.id',
                'users.user_email',
                'users.user_first_name',
                'users.user_middle_name',
                'users.user_last_name',
                'user_types.type_name',
                'users.user_mobile'
            ]);
        return view('Settings::maintenance-mode.add-form', compact('user_types', 'maintenance_data', 'users'));
    }

    public function maintenanceModeStore(Request $request)
    {
        //dd($request->all());
        if ($request->has('submit_btn') && $request->get('submit_btn') == 'add_user') {
            $this->validate($request, [
                'user_email' => 'required|email'
            ]);
        } else {
            $rules = [];
            $rules['alert_message'] = 'required_if:operation_mode,==,2';
            $rules['operation_mode'] = 'required|numeric';

            $messages = [];
            $messages['alert_message.required_if'] = 'The alert message field is required when operation mode is Maintenance.';
            $this->validate($request, $rules, $messages);
        }


        try {

            if ($request->has('submit_btn') && $request->get('submit_btn') == 'add_user') {
                $user = Users::where('user_email', $request->get('user_email'))->first(['id']);
                if ($user) {
                    $maintenance_data = MaintenanceModeUser::find(1);
                    $allowed_user_array = (empty($maintenance_data->allowed_user_ids) ? [] : explode(',',
                        $maintenance_data->allowed_user_ids));

                    if (in_array($user->id, $allowed_user_array)) {
                        Session::flash('error', 'This user is already added [SC-320]');
                        return Redirect::back()->withInput();
                    }
                    array_push($allowed_user_array, $user->id);
                    $maintenance_data->allowed_user_ids = implode(',', $allowed_user_array);
                    $maintenance_data->save();
                    Session::flash('success', 'The user has been added successfully');
                    return Redirect::back()->withInput();
                }
                Session::flash('error', 'Invalid user email [SC-321]');
                return Redirect::back()->withInput();
            } else {
                $maintenance_data = MaintenanceModeUser::findOrNew(1);
                $maintenance_data->allowed_user_types = (empty($request->get('user_types')) ? '' : implode(',',$request->get('user_types')));
                $maintenance_data->alert_message = $request->get('alert_message');
                $maintenance_data->operation_mode = $request->get('operation_mode');
                $maintenance_data->save();

                //get all active user
                $getActiveUser = DB::select("select id, login_token from users where user_status = 'active' and login_token != '' and id not in ($maintenance_data->allowed_user_ids)");
                if ($getActiveUser) {
                    //logout all active users
                    foreach ($getActiveUser as $value) {
                        $sessionID = Encryption::decode($value->login_token);
                        session::getHandler()->destroy($sessionID);
                    }
                    //update user login_token
                    DB::statement("UPDATE users SET login_token = '' WHERE user_status = 'active' and id not in ($maintenance_data->allowed_user_ids) and login_token !='' ");
                }
                //end logout

                Session::flash('success', 'Maintenance mode saved successfully!');
                return Redirect::back();
            }
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->get(), $e->getFile());
            Session::flash('error', 'Sorry! Something Wrong.[SC-322]');
            return Redirect::back()->withInput();
        }
    }

    public function removeUserFromMaintenance($user_id)
    {
        $user_id = Encryption::decodeId($user_id);

        $maintenance_data = MaintenanceModeUser::find(1);

        $users_array = explode(',', $maintenance_data->allowed_user_ids);
        if (($key = array_search($user_id, $users_array)) !== false) {
            unset($users_array[$key]);
        }

        $maintenance_data->allowed_user_ids = (empty($user_id) ? '' : implode(',', $users_array));
        $maintenance_data->save();
        Session::flash('success', 'The user has been removed from allowed users.[SC-323]');
        return Redirect::back()->withInput();
    }
    /**************** Ending of Maintenance mode related Functions *********/


    public function get_district_by_division_id(Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'V')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $divisionId = $request->get('divisionId');

        $districts = Area::where('PARE_ID', $divisionId)->orderBy('AREA_NM', 'ASC')->pluck('AREA_NM', 'AREA_ID')->toArray();
        $data = ['responseCode' => 1, 'data' => $districts];
        return response()->json($data);
    }

    public function getPoliceStations(Request $request)
    {
        if ($request->get('lang') && $request->get('lang') == 'en') {
            $areaField = 'area_info.area_nm';
        } else {
            $areaField = 'area_info.area_nm_ban';
        }

        $data = ['responseCode' => 0, 'data' => ''];
        $area = Area::where($areaField, $request->get('districtId'))->where('area_type', 2)->first();
        if ($area) {
            $area_id = $area->area_id;
            $get_data = Area::where('pare_id', DB::raw($area_id))
                ->whereNotNull($areaField)
                ->where('area_type', 3)
                ->select($areaField)
                ->orderBy($areaField)
                ->lists($areaField);

            $data = ['responseCode' => 1, 'data' => $get_data];
        }
        return response()->json($data);
    }



    public function uploadDocument()
    {
        return View::make('Settings::ajaxUploadFile');
    }


    //Application Rollback

    public function applicationRollbackList()
    {

        if (!in_array(Auth::user()->user_type, ["1x101","4x404"])) {
            die('You have no access right! Please contact system administration for more information. [[AR-1025]');
        }
        return view('Settings::app_rollback.list');
    }

    public function getApplicationList()
    {

        $list = ApplicationRollback::applicationRollbackList();

        return Datatables::of($list)
            ->editColumn('status_id', function ($list) {
                if ($list->status_id == 1) {
                    return "<span class='badge badge-primary'>Submit</span>";
                } elseif ($list->status_id == 25) {
                    return "<span class='badge badge-success'>Approved</span>";
                } elseif ($list->status_id == 6) {
                    return "<span class='badge badge-danger'>Rejected</span>";
                }
            })
            ->editColumn('last_modified', function ($list) {
                return $list->modified_user . '<br>' . $list->updated_at;
            })
            ->addColumn('action', function ($list) {
                return '<a href="' . URL::to('settings/app-rollback-view/' . Encryption::encodeId($list->id)) .
                    '" class="btn btn-primary btn-xs"><i class="fa fa-folder-open"></i> Open</a> ';

            })
            ->rawColumns(['status_id','last_modified','action'])
            ->removeColumn('id')
            ->make(true);
    }

    public function applicationSearch()
    {
        if (!in_array(Auth::user()->user_type, ['1x101','4x404'])) {
            Session::flash('error', 'You have no access right! Please contact system administration for more information. [AR-1030]');
            return redirect()->back();
        }
        return view('Settings::app_rollback.search');
    }

    public function applicationRollbackOpen(Request $request)
    {
        $user_type = Auth::user()->user_type;

        if (!in_array($user_type, ['1x101','4x404'])) {
            Session::flash('error', 'You have no access right! Please contact system administration for more information. [AR-1035]');
            return redirect()->back();
        }

        try {
            $trackingNo = trim($request->get('tracking_no'));

            $appInfo = ProcessList::leftJoin('users', 'process_list.user_id', '=', 'users.id')
                ->leftJoin('user_desk', 'process_list.desk_id', '=', 'user_desk.id')
                ->leftJoin('company_info', 'process_list.company_id', '=', 'company_info.id')
//                ->leftJoin('office', 'process_list.department_id', '=', 'department.id')
//                ->leftJoin('sub_department', 'process_list.sub_department_id', '=', 'sub_department.id')
                ->leftJoin('process_status', function ($join) {
                    $join->on('process_list.process_type_id', '=', 'process_status.process_type_id')
                        ->on('process_list.status_id', '=', 'process_status.id');
                })
                ->leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
                ->where('process_list.tracking_no', $trackingNo)
                ->orderBy('id', 'desc')
                ->first([
                    DB::raw("CONCAT(users.user_first_name, ' ', users.user_middle_name, ' ', users.user_last_name) as desk_user_name"),
                    'user_desk.desk_name',
                    'process_status.status_name',
                    'company_info.org_nm',
//                    'department.name as department_name',
//                    'sub_department.name as sub_department_name',
                    'process_type.form_id',
                    'process_type.form_url',
                    'process_list.*',
                ]);

            if (empty($appInfo)) {
                Session::flash('error', 'Sorry! Application not found');
                return redirect()->back()->withInput();
            }

            if ($user_type == '4x404') { // Desk User

                /*
                |--------------------------------------------------------------------------
                | Only active services can be rollback from the desk user
                |--------------------------------------------------------------------------
                | 13	=   IRC Recommendation 1st adhoc
                |
                */
                if (!in_array($appInfo->process_type_id, [13])) {
                    Session::flash('error', 'Sorry! this service is not allowed to rollback.');
                    return redirect()->back()->withInput();
                }

                /*
                |--------------------------------------------------------------------------
                | The application can be rollback until the next desk user takes any action
                |--------------------------------------------------------------------------
                |
                */
                if ($appInfo->updated_by != Auth::user()->id) {
                    Session::flash('error', 'Sorry! you have no right to rollback the application because someone has already taken action.');
                    return redirect()->back()->withInput();
                }

                /*
                |--------------------------------------------------------------------------
                | Some status will not be rollback from the desk user
                |--------------------------------------------------------------------------
                | 25	=   approved
                |
                */
                if (in_array($appInfo->status_id, [25])) {
                    Session::flash('error', 'Sorry! the approved application will not be rollback.');
                    return redirect()->back()->withInput();
                }
            }

            // get application open url
            $openAppRoute = 'process/' . $appInfo->form_url . '/view/' . Encryption::encodeId($appInfo->ref_id) . '/' . Encryption::encodeId($appInfo->process_type_id);
            // get corresponding basic information application ID
//            $basicAppID = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
//                ->where('process_list.process_type_id', 100)
//                ->where('process_list.status_id', 25)
//                ->where('process_list.company_id', $appInfo->company_id)
//                ->first(['process_list.ref_id', 'process_list.process_type_id', 'process_list.department_id', 'ea_apps.*']);
//            if ($basicAppID->applicant_type == 'New Company Registration') {
//                $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('NCR') . '/' . Encryption::encodeId($appInfo->company_id);
//            } else if ($basicAppID->applicant_type == 'Existing Company Registration') {
//                $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('ECR') . '/' . Encryption::encodeId($appInfo->company_id);
//            } else if ($basicAppID->applicant_type == 'Existing User for BIDA services') {
//                $BiRoute = 'basic-information/form-bida/' . Encryption::encodeId('EUBS') . '/' . Encryption::encodeId($appInfo->company_id);
//            } else {
//                $BiRoute = 'basic-information/form-bida/' . Encryption::encodeId('NUBS') . '/' . Encryption::encodeId($appInfo->company_id);
//            }

            $status = ['' => 'Select One'] + ProcessStatus::where('process_type_id', $appInfo->process_type_id)
                    ->whereNotIn('id', [19])->pluck('status_name', 'id')->toArray();

            $desk = ['' => 'Select One', 0 => 'Applicant'] + UserDesk::where('id', '<', 6)
                    ->pluck('desk_name', 'id')->toArray();
            return view('Settings::app_rollback.open', compact('appInfo', 'openAppRoute', 'status', 'desk'));
        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[AR-1075]");
            return \redirect()->back();
        }
    }

    protected function getUserByDesk(Request $request)
    {
        $desk_to = trim($request->get('desk_to'));

        $sql = "SELECT id as user_id,user_first_name as user_full_name from users WHERE is_approved = 1
                AND user_status='active' AND desk_id != 0
                AND desk_id REGEXP '^([0-9]*[,]+)*$desk_to([,]+[,0-9]*)*$'";
        $userList = DB::select(DB::raw($sql));

        $data = ['responseCode' => 1, 'data' => $userList];
        return response()->json($data);
    }

    public function applicationRollbackUpdate(Request $request)
    {
        $user_type = Auth::user()->user_type;

        if (!in_array($user_type, ['1x101','4x404'])) {
            Session::flash('error', 'You have no access right! Please contact system administration for more information. [AR-1036]');
            return redirect()->back();
        }

        $rules = [];
        $messages = [];

        $rules['remarks'] = 'required';
        $messages['remarks.required'] = 'Remarks field is required';

        if ($user_type != '4x404') {
            $rules['status_id'] = 'required';
            $rules['desk_id'] = 'required';

            $messages['status_id.required'] = 'Apply status field is required';
            $messages['desk_id.required'] = 'Send to desk field is required';
        }

        $this->validate($request, $rules, $messages);

        try {
            DB::beginTransaction();

            $decodedId = Encryption::decodeId($request->get('process_list_id'));
            $processData = ProcessList::find($decodedId);

            $presentInfo = ProcessList::leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('process_status as ps', function ($join) use ($processData) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($processData->process_type_id));
                })
                ->leftJoin('users', 'users.id', '=', 'process_list.user_id')
//                ->leftJoin('board_meting', 'board_meting.id', '=', 'process_list.bm_process_id')
                ->where('process_list.id', $decodedId)
                ->first([
                    'process_list.id',
                    'process_list.status_id',
                    'process_list.desk_id',
                    'process_list.user_id',
                    'ps.status_name',
                    'user_desk.desk_name',
                    'process_list.process_desc',
//                    'process_list.resend_deadline',
//                    'process_list.bm_process_id',
//                    'board_meting.meting_number',
                    DB::raw("CONCAT(users.user_first_name,' ',users.user_middle_name, ' ',users.user_last_name,  '', concat(' (',  user_email, ')')) as user_full_name")
                ]);

//            if ($user_type == '4x404') { // desk user
//                $processHistoryInfo = ProcessHistory::leftJoin('board_meting', 'board_meting.id', '=', 'process_list_hist.bm_process_id')
//                    ->where('process_list_hist.process_id',$presentInfo->id)
//                    ->orderBy('process_list_hist.id','desc')
//                    ->skip(1)
//                    ->take(1)
//                    ->first([
//                        'process_list_hist.id',
//                        'process_list_hist.status_id',
//                        'process_list_hist.user_id',
//                        'process_list_hist.desk_id',
//                        'process_list_hist.process_desc',
//                        'process_list_hist.resend_deadline',
//                        'process_list_hist.bm_process_id',
//                        'board_meting.meting_number',
//                    ]);
//
//                $desk_id = $processHistoryInfo->desk_id;
//                $status_id = $processHistoryInfo->status_id;
//                $assignUserId = $processHistoryInfo->user_id;
//            } else { // system admin
                $desk_id = $request->get('desk_id');
                $status_id = $request->get('status_id');
                $assignUserId = empty($request->get('is_user')) ? 0 : $request->get('is_user');
//            }

            $ChangeInfo = ProcessList::leftJoin('user_desk', 'user_desk.id', '=', DB::raw($desk_id))
                ->leftJoin('process_status as ps', function ($join) use ($processData, $status_id) {
                    $join->on('ps.id', '=', DB::raw($status_id));
                    $join->on('ps.process_type_id', '=', DB::raw($processData->process_type_id));
                })
                ->leftJoin('users', 'users.id', '=', DB::raw($assignUserId))
                ->where('process_list.id', $decodedId)
                ->first([
                    'ps.status_name',
                    'user_desk.desk_name',
                    DB::raw("CONCAT(users.user_first_name,' ',users.user_middle_name, ' ',users.user_last_name,  '', concat(' (',  user_email, ')')) as user_full_name")
                ]);

            $jsonData[] = [
                'caption' => 'Status',
                'old_id' => $presentInfo->status_id,
                'old_value' => $presentInfo->status_name,
                'new_id' => $status_id,
                'new_value' => $ChangeInfo->status_name
            ];
            $jsonData[] = [
                'caption' => 'Desk',
                'old_id' => $presentInfo->desk_id,
                'old_value' => $presentInfo->desk_name,
                'new_id' => $desk_id,
                'new_value' => $ChangeInfo->desk_name
            ];
            $jsonData[] = [
                'caption' => 'User',
                'old_id' => empty($presentInfo->user_id) ? 0 : $presentInfo->user_id,
                'old_value' => empty($presentInfo->user_id) ? 'N/A' : $presentInfo->user_full_name,
                'new_id' => $assignUserId,
                'new_value' => empty($assignUserId) ? 'N/A' : $ChangeInfo->user_full_name,
            ];

//            if ($user_type == '4x404') {
//                if (!empty($presentInfo->bm_process_id) || !empty($processHistoryInfo->bm_process_id)) {
//                    $jsonData[] = [
//                        'caption' => 'Meeting Number',
//                        'old_id' => $presentInfo->bm_process_id,
//                        'old_value' => $presentInfo->meting_number,
//                        'new_id' => $processHistoryInfo->bm_process_id,
//                        'new_value' => $processHistoryInfo->meting_number,
//                    ];
//                }
//
//                if ($presentInfo->resend_deadline != '0000-00-00' || $processHistoryInfo->resend_deadline != '0000-00-00') {
//                    $jsonData[] = [
//                        'caption' => 'Resend Deadline',
//                        'old_value' => $presentInfo->resend_deadline,
//                        'new_value' => $processHistoryInfo->resend_deadline,
//                    ];
//                }
//
//                $jsonData[] = [
//                    'caption' => 'Process Remarks',
//                    'old_value' => $presentInfo->process_desc,
//                    'new_value' => $processHistoryInfo->process_desc,
//                ];
//            }

            $jsonDataEncoded = json_encode($jsonData);

            $rollbackData = new ApplicationRollback();
            $rollbackData->app_tracking_no = $processData->tracking_no;
            $rollbackData->data = $jsonDataEncoded;
            $rollbackData->remarks = $request->get('remarks');
            $rollbackData->status_id = 25;
            $rollbackData->save();

            if (empty($rollbackData->tracking_no)) {
                $trackingPrefix = 'AR-' . date("dMY") . '-';
                DB::statement("update  application_rollback_list, application_rollback_list as table2  SET application_rollback_list.tracking_no=(
                                select concat('$trackingPrefix',
                                        LPAD( IFNULL(MAX(SUBSTR(table2.tracking_no,-5,5) )+1,1),5,'0')
                                              ) as tracking_no
                                 from (select * from application_rollback_list ) as table2
                                 where table2.id!='$rollbackData->id' and table2.tracking_no like '$trackingPrefix%'
                            )
                          where application_rollback_list.id='$rollbackData->id' and table2.id='$rollbackData->id'");
            }

            $processData->status_id = $status_id;
            $processData->desk_id = $desk_id;
            $processData->user_id = $assignUserId;
//            if ($user_type == '4x404') {
//                $processData->bm_process_id = $processHistoryInfo->bm_process_id;
//                $processData->resend_deadline = $processHistoryInfo->resend_deadline;
//                $processData->process_desc = $processHistoryInfo->process_desc;
//            } else {
                $processData->process_desc = $request->get('remarks');
//            }
            $processData->save();

            DB::commit();
            Session::flash('success', 'Successfully application rollbacked !');
            return redirect('/settings/app-rollback');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('ArStore : ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' [AR-1060]');
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . ' [AR-1060]');
            return redirect()->back()->withInput();
        }

    }

    public function viewApplicationRollback($encoded_id)
    {
        if (!in_array(Auth::user()->user_type, ['1x101','4x404'])) {
            Session::flash('error', 'You have no access right! Please contact system administration for more information. [AR-1040]');
            return redirect()->back();
        }

        try {
            $id = Encryption::decodeId($encoded_id);
            $rollbackAppInfo = ApplicationRollback::where('id', $id)
                ->first();

            $appInfo = ProcessList::leftJoin('users', 'process_list.user_id', '=', 'users.id')
                ->leftJoin('user_desk', 'process_list.desk_id', '=', 'user_desk.id')
                ->leftJoin('company_info', 'process_list.company_id', '=', 'company_info.id')
//                ->leftJoin('department', 'process_list.department_id', '=', 'department.id')
//                ->leftJoin('sub_department', 'process_list.sub_department_id', '=', 'sub_department.id')
                ->leftJoin('process_status', function ($join) {
                    $join->on('process_list.process_type_id', '=', 'process_status.process_type_id')
                        ->on('process_list.status_id', '=', 'process_status.id');
                })
                ->leftJoin('process_type', 'process_list.process_type_id', '=', 'process_type.id')
                ->where('process_list.tracking_no', $rollbackAppInfo->app_tracking_no)
                ->orderBy('id', 'desc')
                ->first([
                    DB::raw("CONCAT(users.user_first_name, ' ', users.user_middle_name, ' ', users.user_last_name) as desk_user_name"),
                    'user_desk.desk_name',
                    'process_status.status_name',
                    'company_info.org_nm',
                    'company_info.org_nm_bn',
//                    'department.name as department_name',
//                    'sub_department.name as sub_department_name',
                    'process_type.form_id',
                    'process_type.form_url',
                    'process_list.*',
                ]);

            if ($appInfo == '') {
                Session::flash('error', 'Sorry! Application not found. [AR-1070]');
                return Redirect::back();
            }
            //get application open url
            $redirectPath = CommonFunction::getAppRedirectPathByJson($appInfo->form_id);
            $openAppRoute = 'process/' . $appInfo->form_url . '/' . $redirectPath['view'] . '/' . Encryption::encodeId($appInfo->ref_id) . '/' . Encryption::encodeId($appInfo->process_type_id);

            // get corresponding basic information application ID
//            $basicAppID = ProcessList::leftjoin('ea_apps', 'ea_apps.id', '=', 'process_list.ref_id')
//                ->where('process_list.process_type_id', 100)
//                ->where('process_list.status_id', 25)
//                ->where('process_list.company_id', $appInfo->company_id)
//                ->first(['process_list.ref_id', 'process_list.process_type_id', 'process_list.department_id', 'ea_apps.*']);
//            if ($basicAppID->applicant_type == 'New Company Registration') {
//                $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('NCR') . '/' . Encryption::encodeId($appInfo->company_id);
//            } else if ($basicAppID->applicant_type == 'Existing Company Registration') {
//                $BiRoute = 'basic-information/form-stakeholder/' . Encryption::encodeId('ECR') . '/' . Encryption::encodeId($appInfo->company_id);
//            } else if ($basicAppID->applicant_type == 'Existing User for BIDA services') {
//                $BiRoute = 'basic-information/form-bida/' . Encryption::encodeId('EUBS') . '/' . Encryption::encodeId($appInfo->company_id);
//            } else {
//                $BiRoute = 'basic-information/form-bida/' . Encryption::encodeId('NUBS') . '/' . Encryption::encodeId($appInfo->company_id);
//            }

            $data = json_decode($rollbackAppInfo->data);

            return view('Settings::app_rollback.view', compact('data', 'appInfo', 'rollbackAppInfo', 'openAppRoute'));

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[AR-1065]");
            return \redirect()->back();
        }
    }

    /* Forcefully data update update functions */
    public function forcefullyDataUpdate()
    {
        if (!ACL::getAccsessRight('settings', 'V')) {
            die('You have no access right! Please contact system administration for more information. [FDU-906]');
        }
        return view("Settings::forcefully_data_update.list");
    }

    public function getForcefullyDataList(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        $mode = ACL::getAccsessRight('settings', 'V');

        $data = ForcefullyDataUpdate::leftJoin('users', 'users.id', '=', 'forcefully_data_update.updated_by')
            ->where('is_archive', 0)
            ->orderBy('id', 'desc')
            ->get([
                'forcefully_data_update.*',
                DB::raw("CONCAT(users.user_first_name, ' ', users.user_middle_name, ' ', users.user_last_name) as modified_user"),
            ]);

        return Datatables::of($data)
            ->addColumn('action', function ($data) use ($mode) {
                if ($mode) {
                    return '<a href="/settings/forcefully-data-update-view/' . Encryption::encodeId($data->id) . '" class="btn btn-xs btn-primary"><i class="fa fa-folder-open"></i> Open</a>';
                }
            })
            ->editColumn('status_id', function ($data) use ($mode) {
                if ($data->status_id == 1) {
                    return "<span class='label label-primary'>Submit</span>";
                } elseif ($data->status_id == 25) {
                    return "<span class='label label-success'>Approved</span>";
                } elseif ($data->status_id == 6) {
                    return "<span class='label label-danger'>Rejected</span>";
                }
            })
            ->editColumn('data', function ($data) {
                return substr($data->data, 30, 50);
            })
            ->editColumn('last_modified', function ($data) {
                return $data->modified_user . '<br>' . $data->updated_at;

            })
            ->rawColumns(['status_id','last_modified','action'])
            ->make(true);
    }

    public function singleForcefullyViewById($id)
    {
        $id = Encryption::decodeId($id);

        $forcefully_data_update = ForcefullyDataUpdate::leftJoin('users', 'users.id', '=', 'forcefully_data_update.user_id')
            ->leftJoin('company_info', 'company_info.id', '=', 'forcefully_data_update.company_id')
            ->where('forcefully_data_update.id', $id)
            ->first([
                'forcefully_data_update.*',
                DB::raw('CONCAT(user_first_name," ",user_middle_name," ",user_last_name, "(", user_email,")") AS user_info'),
                'company_info.org_nm'
            ]);

        $datas = json_decode($forcefully_data_update->data);
        $affected_rows = explode(',', $forcefully_data_update->affected_row_ids);
        $affected_rows_count = count($affected_rows);
        return view("Settings::forcefully_data_update.view", compact('forcefully_data_update', 'datas', 'affected_rows_count'));
    }

    public function createForcefullyDataUpdate()
    {
        if (!ACL::getAccsessRight('settings', 'V')) {
            die('You have no access right! Please contact system administration for more information. [FDU-907]');
        }

        try {
            $companies = CompanyInfo::where('company_status', 1)
                ->where('is_approved', 1)
                ->where('is_rejected', 'no')
                ->where('is_archived', 0)
                ->get(['org_nm', 'id']);

            $users = Users::select('id', DB::raw('CONCAT(user_first_name," ",user_middle_name," ",user_last_name, "(", user_email,")") AS user_info'))
                ->where('user_type', '5x505')
                ->where('is_approved', 1)
                ->where('user_status', '!=', 'rejected')
                ->orderBy('user_first_name', 'ASC')
                ->get(['user_info', 'id'])
                ->all();

            return view("Settings::forcefully_data_update.create", compact('users', 'companies'));
        } catch (Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[FDU-6001]");
            return \redirect()->back();
        }
    }

    public function storeForcefullyDataUpdate(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => true,
                'status' => 'Sorry! this is a request without proper way. [FDU-1014]',
            ]);
        }

        if (!ACL::getAccsessRight('settings', 'A')) {
            return response()->json([
                'error' => true,
                'status' => 'You have no access right! Please contact system administration for more information. [FDU-907]',
            ]);
        }

        $rules = [];
        $messages = [];

        $rules["table_name"] = 'required';
        $messages["table_name.required"] = 'Table name field is required';

        $rules["update_type"] = 'required';
        $messages["update_type.required"] = 'Update type field is required';

        $rules["user_id"] = 'required_if:update_type,user';
        $messages["user_id.required_if"] = 'User ID is required';

        $rules["company_id"] = 'required_if:update_type,company';
        $messages["company_id.required_if"] = 'Company ID is required';

        $rules["row_id"] = 'required_if:update_type,field';
        $messages["row_id.required_if"] = 'Row ID is required';

        $rules["label_name"] = 'requiredArray';
        $rules["column_name"] = 'requiredArray';
        //$rules["column_value"] = 'requiredArray';

        foreach ($request->get('column_value') as $key => $value) {
            $rules["column_value.$key"] = 'required';
            $messages["column_value.$key.required"] = 'Value field is required';
        }

        $validation = Validator::make($request->all(), $rules, $messages);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validation->errors(),
            ]);
        }

        try {
            DB::beginTransaction();

            $update_type = $request->get('update_type');
            $table_name = $request->get('table_name');
            $company_id = (!empty($request->get('company_id')) ? Encryption::decodeId($request->get('company_id')) : '');
            $user_id = (!empty($request->get('user_id')) ? Encryption::decodeId($request->get('user_id')) : '');
            $row_id = $request->get('row_id');

            // Column name check validation
            foreach ($request->get('column_name') as $key => $value) {
                $column_name = trim($request->get('column_name')[$key]);
                $is_column_exists = DB::select(DB::raw("SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table_name . "' AND column_name = '" . $column_name . "' LIMIT 1"));
                if (empty($is_column_exists)) {
                    DB::rollback();
                    return response()->json([
                        'error' => true,
                        'status' => 'The ' . $column_name . ' column name is not found. [FDU-912]',
                    ]);
                }
            }

            $forcefully_data_update = new ForcefullyDataUpdate();
            $forcefully_data_update->table_name = $table_name;
            $forcefully_data_update->update_type = $update_type;
            $affected_row_array = [];


            if ($update_type == 'company') { /// the code only process list oriented
                $process_data = DB::table("process_list")
                    ->leftJoin("process_type", "process_list.process_type_id", "=", "process_type.id")
                    ->leftJoin($table_name, "process_list.ref_id", "=", "$table_name.id")
                    ->where("process_list.company_id", $company_id)
                    ->where("process_type.table_name", $table_name)
                    ->whereNotIn("process_list.status_id", [-1, 5, 6, 22])
                    ->get([
                        "process_list.process_type_id as process_type_id",
                        "process_list.ref_id as ref_id",
                    ]);

                if (empty($process_data)) {
                    DB::rollback();
                    return response()->json([
                        'error' => true,
                        'status' => 'No company data found. [FDU-908]',
                    ]);
                }

                $forcefully_data_update->company_id = $company_id;

                // store JSON data
                $data = [];
                foreach ($process_data as $process) {

                    foreach ($request->get('label_name') as $key => $value) {

                        $column_name = trim($request->get('column_name')[$key]);
                        $get_old_value = DB::table($table_name)->where('id', $process->ref_id)->value($column_name);

                        $data1 = [];
                        $data1['label_name'] = $request->get('label_name')[$key];
                        $data1['column_name'] = $column_name;
                        $data1['old_value'] = $get_old_value;
                        $data1['new_value'] = $request->get('column_value')[$key];
                        $data[] = $data1;
                    }

                    $affected_row_array[] = $process->ref_id;
                }

            } elseif ($update_type == 'user') {

                $where_column_name = $table_name == 'users' ? 'id' : 'created_by';
                $get_user_type_data = DB::table($table_name)->where($where_column_name, $user_id)->get(['id']);

                if (empty($get_user_type_data)) {
                    DB::rollback();
                    return response()->json([
                        'error' => true,
                        'status' => 'No data found. [FDU-909]',
                    ]);
                }

                $forcefully_data_update->user_id = $user_id;

                // store JSON data
                $data = [];
                foreach ($get_user_type_data as $user_data) {

                    foreach ($request->get('label_name') as $key => $value) {

                        $column_name = trim($request->get('column_name')[$key]);
                        $get_old_value = DB::table($table_name)->where('id', $user_data->id)->value($column_name);

                        $data1 = [];
                        $data1['label_name'] = $request->get('label_name')[$key];
                        $data1['column_name'] = $column_name;
                        $data1['old_value'] = $get_old_value;
                        $data1['new_value'] = $request->get('column_value')[$key];
                        $data[] = $data1;
                    }


                    $affected_row_array[] = $user_data->id;
                }

            } elseif ($update_type == 'field') {

                $table_data = DB::table($table_name)->where('id', $row_id)->value('id');
                if (empty($table_data)) {
                    DB::rollback();
                    return response()->json([
                        'error' => true,
                        'status' => 'No data found. [FDU-930]',
                    ]);
                }

                $forcefully_data_update->row_id = $row_id;

                // store JSON data
                $data = [];
                foreach ($request->get('label_name') as $key => $value) {

                    $column_name = trim($request->get('column_name')[$key]);
                    $get_old_value = DB::table($table_name)->where('id', $row_id)->value($column_name);

                    $data1 = [];
                    $data1['label_name'] = $request->get('label_name')[$key];
                    $data1['column_name'] = $column_name;
                    $data1['old_value'] = $get_old_value;
                    $data1['new_value'] = $request->get('column_value')[$key];
                    $data[] = $data1;
                }

                $affected_row_array[] = $row_id;
            }


            // store JSON data
            $column_with_value = [];
            foreach ($request->get('column_name') as $key => $value) {
                $data1 = [];
                $data1['column_name'] = trim($request->get('column_name')[$key]);
                $data1['new_value'] = trim($request->get('column_value')[$key]);
                $column_with_value[] = $data1;
            }

            $forcefully_data_update->data = json_encode($data);
            $forcefully_data_update->column_with_value = json_encode($column_with_value);
            $forcefully_data_update->status_id = 1; // submit
            $forcefully_data_update->affected_row_ids = implode($affected_row_array, ',');
            $forcefully_data_update->save();

            // generate tracking nubmer
            if (empty($forcefully_data_update->tracking_no)) {
                $trackingPrefix = 'FDU-' . date("dMY") . '-';
                DB::statement("update  forcefully_data_update, forcefully_data_update as table2  SET forcefully_data_update.tracking_no=(
                    select concat('$trackingPrefix',
                            LPAD( IFNULL(MAX(SUBSTR(table2.tracking_no,-5,5) )+1,1),5,'0')
                                  ) as tracking_no
                     from (select * from forcefully_data_update ) as table2
                     where table2.id!='$forcefully_data_update->id' and table2.tracking_no like '$trackingPrefix%'
                )
              where forcefully_data_update.id='$forcefully_data_update->id' and table2.id='$forcefully_data_update->id'");
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 'Data has been saved successfully',
                'link' => '/settings/forcefully-data-update',
            ]);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()) . '[FDU-6001]',
            ]);
        }
    }

    public function approveForcefullyDataUpdate(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => true,
                'status' => 'Sorry! this is a request without proper way.',
            ]);
        }

        if (!ACL::getAccsessRight('settings', 'E')) {
            return response()->json([
                'error' => true,
                'status' => 'You have no access right! Please contact system administration for more information.',
            ]);
        }

        try {

            $id = Encryption::decodeId($request->get('id'));
            $status_id = Encryption::decodeId($request->get('status'));

            $forcefully_data_update = ForcefullyDataUpdate::find($id);
            if (empty($forcefully_data_update)) {
                return response()->json([
                    'error' => true,
                    'status' => 'Sorry no data found!',
                ]);
            }

            if (Auth::user()->id == $forcefully_data_update->created_by) {
                return response()->json([
                    'error' => true,
                    'status' => 'Sorry! You have no permission!',
                ]);
            }

            if ($status_id == 6 || $status_id == '6') {
                $forcefully_data_update->status_id = 6; // rejected
                $forcefully_data_update->save();

                return response()->json([
                    'success' => true,
                    'status' => 'Your data has been successfully rejected.',
                ]);
            } else if ($status_id == 25 || $status_id == '25') {

                $array_ids = explode(',', $forcefully_data_update->affected_row_ids);
                $json_decode = json_decode($forcefully_data_update->column_with_value);
                $array_data = [];
                foreach ($json_decode as $data) {
                    $array_data[$data->column_name] = $data->new_value;
                }

                DB::table($forcefully_data_update->table_name)
                    ->whereIn('id', $array_ids)
                    ->update($array_data);

                $forcefully_data_update->status_id = 25;
                $forcefully_data_update->save();

                return response()->json([
                    'success' => true,
                    'status' => 'Your file has been successfully approved.',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'status' => CommonFunction::showErrorPublic($e->getMessage()) . "[FDU-5001]",
            ]);
        }
    }

}
