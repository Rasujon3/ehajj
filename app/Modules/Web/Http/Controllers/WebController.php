<?php namespace App\Modules\Web\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\InsightDbApiManager;
use App\Libraries\PostApiData;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\Settings\Models\ActRules;
use App\Modules\Settings\Models\Area;
use App\Modules\Settings\Models\HomeContent;
use App\Modules\Settings\Models\HomePageSlider;
use App\Modules\Settings\Models\IframeList;
use App\Modules\Settings\Models\IndustrialCityList;
use App\Modules\Settings\Models\NeedHelp;
use App\Modules\Settings\Models\Notice;
use App\Modules\Settings\Models\TermsCondition;
use App\Modules\Settings\Models\UserManual;
use App\Modules\Settings\Models\WhatsNew;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Web\Models\ResourcesLinks;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use yajra\Datatables\Datatables;
use Config;

class WebController extends Controller
{

    public function index()
    {
        // dd(Session::flush(), Session::all());
        if (Auth::check()) {
            return redirect("dashboard");
        }
        // Configuration
        $notice_config = Configuration::where('caption','NOTICE')->first(['value','value2']);
        $schedule_flight_config = Configuration::where('caption','SCHEDULE_FLIGHT')->first(['value','value2']);
        $is_notice_show = $this->isConfigValid($notice_config);
        $is_schedule_flight_show = $this->isConfigValid($schedule_flight_config);

        $notice = Notice::where('status', 'public')
            ->where('is_active', 1)
            ->orderBy('notice.updated_at', 'desc')
            ->limit(3)
            ->get();
        $latestnotice = $notice->take(2);
        if ($notice->count() <= 2){
            $totalNotice = 0;
        }else{
            $totalNotice = 1;
        }

        $topNotice =  Notice::where('status', 'public')
            ->where('is_active', 1)
            ->where( 'updated_at', '>', Carbon::now()->subDays(3))
            ->orderBy('notice.updated_at', 'desc')
            ->get();

        // $home_slider_image = HomePageSlider::where('status', 1)
        // ->orderby('slider_order', 'ASC')
        // ->take(5)
        // ->get([
        //     'slider_image',
        //     'slider_title',
        //     'slider_url',
        // ])
        // ->toArray();

        $home_slider_image = HomePageSlider::where('status', 1)
                            ->where('slider_order', '>', 0)
                            ->orderby('slider_order', 'ASC')
                            ->take(5)
                            ->get([
                                'slider_image',
                                'slider_title',
                                'slider_url',
                            ])
                            ->toArray();

        $home_content = HomeContent::whereIn('type', ['chairman', 'necessary info'])
        ->where('status', 1)
        ->get();
        $chairmanData = $home_content->where('type', 'chairman')->first();
        $necessaryInfo = $home_content->where('type', 'necessary info')
        ->sortBy('order', SORT_NUMERIC)
        ->all();

        $industrialCity = IndustrialCityList::leftJoin('area_info as district', 'district.area_id', '=', 'industrial_city_list.district_en')
            ->leftJoin('area_info as upazila', 'upazila.area_id', '=', 'industrial_city_list.upazila_en')
            ->where('status', 1)
            ->where('type', 0)
            ->where('is_archive', 0)
            ->orderby('order', 'ASC')
            ->get([
                'industrial_city_list.*',
                'district.area_nm_ban as district_name',
                'upazila.area_nm_ban as upazila_name',
                'district.area_nm as district_name_en',
                'upazila.area_nm as upazila_name_en'
            ]);

        $industryData = DB::table('area_info')
            ->select ([DB::raw('count(area_info.area_nm) as total_item'),'a2.area_nm_ban as divisons', 'a2.area_id as area_id'])
            ->join('industrial_city_list','industrial_city_list.district' ,'=','area_info.area_id')
            ->join ('area_info as a2' ,'area_info.pare_id','=','a2.area_id')
            ->where('industrial_city_list.type',0)
            ->where('industrial_city_list.status',1)
            ->groupBy('divisons')
            ->get();

        $iframe_list = IframeList::whereIn('key', ['industrialCity', 'bscicDocumentary'])
        ->where('status', 1)
        ->get();
//        $industrialMap = $iframe_list->where('key', 'industrialCity')->first();
        $bscicDocumentary = $iframe_list->where('key', 'bscicDocumentary')->first();

        $CityData = IndustrialCityList::where('status', 1)
        ->where('type', 0)
        ->where('is_archive', 0)
        ->count();

        // Cache for 1 day
        $total_stakeholder = Cache::remember('total_stakeholder', 60 * 60 * 24, function () {
            return Configuration::where('caption', 'STAKEHOLDER_SERVCE')->value('value');
        });

        // Cache for 1 day
        $processType = Cache::remember('total_process_type', 60 * 60 * 24, function () {
            return ProcessType::where('status', 1)->count();
        });
        $serviceList = ProcessList::where('status_id', 25)->count();

        return view('home', compact('notice', 'home_slider_image', 'topNotice',
            'chairmanData', 'necessaryInfo', 'industrialCity', 'industryData',
            'bscicDocumentary', 'latestnotice', 'totalNotice', 'CityData', 'total_stakeholder', 'processType', 'serviceList',
            'is_notice_show',
            'is_schedule_flight_show'));
    }

