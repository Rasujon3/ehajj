<?php namespace App\Modules\Flight\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
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
use Illuminate\Support\Facades\Validator;
use yajra\Datatables\Datatables;
use Config;

class FlightController extends Controller
{

    public function index() {
        $accessMode = ACL::getAccsessRight('flight');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }

        try {

            // $flightListData = $this->getHajjFlilghtList();
            // $flightList = $flightListData['activeFlightList'];
            // $activeSession = $flightListData['hajjSession']['caption'];

            $postData = [];

            $postdata = http_build_query($postData);
            $base_url = env('API_URL');
            $url = "$base_url/api/get-hajj-session";

            $response = PostApiData::getData($url,$postdata);
            $response_data = json_decode($response,true);

            $hajjSession = [];
            if (!empty($response_data) && isset($response_data['status']) &&  $response_data['status']== 200) {
                $hajjSession = $response_data['data']['hajjSession'];

            }else{
                return response()->json(['responseCode' => 0]);
            }

            return view('Flight::flightList', compact('hajjSession'));
        } catch(\Exception $e) {
            dd($e);
        }
    }

    public function getHajjFlilghtList(Request $request) {
        $accessMode = ACL::getAccsessRight('flight');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }

        if(!$request->type) {
            return response()->json(['responseCode' => 0]);
        }

        $userEmail = substr(Auth::user()->user_email, 0, strpos(Auth::user()->user_email, "_prp"));
        $postData = [
            'userEmail' => $userEmail,
            'type' => $request->type,
        ];

        $postdata = http_build_query($postData);
        $base_url = env('API_URL');
        $url = "$base_url/api/get-hajj-flight-list";

        $response = PostApiData::getData($url,$postdata);
        $response_data = json_decode($response,true);
        $data = [];
        if (!empty($response_data) && isset($response_data['status']) &&  $response_data['status']== 200) {
            $data = $response_data['data']['activeFlightList'];
        }

        return Datatables::of($data)
        ->addColumn('action', function ($data) {
            return '<div class="btn-flex-center">
                        <a href="'.url('/flight/flight-details/'.Encryption::encodeId($data['id'])) .'" class="btn-outline-blue">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.666504 3.8335C0.666504 3.30304 0.877218 2.79431 1.25229 2.41922C1.62736 2.04413 2.13607 1.8334 2.6665 1.8334H5.62517C6.07112 1.83345 6.50425 1.98255 6.85576 2.257C7.20726 2.53145 7.45695 2.91551 7.56517 3.34815L7.8545 4.5002H13.3332C13.8636 4.5002 14.3723 4.71093 14.7474 5.08602C15.1225 5.46111 15.3332 5.96984 15.3332 6.5003V13.1673C15.3332 13.6978 15.1225 14.2065 14.7474 14.5816C14.3723 14.9567 13.8636 15.1674 13.3332 15.1674H2.6665C2.13607 15.1674 1.62736 14.9567 1.25229 14.5816C0.877218 14.2065 0.666504 13.6978 0.666504 13.1673V3.8335ZM1.99984 6.5003V13.1673C1.99984 13.3441 2.07008 13.5137 2.1951 13.6387C2.32012 13.7638 2.48969 13.834 2.6665 13.834H13.3332C13.51 13.834 13.6796 13.7638 13.8046 13.6387C13.9296 13.5137 13.9998 13.3441 13.9998 13.1673V6.5003C13.9998 6.32348 13.9296 6.15391 13.8046 6.02887C13.6796 5.90384 13.51 5.8336 13.3332 5.8336H2.6665C2.48969 5.8336 2.32012 5.90384 2.1951 6.02887C2.07008 6.15391 1.99984 6.32348 1.99984 6.5003ZM6.47984 4.5002H2.6665C2.43317 4.5002 2.2085 4.54021 1.99984 4.61354V3.8335C1.99984 3.65668 2.07008 3.48711 2.1951 3.36208C2.32012 3.23705 2.48969 3.1668 2.6665 3.1668H5.62517C5.77379 3.16681 5.91815 3.21647 6.03531 3.30791C6.15247 3.39935 6.23572 3.52732 6.27184 3.6715L6.47984 4.5002Z" fill="#0063F7"/>
                            </svg>
                            Open
                        </a>
                    </div>';
        })
        ->make(true);
    }

    public function createFlight() {
        $accessMode = ACL::getAccsessRight('flight');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }

        $flight_type = ['' => 'Select One',
            'departure' => 'Hajj Flight',
            'arrival' => 'Return Flight',
        ];
        $pilgrim_type = ['' => 'Select One',
            '0' => 'Private',
            '1' => 'Government',
            '2' => 'Mixed',
        ];

        $userEmail = substr(Auth::user()->user_email, 0, strpos(Auth::user()->user_email, "_prp"));
        $postData = [
            'userEmail' => $userEmail,
        ];

        $postdata = http_build_query($postData);
        $base_url = env('API_URL');
        $url = "$base_url/api/get-cities-airlines-aircraft-season";
        $response = PostApiData::getData($url,$postdata);
        $response_data = json_decode($response,true);

        if($response_data['status'] == 200) {
            //$bd_cities = $response_data['data']['bd_cities'];
            $bd_cities = '';
            foreach ($response_data['data']['bd_cities'] as $key => $bd_city) {
                $bd_cities = $bd_cities.'<option value="'.$key.'">'.$bd_city.'</option>';
            }
            // $ksa_cities = $response_data['data']['ksa_cities'];
             $ksa_cities = '';
            foreach ($response_data['data']['ksa_cities'] as $key => $bd_city) {
                $ksa_cities = $ksa_cities.'<option value="'.$key.'">'.$bd_city.'</option>';
            }

            $airlines = $response_data['data']['airlines'];
            $aircrafts = $response_data['data']['aircrafts'];
            $userSubType = $response_data['data']['user_subType'];
            return view('Flight::createFlight', compact('flight_type', 'pilgrim_type','bd_cities', 'ksa_cities', 'airlines', 'aircrafts', 'userSubType'));
        }

        return redirect()->back();
    }
    public function storeFlight(Request $request) {
        $accessMode = ACL::getAccsessRight('flight');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        try {
            // if (!ACL::getAccsessRight('flight', 'A')) {
            //     die('You have no access right! Please contact with system admin if you have any query.');
            // }

            $rules = [
                'airlines' => 'required',
                'flight_code' => 'required',
                'departure_time' => 'required',
                'departure_city' => 'required',
                'flight_capacity' => 'required',
                'aircraft' => 'required',
                'pilgrim_type' => 'required',
                'type' => 'required',
                'flight_duration' => 'required',
                'arrival_time' => 'required',
                'arrival_city' => 'required',
                'description' => 'nullable',
            ];
            if($request->get('type') == 'departure'){
                $rules['route_to_makkah'] = 'required';
            }

            $validation = Validator::make($request->all(), $rules);

            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation->errors())->withInput();
            }

            $userEmail = substr(Auth::user()->user_email, 0, strpos(Auth::user()->user_email, "_prp"));
            $postData = [
                'airlines' => $request->get('airlines'),
                'route_to_makkah' => $request->get('route_to_makkah') != null ? $request->get('route_to_makkah'):'No',
                'flight_code' => $request->get('flight_code'),
                'departure_time' => $request->get('departure_time'),
                'departure_city' => $request->get('departure_city'),
                'flight_capacity' => $request->get('flight_capacity'),
                'pnl_time' => $request->get('pnl_time'),
                'aircraft' => $request->get('aircraft'),
                'pilgrim_type' => $request->get('pilgrim_type'),
                'type' => $request->get('type'),
                'flight_duration' => $request->get('flight_duration'),
                'arrival_time' => $request->get('arrival_time'),
                'arrival_city' => $request->get('arrival_city'),
                'gaca_reservation_no' => $request->get('gaca_reservation_no'),
                'description' => isset($request->description) ? $request->get('description') : '',
                'userEmail' =>$userEmail,
            ];

            $postdata = http_build_query($postData);

            $base_url = env('API_URL');
            $url = "$base_url/api/store-flight-data";
            $response = PostApiData::getData($url,$postdata);
            $response_data = json_decode($response,true);

            if($response_data['status'] == 200) {
                Session::flash('success', $response_data['msg']);
                return redirect('/flight');
            }

            Session::flash('error', $response_data['msg']);
            return redirect()->back();

        } catch (\Exception $e) {
            dd('from exception');
            return redirect()->back();
        }
    }

    public function flightDetails($id = '') {
        try {
            $userEmail = substr(Auth::user()->user_email, 0, strpos(Auth::user()->user_email, "_prp"));

            $decodedId = Encryption::decodeId($id);
            $postData = [
                'flightId' => $decodedId,
                'userEmail' => $userEmail,
            ];
            $postdata = http_build_query($postData);
            $base_url = env('API_URL');
            $url = "$base_url/api/get-hajj-flight-details";
            $response = PostApiData::getData($url,$postdata);
            $response_data = json_decode($response,true);

            if (!empty($response_data) && isset($response_data['status']) &&  $response_data['status']== 200) {
                $flightDetails = $response_data['data']['flightDetails'];
                $hajjSession = $response_data['data']['hajjSession'];
                return view('Flight::flightDetails', compact('flightDetails', 'hajjSession'));
            }else{
                return redirect()->back();
            }
        } catch(\Exception $e) {
            // dd($e);
            return redirect()->back();
        }

    }

    public function editFlight($id = '') {
        $accessMode = ACL::getAccsessRight('flight');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        try {
            $decodedId = Encryption::decodeId($id);
            $userEmail = substr(Auth::user()->user_email, 0, strpos(Auth::user()->user_email, "_prp"));
            $postData = [
                'flightId' => $decodedId,
                'userEmail' => $userEmail,
            ];
            $postdata = http_build_query($postData);
            $base_url = env('API_URL');
            $url = "$base_url/api/edit-flight";
            $response = PostApiData::getData($url,$postdata);
            $response_data = json_decode($response,true);

            if (!empty($response_data) && isset($response_data['status']) &&  $response_data['status']== 200) {
                $returnData = $response_data['data'];

                return view('Flight::editFlight', compact('returnData'));
            }else{
                return redirect()->back();
            }
        } catch(\Exception $e) {
            dd($e);
        }

    }

    public function updateFlight($id = '', Request $request) {
        $accessMode = ACL::getAccsessRight('flight');
        if (!ACL::isAllowed($accessMode, 'V')) {
            die('You have no access right! Please contact with system admin for more information.');
        }
        try {
            $decodedId = Encryption::decodeId($id);
            $userEmail = substr(Auth::user()->user_email, 0, strpos(Auth::user()->user_email, "_prp"));

            $rules = [
                'airlines' => 'required',
                'flight_code' => 'required',
                'departure_time' => 'required',
                'departure_city' => 'required',
                'flight_capacity' => 'required',
                'aircraft' => 'required',
                'pilgrim_type' => 'required',
                'type' => 'required',
                'flight_duration' => 'required',
                'arrival_time' => 'required',
                'arrival_city' => 'required',
                'flight_status' => 'required',
                'description' => 'nullable',
            ];
            if($request->get('type') == 'departure'){
                $rules['route_to_makkah'] = 'required';
            }

            if($request->get('flight_status') == 'departed') {
                $rules['actual_departed_time'] = 'required';
                $rules['total_departed_pilgrims'] = 'required';
            } elseif($request->get('flight_status') == 'arrived') {
                $rules['actual_arrival_time'] = 'required';
                $rules['total_arrived_pilgrims'] = 'required';
            }

            $validation = Validator::make($request->all(), $rules);

            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation->errors())->withInput();
            }

            $postData = [
                'airlines' => $request->get('airlines'),
                'route_to_makkah' => $request->get('route_to_makkah'),
                'flight_code' => $request->get('flight_code'),
                'departure_time' => $request->get('departure_time'),
                'departure_city' => $request->get('departure_city'),
                'flight_capacity' => $request->get('flight_capacity'),
                'pnl_time' => $request->get('pnl_time'),
                'aircraft' => $request->get('aircraft'),
                'pilgrim_type' => $request->get('pilgrim_type'),
                'type' => $request->get('type'),
                'flight_duration' => $request->get('flight_duration'),
                'arrival_time' => $request->get('arrival_time'),
                'arrival_city' => $request->get('arrival_city'),
                'gaca_reservation_no' => $request->get('gaca_reservation_no'),
                'description' => isset($request->description) ? $request->get('description') : '',
                'userEmail' => $userEmail,
                'flight_status' => $request->get('flight_status'),
                'total_passenger_departed' => isset($request->total_departed_pilgrims) ? $request->get('total_departed_pilgrims') : '',
                'departure_actual_time' => (isset($request->actual_departed_time) && !empty($request->actual_departed_time)) ? $request->get('actual_departed_time') : '0',
                'total_passenger_arrived' => isset($request->total_arrived_pilgrims) ? $request->get('total_arrived_pilgrims') : '',
                'arrival_actual_time' => (isset($request->actual_arrival_time) && !empty($request->actual_arrival_time)) ? $request->get('actual_arrival_time') : '0',
            ];

            // dd($postData);

            $postdata = http_build_query($postData);

            $base_url = env('API_URL');
            $url = "$base_url/api/update-flight-data/$decodedId";
            $response = PostApiData::getData($url,$postdata);
            $response_data = json_decode($response,true);

            if($response_data['status'] == 200) {
                Session::flash('success', $response_data['msg']);
                return redirect()->back();
            }

            Session::flash('error', $response_data['msg']);
            return redirect()->back();

        } catch(\Exception $e) {
            dd($e);
        }
    }

    public function getDashboardData() {
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
            $airlinesInfo['data'] = $response_data['data']['dashboardData'];
            $airlinesInfo['response_code'] = 1;
            return response()->json($airlinesInfo);
        }
        $airlinesInfo['response_code'] = -1;
        return response()->json($airlinesInfo);
    }

    public function getHajjFlilghtCitys(Request $request) {
        $flighttype = $request->get('flightType');
        $userEmail = substr(Auth::user()->user_email, 0, strpos(Auth::user()->user_email, "_prp"));
        $postData = [
            'userEmail' => $userEmail,
        ];

        $postdata = http_build_query($postData);
        $base_url = env('API_URL');
        $url = "$base_url/api/get-cities-airlines-aircraft-season";
        $response = PostApiData::getData($url,$postdata);
        $response_data = json_decode($response,true);

        if($response_data['status'] == 200) {
            if($flighttype == 'departure') {
                $departureCitys = $response_data['data']['bd_cities'];
                $arraivalCitys = $response_data['data']['ksa_cities'];
            } elseif($flighttype == 'arrival') {
                $departureCitys = $response_data['data']['ksa_cities'];
                $arraivalCitys = $response_data['data']['bd_cities'];
            } else {
                return ['responseCode' => 1];
            }
            $returnData = ['departureCitys' => $departureCitys, 'arraivalCitys' => $arraivalCitys];
            return ['responseCode' => 1, 'data' => $returnData];
        }
        return ['responseCode' => 1];
    }
}
