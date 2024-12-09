<?php

namespace App\Modules\RoomAllocation\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\PostApiData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class RoomAllocationController extends Controller
{
    private $flight_id = 0;
    private $guide_id = 0;
    private function getTokenData(){
        $tokenUrl =  env('API_URL')."/api/getToken";
        $credential = [
            'clientid' => env('CLIENT_ID'),
            'username' => env('CLIENT_USER_NAME'),
            'password' => env('CLIENT_PASSWORD')
        ];

        return CommonFunction::getApiToken($tokenUrl, $credential);
    }

    public function index()
    {
        $user_type = Auth::user()->user_type;
        if(!in_array($user_type, ['4x401','4x402','4x404','2x202','2x203'])){
            Session::flash('error', 'Invalid URL ! This incident will be reported.');
            return redirect('dashboard');
        }
        return view('RoomAllocation::index');
    }

    public function getGuideListByFlightId(Request $request)
    {
        try {
            $flight_id = $request['flight_val'];
            if(!$flight_id){
                $responseArr = ['responseCode' => -1, 'msg' => 'Please select flight at first!!'];
                return response()->json($responseArr);
            }

            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/get-flightWise-guide-list";
            $postData = ['flight_id'=>$flight_id];
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

            $guideList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1, 'data' => $guideList]);
        }
        catch (\Exception $e){
            return response()->json(['responseCode' => -1, 'data' => [],'msg'=>'Something went wrong.']);
        }

    }

    public function firstStep()
    {
        try {
            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/get-flight-list";
            $apiResponse = CommonFunction::curlPostRequest($apiUrl, [], $headers, true);

            if ($apiResponse['http_code'] != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!'];
                return response()->json($returnData);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!'];
                return response()->json($returnData);
            }
            $flightList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            $public_html = strval(view("RoomAllocation::steps.first_step", compact('flightList')));

            $returnArr = ['responseCode' => 1, 'html'=>$public_html];
            return response()->json($returnArr);
        }
        catch (\Exception $e){
            $returnArr = ['responseCode' => -1, 'html'=>''];
            return response()->json($returnArr);
        }
    }

    public function secondStep(Request $request)
    {
        try {
            $flight_id = $request['flight_val'];
            $guide_id = $request['guide_val'];
            if(!$flight_id){
                $responseArr = ['responseCode' => -1, 'msg' => 'Please select flight at first!!','html'=>''];
                return response()->json($responseArr);
            }
            if(!$guide_id){
                $responseArr = ['responseCode' => -1, 'msg' => 'Please select guide at first!!','html'=>''];
                return response()->json($responseArr);
            }

            /*$token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $roomAllocationDataUrl = "$base_url/api/room-allocation-data";
            $postData = ['guide_id'=>$guide_id,'flight_id'=>$flight_id];
            $apiResponse = CommonFunction::curlPostRequest($roomAllocationDataUrl, json_encode($postData), $headers, true);*/
            $apiResponse = $this->fetchRoomAllocationData($flight_id,$guide_id);

            if ($apiResponse['http_code'] != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!','html'=>''];
                return response()->json($returnData);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!','html'=>''];
                return response()->json($returnData);
            }
            $guideList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];

            $total_pilgrim_list_arr = [];
            $allocated_pilgrim_list_arr = [];
            $remaining_pilgrim_list_arr = [];
            foreach ($guideList->guideWisePilgrimSummary as $index=> $val){
                if($val->Gender == 'female'){
                    $total_pilgrim_list_arr['female'] = $val->Total;
                    $allocated_pilgrim_list_arr['female'] = $val->TotalAssign;
                    $remaining_pilgrim_list_arr['female'] = $val->Remaining;
                }
                if($val->Gender == 'male'){
                    $total_pilgrim_list_arr['male'] = $val->Total;
                    $allocated_pilgrim_list_arr['male'] = $val->TotalAssign;
                    $remaining_pilgrim_list_arr['male'] = $val->Remaining;
                }
                if($val->Gender == 'Total'){
                    $total_pilgrim_list_arr['Total'] = $val->Total;
                    $allocated_pilgrim_list_arr['Total'] = $val->TotalAssign;
                    $remaining_pilgrim_list_arr['Total'] = $val->Remaining;
                }
            }



            $roomList = [];
//            dd($guideList->roomWisePilgrimList);
            /*foreach ($guideList->roomWisePilgrimList as $pl){
                $roomList[$pl->Gender][]= $pl->MadinahRoomNo;
            }
            $roomList['both'] = count(array_intersect($roomList['male'], $roomList['female'])) > 1 ? array_intersect($roomList['male'], $roomList['female']) : [];

            // Remove the common items from array1 and array2
            $roomList['male'] = count(array_diff($roomList['male'], $roomList['both'])) > 0 ? array_diff($roomList['male'], $roomList['both']) : [];
            $roomList['female'] = count(array_diff($roomList['female'], $roomList['both'])) > 0 ? array_diff($roomList['female'], $roomList['both']) : [];*/

            $public_html = strval(view("RoomAllocation::steps.second_step", compact('guideList','roomList','total_pilgrim_list_arr','allocated_pilgrim_list_arr','remaining_pilgrim_list_arr')));

            $returnArr = ['responseCode' => 1, 'msg'=>'', 'html'=>$public_html];
            return response()->json($returnArr);
        }
        catch (\Exception $e){
            dd($e->getMessage(), $e->getFile(), $e->getLine());
            $returnArr = ['responseCode' => -1, 'msg'=>'Something went wrong', 'html'=>''];
            return response()->json($returnArr);
        }
    }

    public function thirdStep(Request $request)
    {
        try {
            $flight_id = $request['flight_val'];
            $guide_id = $request['guide_val'];
            $house_id = $request['house_id'];
            $floor_id = $request['floor_id'];
            if(!$flight_id){
                $responseArr = ['responseCode' => -1, 'msg' => 'Please select flight at first!!','html'=>''];
                return response()->json($responseArr);
            }
            if(!$guide_id){
                $responseArr = ['responseCode' => -1, 'msg' => 'Please select guide at first!!','html'=>''];
                return response()->json($responseArr);
            }
            if(!$house_id){
                $responseArr = ['responseCode' => -1, 'msg' => 'Please select house at first!!','html'=>''];
                return response()->json($responseArr);
            }
            if(!$floor_id){
                $responseArr = ['responseCode' => -1, 'msg' => 'Please select floor at first!!','html'=>''];
                return response()->json($responseArr);
            }

            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $roomAllocationDataUrl = "$base_url/api/roomList-with-pilgrim-data";

            $postData = [
                'guide_id'=>$guide_id,
                'flight_id'=>$flight_id,
                'house_id'=>$house_id,
                'floor_id'=>$floor_id,
            ];

            $apiResponse = CommonFunction::curlPostRequest($roomAllocationDataUrl, json_encode($postData), $headers, true);

            if ($apiResponse['http_code'] != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!','html'=>''];
                return response()->json($returnData);
            }

            $apiResponseDataArr = json_decode($apiResponse['data']);
            if ($apiResponseDataArr->status != 200){
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong from api server!!!','html'=>''];
                return response()->json($returnData);
            }

            $apiDataList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];

            $prprDataArr = [];
            foreach($apiDataList->roomWisePilgrimList as $index => $item){
                $prprDataArr[$item->MakkahHouseNo][$item->MakkahRoomNo][]=$item;
            }

            $public_html = strval(view("RoomAllocation::steps.third_step", compact('prprDataArr','apiDataList')));

            $returnArr = ['responseCode' => 1, 'msg'=>'', 'html'=>$public_html];
            return response()->json($returnArr);
        }
        catch (\Exception $e){
            #dd($e->getMessage(), $e->getFile(), $e->getLine());
            $returnArr = ['responseCode' => -1, 'msg'=>'Something went wrong', 'html'=>''];
            return response()->json($returnArr);
        }
    }

    public function fetchFloorList(Request $request)
    {
        try {
            $house_val= $request['house_val'];
            if(!$house_val){
                $responseArr = ['responseCode' => -1, 'msg' => 'Please select house at first!!'];
                return response()->json($responseArr);
            }

            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $apiUrl = "$base_url/api/floor-by-house";
            $postData = ['house_id'=>$house_val];
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

            $floorList = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
            return response()->json(['responseCode' => 1, 'data' => $floorList]);
        }
        catch (\Exception $e){
            return response()->json(['responseCode' => -1, 'data' => [],'msg'=>'Something went wrong.']);
        }
    }

    public function fetchRoomAllocationData($flight_id,$guide_id)
    {
        $token = $this->getTokenData();
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $base_url = env('API_URL');
        $roomAllocationDataUrl = "$base_url/api/room-allocation-data";
        $postData = ['guide_id'=>$guide_id,'flight_id'=>$flight_id];
        return CommonFunction::curlPostRequest($roomAllocationDataUrl, json_encode($postData), $headers, true);
    }

    public function setToRoom(Request $request)
    {
        try {
            $room_id = $request['room_id'];
            $pilgrim_list = $request['pilgrim_list'];
            $house_id = $request['house_id'];
            $floor_id = $request['floor_id'];
            if(empty($room_id) || empty($pilgrim_list) || empty($house_id) || empty($floor_id)){
                return response()->json(['responseCode' => -1, 'msg' => 'Please provide all information']);
            }

            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $roomAllocationDataUrl = "$base_url/api/set-to-madina-house";
            $postData = [
                'room_id'=>$room_id,
                'pilgrim_list'=>$pilgrim_list,
                'house_id'=>$house_id,
                'floor_id'=>$floor_id,
            ];
            $apiResponse = CommonFunction::curlPostRequest($roomAllocationDataUrl, json_encode($postData), $headers, true);

            if ($apiResponse['http_code'] != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Something went wrong!!!'];
                return response()->json($returnData);
            }
            return response()->json(['responseCode' => 1, 'msg' => 'Pilgrim assign to room successfully.']);
        }
        catch (\Exception $e){
            return response()->json(['responseCode' => -1, 'msg' => 'Something went wrong']);
        }
    }

    public function removeFromRoom(Request $request)
    {
        try {
            $pilgrim_list = $request['pilgrim_list'];
            if(empty($pilgrim_list)){
                return response()->json(['responseCode' => -1, 'msg' => 'Pilgrim need to be selected']);
            }

            $token = $this->getTokenData();
            $headers = array(
                'APIAuthorization: bearer ' . $token,
                'Content-Type: application/json',
            );
            $base_url = env('API_URL');
            $roomAllocationDataUrl = "$base_url/api/remove-from-madina-house";
            $postData['pilgrim_id_list'] = $pilgrim_list;

            $apiResponse = CommonFunction::curlPostRequest($roomAllocationDataUrl, json_encode($postData), $headers, true);

            if ($apiResponse['http_code'] != 200) {
                $returnData = ['responseCode' => -1, 'msg' => 'Pilgrim cannot be removed!!!'];
                return response()->json($returnData);
            }
            return response()->json(['responseCode' => 1, 'msg' => 'Pilgrim removed successfully.']);
        }
        catch (\Exception $e){
            return response()->json(['responseCode' => -1, 'msg' => 'Something went wrong']);
        }
    }
}
