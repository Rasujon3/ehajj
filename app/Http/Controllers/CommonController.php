<?php

namespace App\Http\Controllers;

use App\Libraries\CommonFunction;
use App\Libraries\PostApiData;
use App\Modules\ProcessPath\Models\ProcessHistory;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Users\Models\UserLogs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Libraries\Encryption;
use Illuminate\Support\Facades\Log;


class CommonController extends Controller
{
    public function activitiesSummary()
    {
        $userType = CommonFunction::getUserType();
        $user_logs = UserLogs::where('user_id', '=', CommonFunction::getUserId())
            ->where('updated_at', '<=', Carbon::now()->subMonth()->format('Y-m-d H:i:s'))
            ->count();

        $totalNumberOfAction = ProcessHistory::join('process_type', 'process_type.id', '=',
            'process_list_hist.process_type_id')
            ->where('process_type.status', 1)
            ->where('process_type.active_menu_for', 'like', "%$userType%")
            ->where('updated_by', CommonFunction::getUserId())
            ->where('process_list_hist.updated_at', '<=', Carbon::now()->subMonth())
            ->groupBy('name')
            ->select(DB::raw('count(process_list_hist.process_type_id) as totalApplication'), 'name')
            ->get();


        $currentPendingYourDesk = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->where('process_type.status', 1)
            ->where('process_type.active_menu_for', 'like', "%$userType%")
            ->where('process_list.desk_id', '=', CommonFunction::getUserDeskIds())
            ->where('process_list.updated_at', '<=', Carbon::now()->subMonth()->format('Y-m-d H:i:s'))
            ->groupBy('process_type.name')
            ->select(DB::raw('count(process_list.process_type_id) as totalApplication'), 'process_type.name')
            ->get();

        $page_header = 'Activities Summary';
        return view('Settings::activities-summary.list', compact('user_logs', 'totalNumberOfAction', 'currentPendingYourDesk', 'page_header'));
    }

    public function getAttachment($fileurl){
        $file = Encryption::decode($fileurl);
        $urlInfo = explode('@expiredtime@',$file);
        if (!Carbon::parse($urlInfo[1])->isPast()) {
           return response()->file(public_path($urlInfo[0]));
        }else{
            $response = "URL expired" ;
            return $response;
        }
    }

    public function migration(Request $request, $module_name = '')
    {

        if (CommonFunction::getUserType() == '1x101') {
            $migrationPath = $module_name ? "/app/Modules/$module_name/database/migrations" : '/database/migrations';

            Artisan::call('migrate', [
                '--path' => $migrationPath,
                '--force' => true,
                '--pretend' => true,
            ]);

            DB::table('migration_audit')->insert([
                'title' => 'Migration',
                'details' => Artisan::output(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            Artisan::call('migrate', [
                '--path' => $migrationPath,
                '--force' => true,
            ]);
            echo $module_name . ":   ";
            return Artisan::output();

        }

    }

    public function getDynamicCountData()
    {

        $tokenData = $this->collectToken();
        $token = json_decode($tokenData)->token;

        $ch = curl_init();

        // Set the API endpoint URL
        $base_url = env('API_URL');
        $url = "$base_url/api/loginPageCount-data-view-api";

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

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }

        curl_close($ch);

        if ($response) {
            $responseData = json_decode($response, true);
        }

        return response()->json(['responseCode' => 1, 'data' => $responseData]);
    }
    public function getNoticeList()
    {
        $base_url = env('API_URL');
        $url = "$base_url/api/get-pilgrim-notice-list";
        $response = PostApiData::getData($url,null);
        $responseData= '';
        if ($response) {
            $responseData = json_decode($response);
        }

        $public_html = strval(view("public_home.hajjtable",
                compact('responseData')));
        return response()->json(['responseCode' => 1, 'html' => $public_html]);
    }

    public static function collectToken()
    {
        $base_url = env('API_URL');
        $api_url_for_token = "$base_url/api/getToken";
        $username          = env('CLIENT_USER_NAME');
        $password          = env('CLIENT_PASSWORD');
        $clientid          = env('CLIENT_ID');


        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
                'clientid'     => $clientid,
                'username'      => $username,
                'password'      => $password
            )));
            curl_setopt($curl, CURLOPT_URL, $api_url_for_token);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($curl);

            if (!$result || !property_exists(json_decode($result), 'token')) {
                $data = ['responseCode' => 0, 'msg' => 'API connection failed!', 'data' => ''];

                return json_encode($data);
            }

            curl_close($curl);
            $decoded_json = json_decode($result, true);

            $data         = [
                'responseCode' => 1,
                'msg'          => 'Success',
                'token'         => $decoded_json['token'],
                'expires_in'   => $decoded_json['expire_on']
            ];

