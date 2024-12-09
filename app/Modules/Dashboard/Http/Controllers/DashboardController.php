<?php

namespace App\Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\MedicineInventoryTrait;
use App\Http\Traits\Token;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\PostApiData;
use App\Modules\Dashboard\Models\Dashboard;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessStatus;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\REUSELicenseIssue\Models\MedicalReceive\MedicalReceiveClinic;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Settings\Models\EmailQueue;
use App\Models\SelfUserHelpText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use DB;

class DashboardController extends Controller
{
    use MedicineInventoryTrait;
    public $processPathTable = 'process_path';
    public $deskTable = 'user_desk';
    public $processStatus = 'process_status';
    public $processType = 'process_type';
    public $shortFallId = '5,6';
    protected $aclName;
    use Token;
    public function __construct()
    {
        $this->aclName = 'processPath';
    }

    public function index() {
        if((Auth::user()->user_type == '21x101') || (Auth::user()->user_type == '18x415')) {
            return redirect('/my-desk');
        }

        $keycloakAuthUrl = env('KEYCLOAK_AUTH_URL');
        $canGoPrpsAndHmis = $this->canGoInPrpsAndHmis();

        // Define the base credentials
        $prps_credentials = [
            'client_id' =>  env('KEYCLOAK_PRPS_CLIENT_ID'),
            'redirect_uri' =>  env('KEYCLOAK_PRPS_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'openid',
        ];
        $prps_httpQueryString = http_build_query($prps_credentials);

        $prp_credentials = [
            'client_id' =>  env('KEYCLOAK_PRP_CLIENT_ID'),
            'redirect_uri' =>  env('KEYCLOAK_PRP_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'openid',
        ];
        $prp_httpQueryString = http_build_query($prp_credentials);

        // Build the keycloak URI based on the conditions
        $keyCloakUri = [];

        if ($canGoPrpsAndHmis && $canGoPrpsAndHmis['prpsLogin']) {
            $keyCloakUri['prps_authorize_uri'] = $keycloakAuthUrl . '?' . $prps_httpQueryString;
        }
        if ($canGoPrpsAndHmis && $canGoPrpsAndHmis['prpLogin']) {
            $keyCloakUri['prp_authorize_uri'] = $keycloakAuthUrl . '?' . $prp_httpQueryString;
        }

        $hmis_credentials = [
            'client_id' =>  env('KEYCLOAK_HMIS_CLIENT_ID'),
            'redirect_uri' =>  env('KEYCLOAK_HMIS_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'openid',
        ];
        $hmis_httpQueryString = http_build_query($hmis_credentials);

        if($canGoPrpsAndHmis && $canGoPrpsAndHmis['hmisLogin']) {
            $keyCloakUri['hmis_authorize_uri'] = $keycloakAuthUrl . '?' . $hmis_httpQueryString;
        }

        # LMS Credentials
        $lms_credentials = [
            'client_id' => env('KEYCLOAK_LMS_CLIENT_ID'),
            'client_secret' => env('KEYCLOAK_LMS_CLIENT_SECRET'),
            'redirect_uri' => env('KEYCLOAK_LMS_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'openid',
        ];
        $queryParameters = http_build_query($lms_credentials);
        if($canGoPrpsAndHmis && $canGoPrpsAndHmis['lmsLogin']) {
            $keyCloakUri['lms_authorize_uri'] = $keycloakAuthUrl . '?' . $queryParameters;
        }

        return view("Dashboard::dashboard_new", compact("keyCloakUri"));
    }

    public function canGoInPrpsAndHmis() {
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
            return redirect()->back();
        }
        $isHmisLoginUrl = $base_url.'/api/can-go-prps-and-hmis';
        $postData = [
            'prp_user_id' => Encryption::encodeId(Auth::user()->prp_user_id),
        ];
        $postdata = json_encode($postData);

        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $apiResponse = CommonFunction::curlPostRequest($isHmisLoginUrl, $postdata, $headers, true);
        if($apiResponse['http_code'] !== 200) {
            return false;
        }
        return json_decode($apiResponse['data'], true)['data'];
    }

    public function myDesk(Dashboard $dashboard, Request $request, $form_url = '', $id = '', $processStatus = '')
    {
        $log = date('H:i:s', time());
        $dbMode = Session::get('DB_MODE');
        $log .= ' - ' . date('H:i:s', time());
        $log .= ' - ' . date('H:i:s', time());
        $dashboardObject = $dashboard->getDashboardObject();
        $pageTitle = 'Dashboard';
        $servicesWiseApplication = null;
        $dashboardObjectBarChart = null;
        $comboChartData = null;
        $appSubmitCount = 0;
        $appApproveCount = 0;
        $deshboardObject = [];
        $userApplicaitons = [];
        $services = null;
        $userType = Auth::user()->user_type;

        $companyId = CommonFunction::getUserCompanyWithZero();
        $userDeskIds = CommonFunction::getUserDeskIds();
        $userOfficeIds = CommonFunction::getUserOfficeIds();
        $notices = CommonFunction::getNotice();
        $profileInfo = [];
        $is_pilgrim_profile = in_array(Auth::user()->user_type, ['21x101']);
        $profile = ($is_pilgrim_profile) ? 'Users::pilgrim-portfolio' : 'Dashboard::index';

        $is_airlines = (Auth::user()->user_type == '6x606' || Auth::user()->user_type == '6x607');
        $is_agency = (Auth::user()->user_type == '12x431' || Auth::user()->user_type == '12x432');
//        if($is_agency) {
//            return view('');
//        }

        $is_owner = 1;

        $pilgrim_bank_info = [];
        $countData = [];
        $postData = [
            'prp_user_id' => Auth::user()->prp_user_id,
        ];
        $postdata = http_build_query($postData);
        $base_url = env('API_URL');
        $url = "$base_url/api/get-dashboard-data";
        $dashboardCount = PostApiData::getData($url,$postdata);
        $dashboard_count_data = json_decode($dashboardCount,true);
        if ($dashboard_count_data['status'] == 200) {
            $countData = $dashboard_count_data['data'];
        }
        $selfUserHelpText = SelfUserHelpText::where('status', 1)
            ->select('service_name', 'heder_text', 'help_text','service_step_image')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['service_name'] => [
                    'heder_text' => $item['heder_text'],
                    'help_text' => $item['help_text'],
                    'service_step_image' => $item['service_step_image'],
                ]];
            })
            ->toArray();
        if(empty($selfUserHelpText)){
            $selfUserHelpText =[];
        }

        if($is_pilgrim_profile){
            if(!empty(Auth::user()->user_email)){
                $postData = [
                    'tracking_no' => explode("@",Auth::user()->user_email)[0],
                    'is_child_list' => 1
                ];
                $postdata = http_build_query($postData);
                $base_url = env('API_URL');
                $url = "$base_url/api/get-pilgrim-profile-information";
                $response = PostApiData::getData($url,$postdata);

                $url2 = "$base_url/api/get-pilgrim-refund-bank-information";
                $response2 = PostApiData::getData($url2,$postdata);

                $response_data = json_decode($response,true);
                $response_data2 = json_decode($response2,true);

                // dd($response2, $response_data2);

                if ($response_data['status'] == 200) {
                    $profileInfo = $response_data['data'];

                    if(isset($profileInfo['basic_info']['pdf_flag'])){
                        $is_archived_check = $profileInfo['basic_info']['pdf_flag'];
                        if($is_archived_check == 'false'){
                            $profile = 'Users::protfolio-archived';
                        }
                    }
                }

                if(isset($response_data2['status']) && $response_data2['status'] == 200) {
                    $pilgrim_bank_info = $response_data2['data'];

                    if($pilgrim_bank_info['owner_type'] != 'Nearest Relative') {
                        $pilgrim_bank_info['relation'] = '';
                    }
                }
            }
        }

        $airlinesInfo = [];
        if($is_airlines) {
            $userEmail = substr(Auth::user()->user_email, 0, strpos(Auth::user()->user_email, "_prp"));
            $postData = [
                'userEmail' => $userEmail,
            ];
            $postdata = http_build_query($postData);
            $base_url = env('API_URL');
            $url = "$base_url/api/get-flight-dashboard-data";
            $response = PostApiData::getData($url,$postdata);

            $response_data = json_decode($response,true);
            if ($response_data['status'] == 200) {
                $airlinesInfo = $response_data['data']['dashboardData'];
            }
        }


        if(!$is_pilgrim_profile){
            if (!empty($companyId)) {
                Session::put('associated_company_name', CommonFunction::getCompanyNameById($companyId));
            } else {
                Session::forget('associated_company_name');
            }

            if ($userType == '1x101') {
                $deshboardObject = DB::table('dashboard_object')
                    ->where('db_obj_caption', 'dashboard_old')
                    ->where('db_obj_status', 1)
                    ->get();
                $dashboardObjectBarChart = DB::table('dashboard_object')->where(
                    'db_obj_type',
                    'BAR_CHART'
                )->where('db_obj_status', 1)->get();
            }


            if ($userType == '5x505') {
                $userApplicaitons = ProcessList::where('company_id', Auth::user()->working_company_id)->pluck('status_id');

                $approvedapp = 0;
                $processingapp = 0;
                $draftapp = 0;
                $rejectapp = 0;
                $shortfallapp = 0;

                if (count($userApplicaitons) > 0) {
                    foreach ($userApplicaitons as $appStatus) {
                        if ($appStatus == 25) {
                            $approvedapp = $approvedapp + 1;
                        } elseif ($appStatus == '-1') {
                            $draftapp = $draftapp + 1;
                        } elseif ($appStatus == 5) {
                            $shortfallapp = $shortfallapp + 1;
                        } elseif ($appStatus == 6) {
                            $rejectapp = $rejectapp + 1;
                        } else {
                            $processingapp = $processingapp + 1;
                        }
                    }
                }

                $totalapp = $approvedapp + $processingapp + $draftapp + $shortfallapp + $rejectapp;

                $userApplicaitons = [
                    'draft' => $draftapp, 'processing' => $processingapp, 'approved' => $approvedapp,
                    'totalapp' => $totalapp, 'shortfallapp' => $shortfallapp, 'rejectapp' => $rejectapp
                ];

                $servicesWiseApplication = ProcessType::whereStatus(1)
                    ->where(function ($query) use ($userType) {
                        $query->where('active_menu_for', 'like', "%$userType%");
                    })
                    ->groupBy('group_name')
                    ->get([DB::raw('group_concat(id) as process_type'), 'group_name']);
            } else {

                $services = DB::table('process_type')
                    ->leftJoin('process_list', function ($on) use ($companyId, $userDeskIds, $userOfficeIds, $userType) {
                        $on->on('process_list.process_type_id', '=', 'process_type.id')
                            // ->where('process_list.desk_id', '!=', 0)
                            ->whereNotIn('process_list.status_id', [-1, 5]);


                        if ($userType == '4x404') {
                            $getSelfAndDelegatedUserDeskOfficeIds = CommonFunction::getSelfAndDelegatedUserDeskOfficeIds();
                            $on->where(function ($query1) use ($getSelfAndDelegatedUserDeskOfficeIds) {
                                $i = 0;
                                foreach ($getSelfAndDelegatedUserDeskOfficeIds as $data) {
                                    $queryInc = '$query' . $i;

                                    if ($i == 0) {
                                        $query1->where(function ($queryInc) use ($data) {
                                            $queryInc->whereIn('process_list.desk_id', $data['desk_ids'])
                                                ->where(function ($query3) use ($data) {
                                                    $query3->where('process_list.user_id', $data['user_id'])
                                                        ->orWhere('process_list.user_id', 0);
                                                })
                                                ->whereIn('process_list.office_id', $data['office_ids']);
                                        });
                                    } else {
                                        $query1->orWhere(function ($queryInc) use ($data) {
                                            $queryInc->whereIn('process_list.desk_id', $data['desk_ids'])
                                                ->where(function ($query3) use ($data) {
                                                    $query3->where('process_list.user_id', $data['user_id'])
                                                        ->orWhere('process_list.user_id', 0);
                                                })
                                                ->whereIn('process_list.office_id', $data['office_ids']);
                                        });
                                    }
                                    $i++;
                                }
                            });
                        }
                    })
                    ->groupBy('process_type.id')
                    ->select([
                        'process_type.name', 'process_type.name_bn', 'process_type.id', 'process_type.form_url',
                        'process_type.panel', 'process_type.icon', DB::raw('COUNT(process_list.process_type_id) as
                    totalApplication'), DB::raw('COUNT(process_list.process_type_id) as totalApplication')
                    ])
                    ->orderBy('process_type.id', 'asc')
                    ->where('process_type.status', '=', 1)
                    ->get();

                $lastSixMonthData = DB::select(DB::raw("SELECT  DATE_FORMAT(updated_at,'%m-%Y') AS month_year,
                                    COUNT(CASE WHEN status_id = 1 then 1 ELSE NULL END) as 'Submit',
                                    COUNT(CASE WHEN status_id = 25 then 1 ELSE NULL END) as 'Approved'
                                    FROM process_list
                                    WHERE updated_at BETWEEN CURDATE() - INTERVAL 6 MONTH AND CURDATE()
                                    AND status_id in (1,25)
                                    GROUP BY DATE_FORMAT(updated_at,'%m-%Y')
                                    ORDER BY updated_at ASC "));

                $appApproveCount = ProcessList::whereIn('status_id', [25])
                    ->count();
                $appSubmitCount =  ProcessList::whereIn('status_id', [1])
                    ->count();

                $comboChartArray = [];
                if (count($lastSixMonthData) > 0) {
                    foreach ($lastSixMonthData as $key => $data) {
                        $comboChartArray[$key][0] = $data->month_year;
                        $comboChartArray[$key][1] = $data->Submit;
                        $comboChartArray[$key][2] = $data->Approved;
                    }
                } else {
                    $comboChartArray[0][0] = 20;
                    $comboChartArray[0][1] = 50;
                    $comboChartArray[0][2] = 10;
                }
                array_unshift($comboChartArray, ['Month', 'Submit', 'Approved']);
                $comboChartData = (array_values($comboChartArray));
            }
        }
        $pharmacyList = MedicalReceiveClinic::where('status', 1)->pluck('name', 'id')->toArray();

//        Process list start
        $process_type_id = ($id != '') ? Encryption::decodeId($id) : 0;

        if (!session()->has('active_process_list')) {
            session()->put('active_process_list', $process_type_id);
        }

//        $ProcessTypeObj = ProcessType::whereStatus(1)
//            ->where(function ($query) use ($userType) {
//                $query->where('active_menu_for', 'like', "%$userType%");
//            })
//            ->orderBy('name');

        $ProcessTypeObj = ProcessType::whereStatus(1)
            ->where(function ($query) use ($userType) {
                $query->where('active_menu_for', 'like', "%$userType%");
            })
            ->leftJoin('process_list', 'process_list.process_type_id', '=', 'process_type.id')
            ->select([
                'process_type.id as id',
                'process_type.name as name',
                'process_type.name_bn as name_bn',
                'process_type.status as status',
                'process_type.final_status as final_status',
                'process_type.type_key as type_key',
                'process_type.active_menu_for as active_menu_for',
                'process_type.panel as panel',
                'process_type.icon as icon',
                'process_type.menu_name as menu_name',
                'process_type.form_url as form_url',
                DB::raw('count(DISTINCT process_list.id) as total')
            ])
            ->orderBy('name')
            ->groupBy('process_type.id','name'); // Exclude 'name' from the GROUP BY clause

        $process_type_data_arr = $ProcessTypeObj->get()->toArray();
//        $ProcessType = $ProcessTypeObj->select(\Illuminate\Support\Facades\DB::raw("CONCAT(name) AS name"), 'id')->pluck('name', 'id')
//            ->toArray();
        $ProcessType = $ProcessTypeObj->pluck('name', 'id')->toArray();

        $process_info = ProcessType::where('id', $process_type_id)->first(['id', 'acl_name', 'form_url', 'name', 'group_name']);
        $processStatus = null;
        $status = ['' => 'Select one'] + ProcessStatus::where('process_type_id', $process_type_id != 0 ? $process_type_id : -1) // -1 means this service not available
            ->where('id', '!=', -1)
                ->where('status', 1)
                ->orderBy('status_name', 'ASC')
                ->pluck('status_name', 'id')->toArray();

        $searchTimeLine = [
            '' => 'select One',
            '1' => '1 Day',
            '7' => '1 Week',
            '15' => '2 Weeks',
            '30' => '1 Month',
        ];
        $aclName = $this->aclName;

        // Global search or dashboar Application list
        $search_by_keyword = '';
        if ($request->isMethod('post')) {
            $search_by_keyword = $request->get('search_by_keyword');
        }


        $number_of_rows = Configuration::where('caption', 'PROCESS_ROW_NUMBER')->value('value');
        $status_wise_apps = null;

        if ($userType == "1x101" || $userType == "4x404") {
            $status_wise_apps = ProcessList::statuswiseAppInDesks($process_type_id);
        }
        $guideline_config_text = Configuration::where('caption', 'READ_GUIDELINE')->value('value');
//        Process list end

        return view($profile,
            compact(
                'log',
                'dbMode',
                'services',
                'deshboardObject',
                'dashboardObject',
                'pageTitle',
                'dashboardObjectBarChart',
                'userApplicaitons',
                'servicesWiseApplication',
                'notices',
                'comboChartData',
                'appApproveCount',
                'appSubmitCount',
                'profileInfo',
                'is_pilgrim_profile',
                'is_owner',
                'pharmacyList',
                'pilgrim_bank_info',
                'pharmacyList',
                'status',
                'ProcessType',
                'processStatus',
                'searchTimeLine',
                'process_type_id',
                'process_info',
                'aclName',
                'search_by_keyword',
                'status_wise_apps',
                'number_of_rows',
                'guideline_config_text',
                'process_type_data_arr',
                'airlinesInfo',
                'countData',
                'selfUserHelpText'
            )
        );
    }

    public function dashboard()
    {

        if (Auth::check()) {
            return view('Dashboard::index');
        }
        return redirect("login")->withSuccess('Opps! You do not have access');
    }


    public function notifications()
    {
        $notifications = EmailQueue::where('email_to', Auth::user()->user_email)
            ->where('web_notification', 0)
            ->whereNotIn('caption', ['PASSWORD_RESET_REQUEST','ONE_TIME_PASSWORD','TWO_STEP_VERIFICATION'])
            ->orWhere('email_cc', Auth::user()->user_email)
            ->orderby('created_at', 'desc')->get([
                'id',
                'email_subject',
                'web_notification',
                'created_at'
            ]);
//        dd($notifications);

        $new_data = $notifications->map(function ($notification) {
            return [
                'id' => Encryption::encodeId($notification->id),
                'email_subject' => $notification->email_subject,
                'web_notification' => $notification->web_notification,
                'created_at' => $notification->created_at
            ];
        });
        return response()->json($new_data);
    }

    public function notificationCount()
    {
        /*
         * Query cache.
         * after every five minutes query will execute
         */
        $notificationsCount = Cache::remember('notificationCount' . Auth::user()->user_email, 5, function () {
            return EmailQueue::where('email_to', Auth::user()->user_email)
                ->whereNotIn('caption', ['PASSWORD_RESET_REQUEST','ONE_TIME_PASSWORD','TWO_STEP_VERIFICATION'])
                ->where('web_notification', 0)
                ->orWhere('email_cc', Auth::user()->user_email)
                ->orderby('created_at', 'desc')
                ->count();
        });

        return response()->json($notificationsCount);
    }

    public function notificationSingle($id)
    {
        $id = Encryption::decodeId($id);
        EmailQueue::where('id', $id)
            ->update([
                'web_notification' => 1,
            ]);

        $singleNotificInfo = EmailQueue::where('id', $id)->first();

        return view('Dashboard::singleNotificInfo', compact('singleNotificInfo'));
    }

    public function notificationAll()
    {
        EmailQueue::where('email_to', Auth::user()->user_email)
            ->orWhere('email_cc', Auth::user()->user_email)
            ->whereNotIn('caption', ['PASSWORD_RESET_REQUEST','ONE_TIME_PASSWORD','TWO_STEP_VERIFICATION'])
            ->update([
                'web_notification' => 1,
            ]);
        $notificationsAll = EmailQueue::where('email_to', Auth::user()->user_email)
            ->orWhere('email_cc', Auth::user()->user_email)
            ->orderby('created_at', 'desc')->get();

        return view('Dashboard::singleNotificInfo', compact('notificationsAll'));
    }

    public function serverInfo()
    {
        if (!in_array(Auth::user()->user_type, ['1x101', '2x202'])) {
            Session::flash('error', 'Invalid URL ! This incident will be reported.');
            return redirect('/');
        }

        $start_time = microtime(TRUE);

        // When used without any option, the free command will display information about the memory and swap in kilobyte.
        $free = shell_exec('free');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        // removes nulls from array
        $mem = array_filter($mem, function ($value) {
            return ($value !== null && $value !== false && $value !== '');
        });
        $mem = array_merge($mem);

        // $mem data format
//        [
//          0 => "Mem:"
//          1 => total
//          2 => used (used = total – free – buff/cache)
//          3 => free (free = total – used – buff/cache)
//          4 => shared
//          5 => buff/cache
//          6 => available
//        ]

        $kb_to_gb_conversion_unit = 1000 * 1000;
        $total_ram_size = round($mem[1] / $kb_to_gb_conversion_unit, 2);
        $used_ram_size = round($mem[2] / $kb_to_gb_conversion_unit, 2);
        $free_ram_size = round($mem[3] / $kb_to_gb_conversion_unit, 2);
        $buffer_cache_memory_size = round($mem[5] / $kb_to_gb_conversion_unit, 2);

        // Formula 1
        // Percentage = (memory used - memory buff/cache) / total ram * 100
        // $total_ram_usage = round(($used_ram_size - $buffer_cache_memory_size) / $total_ram_size * 100, 2);

        // Formula 2.e
        // Percentage = (memory used / total memory) * 100
        // Or
        // Percentage = 100 -(((free + buff/cache) * 100) / total)
        $total_ram_usage = round(($mem[2] / $mem[1]) * 100, 2);


        //$connections = `netstat -ntu | grep :80 | grep ESTABLISHED | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`;
        //$totalconnections = `netstat -ntu | grep :80 | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`;


        /*
         * If the averages are 0.0, then your system is idle.
         * If the 1 minute average is higher than the 5 or 15 minute averages, then load is increasing.
         * If the 1 minute average is lower than the 5 or 15 minute averages, then load is decreasing.
         * If they are higher than your CPU count, then you might have a performance problem (it depends).
         *
         * For example, one can interpret a load average of "1.73 0.60 7.98" on a single-CPU system as:
         * during the last minute, the system was overloaded by 73% on average (1.73 runnable processes, so that 0.73 processes had to wait for a turn for a single CPU system on average).
         * during the last 5 minutes, the CPU was idling 40% of the time on average.
         * during the last 15 minutes, the system was overloaded 698% on average (7.98 runnable processes, so that 6.98 processes had to wait for a turn for a single CPU system on average).
         */
        $load = sys_getloadavg();
        $cpu_load = $load[0];

        // disk_total_space() and disk_free_space() return value as Byte format
        $total_disk_size = round(disk_total_space(".") / 1000000000); // total space in GB
        $free_disk_size = round(disk_free_space(".") / 1000000000); // Free space in GB
        $used_disk_size = round($total_disk_size - $free_disk_size); // used space in GB
        $disk_usage_percentage = round(($used_disk_size / $total_disk_size) * 100); // Disk usage ratio in Percentage(%)

        if ($total_ram_usage > 85 || $cpu_load > 2 || $disk_usage_percentage > 95) {
            $text_class = 'progress-bar-danger';
        } elseif ($total_ram_usage > 70 || $cpu_load > 1 || $disk_usage_percentage > 85) {
            $text_class = 'progress-bar-warning';
        } else {
            $text_class = 'progress-bar-success';
        }

        $db_version = \Illuminate\Support\Facades\DB::select(DB::raw("SHOW VARIABLES like 'version'"));
        $db_version = isset($db_version[0]->Value) ? $db_version[0]->Value : '-';

        $end_time = microtime(TRUE);
        $time_taken = $end_time - $start_time;
        $total_time_of_loading = round($time_taken, 4);

        return view("Dashboard::server-info", compact('cpu_load',
            'total_ram_size', 'used_ram_size', 'free_ram_size', 'buffer_cache_memory_size', 'total_ram_usage',
            'total_disk_size', 'used_disk_size', 'free_disk_size', 'disk_usage_percentage', 'db_version',
            'total_time_of_loading', 'text_class'));
    }

    public function pilgrimProfile() {
        $accessMode = ACL::getAccsessRight('pilgrim');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        if(!empty(Auth::user()->tracking_no)){
            $profile = 'Users::pilgrim-portfolio';
            $base_url = env('API_URL');
            $postData = [
                'tracking_no' => Auth::user()->tracking_no,
                'birth_date' => Auth::user()->user_DOB,
                'is_child_list' => 1,
                'flag' => 'generateImageUrl',
            ];

            $postData = http_build_query($postData);
            $url1 = "$base_url/api/get-pilgrim-profile-information-v2";
            $response = PostApiData::getData($url1, $postData);
            $response_data = json_decode($response,true);
            if ($response_data['status'] != 200) {
                die($response_data['msg']);
            }

            $profileInfo = $response_data['data']['profileInfo'];
            if(isset($profileInfo['basic_info']['pdf_flag'])){
                $is_archived_check = $profileInfo['basic_info']['pdf_flag'];
                if($is_archived_check == 'false'){
                    $profile = 'Users::protfolio-archived';
                }
            }

            $pilgrim_bank_info = $response_data['data']['bankInfo'];
            if(!empty($pilgrim_bank_info) && $pilgrim_bank_info['owner_type'] != 'Nearest Relative') {
                $pilgrim_bank_info['relation'] = '';
            }
            $is_owner = 1;
            return view($profile, compact('profileInfo', 'pilgrim_bank_info', 'is_owner'));
        } else {
            Session::flash('error', 'আপনার প্রোফাইলের বিস্তারিত তথ্য দেখার জন্য নিম্নোক্ত Tracking No স্থানে আপনার Tracking No দিয়ে Save করুন।');
            return redirect('/users/profileinfo')->with('focus', 'tracking_no');
        }
    }
}