    public static function backChannelLogout($refresh_token) {

        // The Keycloak backchannel logout URL
        $logoutURL = env('KEYCLOAK_LOGOUT_URL');

        // Set the request parameters
        $params = array(
            'client_id' => env('KEYCLOAK_CLIENT_ID'),
            'client_secret' => env('KEYCLOAK_CLIENT_SECRET'),
            'refresh_token' => $refresh_token,
        );

        // Create the cURL session
        $ch = curl_init();

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $logoutURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        // Close the cURL session
        curl_close($ch);
    }

    public function loadCityOffice(Request $request){

        $area_id = Encryption::decodeId($request->area_id);

        $office = IndustrialCityList::leftJoin('area_info as area', 'area.area_id', '=', 'industrial_city_list.district')
            ->leftJoin('area_info as district', 'district.area_id', '=', 'industrial_city_list.district_en')
            ->leftJoin('area_info as upazila', 'upazila.area_id', '=', 'industrial_city_list.upazila')
            ->where('status', 1)
            ->where('type', 0)
            ->where('is_archive', 0)
            ->where('area.pare_id', $area_id)
            ->orderby('order', 'ASC')
            ->get([
                'industrial_city_list.*',
                'district.area_nm_ban as district_name',
                'upazila.area_nm_ban as upazila_name',
            ])->toArray();

        foreach ($office as &$data){
          $data['id'] = Encryption::encodeId($data['id']);
        }

        return response()->json($office);
    }

    public function notice(Request $request)
    {
        $this->checkAjaxRequest($request);

        $notice_tab = Notice::where('status', 'public')
            ->where('is_active', 1)
            ->where('is_archive', 0)
            ->orderBy('notice.updated_at', 'desc')
            ->limit(10)
            ->get(['id', 'heading', 'details', 'importance', 'status', 'updated_at as update_date']);

        $content = strval(view('Web::notice', compact('notice_tab')));
        return response()->json(['response' => $content]);
    }

    public function userManual(Request $request)
    {
        $this->checkAjaxRequest($request);

        $content = strval(view('Web::user_manual'));
        return response()->json(['response' => $content]);
    }

