<?php
namespace App\Modules\Web\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\PostApiData;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\Settings\Models\ActRules;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Settings\Models\NeedHelp;
use App\Modules\Settings\Models\UserManual;
use App\Modules\Settings\Models\WhatsNew;
use App\Modules\Web\Models\Complain;
use App\Modules\Web\Models\ComplainReason;
use Config;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ComplainController extends Controller
{

    public function index()
    {
        $complain_reasons = ComplainReason::where('status',1)->get();
        return view('complains',compact('complain_reasons'));
    }

    public function fetchData(Request $request)
    {
        $postData = [
            'tracking_no' => $request->tracking_no
        ];
        $postdata = http_build_query($postData);

        $base_url = env('API_URL');
        $url = "$base_url/api/get-pilgrim-info-for-complain";

        $response = PostApiData::getData($url,$postdata);

        $responseData= '';
        if ($response) {
            $responseData = json_decode($response);
        }
        return response()->json(['responseCode' => 1, 'data' => $responseData]);
    }
    public function fetchDataByPID(Request $request)
    {
        $postData = [
            'pid' => $request->pid
        ];
//        dd($postData);
        $postdata = http_build_query($postData);

        $base_url = env('API_URL');
        $url = "$base_url/api/get-pilgrim-info-for-complain-by-pid";

        $response = PostApiData::getData($url,$postdata);

        $responseData= '';
        if ($response) {
            $responseData = json_decode($response);
        }
        return response()->json(['responseCode' => 1, 'data' => $responseData]);
    }

    public function submitComplain(Request $request)
    {
        $pdf_upload_limit =Configuration::where('caption','PDF_Upload_Limit')->first();
        $max_size = $pdf_upload_limit!=null?$pdf_upload_limit->value:"500";
        $rules = [
            'country' => 'required',
            'self_pilgrim_yes' => 'required',
            'pdf_file' => 'nullable|mimes:pdf|max:' .$max_size,
            'selected_data' => 'required',

        ];

        $messages = [
            'country.required' => 'The country field is required.',
            'selected_data.required' => 'The complain reason field is required.',
            'self_pilgrim_yes.required' => 'The self_pilgrim_yes field is required.',
            'pdf_file.mimes' => "File Should be in PDF format",
            'pdf_file.max' => "Maximum File upload Limit ".$max_size." KB",
        ];

        $validatedData = $request->validate($rules, $messages);
        $pdfData = null;
        if($request->file('pdf_file')){
            $path = $request->file('pdf_file')->getRealPath();
            $pdfData = base64_encode(file_get_contents($path));
        }

        $hajjSessionId = HajjSessions::where(['state' => 'active'])->first('id');

        $processType = ProcessType::where('type_key','pilgrim_complain')->first();
        if($processType!=null){
            $processTypeId = $processType->id;
        }else{
            $processTypeId = 0;
        }
        try{
            DB::beginTransaction();
            $complain = new Complain();
            if ($request->country == 'ban') {
                $complain->country = "Bangladesh";
                $complain->tracking_no = $request->tracking_no;
                $complain->pid = $request->pid;
            }
            else {
                $complain->country = "SaudiArab";
                $complain->tracking_no = $request->tracking_no2;
                $complain->pid = $request->pid2;
            }
            $complain->agency_license_no = $request->license_no;
            $complain->agency_name = $request->agency_name;
            $complain->is_govt = $request->is_govt;
            $complain->is_self = $request->self_pilgrim_yes ? $request->self_pilgrim_yes : 0;
            $complain->pilgrim_name = $request->pilgrim_name;
            $complain->mobile = $request->pilgrim_mobile;
            $complain->email = $request->pilgrim_email;
            $complain->nid = $request->pilgrim_nid;
            $complain->status = 1;
            $complain->complain_reason = json_encode($request->selected_data);
            $complain->session_id = !empty($hajjSessionId->id) ? $hajjSessionId->id : 0;
            $complain->comment = $request->comment;
            $complain->complain_attachment = $pdfData;
            $complain->request_data = json_encode($request->all());


            $appData = new PilgrimDataList();
            $appData->listing_id = $complain->tracking_no;
            $appData->process_type = "Pilgrim Complain";
            $appData->process_type_id = $processTypeId;
            $appData->session_id = !empty($hajjSessionId->id) ? $hajjSessionId->id : 0;
            $appData->request_type = 'pilgrim_complain';
            $appData->json_object = $complain->request_data;

            $appData->save();
            $complain->pilgrim_data_list_id = $appData->id;
            $complain->save();
            $processData = new ProcessList();
            $processData->submitted_at = date('Y-m-d H:i:s', time());
            $processData->company_id = 0;
            $processData->locked_at = date('Y-m-d H:i:s');
            $processData->hash_value = '';
            $processData->previous_hash = '';
            $processData->process_desc = 'desc';
            $processData->pid = Str::uuid()->toString();
            $processData->status_id = 1;
            $processData->desk_id   = 5;
            $processData->process_type_id = $processTypeId;
            $processData->priority = 1;
            $processData->ref_id = $appData->id;
            $search_paramers = ['Tracking_no' => $complain->tracking_no];
            $processData->json_object = json_encode($search_paramers);
            $processData->created_at = date('Y-m-d H:i:s');
            $trackingPrefix = 'PC';

            $tracking_no = $trackingPrefix . strtoupper(dechex($processTypeId . $appData->id));
            $processData->tracking_no = $tracking_no;
            $processData->save();
            DB::commit();
            Session::flash('success', 'Successfully submitted this request');
            return redirect()->back();
        }catch(\Exception $e){
            Session::flash('error', $e->getMessage().$e->getFile().$e->getLine());
            return redirect()->back()->with('error',$e->getMessage().$e->getFile().$e->getLine());
        }

    }
//    public function showPdf($data){
//        dd(111);
//        $base64PDF = $data; // Base64 encoded PDF string
//
//        $pdfData = base64_decode($base64PDF);
//
//        return Response::make($pdfData, 200, [
//            'Content-Type' => 'application/pdf',
//            'Content-Disposition' => 'inline; filename="' . $data . '.pdf"',
//        ]);
//
//    }

}
