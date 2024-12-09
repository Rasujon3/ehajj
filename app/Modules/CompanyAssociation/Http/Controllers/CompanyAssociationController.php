<?php

namespace App\Modules\CompanyAssociation\Http\Controllers;

use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\CompanyAssociation\Models\CompanyAssociationMaster;
use App\Modules\CompanyProfile\Models\CompanyInfo;
use App\Modules\CompanyProfile\Models\CompanyType;
use App\Modules\CompanyProfile\Models\RegistrationType;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\Area;
use App\Modules\Users\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use yajra\Datatables\Datatables;

class CompanyAssociationController extends Controller
{

    protected $aclName;
    protected $process_type_id;

    public function __construct()
    {
        if (Session::has('lang')) {
            App::setLocale(Session::get('lang'));
        }
        $this->aclName = 'CompanyAssociation';
        $this->process_type_id = 500;
    }


    public function getList()
    {
        if (!ACL::getAccsessRight($this->aclName, '-V-'))
            abort('400', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');

        return view("CompanyAssociation::list");
    }


    public function appForm()
    {

        if (!ACL::getAccsessRight($this->aclName, '-A-'))
            abort('400', 'You have no access right! This incidence will be reported. Contact with system admin for more information.');
        try {
            $bscicUsers = collect(DB::select("select user_first_name as user_full_name, id from users where desk_id REGEXP '^([0-9]*[,]+)*2([,]+[,0-9]*)*$' and user_status = 'active' "))->pluck("user_full_name", "id")->toArray();
            $public_html = (string)view(
                "CompanyAssociation::company-association-form",
                compact(['bscicUsers'])
            );

            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()));
            return redirect()->back();
        }
    }

    public function storeCompanyAssociation(Request $request)
    {
        $save_company = $request->get('save_company_yes');
        DB::beginTransaction();

        $companyAssociationData = new CompanyAssociationMaster();

        $requestedUser = $request->get('bscic_user');

        $requestedUser = Auth::user()->id;



        if ($save_company == 1) {
            $companyAssociationData->request_to_user_id = $requestedUser;
            $companyAssociationData->request_from_user_id = Auth::user()->id;
            $companyAssociationData->org_nm = $request->get('company_name_english');
            $companyAssociationData->org_nm_bn = $request->get('company_name_bangla');
            $companyAssociationData->regist_type = $request->get('reg_type_id');
            $companyAssociationData->org_type = $request->get('company_type_id');
            $companyAssociationData->division = $request->get('company_office_division_id');
            $companyAssociationData->district = $request->get('company_office_district_id');
            $companyAssociationData->thana = $request->get('company_office_thana_id');
            $companyAssociationData->save();

            $organization = new  CompanyInfo();
            $organization->org_nm = $request->get('company_name_english');
            $organization->org_nm_bn = $request->get('company_name_bangla');
            $organization->regist_type = $request->get('reg_type_id');
            $organization->org_type = $request->get('company_type_id');
            $organization->office_division = $request->get('company_office_division_id');
            $organization->office_district = $request->get('company_office_district_id');
            $organization->office_thana = $request->get('company_office_thana_id');
            $organization->company_status = 1;
            $organization->save();

            $user = Users::where('id', $companyAssociationData->request_from_user_id)->first();

            if (empty($user->working_company_id)) {
                $user->working_company_id = $organization->id;
                $user->working_user_type = 'Employee';
            }
            $user->save();

            $companyAssociationData->status = 25; // first time or new company auto approved
            $companyAssociationData->desk_remarks = 'auto approved'; // first time or new company auto approved
            $companyAssociationData->type = 'Employee';
        } else {
            
            // need to check choma separator
            $organization = CompanyInfo::where('id', $request->get('org_company_id'))->first();
            
            $user = Users::find(Auth::user()->id);
            $user->working_company_id=  $request->get('org_company_id') ;
            $user->save();
            $companyAssociationData->request_to_user_id = $requestedUser;
            $companyAssociationData->request_from_user_id = Auth::user()->id;
            $companyAssociationData->org_nm = $organization->org_nm;
            $companyAssociationData->org_nm_bn = $organization->org_nm_bn;
            $companyAssociationData->regist_type = $organization->regist_type;
            $companyAssociationData->org_type = $organization->org_type;
            $companyAssociationData->division = $organization->office_division;
            $companyAssociationData->district = $organization->office_district;
            $companyAssociationData->thana = $organization->office_thana;
            $companyAssociationData->save();
        }

        $companyAssociationData->company_id = $organization->id;
        $companyAssociationData->save();

        if (!empty($request->get('user_type')) && $request->get('user_type') == 'company') {
            $appInfo = [
                'app_id' => $companyAssociationData->id,
                'status_id' => 0,
                'process_type_id' => $this->process_type_id,
                'tracking_no' => '',
                'process_type_name' => 'Company Association',
                'remarks' => '',
            ];
            $receiverInfo = Users::where('id', $requestedUser)->get(['user_email', 'user_mobile']);
        }

        DB::commit();

        if ($save_company == 1) {
            \Session::flash('success', ' আপনার নতুন কোম্পানি  তৈরি হয়েছে । নতুন কোম্পানি দিয়ে অ্যাপ্লিকেশান  করতে চাইলে  লগ আউট করে আবার লগইন  করুন');
            return redirect()->to('/client/company-profile/create');
        } else {
            \Session::flash('success', 'Your request successfully store');
            return redirect()->to('/dashboard');
        }
    }

    public function companyActivatesAction(Request $request)
    {

        try {
            $id = Encryption::decodeId($request->companyAssocId);
            $type = $request->type;
            $key = $request->key;

            DB::beginTransaction();
            $companyAssoc = CompanyAssociationMaster::find($id);
            $user = Users::find($companyAssoc->request_from_user_id);
            $company_id = $companyAssoc->company_id;
            $user_id = $companyAssoc->request_from_user_id;

            if ($key == 'approved') {
                $companyAssoc->status = 25;
                $companyAssoc->type = $type;
                $companyAssoc->is_active = 1;
                $companyAssoc->is_archive = 0;
                if ($user->working_company_id == 0) {
                    $user->working_company_id = $company_id;
                    $user->working_user_type = $type;
                }
                $companyAssoc->save();
            }

            if ($key == 'reject') {
                $companyAssoc->status = 6;
                $companyAssoc->save();
            }

            if ($key == 'is_archived') { // remove company from user profile
                $companyAssoc->is_archive = 1;
                $companyAssoc->save();

                $anotherCompany = CompanyAssociationMaster::where('request_from_user_id', $user_id)
                    ->where('is_active', 1)
                    ->where('status', 25)
                    ->where('is_archive', 0)
                    ->first();

                if (empty($anotherCompany)) {
                    $user->working_company_id = 0;
                } else {
                    if ($user->working_company_id == $company_id) { //current working id change if is current company
                        $user->working_company_id = $anotherCompany->company_id;
                    }
                }
            }

            if ($key == 'activate') {
                $companyAssoc->is_active = 1;
                $companyAssoc->save();
                if ($user->working_company_id == 0) {
                    $user->working_company_id = $companyAssoc->company_id;
                }
            }

            if ($key == 'deactivate') {
                $companyAssoc->is_active = 0;
                $companyAssoc->save();
                $anotherCompany = CompanyAssociationMaster::where('request_from_user_id', $user_id)
                    ->where('is_active', 1)
                    ->where('status', 25)
                    ->where('is_archive', 0)
                    ->first();

                if (empty($anotherCompany)) {
                    $user->working_company_id = 0;
                } else {
                    if ($user->working_company_id == $company_id) { //current working id change if is current company
                        $user->working_company_id = $anotherCompany->company_id;
                    }
                }
            }

            if ($key == 'cancel') { // associate after 24 hours the button/ cond. is working
                $companyAssoc->is_archive = 1;
                $companyAssoc->save();
            }

            $user->save();
            DB::commit();

            return response()->json();
        } catch (\Exception $e) {
            return response()->json();
        }
    }


    public function skipCompanyAssociation()
    {
        try {

            if (empty(Auth::user()->working_company_id)) {
                Session::flash('error', 'Sorry! you have to select new company. [CAC-102]');
                return redirect()->back();
            }
            if (CommonFunction::checkEligibility() == 0) {
                Session::flash('error', 'Your selected Company is inactive. please contact with system admin');
                sleep(1);
            } else {
                Session::flash('success', 'Company association updated successfully.');
            }
            return redirect('/dashboard');
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry, Something went wrong [CAC-103]');
            return redirect()->back();
        }
    }

    public function selectCompany()
    {
        $companyIds = CommonFunction::getUserAllCompanyIdsWithZero();
        $user_multiple_company = 1;
        $pageTitle = 'Company selection';
        $last_working_company = CommonFunction::getCompanyNameById(Auth::user()->working_company_id);
        $companyList = CompanyInfo::whereIn('id', $companyIds)
            ->get(['org_nm_bn as company_name', 'id']);
        return view('Dashboard::index', compact('user_multiple_company', 'companyList', 'last_working_company', 'pageTitle'));
    }

    public function updateWorkingCompany(Request $request)
    {
        $rules['requested_company_id'] = 'required';
        $messages = [
            'requested_company_id.required' => 'At least select one company'
        ];

        $this->validate($request, $rules, $messages);

        try {
            $user_id = Auth::user()->id;
            $request_company_id = $request->get('requested_company_id');

            $companyIds = CompanyAssociationMaster::where('request_from_user_id', Auth::user()->id)
                ->where('is_active', 1)
                ->where('status', 25)
                ->where('is_archive', 0)
                ->pluck('type', 'company_id')
                ->toArray();


            if (array_key_exists($request_company_id, $companyIds)) {
                $user = Users::where('id', $user_id)->first();
                $user->working_company_id = $request_company_id;
                $user->working_user_type = $companyIds[$request_company_id];
                $user->save();

                if (CommonFunction::checkEligibility() == 0) {
                    Session::flash('error', 'Your selected Company is inactive. please contact with system admin');
                } else {
                    Session::flash('success', 'Company association updated successfully.');
                }

                return redirect('/dashboard');
            }

            Session::flash('error', 'You are not eligible for this company.[CAC-USC-001]');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry, Something went wrong');
            return redirect()->back();
        }
    }

    public function appFormView(Request $request, $id, $openMode = '')
    {

        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }
        $appId = Encryption::decodeId($id);

        $data['divisions'] = Area::where('area_type', 1)->orderBy('area_nm_ban', 'asc')->pluck('area_nm_ban', 'area_id')->toArray();
        $data['regType'] = RegistrationType::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
        $data['companyType'] = CompanyType::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
        $data['bscicUsers'] = Users::where('user_status', 'active')->where('user_type', '4x404')->select(DB::raw("CONCAT_WS(' ', users.user_first_name, users.user_middle_name, users.user_last_name) as user_full_name"), 'id')->orderBy('user_first_name')->pluck('user_full_name', 'id')->toArray();


        $data['appInfo'] = CompanyAssociationMaster::leftJoin('registration_type', 'registration_type.id', '=', 'company_association_master.regist_type')
            ->leftJoin('company_type', 'company_type.id', '=', 'company_association_master.org_type')
            ->leftJoin('area_info as division', 'division.area_id', '=', 'company_association_master.division')
            ->leftJoin('area_info as district', 'district.area_id', '=', 'company_association_master.district')
            ->leftJoin('area_info as thana', 'thana.area_id', '=', 'company_association_master.thana')
            ->where('company_association_master.id', $appId)->first([
                'company_association_master.*',
                'registration_type.name_bn as reg_type',
                'company_type.name_bn as company_type',
                'division.area_nm_ban as division',
                'district.area_nm_ban as district',
                'thana.area_nm_ban as thana',
            ]);

        $public_html = strval(view("CompanyAssociation::application-view", $data));
        return response()->json(['responseCode' => 1, 'html' => $public_html]);
    }

    public function getAssociationCompanyList()
    {

        $companyInfo = CompanyAssociationMaster::leftJoin('company_info', 'company_association_master.company_id', '=', 'company_info.id')
            ->leftJoin('users', 'users.id', '=', 'company_info.created_by')
            ->where('company_association_master.request_from_user_id', Auth::user()->id)
            ->where('company_association_master.is_active', 1)
            ->where('company_association_master.status', 25)
            ->where('company_association_master.is_archive', 0)
            ->get([
                'company_association_master.id as cap_id',
                'company_association_master.type as type',
                'company_info.org_nm as org_nm',
                'users.user_email as user_email',
            ]);


        return Datatables::of($companyInfo)
            ->editColumn('email', function ($data) {
                return $data->user_email;
            })
            ->editColumn('org_nm', function ($data) {
                return $data->org_nm . "<br>($data->type)";
            })
            ->editColumn('action', function ($data) {
                $is_archived = "'is_archived'";
                return ' <button type="button" class="btn btn-danger btn-xs" value="' . Encryption::encodeId($data->cap_id) . '" onclick="deleteAssocCompany(this ' . ',' . $is_archived . ')"> <i class="fa fa-trash"></i> Remove </button>';
            })
            ->rawColumns(['action', 'org_nm'])
            ->make(true);
    }

    public function getAssociationUserList($company_id)
    {
        $companyId = (!empty($company_id) ? Encryption::decodeId($company_id) : Auth::user()->working_company_id);

        $companyUser = CompanyAssociationMaster::leftJoin('users', 'company_association_master.request_from_user_id', '=', 'users.id')
            ->leftJoin('user_logs', 'user_logs.user_id', '=', 'users.id')
            ->select(
                'company_association_master.id as id',
                'company_association_master.request_from_user_id as user_id',
                'company_association_master.company_id',
                'company_association_master.type as type',
                'company_association_master.is_active as is_active',
                'users.user_email as user_email',
                DB::raw('max(user_logs.login_dt) AS last_login')
            )
            ->groupBy('users.id')
            ->where('company_association_master.is_archive', 0)
            ->where('company_association_master.status', 25) //25 == the company is approved
            ->where('company_association_master.company_id', $companyId)
            ->get();

        return Datatables::of($companyUser)
            ->editColumn('user_email', function ($companyUser) {
                return $companyUser->user_email . "<br>($companyUser->type)";
            })
            ->editColumn('last_login', function ($companyUser) {

                return $lastLogin = "<span class='input_ban'>" . Carbon::parse($companyUser->last_login)->format('d-m-Y') . "</span>";
            })
            ->editColumn('action', function ($companyUser) {
                $perm1 = "'activate'";
                $perm2 = "'deactivate'";
                $content = '';

                if (Auth::user()->working_user_type == 'Employee') { // type if employee he should be access deactive permission for all type emp
                    if ($companyUser->is_active == 0) {
                        $content = ' <button type="button" class="btn btn-success btn-xs" value="' . Encryption::encodeId($companyUser->id) . '" onclick="activateDeactiveUser(this ' . ',' . $perm1 . ')"> Activate </button>';
                    } else {
                        $content = ' <button type="button" class="btn btn-danger btn-xs" value="' . Encryption::encodeId($companyUser->id) . '" onclick="activateDeactiveUser(this ' . ',' . $perm2 . ')"> Deactivate</button>';
                    }
                } else {
                    if ($companyUser->type == 'Consultant') { // only remove self
                        $content = ' <button type="button" class="btn btn-danger btn-xs" value="' . Encryption::encodeId($companyUser->id) . '" onclick="activateDeactiveUser(this ' . ',' . $perm2 . ')"> Deactivate </button>';
                    }
                }

                return $content;
            })
            ->rawColumns(['user_email', 'last_login', 'action'])
            ->make(true);
    }

    public function associationCompanyInfo(Request $request)
    {

        $companyNameEngRaw = preg_replace('!\s+!', ' ', $request->get('companyname'));
        $companyNameEng = str_replace(array('.'), '', $companyNameEngRaw);
        $last_word_start = strrpos($companyNameEng, " ") + 1;

        $last_word_end = strlen($companyNameEng) - 1;
        $last_word = substr($companyNameEng, $last_word_start, $last_word_end);
        $companyNameEnglishHalfLtd = str_ireplace($last_word, 'Ltd', $companyNameEng);
        $companyNameEnglishFullLtd = str_ireplace($last_word, 'Limited', $companyNameEng);


        if ($request->get('companyType') == 3 || $request->get('companyType') == 4) {
            if (
                strtolower($last_word) == "ltd."
                || strtolower($last_word) == "ltd"
                || strtolower($last_word) == "limited."
                || strtolower($last_word) == "limited"
            ) {
                $company_data = CompanyInfo::where('org_nm', $companyNameEng)
                    ->orWhere('org_nm', $companyNameEnglishHalfLtd)
                    ->orWhere('org_nm', $companyNameEnglishFullLtd)
                    ->orWhere('org_nm', $companyNameEnglishHalfLtd . '.')
                    ->orWhere('org_nm', $companyNameEnglishFullLtd . '.')
                    ->first();
            }
        } elseif (
            $request->get('companyType') == 1
            || $request->get('companyType') == 2
        ) {
            $thana_id = $request->get('companyThana');
            $company_data = CompanyInfo::where('org_nm', $companyNameEng)->first();
        } else {
            $company_data = CompanyInfo::where('org_nm', $companyNameEng)
                ->orWhere('org_nm', $companyNameEngRaw)
                ->orWhere('org_nm', $companyNameEngRaw . '.')
                ->orWhere('org_nm', $companyNameEnglishHalfLtd)
                ->orWhere('org_nm', $companyNameEnglishFullLtd)
                ->orWhere('org_nm', $companyNameEnglishHalfLtd . '.')
                ->orWhere('org_nm', $companyNameEnglishFullLtd . '.')
                ->first();
        }

        if ($company_data != null) {
            $checkPreviousRequest = CompanyAssociationMaster::where('company_id', $company_data->id)
                ->where('request_from_user_id', Auth::user()->id)
                ->whereIn('status', ['1', '25'])
                ->where('is_active', 1)
                ->where('is_archive', 0)
                ->count();

            if ($checkPreviousRequest > 0) {
                return response()->json(['responseCode' => 3]); //ইতিমধ্যেই এই কোম্পানিটি এসোসিয়েশন এর জন্য আবেদন করা হয়েছে/আসোসিয়েটেড অবস্থায় আছে
            }

            $company_datas = CompanyInfo::leftJoin('company_type', 'company_type.id', '=', 'company_info.org_type')
                ->leftJoin('registration_type', 'registration_type.id', '=', 'company_info.regist_type')
                ->leftJoin('area_info as division', 'division.area_id', '=', 'company_info.office_division')
                ->leftJoin('area_info as district', 'district.area_id', '=', 'company_info.office_district')
                ->leftJoin('area_info as thana', 'thana.area_id', '=', 'company_info.office_thana')
                ->where('company_info.id', $company_data->id)->first([
                    'company_info.*',
                    'company_type.name_bn as company_type_bn',
                    'registration_type.name_bn as regist_type_bn',
                    'division.area_nm_ban as division',
                    'district.area_nm_ban as district',
                    'thana.area_nm_ban as thana',
                ]);

            $companyUser = CompanyAssociationMaster::leftJoin('users', 'users.id', '=', 'company_association_master.request_from_user_id')
                ->where('company_association_master.company_id', $company_data->id)
                ->where('company_association_master.is_active', 1)
                ->where('company_association_master.status', 25)
                ->where('company_association_master.is_archive', 0)
                ->pluck('users.user_first_name', 'users.id')->toArray();

            $lastLogin['time'] = '0000-00-00';
            $lastLogin['user_name'] = '';
            $lastLogin['user_email'] = '';
            $lastLogin['user_mobile'] = '';
            foreach ($companyUser as $key => $user) {
                $userId = $key;
                $latestUser = collect(DB::select("select user_first_name as user_name, user_email, user_mobile, MAX(user_logs.login_dt) as last_login from user_logs LEFT JOIN users ON user_logs.user_id = users.id where user_id = $userId"))->toArray();
                if (strtotime($latestUser[0]->last_login) > strtotime($lastLogin['time'])) {
                    $lastLogin['time'] = $latestUser[0]->last_login;
                    $lastLogin['user_name'] = $latestUser[0]->user_name;
                    $lastLogin['user_email'] = $latestUser[0]->user_email;
                    $lastLogin['user_mobile'] = $latestUser[0]->user_mobile;
                }
            }
            if ($lastLogin['time'] == '0000-00-00' || $lastLogin['time'] == '') {
                $lastLogin['time'] = 'তথ্য পাওয়া যায়নি';
            } else {
                $lastLogin['time'] = CommonFunction::updatedOn($lastLogin['time']);
            }
            return response()->json(['responseCode' => 1, 'companyusers' => $companyUser, 'status' => 1, 'company_id' => $company_data->id, 'company_data' => $company_datas, 'last_login_data' => $lastLogin]);
        } else {
            return response()->json(['responseCode' => 1, 'status' => 2, 'company_id' => '']);
        }
    }
}