    public function getUserManual(Request $request)
    {
        $this->checkAjaxRequest($request);

        $data = UserManual::where('status', 1)->orderBy('id', 'desc')->limit(9)->get(['typeName',
            'pdfFile',]);
        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                if (file_exists($data->pdfFile)) {
                    return '<a href="' . '/' . $data->pdfFile . '" class="btn btn-xs btn-success" aria-hidden="true" target="_blank" download><i class="fa fa-download"></i> Download</a>';
                } else {
                    return '';
                }

            })
            ->removeColumn('id')
            ->make(true);
    }

    public function actandRules(Request $request)
    {
        $this->checkAjaxRequest($request);

        $content = strval(view('Web::act&rules'));
        return response()->json(['response' => $content]);
    }

    public function getActandRules(Request $request)
    {
        $this->checkAjaxRequest($request);

        DB::statement(DB::raw('set @rownum=0'));
        $data = ActRules::where('status', 1)->get([
            DB::raw('@rownum := @rownum+1 AS sl'),
            'subject',
            'pdf_link'
        ]);
        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                if (file_exists($data->pdf_link)) {
                    return '<a href="' . '/' . $data->pdf_link . '" class="btn btn-xs btn-success" aria-hidden="true" target="_blank" download><i class="fa fa-download"></i> Download</a>';
                } else {
                    return '';
                }
            })
            ->make(true);
    }

    public function termsConditions(Request $request)
    {
        $this->checkAjaxRequest($request);

        $content = strval(view('Web::terms&conditions'));
        return response()->json(['response' => $content]);
    }

    public function getTermsConditions(Request $request)
    {
        $this->checkAjaxRequest($request);

        DB::statement(DB::raw('set @rownum=0'));
        $data = TermsCondition::where('status', 1)->get([
            DB::raw('@rownum := @rownum+1 AS sl'),
            'subject',
            'pdf_link',
        ]);

        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                if (file_exists($data->pdf_link)) {
                    return '<a href="' . '/' . $data->pdf_link . '" class="btn btn-xs btn-success" aria-hidden="true" target="_blank" download><i class="fa fa-download"></i> Download</a>';
                } else {
                    return '';
                }
            })
            ->make(true);
    }

    public function serviceList(Request $request)
    {
        $this->checkAjaxRequest($request);

        $content = strval(view('Web::service-list'));
        return response()->json(['response' => $content]);
    }

    public function getServiceList(Request $request)
    {
        $this->checkAjaxRequest($request);

        DB::statement(DB::raw('set @rownum=0'));
        $data = ProcessType::leftJoin('service_details as sd', 'sd.process_type_id', '=', 'process_type.id')
            ->where('sd.status', 1)
            ->get([
                DB::raw('@rownum := @rownum+1 AS sl'),
                'process_type.name',
                'process_type.name_bn',
                'sd.attachment'
            ]);

        return Datatables::of($data)
        // ->editColumn('sl', function () {
        //     return '';
        // })
            ->addColumn('action', function ($data) {
                if (file_exists($data->attachment)) {
                    $btn = '<a href="' . '/' . $data->attachment . '" class="btn btn-xs btn-info" aria-hidden="true" target="_blank" type="button"><i class="fa fa-folder-open-o"></i> ' . __('messages.available_sevices.view') . '</a>';
                    $btn .= ' <a href="' . '/' . $data->attachment . '" class="btn btn-xs btn-success" aria-hidden="true" target="_blank" download type="button"><i class="fa fa-download"></i> ' . __('messages.available_sevices.download') . '</a>';
                    return $btn;
                } else {
                    return '';
                }
            })
            ->make(true);
    }

    public function applicationChart(Request $request)
    {
        $this->checkAjaxRequest($request);

        $dashboardObjectChart = \Illuminate\Support\Facades\DB::table('dashboard_object')
            ->where('db_obj_caption', 'dashboard_graph')
            ->where('db_obj_status', 1)
            ->orderBy('db_obj_sort')
            ->get();
        $content = strval(view('Web::chart', compact('dashboardObjectChart')));
        return response()->json(['response' => $content]);
    }

    public function viewNotice($id, $slug ='')
    {
//        $noticeId = Encryption::decodeId($id);
        $noticeData = Notice::find(CommonFunction::vulnerabilityCheck($id,'integer'));
        if(empty($noticeData)){
            abort(404);
        }
        $notice_doc = $noticeData->notice_document;
        return view('Web::view-notice', compact('noticeData', 'notice_doc'));
    }

    public function industrialCityDetails($id)
    {
        $industrialId = Encryption::decodeId($id);
        $industrialData = IndustrialCityList::find($industrialId);
        return view('Web::industrial-city-details', compact('industrialData'));
    }

    public function support()
    {
        $needHelp = NeedHelp::where('status', 1)->first();
        return view('Web::need-help', compact('needHelp'));
    }

    public function loadMoreNotice(Request $request)
    {
        $this->checkAjaxRequest($request);

        $notice = Notice::where('status', 'public')
            ->where('is_active', 1)
            ->orderBy('notice.updated_at', 'desc')
            ->paginate(2);

        if ($notice->count()) {
            $view = view('Web::notice-load-more', compact('notice'))->render();

            if ($notice->count() < 2){
                return response()->json([
                    'status' => true,
                    'data' => $view,
                    'hide' => 'hide',
                ]);
            }else{
                return response()->json([
                    'status' => true,
                    'data' => $view,
                ]);
            }

        }else{
            return response()->json([
                'status' => true,
                'hide' => 'hide',
            ]);
        }

    }

    public function checkAjaxRequest($request)
    {
        if (!$request->ajax()) {
            dd('Sorry! this is a request without proper way.');
        }
    }

    public function switchLang($lang){
        if (array_key_exists($lang, Config::get('languages'))) {
            Session::put('applocale', $lang);
        }
        return redirect()->back();
    }

    public function getResourcesLinksData(){
        $resourcesLink = ResourcesLinks::where('link_type', 'Resources Link')->where('status', 1)->get();
        $importantLink = ResourcesLinks::where('link_type', 'Important Link')->where('status', 1)->get();
        $public_html_resources_link = (string)view("Web::resourcesLink", compact('resourcesLink'));
        $public_html_important_link = (string)view("Web::importantLink", compact('importantLink'));
        return response()->json([
            'responseCode' => 0,
            'resources_link' => $public_html_resources_link,
            'important_link' => $public_html_important_link,
        ]);

    }

    public function isConfigValid($configObject) : bool{
        date_default_timezone_set("Asia/Dhaka");
        if (!isset($configObject->value) || !isset($configObject->value2)){
            return true;
        }
        return ($configObject->value && (\DateTime::createFromFormat('Y-m-d',$configObject->value2) && strtotime(date('Y-m-d')) <= strtotime($configObject->value2)));
    }


    public function flightListPage(){
        $base_url = env('API_URL');
        $url = "$base_url/api/get-display-flight-list";
        $response = PostApiData::getData($url,null);
        $responseData= '';
        if ($response) {
            $responseData = json_decode($response);
        }
        $total_rows = $responseData->data->total_rows;
        $notice = $responseData->data->notice;
        $curDay = $responseData->data->curDay;
        $curMonth = $responseData->data->curMonth;
        $percentage = $responseData->data->percentage;
        $list_title = $responseData->data->list_title;
        $flights = $responseData->data->flights;
        return view("flight-list", compact('flights', 'total_rows', 'notice', 'curDay', 'curMonth', 'percentage', 'list_title'));
    }

    public function ajaxFlightList(Request $request){
        $base_url = env('API_URL');

        $url = "$base_url/api/get-display-ajax-flight-list";
        $postData = [
            'incrementer' => $request->incrementer
        ];
        $postdata = http_build_query($postData);
//        $postData =$request->get('incrementer');
        $response = PostApiData::getData($url,$postdata);
        $responseData= '';
        if ($response) {
            $responseData = json_decode($response);
        }
//        dd($responseData);
        $notice = $responseData->data->notice;
        $curDay = $responseData->data->curDay;
        $curMonth = $responseData->data->curMonth;
        $flights = $responseData->data->flights;
        return strval(view('ajax-list')->with('flights', $flights)->with('curDay', $curDay)->with('curMonth', $curMonth)->with('notice', $notice));
    }

    public function getParcentage(){
        $base_url = env('API_URL');
        $url = "$base_url/api/get-display-flight-list-parcentage";

        $response = PostApiData::getData($url,null);
        $responseData= '';
        if ($response) {
            $responseData = json_decode($response);
        }
        $data = [
            'responseCode' => $responseData->data->responseCode,
            'percentage' => $responseData->data->percentage,
            ];
        return response()->json($data);
    }

    public function allNews()
    {
        $is_notice_show =  true;
        return view('allNews',compact('is_notice_show'));
    }


    public function fetchAllNews(Request $request)
    {
        $date = $request['date'];
        if(empty($date)){
            return response()->json(['responseCode' => -1, 'html' => '','msg'=>'Please select a date first!!!']);
        }
        $date = date('Y-m-d',strtotime($date));
        $year = date('Y',strtotime($date));
        $month = date('m',strtotime($date));

        $url = env('INSIGHTDB_API_BASE_URL')."/HAJ_WEB_NEWS_LIST_DATE_PICKER?Year=0&Month=0&Date=$date";
        $response = InsightDbApiManager::getAllNews($url);
        if($response['responseCode'] != 200){
            return response()->json(['responseCode' => -1, 'html' => '','msg'=>'Data cannot be fetched!!!']);
        }
        $responseData= $response['data'];
        $public_html = strval(view("public_home.allNewsTable", compact('responseData')));
        return response()->json(['responseCode' => 1, 'html' => $public_html]);
    }

}
