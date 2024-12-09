<?php

namespace App\Modules\REUSELicenseIssue\Http\Controllers;

use App\Http\Traits\GenderChangeRequestTrait;
use App\Http\Traits\HajjCanceledRequestTrait;
use App\Http\Traits\TravelPlanTrait;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\PostApiData;
use App\Modules\News\Services\News;
use App\Modules\News\Services\NewsService;
use App\Modules\ProcessPath\Models\FlightRequestPilgrim;
use App\Modules\REUSELicenseIssue\Http\FormHandler;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Libraries\Encryption;
use App\Modules\REUSELicenseIssue\Services\StickerVisa;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\SonaliPayment\Services\SPPaymentManager;
use App\Modules\SonaliPayment\Services\SPAfterPaymentManager;
use App\Http\Traits\Token;
use Illuminate\Support\Str;
use App\Http\Traits\MaterialReceiveTrait;
use App\Http\Traits\MaterialIssueTrait;
use App\Modules\REUSELicenseIssue\Services\PilgrimAssignByGuide;
use App\Modules\REUSELicenseIssue\Services\PilgrimComplain;
use App\Modules\REUSELicenseIssue\Http\Controllers\AjaxRequestController;

class ReuseController extends Controller
{
    use SPPaymentManager;
    use SPAfterPaymentManager;
    use Token;
    use MaterialReceiveTrait;
    use MaterialIssueTrait;
    use TravelPlanTrait;
    use GenderChangeRequestTrait;
    use HajjCanceledRequestTrait;

    protected $process_type_id;
    protected $acl_name;
    protected $process_info;
    protected $prefix = '';


    public function __construct($process_type_id = 0, $process_info = '')
    {
        $session_id = HajjSessions::where('state', 'active')->first(['caption', 'id as session_id']);
        $this->session_id = $session_id;
        $this->process_type_id = $process_type_id;
        $this->acl_name = $process_info ? $process_info->acl_name : '';
        $this->process_info = $process_info;

        $user_type = Auth::user()->user_type;
        $type = explode('x', $user_type);
        if ($type[0] == 5) {
            $this->prefix = 'client';
        }
    }

    public function processContentAddForm()
    {

        switch ($this->process_type_id) {
            case 1:
                $data = array();
                $data['process_type_id'] = $this->process_type_id;
                $data['session_id'] = $this->session_id;
                $data['process_info'] = $this->process_info;
                //   $data['pilgirm_dropdown_list'] = $this->getPilgrimListingDropdonw("Government");
                $public_html = strval(view("REUSELicenseIssue::Listing.Government.master", $data));
                break;
            case 2:
                $data = array();
                $data['process_type_id'] = $this->process_type_id;
                $data['session_id'] = $this->session_id;
                $data['process_info'] = $this->process_info;
                //    $data['pilgirm_dropdown_list'] = $this->getPilgrimListingDropdonw("Private");
                $public_html = strval(view("REUSELicenseIssue::Listing.Private.master", $data));
                break;
            case 3:
                $public_html = $this->addTravelPlanForm();
                break;
            case 4:
                $public_html = $this->addMaterialReceiveForm();
                break;
            case 5:
                $public_html = $this->addMaterialIssueForm();
                break;
            case 6:
                $public_html = $this->addGenderChangeRequestForm();
                break;
            case 7:
                $public_html = $this->addHajjCanceledRequestForm();
                break;
            case 8:
                $public_html = (new StickerVisa($this->process_info))->createForm();
                break;
            case 9:
                $public_html = (new PilgrimAssignByGuide($this->process_info))->createForm();
                break;
            case 11:
                $public_html = (new NewsService($this->process_info))->createForm();
                break;

            case 12:
                $data = array();
                $data['process_type_id'] = Encryption::encodeId($this->process_type_id);
                $data['session_id'] = $this->session_id;
                $data['process_info'] = $this->process_info;
                $data['status'] = ['1' => 'Active', '0' => 'Inactive'];
                $data['inactive_reason'] = [
                    'Suspend' => 'Suspend',
                    'Cancel' => 'Cancel',
                    'Regulatory Violations' => 'Regulatory Violations',
                    'Financial Issues' => 'Financial Issues',
                    'Expired' => 'Expired'
                ];
                $public_html = strval(view("REUSELicenseIssue::Agency.agencyInfoUpdate", $data));
                break;
            default:
                return false;
        }
        return $public_html;
    }

