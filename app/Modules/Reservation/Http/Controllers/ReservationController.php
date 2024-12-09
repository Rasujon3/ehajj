<?php

namespace App\Modules\Reservation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\News\Models\News;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Users\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Modules\ProcessPath\Models\ProcessType;
use PDO;
use App\Libraries\PostApiData;

class ReservationController extends Controller
{
    private function getTokenData(){
        $tokenUrl =  env('API_URL')."/api/getToken";
        $credential = [
            'clientid' => env('CLIENT_ID'),
            'username' => env('CLIENT_USER_NAME'),
            'password' => env('CLIENT_PASSWORD')
        ];

        return CommonFunction::getApiToken($tokenUrl, $credential);
    }

    public function reservationList(){
        $user_type = Auth::user()->user_type;
        if(!in_array($user_type, ['6x606','6x607'])){
            Session::flash('error', 'Invalid URL ! This incident will be reported.');
            return redirect('dashboard');
        }

        return view('Reservation::list');
    }

    public function getReservationList(){
        $user_type = Auth::user()->user_type;
        $token = $this->getTokenData();
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $base_url = env('API_URL');
        $apiUrl = "$base_url/api/reservation-list";

        $postData = ['tracking_no'=>explode("_",Auth::user()->user_email)[0]];
        $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);


