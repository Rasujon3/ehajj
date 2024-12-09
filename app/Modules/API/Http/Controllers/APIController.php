<?php

namespace App\Modules\API\Http\Controllers;

use App\Libraries\PostApiData;
use App\Models\ActionInformation;
use App\Models\UrlInformation;
use App\Modules\API\Http\Controllers\Traits\Notification;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Modules\API\Http\Controllers\Traits\ApiRequestManager;
use Illuminate\Http\Request;


class APIController extends Controller
{
    use ApiRequestManager, Notification;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function apiRequest()
    {
        $response = array();
        try {
            $paramValue = str_replace('\"', '"', $_REQUEST['param']);
            $requestData = $this->getParamValue($paramValue);
            $requestType = $requestData['osspidRequest']['requestType'];
            $response = $this->manageRequestType($requestType, $requestData);

        } catch (\Exception $e) {
            // In case of invalid request format
            $response['osspidResponse'] = [
                'responseTime' => Carbon::now()->timestamp,
                'responseType' => '',
                'responseCode' => '400',
                'responseData' => [],
                'message' => 'Bad request format.' . $e->getMessage() . $e->getFile() . $e->getLine()
            ];
            $response = response()->json($response);
        }
        return $response;
    }

    /**
     * Get Parameter as JSON decoded
     * @param $getParam
     * @return mixed
     */
    function getParamValue($getParam)
    {
        $this->writeLog("Request", $getParam);
        return $returnArray = json_decode($getParam, true);
    }

    /**
     * Write Log in Local File
     * @param $type
     * @param $log
     */
    function writeLog($type, $log)
    {
        date_default_timezone_set('Asia/Dhaka');
        $fileName = storage_path() . '/logs/' . date("Ymd") . ".txt";
        //echo $fileName;die();
        $file = fopen($fileName, "a");
        if ($type == "Request") {
            fwrite($file, "\r### " . date("H:i:s") . "\t" . $type . ":" . $log);
        } else {
            fwrite($file, "\r###" . $type . ":" . $log);
        }
        fclose($file);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function newJob(Request $request)
    {
        $client_request = $request->all();

        if ($client_request == ''
            || $client_request == null
            || !isset($client_request)) {
            $response = array( // Response for invalid request
                'status' => 400,
                'success' => false,
                'error' => array(
                    'code' => 'EQR101',
                    'message' => 'Invalid Request or Parameter'
                ),
                'response' => null
            );
        } else {

            $url = (isset($client_request['url']) ? $client_request['url'] : '#');
            $ip_address = $client_request['ip_address'];
            $user_id = $client_request['user_id'];
            $project_code = $client_request['project'];
            $message = $client_request['message'];

            $prev_url = UrlInformation::where('user_id', $user_id)
                ->orderBy('id', 'DESC')
                ->first();

            if (!empty($prev_url)) {
                // store time duration in Hour:Minute format. Ex - 01:02 (1 hour 2 minute). second is not stored
                $time_diff = (new Carbon(date('Y:m:d H:i:s', time())))->diff(new Carbon($prev_url->in_time))->format('%h:%I');
                UrlInformation::where('id', $prev_url->id)->update([
                    'out_time' => date('Y:m:d H:i:s', time()),
                    'duration' => $time_diff
                ]);
            }

            UrlInformation::create([
                'url' => $url,
                'ip_address' => $ip_address,
                'project_code' => $project_code,
                'message' => $message,
                'in_time' => date('Y:m:d H:i:s', time()),
                'user_id' => $user_id
            ]);
            $response = array( // Response for valid request
                'status' => 200,
                'success' => true,
                'error' => null,
            );
            http://localhost:8000/api/new-job?requestData={%22data%22:{%22project%22:%22beza%22,%20%22user_id%22:%2211%22,%22url%22:%22localhost://111.com%22,%22method%22:%22post%22}}

        }
        return \GuzzleHttp\json_encode($response);
    }

    public function actionNewJob(Request $request)
    {
        $client_request = $request->all();


        if ($client_request == ''
            || $client_request == null
            || !isset($client_request)) {
            $response = array( // Response for invalid request
                'status' => 400,
                'success' => false,
                'error' => array(
                    'code' => 'EQR101',
                    'message' => 'Invalid Request or Parameter'
                ),
                'response' => null
            );
        } else {

            $url = (isset($client_request['url']) ? $client_request['url'] : '#');
            $ip_address = $client_request['ip_address'] ?? "";
            $action = trim($client_request['action'] ?? "");
            $user_id = $client_request['user_id'] ?? "";
            $project_code = $client_request['project'] ?? "";
            $message = $client_request['message'] ?? "";

            ActionInformation::create([
                'url' => $url,
                'action' => $action,
                'ip_address' => $ip_address,
                'project_code' => $project_code,
                'message' => $message,
                'user_id' => $user_id
            ]);

            $response = array( // Response for valid request
                'status' => 200,
                'success' => true,
                'error' => null,
            );
            http://localhost:8000/api/new-job?requestData={%22data%22:{%22project%22:%22beza%22,%20%22user_id%22:%2211%22,%22url%22:%22localhost://111.com%22,%22method%22:%22post%22}}
        }
        return \GuzzleHttp\json_encode($response);
    }

}