    public function processContentStore($request, $excel)
    {
        // Set permission mode and check ACL
        $app_id = (!empty($request->get('app_id')) ? Encryption::decodeId($request->get('app_id')) : '');
        $mode = (!empty($request->get('app_id')) ? '-E-' : '-A-');

        $active_menu_for_arr = explode(',',$this->process_info->active_menu_for);
        if (!ACL::getAccsessRight($this->acl_name, $mode, $app_id,$active_menu_for_arr)) {
            abort('400', "You have no access right! Please contact with system admin if you have any query.");
        }
        $hajjSessionId = HajjSessions::where(['state' => 'active'])->first('id');

        try {
            switch ($this->process_type_id) {
                case 1:
                    try {

                        if ($request->get('actionBtn') != "cancel") {

                            if ($request->get('app_id')) {
                                $appData = PilgrimDataList::find($app_id);
                                $processData = ProcessList::where([
                                    'process_type_id' => $this->process_type_id,
                                    'ref_id' => $appData->id
                                ])->first();
                            } else {
                                $appData = new PilgrimDataList();
                                $processData = new ProcessList();
                            }

                            DB::beginTransaction();
                            if ($request->full_name_english) {
                                foreach ($request->full_name_english as $arrKey => $data) {
                                    $pilgrimData[$arrKey]['full_name_english'] = $data;
                                    $pilgrimData[$arrKey]['tracking_no'] = $request->tracking_no[$arrKey];
                                    $pilgrimData[$arrKey]['pilgrim_id'] = $request->pilgrim_id[$arrKey];
                                    $pilgrimData[$arrKey]['serial_no'] = $request->serial_no[$arrKey];
                                    $pilgrimData[$arrKey]['mobile'] = $request->mobile[$arrKey];
                                }
                                $jsonData = json_encode($pilgrimData);
                            }

                            if ($request->full_name_english) {
                                $appData->json_object = $jsonData;
                            }
                            $appData->listing_id = $request->listing_id;
                            $appData->process_type = $request->process_type;
                            $appData->process_type_id = $this->process_type_id;
                            $appData->session_id = !empty($hajjSessionId->id) ? $hajjSessionId->id : 0;
                            $appData->request_type = 'Government';
                            $appData->save();

                            $search_paramers = explode(',', $request->request_data);

                            $processData->company_id = Auth::user()->working_company_id;
                            $processData->locked_at = date('Y-m-d H:i:s');
                            $processData->hash_value = '';
                            $processData->previous_hash = '';
                            $processData->process_desc = 'desc';
                            $processData->submitted_at = date('Y-m-d H:i:s', time());
                            $processData->pid = Str::uuid()->toString();
                            if ($request->get('actionBtn') == "draft") {
                                $processData->status_id = -1;
                                $processData->desk_id = 0;
                            } else {
                                if ($processData->status_id == 5) { // For shortfall
                                    // Get last desk and status
                                    $submission_sql_param = [
                                        'app_id' => $appData->id,
                                        'process_type_id' => $this->process_type_id,
                                    ];
                                    $process_type_info = ProcessType::where('id', $this->process_type_id)
                                        ->orderBy('id', 'desc')
                                        ->first([
                                            'form_url',
                                            'process_type.process_desk_status_json',
                                            'process_type.name',
                                        ]);
                                    $resubmission_data = $this->getProcessDeskStatus('resubmit_json', $process_type_info->process_desk_status_json, $submission_sql_param);
                                    $processData->status_id = $resubmission_data['process_starting_status'];
                                    $processData->desk_id = $resubmission_data['process_starting_desk'];
                                    $processData->process_desc = 'Re-submitted form applicant';
                                    $processData->resubmitted_at = Carbon::now(); // application resubmission Date

                                    $resultData = $processData->id . '-' . $processData->tracking_no .
                                        $processData->desk_id . '-' . $processData->status_id . '-' . $processData->user_id . '-' .
                                        $processData->updated_by;

                                    $processData->previous_hash = isset($processData->hash_value) ? $processData->hash_value : "";
                                    $processData->hash_value = Encryption::encode($resultData);
                                } else {
                                    $processData->status_id = 1;
                                    $processData->desk_id = 5;
                                }
                            }
                            $processData->process_type_id = $this->process_type_id;
                            $processData->priority = 1;
                            $processData->ref_id = $appData->id;
                            $processData->json_object = json_encode($search_paramers);
                            $processData->created_at = date('Y-m-d H:i:s');
                            $processData->save();
                        }
                        else {
                            $processData = ProcessList::where([
                                'process_type_id' => $this->process_type_id,
                                'ref_id' => $app_id
                            ])->first();
                            $processData->status_id = -1;
                            $processData->desk_id = 0;
                            $processData->save();
                        }
                        DB::commit();
                        return Redirect::to('/process/application_list');
                        break;
                    } catch (\Exception $e) {
                        DB::rollback();
                        Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage());
                        return Redirect::back()->withInput();
                    }
                case 2:
                    try {

                        if ($request->get('actionBtn') != "cancel") {

                            if ($request->get('app_id')) {
                                $appData = PilgrimDataList::find($app_id);
                                $processData = ProcessList::where([
                                    'process_type_id' => $this->process_type_id,
                                    'ref_id' => $appData->id
                                ])->first();
                            } else {
                                $appData = new PilgrimDataList();
                                $processData = new ProcessList();
                            }

                            DB::beginTransaction();
                            if ($request->full_name_english) {
                                foreach ($request->full_name_english as $arrKey => $data) {
                                    $pilgrimData[$arrKey]['full_name_english'] = $data;
                                    $pilgrimData[$arrKey]['tracking_no'] = $request->tracking_no[$arrKey];
                                    $pilgrimData[$arrKey]['pilgrim_id'] = $request->pilgrim_id[$arrKey];
                                    $pilgrimData[$arrKey]['serial_no'] = $request->serial_no[$arrKey];
                                    $pilgrimData[$arrKey]['mobile'] = $request->mobile[$arrKey];
                                }
                                $jsonData = json_encode($pilgrimData);
                            }
                            if ($request->full_name_english) {
                                $appData->json_object = $jsonData;
                            }
                            $appData->listing_id = $request->listing_id;
                            $appData->process_type = $request->process_type;
                            $appData->process_type_id = $this->process_type_id;
                            $appData->session_id = !empty($hajjSessionId->id) ? $hajjSessionId->id : 0;
                            $appData->request_type = 'Private';
                            $appData->save();

                            $search_paramers = explode(',', $request->request_data);

                            $processData->submitted_at = date('Y-m-d H:i:s', time());
                            $processData->company_id = 0;
                            $processData->locked_at = date('Y-m-d H:i:s');
                            $processData->hash_value = '';
                            $processData->previous_hash = '';
                            $processData->process_desc = 'desc';
                            $processData->pid = Str::uuid()->toString();
                            if ($request->get('actionBtn') == "draft") {
                                $processData->status_id = -1;
                                $processData->desk_id = 0;
                            } else {
                                if ($processData->status_id == 5) { // For shortfall
                                    // Get last desk and status
                                    $submission_sql_param = [
                                        'app_id' => $appData->id,
                                        'process_type_id' => $this->process_type_id,
                                    ];
                                    $process_type_info = ProcessType::where('id', $this->process_type_id)
                                        ->orderBy('id', 'desc')
                                        ->first([
                                            'form_url',
                                            'process_type.process_desk_status_json',
                                            'process_type.name',
                                        ]);
                                    $resubmission_data = $this->getProcessDeskStatus('resubmit_json', $process_type_info->process_desk_status_json, $submission_sql_param);
                                    $processData->status_id = $resubmission_data['process_starting_status'];
                                    $processData->desk_id = $resubmission_data['process_starting_desk'];
                                    $processData->process_desc = 'Re-submitted form applicant';
                                    $processData->resubmitted_at = Carbon::now(); // application resubmission Date
                                    $resultData = $processData->id . '-' . $processData->tracking_no . $processData->desk_id . '-' . $processData->status_id . '-' . $processData->user_id . '-' . $processData->updated_by;

                                    $processData->previous_hash = isset($processData->hash_value) ? $processData->hash_value : "";
                                    $processData->hash_value = Encryption::encode($resultData);
                                } else {
                                    $processData->status_id = 1;
                                    $processData->desk_id = 5;
                                }
                            }
                            $processData->process_type_id = $this->process_type_id;
                            $processData->priority = 1;
                            $processData->ref_id = $appData->id;
                            $processData->json_object = json_encode($search_paramers);
                            $processData->created_at = date('Y-m-d H:i:s');
                            $processData->save();
                        }
                        else {
                            $processData = ProcessList::where([
                                'process_type_id' => $this->process_type_id,
                                'ref_id' => $app_id
                            ])->first();
                            $processData->status_id = -1;
                            $processData->desk_id = 0;
                            $processData->save();
                        }
                        DB::commit();
                        return Redirect::to('/process/application_list');
                        break;
                    } catch (\Exception $e) {
                        DB::rollback();
                        Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage());
                        return Redirect::back()->withInput();
                    }
                    break;
                case 3:
                    try {
                        $this->storeTravelPlanContent($request, $app_id, $mode);
                        DB::commit();
                        return Redirect::to('/process/list');
                        break;
                    } catch (\Exception $e) {
                        DB::rollback();
                        Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage());
                        return Redirect::back()->withInput();
                    }
                    break;
                case 4:
                    try {
                        $this->storeMaterialReceive($request, $excel);
                        return Redirect::to('/process/list');

                        break;
                    } catch (\Exception $e) {
                        DB::rollback();
                        Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage());
                        return Redirect::back()->withInput();
                    }
                    break;
                case 5:
                    try {
                        $this->storeMaterialIssue($request, $excel);
                        return Redirect::to('/process/list');
                        break;
                    } catch (\Exception $e) {
                        DB::rollback();
                        Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage());
                        return Redirect::back()->withInput();
                    }
                    break;
                case 6:
                    try {
                        if(!in_array(Auth::user()->user_type, $active_menu_for_arr)) {
                            Session::flash('error', 'You have no access right! Please contact with system admin if you have any query.');
                            return Redirect::back()->withInput();
                        }
                        $this->storeGenderChangeRequestContent($request, $app_id, $mode);
                        DB::commit();
                        return Redirect::to('/process/application_list');
                        break;
                    } catch (\Exception $e) {
                        DB::rollback();
                        Log::error($e->getMessage() . ' @@@@ ' . $e->getFile() . ' @@@@ ' . $e->getLine().' [SGCRC-001]');
                        Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage() . '[SGCRC-001]');
                        return Redirect::back()->withInput();
                    }
                    break;
                case 7:
                    try {
                        $this->storeHajjCanceledRequestContent($request, $app_id, $mode);
                        DB::commit();
                        return Redirect::to('/process/application_list');
                        break;
                    } catch (\Exception $e) {
                        DB::rollback();
                        Session::flash('error', 'Sorry! Something is Wrong.' . $e->getMessage());
                        return Redirect::back()->withInput();
                    }
                    break;
                case 8:
                    return (new StickerVisa($this->process_info))->storeForm($request);
                case 9:
                    return (new PilgrimAssignByGuide($this->process_info))->storeForm($request);
                case 11:
                    return (new NewsService($this->process_info))->storeForm($request);
                case 12:
                    return (new AjaxRequestController())->agencyInfoStoreForm($request);
                default:
                    return false;

            }
        } catch (\Exception $exception) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($exception->getMessage()) . '[IN-1025]');
            return redirect()->back()->withInput();
        }

    }

    public function processContentEdit($applicationId, $openMode = '', $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        $active_menu_for_arr = explode(',',$this->process_info->active_menu_for);
        if (!ACL::getAccsessRight($this->acl_name, '-A-',null,$active_menu_for_arr)) {
            return response()->json([
                'responseCode' => 1,
                'html' => 'You have no access right! Contact with system admin for more information1'
            ]);
        }

        switch ($this->process_type_id) {
            case 1:
                $appmasterId = Encryption::decodeId($applicationId);
                $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
                    ->first();
                $data = array();
                $data['data'] = $pilgrimdata;
                $data['session_id'] = $this->session_id;
                $data['process_type_id'] = $this->process_type_id;
                $data['process_info'] = $this->process_info;
                $data['pilgirm_dropdown_list'] = $this->getPilgrimListingDropdonw("Government");
                $public_html = (string)view("REUSELicenseIssue::Listing.Government.masterEdit", $data);
                return response()->json(['responseCode' => 1, 'html' => $public_html]);
                break;

            case 2:
                $appmasterId = Encryption::decodeId($applicationId);
                $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
                    ->first();

                $data = array();
                $data['data'] = $pilgrimdata;
                $data['session_id'] = $this->session_id;
                $data['process_type_id'] = $this->process_type_id;
                $data['process_info'] = $this->process_info;
                $data['pilgirm_dropdown_list'] = $this->getPilgrimListingDropdonw("Private");
                $public_html = (string)view("REUSELicenseIssue::Listing.Private.masterEdit", $data);
                return response()->json(['responseCode' => 1, 'html' => $public_html]);
                break;
            case 3:
                $appmasterId = Encryption::decodeId($applicationId);
                $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
                    ->first();
                $data = array();
                $data['data'] = $pilgrimdata;
                $data['session_id'] = $this->session_id;
                $data['process_type_id'] = $this->process_type_id;
                $data['flight_drop_down_list'] = $this->getFlightDropdonw();
                $data['flight_request_pilgrims_array'] = FlightRequestPilgrim::where('pilgrim_data_list_id', $pilgrimdata->id)->where('session_id', $data['session_id']->session_id)->pluck('pid')->toArray();
                $data['pilgrim_array'] = json_encode($data['flight_request_pilgrims_array']);


                $public_html = (string)view("REUSELicenseIssue::Listing.TravelPlan.masterEdit", $data);
                return response()->json(['responseCode' => 1, 'html' => $public_html]);
                break;
            case 4:
                break;
            case 8:
                return (new StickerVisa($this->process_info))->editForm($request, $applicationId);
                break;
            case 9:
                $public_html = (new PilgrimAssignByGuide($this->process_info))->editForm($this->process_type_id, $applicationId);

                return $public_html;
                break;

            case 11:
                $public_html = (new NewsService($this->process_info))->editForm($this->process_type_id, $applicationId);
                return $public_html;
                break;
            default:
                return false;
        }
    }

    public function precessContentView($appId, $openMode = '', $request)
    {
        $active_menu_for_arr = explode(',',$this->process_info->active_menu_for);
        if (!ACL::getAccsessRight($this->acl_name, '-V-',null,$active_menu_for_arr)) {
            return response()->json([
                'responseCode' => 0,
                'html' => "<h4>You have no access right! Contact with system admin for more information. [BRC-974-1]</h4>"
            ]);
        }

        switch ($this->process_type_id) {
            case 1:
                $appmasterId = Encryption::decodeId($appId);
                $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
                    ->first();
                $data = array();
                $data['data'] = $pilgrimdata;
                $data['process_type_id'] = $this->process_type_id;

                $public_html = (string)view("REUSELicenseIssue::Listing.Government.masterView", $data);

                return response()->json(['responseCode' => 1, 'html' => $public_html]);
                break;

            case 2:

                $appmasterId = Encryption::decodeId($appId);
                $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
                    ->first();
                $data = array();
                $data['data'] = $pilgrimdata;
                $data['process_type_id'] = $this->process_type_id;

                $public_html = (string)view("REUSELicenseIssue::Listing.Private.masterView", $data);

                return response()->json(['responseCode' => 1, 'html' => $public_html]);
                break;
            case 3:
                $public_html = $this->viewTravelPlanForm($appId);
                return response()->json(['responseCode' => 1, 'html' => $public_html]);
                break;
            case 4:
                $public_html = $this->viewMaterialReceiveForm($appId);
                return response()->json(['responseCode' => 1, 'html' => $public_html]);
                break;
            case 5:
                $public_html = $this->viewMaterialIssueForm($appId);
                return response()->json(['responseCode' => 1, 'html' => $public_html]);
                break;

            case 6:
                $public_html = $this->viewGenderChangeRequestForm($appId);
                return response()->json(['responseCode' => 1, 'html' => $public_html]);
                break;

                return response()->json(['responseCode' => 1, 'html' => $public_html]);
                break;

            case 7:
                $public_html = $this->viewHajjCanceledRequestForm($appId);
                return response()->json(['responseCode' => 1, 'html' => $public_html]);
                break;
            case 8:
                return (new StickerVisa($this->process_info))->viewForm($request, $appId);
            case 9:
                $public_html = (new PilgrimAssignByGuide($this->process_info))->viewForm($this->process_type_id, $appId);
                return $public_html;
                break;
            case 10:
                $public_html = (new PilgrimComplain($this->process_info))->viewForm($this->process_type_id, $appId);
                return $public_html;
                break;
            case 11:
                $public_html = (new NewsService($this->process_info))->viewForm($this->process_type_id, $appId);
                return $public_html;
                break;
            case 12:
                $public_html = (new AjaxRequestController())->viewAgencyInfo($this->process_type_id, $appId);
                return $public_html;
                break;

            default:
                return false;
        }
    }

    public function getPilgrimListingDropdonw($process_type)
    {
        $tokenData = $this->getToken();
        $token = json_decode($tokenData)->token;

        $ch = curl_init();

        // Set the API endpoint URL
        $base_url = env('API_URL');
        $url = "$base_url/api/get-pilgrim_listing_dropdown?pilgrim_type=$process_type";


        // $pilgrim_type = "Government";
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/x-www-form-urlencoded',
        );

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


        $response = curl_exec($ch);

        if (curl_error($ch)) {
            echo 'cURL error: ' . curl_error($ch);
        }

        curl_close($ch);

        if ($response) {
            $responseData = json_decode($response, true);
        }
        return $responseData;
    }

    public function getFlightDropdonw()
    {
        $base_url = env('API_URL');
        $url = "$base_url/api/active-flight-list";
        $response = PostApiData::getData($url, []);
        $hajjSessionId = HajjSessions::where(['state' => 'active'])->first('id');
        if ($response) {
            $responseData = json_decode($response, 1);
            if (!isset($responseData['data'])) {
                return $response;
            }
            $user_id = Auth::user()->id;

            $pilgrim_data_id_array = PilgrimDataList::where('created_by', $user_id)
                ->where('request_type', 'Travel_Plan')
                ->where('process_type_id', 3)
                ->where('session_id', $hajjSessionId->id)
                ->pluck('id')
                ->toArray();


            if (count($pilgrim_data_id_array) > 0) {
                $status_array = [1, 25];
                $process_list_status = ProcessList::where('process_type_id', 3)
                    ->whereIn('ref_id', $pilgrim_data_id_array)
                    ->whereIn('status_id', $status_array)
                    ->select('ref_id', 'status_id')
                    ->orderBy('id', 'desc')
                    ->first();

                if (isset($process_list_status)) {
                    $array_flight_id = [];
                    foreach ($responseData['data'] as $key => $d){
                        array_push($array_flight_id,$d['id']);
                    }
                    if(!in_array(Auth::user()->flight_id,$array_flight_id)){
                        $data = [
                            'responseTime' => $responseData['responseTime'],
                            'responseType' => '',
                            'status' => 200,
                            'response' => 'success',
                            'msg' => "Flight found!",
                            "data" => []
                        ];
                        $responseData = $data;
                    }else{
                        if (Auth::user()->flight_id == 0) {
                            $existing_flight_id = PilgrimDataList::where('id', $process_list_status->ref_id)
                                ->first(['listing_id']);
                            foreach ($responseData['data'] as $key => $d) {
                                if ($d['id'] == $existing_flight_id->listing_id) {
                                    $data = [
                                        'responseTime' => $responseData['responseTime'],
                                        'responseType' => '',
                                        'status' => 200,
                                        'response' => 'success',
                                        'msg' => "Flight found!",
                                        "data" => [$responseData['data'][$key]]
                                    ];
                                    $responseData = $data;
                                    break;
                                }
                            }
                        }
                        else {
                            foreach ($responseData['data'] as $key => $d) {
                                if ($d['id'] == Auth::user()->flight_id) {
                                    $data = [
                                        'responseTime' => $responseData['responseTime'],
                                        'responseType' => '',
                                        'status' => 200,
                                        'response' => 'success',
                                        'msg' => "Flight found!",
                                        "data" => [$responseData['data'][$key]]
                                    ];
                                    $responseData = $data;
                                    break;
                                }
                            }
                        }
                    }


                    return $responseData;
                }
            }
            else {
                if (Auth::user()->flight_id == 0) {
                    return $responseData;
                } else {
                    foreach ($responseData['data'] as $key => $d) {
                        if ($d['id'] == Auth::user()->flight_id) {
                            $data = [
                                'responseTime' => $responseData['responseTime'],
                                'responseType' => '',
                                'status' => 200,
                                'response' => 'success',
                                'msg' => "Flight found!",
                                "data" => [$responseData['data'][$key]]
                            ];
                            $responseData = $data;
                            break;
                        }
                    }
                    return $responseData;
                }
            }

        } else {
            return "Something went wrong in flight list dropdown get api";
        }
    }

    public function getInstance()
    {
        # Service class names
        $modules = [
            8 => 'StickerVisa'
        ];

        if (!isset($modules[$this->process_type_id])) {
            return false;
        }

        # Instantiate the appropriate service class based on the process type ID
        $class = $modules[$this->process_type_id];
        $object = new $class($this->process_info);
        if (!$object instanceof FormHandler) {
            return false;
        }
        return $object;
    }

}