            return json_encode($data);
        } catch (Exception $e) {
            Log::error($e);
            return false;
        }
    }

    public function getFlightList()
    {
        $base_url = env('API_URL');
        $url = "$base_url/api/schedule-flight";
        $response = PostApiData::getData($url, null);
        $responseData = '';
        if ($response) {
            $responseData = json_decode($response, true);
        }

        $flightData = $responseData['data']['flight_data'] ?? [];
        $flightCount = count($flightData);

        $viewData = [
            'title' => $responseData['data']['title'] ?? '',
            'flight_data' => $flightData,
        ];

        $view = strval(view("public_home.schedule-flight-list", $viewData));

        return response()->json([
            'responseCode' => 1,
            'html' => $view,
            'flight_list_count' => $flightCount,
            "title" =>  $responseData['data']['title'] ?? ''
        ]);
    }

    public function getPublicPageApi()
    {
        try {
            $data = $this->fetchDataFromApi();
            return response()->json(['responseCode' => 1, 'data' => $data]);
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine());
            return response()->json(['responseCode' => -1, 'data' => '']);
        }
    }

    public function fetchDataFromApi()
    {
        $selectedYear = 'recent';
        $token = $this->getClientidClientSecret();
        $news_api_url = config('constant.INSIGHTDB_NEWS_LIST_API_URL');
        $newsList = $this->fetchNewsListFromApi($token, $news_api_url);
        $returnData['newsList'] = strval(view('Web::newsList', compact('newsList', 'selectedYear')));

        return $returnData;
    }


    public function fetchNewsListFromApi($token, $news_api_url)
    {
        $postdata = [];
        $headers = ["Authorization: Bearer $token"];

        $news_api_data = CommonFunction::curlGetRequest($news_api_url, $postdata, $headers);
        if ($news_api_data['http_code'] != 200) {
            return [];
        }
        $data_arr = json_decode($news_api_data['data'], true);
        if ($data_arr['responseCode'] != 200) {
            return [];
        }
        return $data_arr['data'];
    }

    public function getSelectedNews($year)
    {
        $selectedYear = !empty($year) ? $year : '';
        if (empty($selectedYear)) {
            return ['responseCode' => -1, 'msg' => 'Year need to be selected.', 'html' => ''];
        }
        try {
            $token = $this->getClientidClientSecret();
            $news_api_url = config('constant.INSIGHTDB_NEWS_LIST_BY_YEAR_API_URL') . '?year=' . $selectedYear;

            $newsList = $this->fetchNewsThroughApi($token, $news_api_url);
            // $html = strval(view('Web::newsListForYear', compact('newsList', 'selectedYear')));
            // return ['responseCode' => 1, 'msg' => 'Successfully fetch news.', 'html' => $html];
            if(count($newsList) > 0) {
                $html = strval(view('Web::newsList', compact('newsList', 'selectedYear')));
                return ['responseCode' => 1, 'msg' => 'Successfully fetch news.', 'html' => $html];
            }
            $html = strval(view('Web::newsList', compact('newsList', 'selectedYear')));
            return ['responseCode' => -1, 'msg' => 'Something went wrong.', 'html' => $html];
        } catch (\Exception $e) {
            return ['responseCode' => -1, 'msg' => 'Something went wrong.', 'html' => ''];
        }
    }

    public function allNews()
    {
        try {
            $token = $this->getClientidClientSecret();
            $selectedYear = date('Y');
            $news_api_url = config('constant.INSIGHTDB_NEWS_LIST_BY_YEAR_API_URL') . '?year=' . $selectedYear;
            $newsList = $this->fetchNewsThroughApi($token, $news_api_url);
            $html = strval(view('Web::all_news', compact('newsList', 'selectedYear')));
            return ['responseCode' => 1, 'msg' => 'Successfully fetch news.', 'html' => $html];
        }
        catch (\Exception $e){
            return ['responseCode' => -1, 'msg' => 'News cannot be fetched. ', 'html' => ''];
        }
    }

    public function fetchNewsThroughApi($token, $news_api_url)
    {
        $newsList = $this->fetchNewsListFromApi($token, $news_api_url);
        $returnData = [];
        if (count($newsList) > 0) {
            foreach ($newsList as $newsIndex => $news) {
                if ($news['url']) {
                    $returnData[] = $news;
                }
            }
        }
        return $returnData;
    }
    public function getImpotantLink(){
        $token = $this->getClientidClientSecret();
        $important_link_list_api = config('constant.INSIGHTDB_LINK_LIST_URL');
        $important_link_list = $this->fetchImportantLinkListFromApi($token, $important_link_list_api);
        return view('Web::footer-link-response', compact('important_link_list'))->render();
    }

    public function fetchImportantLinkListFromApi($token, $important_link_api_url)
    {
        $important_link_url = [];
        $postdata = [];
        $headers = ["Authorization: Bearer $token"];
        $important_link_api_data = CommonFunction::curlGetRequest($important_link_api_url, $postdata, $headers);
        if ($important_link_api_data['http_code'] == 200) {
            $data_arr = json_decode($important_link_api_data['data'], true);
            if ($data_arr['responseCode'] == 200) {
                $important_link_url = $data_arr['data'];
            }
        }
        return $important_link_url;
    }

    public function getClientidClientSecret(){
        $clientID = config('constant.INSIGHTDB_API_CLIENT_ID');
        $clientSecret = config('constant.INSIGHTDB_API_CLIENT_SECRET');
        $url =config('constant.INSIGHTDB_API_TOKEN_URL');
        $token = $this->getToken($clientID, $clientSecret,$url);
        if($token){
            return $token;
        }
        return false;
    }

    public function getToken($client_id, $client_secret,$url)
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
