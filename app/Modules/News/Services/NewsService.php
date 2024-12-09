<?php

namespace App\Modules\News\Services;

use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\News\Models\News;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class NewsService
{
    protected $process_type_name;
    protected $process_type_id;
    protected $acl_name;
    protected $api_url;

    public function __construct(Object $processInfo)
    {
        $this->session_id = HajjSessions::where('state', 'active')->first(['caption', 'id as session_id']);
        $this->process_type_id = $processInfo->process_type_id ?? null;
        $this->process_type_name = $processInfo->name ?? '';
        $this->acl_name = $processInfo->acl_name ?? '';
        $this->api_url =  env('API_URL');
    }

    public function createForm(): string
    {
        $process_info = ProcessType::where('id', $this->process_type_id)->first([
            'id as process_type_id',
            'acl_name',
            'form_id',
            'name',
            'active_menu_for',
        ]);

        if(!in_array(\Auth::user()->user_type, explode(',',$process_info->active_menu_for))){
            Session::flash('error','You have no access right! Please contact system administration for more information.');
            return redirect('/process/list');
        }
        $data['process_type_id'] = $this->process_type_id;
        $data['session_id'] = $this->session_id;

        $data['post_types'] = CommonFunction::get_enum_values( 'news', 'post_type' );
        return strval(view("News::form",$data));
    }

    public function storeForm(Request $request)
    {
        if ($request->get('app_id')) {
            $appData = News::find(Encryption::decodeId($request->get('app_id')));
            $processData = ProcessList::where([
                'process_type_id' => $this->process_type_id,
                'ref_id' => $appData->id
            ])->first();
        } else {
            $appData = new News();
            $processData = new ProcessList();
            $appData->file_path = '';
        }
        if(empty($request['title']) || empty($request['description']) || empty($request['publish_date']) || empty($request['post_type'])){
            Session::flash('error','News related information need to be provided');
            return redirect()->back();
        }

        $appData->title = $request['title'];
        $appData->description = $request['description'];
        $appData->publish_date = date('Y-m-d', strtotime($request['publish_date']));
        $appData->post_type = $request['post_type'];

        $appData->status = 0;
        $appData->post_status = 'private';
        if ($request->get('actionBtn') === 'draft') {
            $appData->status = -1;
            $appData->post_status = 'draft';
        }

        if ($request->hasFile('file')) {
            $path = 'news/uploads/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $uploadedFile = $request->file('file');
            $originalFileName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->getClientOriginalExtension();
            $newFileName = uniqid() .  str_replace(' ','',microtime()) . '.' . $extension;
            $fileContent = file_get_contents($uploadedFile->getRealPath());
            if (file_put_contents($path.$newFileName, $fileContent)) {
                $appData->file_path = $path.$newFileName;
            }
        }
        $masterData = [
            'title' => $appData->title,
//            'description' => $appData->description,
            'publish_date' => $appData->publish_date,
//            'file_path' => $appData->file_path,
        ];
    
        $appData->save();

        if ($appData->id) {
            # store process list information
            $this->storeProcessListData($request, $processData, $appData, $masterData);
        }

        DB::commit();
        CommonFunction::setFlashMessageByStatusId($processData->status_id);
        return redirect('/process/list');
    }

    public function viewForm($processTypeId, $applicationId): JsonResponse
    {
        try {
            $appmasterId = Encryption::decodeId($applicationId);
            $data = array();
            $data['data'] = News::where('id', $appmasterId)->first();
            $data['session_id'] = $this->session_id;
            $data['process_type_id'] = $processTypeId;
            $public_html =  strval(view("News::view",$data));
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        }
        catch (\Exception $e){
            echo 'something went wrong.';
            exit();
//            dd($e->getMessage());
        }
    }

    public function editForm($processTypeId, $applicationId): JsonResponse
    {
        $appmasterId = Encryption::decodeId($applicationId);
        $data = array();

        $data['data'] = News::where('id', $appmasterId)->first();
        $data['session_id'] = $this->session_id;
        $data['process_type_id'] = $processTypeId;
        $data['post_types'] = CommonFunction::get_enum_values( 'news', 'post_type' );

        $public_html = (string)view("News::form-edit", $data);
        return response()->json(['responseCode' => 1, 'html' => $public_html]);
    }

    private function storeProcessListData(Request $request, $processListObj, $appData, $masterData = [])
    {
        $processListObj->company_id = 0;
        $processListObj->cat_id = 1;

        if ($request->get('actionBtn') === 'draft') {
            $processListObj->status_id = -1;
            $processListObj->desk_id = 0;
        } elseif ($processListObj->status_id === 5) {
            // For shortfall
            $submission_sql_param = [
                'app_id' => $appData->id,
                'process_type_id' => $this->process_type_id,
            ];

            $process_type_info = ProcessType::where('id', $this->process_type_id)
                ->orderBy('id', 'desc')
                ->first([
                    'form_url',
                    'process_type.process_desk_status_json',
                    'process_type.name'
                ]);

            $resubmission_data = $this->getProcessDeskStatus('resubmit_json', $process_type_info->process_desk_status_json, $submission_sql_param);
            $processListObj->status_id = $resubmission_data['process_starting_status'];
            $processListObj->desk_id = $resubmission_data['process_starting_desk'];
            $processListObj->process_desc = 'Re-submitted form applicant';
            $processListObj->resubmitted_at = Carbon::now(); // application resubmission Date

            $resultData = "{$processListObj->id}-{$processListObj->tracking_no}{$processListObj->desk_id}-{$processListObj->status_id}-{$processListObj->user_id}-{$processListObj->updated_by}";

            $processListObj->previous_hash = $processListObj->hash_value ?? '';
            $processListObj->hash_value = Encryption::encode($resultData);

        } else {
            $processListObj->status_id = 1;
            $processListObj->desk_id = 5;
        }
        # generate tracking no
        if ($request->get('actionBtn') == 'submit') {
            if (empty($processListObj->tracking_no)) {
                $trackingPrefix = 'SV-';
                $processListObj->tracking_no = $trackingPrefix . strtoupper(dechex($this->process_type_id . $appData->id));
            }
        }

        $processListObj->ref_id = $appData->id;
        $processListObj->process_type_id = $this->process_type_id;
        $processListObj->office_id = 0;
        $processListObj['json_object'] = json_encode($masterData);
        $processListObj->submitted_at = Carbon::now();
        $processListObj->save();
        return $processListObj;
    }


    public function getProcessDeskStatus($payment_json_name, $json_data, $sql_params)
    {
        $decoded_json = json_decode($json_data, true);
        if (!isset($decoded_json[$payment_json_name]) or empty($decoded_json[$payment_json_name])) {
            throw new Exception('Proper Json data found for this payment processing. Please configure proper json data.');
        }

        if (!isset($decoded_json[$payment_json_name]['process_starting_desk_sql']) or empty($decoded_json[$payment_json_name]['process_starting_desk_sql'])) {
            throw new Exception('Process desk SQL not found for this payment processing. Please configure proper json data.');
        }

        if (!isset($decoded_json[$payment_json_name]['process_starting_status_sql']) or empty($decoded_json[$payment_json_name]['process_starting_status_sql'])) {
            throw new Exception('Process status SQL not found for this payment processing. Please configure proper json data.');
        }

        $process_desk_sql = $decoded_json[$payment_json_name]['process_starting_desk_sql'];
        $process_desk_sql = str_replace("{app_id}", $sql_params['app_id'], $process_desk_sql);

        $process_status_sql = $decoded_json[$payment_json_name]['process_starting_status_sql'];
        $process_status_sql = str_replace("{app_id}", $sql_params['app_id'], $process_status_sql);

        $process_desk_result = DB::select(DB::raw($process_desk_sql));
        $process_status_result = DB::select(DB::raw($process_status_sql));

        if (!isset($decoded_json[$payment_json_name]['process_starting_user_sql']) or empty($decoded_json[$payment_json_name]['process_starting_user_sql'])) {
            $process_starting_user = 0;
        } else {
            $process_user_sql = $decoded_json[$payment_json_name]['process_starting_user_sql'];
            $process_user_sql = str_replace("{app_id}", $sql_params['app_id'], $process_user_sql);

            $process_user_result = DB::select(DB::raw($process_user_sql));
            $process_starting_user = (int) $process_user_result[0]->process_starting_user;
        }

        $data = [
            'process_starting_desk' => (int)$process_desk_result[0]->process_starting_desk,
            'process_starting_status' => (int) $process_status_result[0]->process_starting_status,
            'process_starting_user' => $process_starting_user
        ];

        return $data;
    }
}