        $apiResponseDataArr = json_decode($apiResponse['data']);
        $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];

        return Datatables::of($data)
        ->addColumn('action', function ($data) {
            return '<div class="btn-flex-center">
                        <a class="btn btn-primary btn-sm" href="' . url('reservation/make/3/' . $data->tracking_no) .'">
                            <span>Make Reservations</span>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="5" height="9" viewBox="0 0 5 9" fill="none">
                                    <path d="M1.29492 1.29165L4.50326 4.49998L1.29492 7.70831" stroke="white" stroke-width="0.6875" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </a>
                    </div>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function makeReservation(Request $request){
        $status_id = $request->segment(3);
        $tracking_no = $request->segment(4);
        try {
            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/reservation-view/$tracking_no";
            $postData = ['userId'=>Auth::user()->prp_user_id, 'status_id' => $status_id];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);

            if ($apiResponse['http_code'] != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!'];
                return response()->json($returnData);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!'];
                return response()->json($returnData);
            }

            $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            $pp = (array) $data->pilgrimTicketRequisitionDetails;
            if($data){
                $pilgrimTicketRequisitionDetails = $pp;
                $voucherInfo = $data->voucherInfo;
                $pilgrims = $data->pilgrims;
                $airlinesInfo = $data->airlinesInfo;
            }

            return view("Reservation::view", compact('voucherInfo', 'pilgrims', 'pilgrimTicketRequisitionDetails','airlinesInfo'));
        }
        catch (\Exception $e){
            return response()->json(['responseCode' => -1, 'data' => [],'msg'=>'Something went wrong.']);
        }
    }

    public function getFlightCode(Request $request){
        $date = date('Y-m-d', ($request->date));
        try {
            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/reservation-flight-code";
            $postData = [
                'tracking_no'=>explode("_",Auth::user()->user_email)[0],
                'date'=> $date
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);

            if ($apiResponse['http_code'] != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!'];
                return response()->json($returnData);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!'];
                return response()->json($returnData);
            }

            $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1, 'data' => $data]);
        }
        catch (\Exception $e){
            return response()->json(['responseCode' => -1, 'data' => [],'msg'=>'Something went wrong.']);
        }
    }

    public function showPilgrimListModal(Request $request)
    {
        $processRefId = $request->process_ref;
        $flightDate = $request->flightDate;
        $flightCode = $request->flightCode;
        $pnrNumber = $request->pnrNumber;
        $noOfPilgrim = $request->noOfPilgrim;
        $flight_slot_id = $request->flight_slot_id;
        return view('Reservation::add-pilgrim-ticket-reservation-modal', compact('processRefId', 'flightDate', 'flightCode', 'pnrNumber', 'noOfPilgrim', 'flight_slot_id'));
    }

    public function getPilgrimByFlightDate(Request $request)
    {
        $requisitionMasterId = Encryption::decodeId($request->get('requisition_master_id'));
        $flight_slot_id = Encryption::decode($request->get('flight_slot_id'));
        try {
            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/get-pilgrim-list-flight-date";
            $postData = [
                'requisition_master_id'=>$requisitionMasterId,
                'flight_slot_id'=> $flight_slot_id
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
            if ($apiResponse['http_code'] != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!'];
                return response()->json($returnData);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!'];
                return response()->json($returnData);
            }

            $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return Datatables::of($data)
                ->addColumn('action', function ($data) {
                    $checked = in_array($data->air_status, [1, 2]) ? 'checked' : '';
                    return '<div class="checkbox"><label><input type="checkbox" ' . $checked . ' value="' . Encryption::encodeId($data->id) . '" class="pilgrim_id"></label></div>';
                })
                ->removeColumn('id')
                ->make(true);
        }
        catch (\Exception $e){
            return response()->json(['responseCode' => -1, 'data' => [],'msg'=>'Something went wrong.']);
        }
    }


    public function addPilgrimToTicketReservation(Request $request)
    {

        try {
            $ticket_requisition_id = Encryption::decodeId($request->get('ticket_requisition_id'));
            $pilgrimIds = $request->get('pilgrims_id');
            $flight_date = !empty($request->get('flight_date')) ? Encryption::decode($request->get('flight_date')) : 0;
            $flight_code = !empty($request->get('flight_code')) ? Encryption::decode($request->get('flight_code')) : 0;
            $pnr_number = !empty($request->get('pnr_number')) ? Encryption::decode($request->get('pnr_number')) : null;
            $no_Of_pilgrim = !empty($request->get('no_Of_pilgrim')) ? Encryption::decode($request->get('no_Of_pilgrim')) : 0;
            $formatedFlightDate = !empty($flight_date) ? date('Y-m-d', strtotime($flight_date)) : null;
            $flight_slot_id = Encryption::decode($request->get('flight_slot_id'));

            $decoded_pilgrim_ids = [];
            foreach ($pilgrimIds as $pilgrimId) {
                $decoded_pilgrim_ids[] = Encryption::decodeId($pilgrimId);
            }

            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/add-pilgrim-to-ticket-reservation";
            $postData = [
                'tracking_no'=>explode("_",Auth::user()->user_email)[0],
                'ticket_requisition_id'=>$ticket_requisition_id,
                'pilgrimIds'=>$decoded_pilgrim_ids,
                'flight_date'=>$flight_date,
                'flight_code'=>$flight_code,
                'pnr_number'=>$pnr_number,
                'no_Of_pilgrim'=>$no_Of_pilgrim,
                'formatedFlightDate'=>$formatedFlightDate,
                'flight_slot_id'=> $flight_slot_id
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
            if ($apiResponse['http_code'] != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!'];
                return response()->json($returnData);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!'];
                return response()->json($returnData);
            }

            $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            Session::flash('success', 'Pilgrim add to ticket reservation has been updated successfully.');
            return response()->json(['responseCode' => 1, 'flight_slot_id' => Encryption::encode($data->flight_slot_id)]);
        } catch (\Exception $e) {
            Session::flash('error', 'Pilgrim add to ticket reservation failed, TRC:002');
            return response()->json(['responseCode' => 0, 'message' => 'Pilgrim add to ticket reservation failed, TRC:002']);
        }
    }
    public function getReservationDoneList(){
        $user_type = Auth::user()->user_type;
        $token = $this->getTokenData();
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $base_url = env('API_URL');
        $apiUrl = "$base_url/api/reservation-done-list";

        $postData = ['tracking_no'=>explode("_",Auth::user()->user_email)[0]];

        $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);

        $apiResponseDataArr = json_decode($apiResponse['data']);
        $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
        return Datatables::of($data)
            ->addColumn('action', function ($data) {
                return '<div class="btn-flex-center">
                        <a class="btn btn-primary btn-sm" href="' . url('reservation/make/4/' . $data->tracking_no) .'">
                            <span>Open</span>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="5" height="9" viewBox="0 0 5 9" fill="none">
                                    <path d="M1.29492 1.29165L4.50326 4.49998L1.29492 7.70831" stroke="white" stroke-width="0.6875" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </a>
                    </div>';
            })
            ->rawColumns(['action'])
            ->make(true);

    }

    public function completeTicketReservation(Request $request)
    {
        $ticket_requisition_id = Encryption::decodeId($request->get('ticket_req_master_id'));
        $flight_new_slot = $request->get('fligh_new_slots');
        $fligh_ext_slots = $request->get('fligh_ext_slots');
        if(!empty($fligh_ext_slots)){
            foreach ($fligh_ext_slots as &$flight_slot) {
                $flight_slot['flight_slot_id'] = Encryption::decodeId($flight_slot['flight_slot_id']);
            }
        }
        $token = $this->getTokenData();
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $base_url = env('API_URL');
        $apiUrl = "$base_url/api/complete-ticket-reservation";
        $postData = [
            'tracking_no' => explode("_prp",Auth::user()->user_email)[0],
            'ticket_requisition_id'=> $ticket_requisition_id,
            'flight_new_slot'=> $flight_new_slot,
            'fligh_ext_slots'=> $fligh_ext_slots,
        ];
        $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
        $apiResponseDataArr = json_decode($apiResponse['data']);
        if ($apiResponseDataArr->status != 200){
            $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
            return response()->json($returnData);
        }
        $response = ['responseCode' => 1, 'data' => [], 'msg'=> $apiResponseDataArr->msg];
        return $response;
    }

    public function draftTicketReservation(Request $request)
    {
        $ticket_requisition_id = Encryption::decodeId($request->get('ticket_req_master_id'));
        $flight_new_slot = $request->get('fligh_new_slots');
        $fligh_ext_slots = $request->get('fligh_ext_slots');
        if(!empty($fligh_ext_slots)){
            foreach ($fligh_ext_slots as &$flight_slot) {
                $flight_slot['flight_slot_id'] = Encryption::decodeId($flight_slot['flight_slot_id']);
            }
        }
        $token = $this->getTokenData();
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $base_url = env('API_URL');
        $apiUrl = "$base_url/api/draft-ticket-reservation";
        $postData = [
            'tracking_no' => explode("_prp",Auth::user()->user_email)[0],
            'ticket_requisition_id'=> $ticket_requisition_id,
            'flight_new_slot'=> $flight_new_slot,
            'fligh_ext_slots'=> $fligh_ext_slots,
        ];
        $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
        $apiResponseDataArr = json_decode($apiResponse['data']);
        if ($apiResponseDataArr->status != 200){
            $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
            return response()->json($returnData);
        }
        $response = ['responseCode' => 1, 'data' => [], 'msg'=> $apiResponseDataArr->msg];
        return $response;
    }

    public function cancelReservation(Request $request)
    {
        $ticket_requisition_id = Encryption::decodeId($request->get('ticket_req_master_id'));
        $token = $this->getTokenData();
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $base_url = env('API_URL');
        $apiUrl = "$base_url/api/ticket-reservation-cancel";
        $postData = [
            'tracking_no' => explode("_prp",Auth::user()->user_email)[0],
            'ticket_requisition_id'=> $ticket_requisition_id,
        ];
        $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
        $apiResponseDataArr = json_decode($apiResponse['data']);
        if ($apiResponseDataArr->status != 200){
            $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
            return response()->json($returnData);
        }
        $response = ['responseCode' => 1, 'data' => [], 'msg'=> $apiResponseDataArr->msg];
        return $response;
    }

    public function removePilgrimFromFlightDate(Request $request)
    {
        $ticket_requisition_id = Encryption::decodeId($request->get('process_ref'));
        $flight_slot_id = Encryption::decode($request->get('flight_slot_id'));
        $token = $this->getTokenData();
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $base_url = env('API_URL');
        $apiUrl = "$base_url/api/remove-pilgrims-from-flight";
        $postData = [
            'tracking_no' => explode("_prp",Auth::user()->user_email)[0],
            'ticket_requisition_id'=> $ticket_requisition_id,
            'flight_slot_id'=> $flight_slot_id
        ];
        $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
        $apiResponseDataArr = json_decode($apiResponse['data']);
        if ($apiResponseDataArr->status != 200){
            $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
            return response()->json($returnData);
        }
        $response = ['responseCode' => 1, 'data' => [], 'msg'=> $apiResponseDataArr->msg];
        return json_encode($response);
    }

    public function removePilgrimFromFlightRow(Request $request) {
        try {
            $pilgrim_id = Encryption::decodeId($request->get('pilgrim_id'));
            // $session_id = HajjSessions::where('state', '=', 'active')->pluck('id');

            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/remove-pilgrims-from-flight-row";
            $postData = [
                'tracking_no'=>explode("_",Auth::user()->user_email)[0],
                'pilgrim_id' => $pilgrim_id,
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);

            if ($apiResponse['http_code'] != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!'];
                return response()->json($returnData);
            }
            $returnData = ['responseCode' => 1, 'data' => 'Successfully removed.'];
            return response()->json($returnData);


        } catch (\Exception $e) {
            $response = ['responseCode' => 0, 'data' => Utility::eMsg($e, 'Could not removed, please try again.[TRC:004]')];
            return response()->json($returnData);
        }

    }

    public function addAllPilgrimToTicketReservation(Request $request)
    {
            $ticket_requisition_id = Encryption::decodeId($request->get('ticket_requisition_id'));
            $flight_date = $request->get('flight_date');
            $flight_code = $request->get('flight_code');
            $pnr_number = $request->get('pnr_number');
            $no_of_pilgrim = $request->get('no_of_pilgrim');
            $no_of_pilgrim  = (int)$no_of_pilgrim;
            $formatedFlightDate = !empty($flight_date) ? date('Y-m-d', strtotime($flight_date)) : null;
            $flight_slot_id = Encryption::decode($request->get('flight_slot_id'));

            // API Start
            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/add-all-pilgrim-to-ticket-reservation";
            $postData = [
                'tracking_no' => explode("_prp",Auth::user()->user_email)[0],
                'ticket_requisition_id' => $ticket_requisition_id,
                'flight_date' => $flight_date,
                'flight_code' => $flight_code,
                'pnr_number' => $pnr_number,
                'no_of_pilgrim' => $no_of_pilgrim,
                'flight_slot_id' => $flight_slot_id,
                'formatedFlightDate' => $formatedFlightDate
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $response = ['responseCode' => 1, 'data' => [], 'msg'=> $apiResponseDataArr->msg];
            return json_encode($response);
            // API End

    }

    public function removeAllPilgrimToTicketReservation(Request $request)
    {
            $ticket_requisition_id = Encryption::decodeId($request->get('ticket_requisition_id'));
            $flight_slot_id = Encryption::decode($request->get('flight_slot_id'));

            // API Start
            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/remove-all-pilgrim-to-ticket-reservation";
            $postData = [
                'tracking_no' => explode("_prp",Auth::user()->user_email)[0],
                'ticket_requisition_id' => $ticket_requisition_id,
                'flight_slot_id' => $flight_slot_id
            ];
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, json_encode($postData), $headers, true);
            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => $apiResponseDataArr->msg];
                return response()->json($returnData);
            }
            $response = ['responseCode' => 1, 'data' => [], 'msg'=> $apiResponseDataArr->msg];
            return json_encode($response);
            // API End
    }

}
