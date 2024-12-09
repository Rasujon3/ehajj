<?php

namespace App\Modules\IndustryNew\Http\Controllers;

use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\ImageProcessing;
use App\Modules\CompanyProfile\Models\CompanyDirector;
use App\Modules\CompanyProfile\Models\CompanyType;
use App\Modules\CompanyProfile\Models\Designation;
use App\Modules\CompanyProfile\Models\IndustrialCategory;
use App\Modules\CompanyProfile\Models\IndustrialSectorSubSector;
use App\Modules\CompanyProfile\Models\InvestingCountry;
use App\Modules\CompanyProfile\Models\InvestmentType;
use App\Modules\CompanyProfile\Models\InvestorInfo;
use App\Modules\CompanyProfile\Models\CompanyInfo;
use App\Modules\CompanyProfile\Models\RegistrationType;
use App\Modules\Documents\Http\Controllers\DocumentsController;
use App\Modules\Documents\Models\DocumentTypes;
use App\Modules\IndustryNew\Models\AnnualProductionCapacity;
use App\Modules\IndustryNew\Models\ApcUnit;
use App\Modules\IndustryNew\Models\BusinessCategory;
use App\Modules\IndustryNew\Models\CsvUploadLog;
use App\Modules\IndustryNew\Models\IndLocalRawMaterial;
use App\Modules\IndustryNew\Models\IndRegInvCountry;
use App\Modules\IndustryNew\Models\IndustryNew;
use App\Modules\IndustryNew\Models\LoanSourceCountry;
use App\Modules\IndustryNew\Models\PublicUtilityResource;
use App\Modules\IndustryNew\Models\UtilityService;
use App\Modules\ProcessPath\Models\PayOrderAmountSetup;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Settings\Models\Currencies;
use App\Modules\Settings\Models\IndustrialCityList;
use App\Modules\SonaliPayment\Services\SPAfterPaymentManager;
use App\Modules\Settings\Models\Area;
use App\Modules\Users\Models\Countries;
use App\Modules\Users\Models\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\SonaliPayment\Http\Controllers\SonaliPaymentController;
use App\Modules\SonaliPayment\Services\SPPaymentManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class IndustryNewController extends Controller
{
    use SPPaymentManager, SPAfterPaymentManager;

    protected $process_type_id;
    protected $acl_name;

    public function __construct()
    {
        $this->process_type_id = 1; // 1 is for IndustryNew
        $this->acl_name = 'IndustryNew';
    }

    public function appForm(Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        if (!ACL::getAccsessRight($this->acl_name, '-A-')) {
            return response()->json(['responseCode' => 1, 'html' => 'You have no access right! Contact with system admin for more information']);
        }

        try {
            $data['vat_percentage']  = Configuration::where('caption', 'GOVT_VENDOR_VAT_FEE')->value('value');
            $companyId = CommonFunction::getUserCompanyWithZero();

            $data['companyInfo'] = CompanyInfo::where('is_approved', 1)->where('id', $companyId)->first();

            if (!$data['companyInfo']) {
                return response()->json([
                    'responseCode' => 1,
                    'html' => 'Working Company Not Found!'
                ]);
            }

            $data['regOffice'] = IndustrialCityList::where('status', 1)->where('type', 1)->where('is_archive', 0)->orderBy('name')->pluck('name as name_bn', 'id')->toArray();
            $data['regType'] = RegistrationType::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['companyType'] = CompanyType::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['businessCategory'] = BusinessCategory::where('status', 1)->where('is_archive', 0)->where('category_type', 1)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['investmentType'] = InvestmentType::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['industrialCategory'] = IndustrialCategory::where('status', 1)->where('is_archive', 0)->orderBy('id')->pluck('name_bn', 'id')->toArray();
            $data['industrialSector'] = IndustrialSectorSubSector::where('type', 1)->where('status', 1)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['industrialSubSector'] = IndustrialSectorSubSector::where('type', 2)->where('status', 1)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['divisions'] = Area::where('area_type', 1)->orderBy('area_nm_ban')->pluck('area_nm_ban', 'area_id')->toArray();
            $data['districts'] = Area::where('area_type', 2)->orderBy('area_nm', 'ASC')->pluck('area_nm_ban', 'area_id')->toArray();
            $data['thanas'] = Area::where('area_type', 3)->orderBy('area_nm', 'ASC')->pluck('area_nm_ban', 'area_id')->toArray();
            $data['designation'] = Designation::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['nationality'] = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')
                ->orderby('nationality')->pluck('nationality', 'id')->toArray();

            $data['apc_unit'] = ApcUnit::where('status', 1)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();

            $data['public_utility'] = PublicUtilityResource::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->get(['id', 'name_en', 'name_bn']);
            $data['currencyBDT'] = Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive', 0)->where('is_active', 1)->orderBy('code')->pluck('code', 'id')->toArray();
            $data['currencyBDT']['114'] = "টাকা";
            $data['totalFee'] = DB::table('industrial_category')->where('status', 1)->orderBy('id')->get([
                'inv_limit_start', 'inv_limit_end', 'reg_fee', 'name_bn', 'oss_fee'
            ]);
            $data['country'] = Countries::where('country_status', 'yes')->orderBy('nicename')->pluck('nicename', 'id')->toArray();
            $data['companyDirector'] = CompanyDirector::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();

            $data['investing_country'] = InvestingCountry::where('company_id', $data['companyInfo']->id)->get(['country_id'])->toArray();

            $data['investing_country'] = "";
            $data['process_type_id'] = $this->process_type_id;
            $investors = [];
            if (!empty($data['companyInfo'])) {
                $data['investing_country'] = InvestingCountry::where('company_id', $data['companyInfo']->id)->first([DB::raw('group_concat(country_id) as country_id')]);
            }
            $data['companyUserType'] = CommonFunction::getCompanyUserType();
            $public_html = (string)view("IndustryNew::application-form", $data);
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            return response()->json(['responseCode' => 1, 'html' => CommonFunction::showErrorPublic($e->getMessage()) . ' [VA-1014]']);
        }
    }

    public function appStore(Request $request)
    {
        $rules['business_category_id'] = 'required';
        $rules['total_investment'] = 'required';
        $rules['industrial_category_id'] = 'required';
        $rules['pref_reg_office'] = 'required';

        $messages['pref_reg_office.required'] = 'Please select your office';

        $this->validate($request, $rules, $messages);

        // Set permission mode and check ACL
        $app_id = (!empty($request->get('app_id')) ? Encryption::decodeId($request->get('app_id')) : '');
        $mode = (!empty($request->get('app_id')) ? '-E-' : '-A-');
        if (!ACL::getAccsessRight($this->acl_name, $mode, $app_id))
            abort('400', "You have no access right! Please contact with system admin if you have any query.");

        // check Approved IN application, if have go back
        $company_id = CommonFunction::getUserCompanyWithZero();

        try {

            DB::beginTransaction();

            if ($request->get('app_id')) {
                $appData = IndustryNew::find($app_id);
                $processData = ProcessList::where(['process_type_id' => $this->process_type_id, 'ref_id' => $appData->id])->first();
            } else {
                $appData = new IndustryNew();
                $processData = new ProcessList();
            }

            $appData->company_id = $company_id;
            $appData->org_nm = $request->get('company_name_english');
            $appData->org_nm_bn = $request->get('company_name_bangla');
            $appData->project_nm = $request->get('project_name');
            $appData->regist_type = $request->get('reg_type_id');
            $appData->org_type = $request->get('company_type_id');
            $appData->business_category_id = $request->get('business_category_id');
            $appData->invest_type = $request->get('investment_type_id');
            $appData->investment_limit = $request->get('total_investment');
            $appData->ind_category_id = $request->get('industrial_category_id');
            $appData->ins_sector_id = $request->get('industrial_sector_id');
            $appData->ins_sub_sector_id = $request->get('industrial_sub_sector_id');


            $appData->bscic_office_id = $request->get('pref_reg_office');
            $appData->main_activities = $request->get('company_main_works');

            $appData->sales_local = $request->get('local_sales');
            $appData->sales_foreign = $request->get('foreign_sales');
            $appData->apc_price_total = $request->get('apc_price_total');
            $appData->local_raw_price_total = $request->get('local_raw_price_total');

            $appData->local_male = $request->get('local_male');
            $appData->local_female = $request->get('local_female');
            $appData->local_total = $request->get('local_total');
            $appData->foreign_male = $request->get('foreign_male');
            $appData->foreign_female = $request->get('foreign_female');
            $appData->foreign_total = $request->get('foreign_total');
            $appData->manpower_total = $request->get('manpower_total');
            $appData->manpower_local_ratio = $request->get('manpower_local_ratio');
            $appData->manpower_foreign_ratio = $request->get('manpower_foreign_ratio');

            $appData->ceo_taka_invest = $request->get('ceo_taka_invest');
            $appData->ceo_dollar_invest = $request->get('ceo_dollar_invest');
            $appData->ceo_loan_org_country = $request->get('ceo_loan_org_country');
            $appData->local_loan_taka = $request->get('local_loan_taka');
            $appData->local_loan_dollar = $request->get('local_loan_dollar');
            $appData->local_loan_org_country = $request->get('local_loan_org_country');
            $appData->foreign_loan_taka = $request->get('foreign_loan_taka');
            $appData->foreign_loan_dollar = $request->get('foreign_loan_dollar');
            $appData->foreign_loan_org_country = $request->get('foreign_loan_org_country');
            $appData->total_inv_taka = $request->get('total_inv_taka');
            $appData->total_inv_dollar = $request->get('total_inv_dollar');

            $appData->raw_local_number = $request->get('local_raw_material_number');
            $appData->raw_local_price = $request->get('local_raw_material_price');
            $appData->raw_imported_number = $request->get('import_raw_material_number');
            $appData->raw_imported_price = $request->get('import_raw_material_price');
            $appData->raw_total_number = $request->get('raw_material_total_number');
            $appData->raw_total_price = $request->get('raw_material_total_price');

            $appData->auth_person_nm = $request->get('auth_person_nm');
            $appData->auth_person_desig = $request->get('auth_person_desig');
            $appData->auth_person_address = $request->get('auth_person_address');
            $appData->auth_person_mobile = $request->get('auth_person_mobile');
            $appData->auth_person_email = $request->get('auth_person_email');

            if ($request->hasFile('authorization_letter')) {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = 'uploads/' . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $_file_path = $request->file('authorization_letter');
                $file_path = trim(uniqid('BSCIC_IR-' . '-', true) . $_file_path->getClientOriginalName());
                $_file_path->move($path, $file_path);
                $appData->authorization_letter = $yearMonth . $file_path;
            }

            if (!empty($request->correspondent_photo_base64)) {
                $yearMonth = date("Y") . "/" . date("m") . "/";
                $path = "uploads/image/" . $yearMonth;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $splited = explode(',', substr($request->get('correspondent_photo_base64'), 5), 2);
                $imageData = $splited[1];
                $base64ResizeImage = base64_encode(ImageProcessing::resizeBase64Image($imageData, 300, 300));
                $base64ResizeImage = base64_decode($base64ResizeImage);
                $correspondent_photo_name = trim(uniqid('BSCIC_IR-' . '-', true) . '.' . 'jpeg');
                file_put_contents($path . $correspondent_photo_name, $base64ResizeImage);
                $appData->auth_person_pic = $path . $correspondent_photo_name;
            } else {
                if (empty($appData->auth_person_pic)) {
                    $appData->auth_person_pic = Auth::user()->user_pic;
                }
            }

            $appData->accept_terms = (!empty($request->get('accept_terms')) ? 1 : 0);

            $appData->save();

            $this->updateCompanyProfile($request, $appData, $processData);

            if (!empty($appData->id) && !empty($request->utility_id[0])) {

                foreach ($request->utility_id as $item => $value) {
                    $indRegUtilityId = $request->get('ind_reg_utility_id') != null ? $request->get('ind_reg_utility_id')[$item] : '';
                    $utility = UtilityService::findOrNew($indRegUtilityId);
                    $utility->app_id = $appData->id;
                    $utility->utility_id = $request->get('utility_id')[$item];
                    if (isset($request->get('services_availability')[$item])) {
                        $utility->services_availability = $request->get('services_availability')[$item];
                    } else {
                        $utility->services_availability = 0;
                    }
                    $utility->utility_distance = $request->get('utility_distance')[$item];
                    $utility->distance_unit = $request->get('distance_unit')[$item];

                    $utility->save();
                }
            }


            if (!empty($appData->id) && !empty($request->investing_country_id[0])) {
                $indRegCountryIds = [];
                foreach ($request->investing_country_id as $item => $value) {
                    $indRegCountryId = $request->get('ind_reg_inv_country_id') != null ? $request->get('ind_reg_inv_country_id')[$item] : '';
                    $investingCountry = IndRegInvCountry::findOrNew($indRegCountryId);
                    $investingCountry->app_id = $appData->id;
                    $investingCountry->country_id = $value;
                    $investingCountry->save();
                    $indRegCountryIds[] = $investingCountry->id;
                }

                if (count($indRegCountryIds) > 0) {
                    IndRegInvCountry::where('app_id', $appData->id)->whereNotIn('id', $indRegCountryIds)->delete();
                }
            }

            //Annual production capacity
            if (!empty($appData->id) && !empty($request->apc_service_name[0])) {
                $apc_ids = [];
                foreach ($request->apc_service_name as $proKey => $proData) {
                    $apc_id = $request->get('apc_id') != null ? $request->get('apc_id')[$proKey] : '';
                    $annualProduction = AnnualProductionCapacity::findOrNew($apc_id);
                    $annualProduction->app_id = $appData->id;
                    $annualProduction->service_name = $request->apc_service_name[$proKey];
                    $annualProduction->quantity = $request->apc_quantity[$proKey];
                    $annualProduction->unit = $request->apc_unit[$proKey];
                    $annualProduction->amount_bdt = $request->apc_amount_bdt[$proKey];
                    $annualProduction->save();

                    $apc_ids[] = $annualProduction->id;
                }
                if (count($apc_ids) > 0) {
                    AnnualProductionCapacity::where('app_id', $appData->id)->whereNotIn('id', $apc_ids)->delete();
                }
            }

            //Local raw material
            if (!empty($appData->id) && !empty($request->local_raw_material_name[0])) {
                $local_raw_material_ids = [];
                foreach ($request->local_raw_material_name as $localMatKey => $localMatData) {
                    $local_raw_material_id = $request->get('local_raw_material') != null ? $request->get('local_raw_material')[$localMatKey] : '';
                    $indRegLocalMaterial = IndLocalRawMaterial::findOrNew($local_raw_material_id);
                    $indRegLocalMaterial->app_id = $appData->id;
                    $indRegLocalMaterial->local_raw_material_name = $request->local_raw_material_name[$localMatKey];
                    $indRegLocalMaterial->local_raw_material_quantity = $request->local_raw_material_quantity[$localMatKey];
                    $indRegLocalMaterial->local_raw_material_unit = $request->local_raw_material_unit[$localMatKey];
                    $indRegLocalMaterial->local_raw_material_amount_bdt = $request->local_raw_material_amount_bdt[$localMatKey];
                    $indRegLocalMaterial->save();

                    $local_raw_material_ids[] = $indRegLocalMaterial->id;
                }
                if (count($local_raw_material_ids) > 0) {
                    IndLocalRawMaterial::where('app_id', $appData->id)->whereNotIn('id', $local_raw_material_ids)->delete();
                }
            }

            if (!empty($appData->id) && !empty($request->loan_country_id[0])) {
                $loan_source_ids = [];
                foreach ($request->loan_country_id as $item => $value) {
                    $loan_source_id = $request->get('loan_source_id') != null ? $request->get('loan_source_id')[$item] : '';
                    $loanSource = LoanSourceCountry::findOrNew($loan_source_id);
                    $loanSource->app_id = $appData->id;
                    $loanSource->loan_country_id = $request->loan_country_id[$item];
                    $loanSource->loan_org_nm = $request->loan_org_name[$item];
                    $loanSource->loan_amount = $request->loan_amount[$item];
                    $loanSource->loan_receive_date = !empty($request->loan_receive_date[$item]) ? date('Y-m-d', strtotime($request->loan_receive_date[$item])) : '';
                    $loanSource->save();

                    $loan_source_ids[] = $loanSource->id;
                }

                if (count($loan_source_ids) > 0) {
                    LoanSourceCountry::where('app_id', $appData->id)->whereNotIn('id', $loan_source_ids)->delete();
                }
            }
            $processData->company_id = $company_id;

            //Set category id for process differentiation
            $processData->cat_id = 1;
            if (in_array($appData->ind_category_id, [1, 2, 3])) {
                $processData->cat_id = 2;
            }

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

                    $processData->previous_hash = $processData->hash_value ?? "";
                    $processData->hash_value = Encryption::encode($resultData);
                }else{
                    $processData->status_id = -1;
                    $processData->desk_id = 0;
                }


            }

            $processData->ref_id = $appData->id;
            $processData->process_type_id = $this->process_type_id;
            $processData->office_id = $request->get('pref_reg_office');
            $jsonData['Applicant Name'] = Auth::user()->user_first_name;
            // need to change
            $jsonData['Company Name'] = CommonFunction::getCompanyNameById($company_id);
            $jsonData['Office name'] = CommonFunction::getBSCICOfficeName($request->get('pref_reg_office'));
            $jsonData['Email'] = Auth::user()->user_email;
            $jsonData['Phone'] = Auth::user()->user_mobile;
            $processData['json_object'] = json_encode($jsonData);
            $processData->save();

            $doc_type_id = $request->doc_type_key;
            //            $doc_type_id = 0;
            //            if ($doc_type_key) {
            //                $doc_type_id = DocumentTypes::where('key', $doc_type_key)->value('id');
            //            }

            //  Required Documents for attachment
            $process_type_id = $this->process_type_id;
            DocumentsController::storeAppDocuments($process_type_id, $doc_type_id, $appData->id, $request);

            // Payment info will not be updated for resubmit
            if ($processData->status_id != 2 && !empty($appData->ind_category_id)) {

                $unfixed_amount_array = $this->unfixedAmountsForVendorServiceFee($appData->ind_category_id, 1);

                $contact_info = [
                    'contact_name' => $request->get('contact_name'),
                    'contact_email' => $request->get('contact_email'),
                    'contact_no' => $request->get('contact_no'),
                    'contact_address' => $request->get('contact_address'),
                ];

                $payment_id = $this->storeSubmissionFeeData($appData->id, 1, $contact_info, $unfixed_amount_array);
            }

            /*
             * if application submitted and status is equal to draft then
             * generate tracking number and payment initiate
             */
            if ($request->get('actionBtn') == 'Submit'  && $processData->status_id == -1) {

                if (empty($processData->tracking_no)) {
                    //                    $dist_name = CommonFunction::getDistrictFirstTwoChar($appData->factory_district);
                    $trackingPrefix = 'IR-' . date("Ymd") . '-';
                    $processTypeId = $this->process_type_id;
                    DB::statement("update  process_list, process_list as table2  SET process_list.tracking_no=(
                                                            select concat('$trackingPrefix',
                                                                    LPAD( IFNULL(MAX(SUBSTR(table2.tracking_no,-7,7) )+1,1),7,'0')
                                                                          ) as tracking_no
                                                             from (select * from process_list ) as table2
                                                             where table2.process_type_id ='$processTypeId' and table2.id!='$processData->id' and table2.tracking_no like '$trackingPrefix%'
                                                        )
                                                      where process_list.id='$processData->id' and table2.id='$processData->id'");
                }
                DB::commit();
                return SonaliPaymentController::RedirectToPaymentPortal(Encryption::encodeId($payment_id));
            }
            // Send Email notification to user on application re-submit

            if ($processData->status_id == 2) {
                $appInfo = [
                    'app_id' => $processData->ref_id,
                    'status_id' => $processData->status_id,
                    'process_type_id' => $processData->process_type_id,
                    'tracking_no' => $processData->tracking_no,
                    'process_type_name' => 'Industry New',
                    'remarks' => '',
                ];

                $receiverInfo = Users::where('id', Auth::user()->id)->get(['user_email', 'user_mobile']);
                //send email for application re-submission...
                CommonFunction::sendEmailSMS('APP_RESUBMIT', $appInfo, $receiverInfo);
            }

            DB::commit();

            if ($processData->status_id == -1) {
                Session::flash('success', 'Successfully updated the Application!');
            } elseif ($processData->status_id == 1) {
                Session::flash('success', 'Successfully Application Submitted !');
            } elseif ($processData->status_id == 2) {
                Session::flash('success', 'Successfully Application Re-Submitted !');
            } else {
                Session::flash('error', 'Failed due to Application Status Conflict. Please try again later! [VA-1023]');
            }
            return redirect('client/industry-new/list/' . Encryption::encodeId($this->process_type_id));
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[IN-1025]");
            return redirect()->back()->withInput();
        }
    }

    public function updateCompanyProfile($request, $appData, $processData)
    {
        $company_id = CompanyInfo::where('id', Auth::user()->working_company_id)->value('id');

        try {
            $companyInfo = CompanyInfo::find($company_id);
            $companyInfo->org_nm = $appData->org_nm;
            $companyInfo->org_nm_bn = $appData->org_nm_bn;
            $companyInfo->regist_type = $appData->regist_type;
            $companyInfo->org_type = $appData->org_type;
            $companyInfo->invest_type = $appData->invest_type;
            $companyInfo->investment_limit = $appData->investment_limit;
            $companyInfo->ind_category_id = $appData->ind_category_id;
            $companyInfo->ins_sector_id = $appData->ins_sector_id;
            $companyInfo->ins_sub_sector_id = $appData->ins_sub_sector_id;
            $companyInfo->office_division = $appData->office_division;
            $companyInfo->office_district = $appData->office_district;
            $companyInfo->office_thana = $appData->office_thana;
            $companyInfo->office_postcode = $appData->office_postcode;
            $companyInfo->office_location = $appData->office_location;
            $companyInfo->office_email = $appData->office_email;
            $companyInfo->office_mobile = $appData->office_mobile;

            $companyInfo->is_same_address = $appData->is_same_address;

            $companyInfo->factory_division = $appData->factory_division;
            $companyInfo->factory_district = $appData->factory_district;
            $companyInfo->factory_thana = $appData->factory_thana;
            $companyInfo->factory_postcode = $appData->factory_postcode;
            $companyInfo->factory_location = $appData->factory_location;
            $companyInfo->factory_email = $appData->factory_email;
            $companyInfo->factory_mobile = $appData->factory_mobile;


            $companyInfo->director_type = $appData->director_type;
            $companyInfo->ceo_name = $appData->ceo_name;
            $companyInfo->ceo_father_nm = $appData->ceo_father_nm;

            if ($appData->passport != '') {
                $companyInfo->passport = $appData->passport;
                $companyInfo->nid = null;
            }
            if ($appData->nid != '') {
                $companyInfo->nid = $appData->nid;
                $companyInfo->passport = null;
            }
            $companyInfo->dob = $appData->dob;
            $companyInfo->nationality = $appData->nationality;
            $companyInfo->designation = $appData->designation;
            $companyInfo->ceo_email = $appData->ceo_email;
            $companyInfo->ceo_mobile = $appData->ceo_mobile;
            $companyInfo->entrepreneur_signature = $appData->entrepreneur_signature;
            $companyInfo->bscic_office_id = $appData->bscic_office_id;
            $companyInfo->main_activities = $appData->main_activities;
            $companyInfo->save();

            $processData->company_id =  $companyInfo->id;
            $processData->save();


            if (!empty($companyInfo->id) && !empty($request->investing_country_id[0])) {
                $indRegCountryIds = [];
                foreach ($request->investing_country_id as $item => $value) {
                    $investingCountry = new InvestingCountry();
                    $investingCountry->company_id = $companyInfo->id;
                    $investingCountry->country_id = $value;
                    $investingCountry->save();

                    $indRegCountryIds[] = $investingCountry->id;
                }

                if (count($indRegCountryIds) > 0) {
                    InvestingCountry::where('company_id', $companyInfo->id)->whereNotIn('id', $indRegCountryIds)->delete();
                }
            }
        } catch (\Exception $e) {
            dd($e->getLine(), $e->getFile(), $e->getMessage());
        }
    }

    public function appFormEdit($applicationId, $openMode = '', Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way.';
        }

        if (!ACL::getAccsessRight($this->acl_name, '-A-')) {
            return response()->json(['responseCode' => 1, 'html' => 'You have no access right! Contact with system admin for more information']);
        }

        try {
            $data['process_type_id'] = $this->process_type_id;
            $data['vat_percentage']  = Configuration::where('caption', 'GOVT_VENDOR_VAT_FEE')->value('value');

            $applicationId = Encryption::decodeId($applicationId);
            $process_type_id = $this->process_type_id;

            $data['apc_unit'] = ApcUnit::where('status', 1)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['businessCategory'] = BusinessCategory::where('status', 1)->where('is_archive', 0)->where('category_type', 1)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['regOffice'] = IndustrialCityList::where('status', 1)->where('type', 1)->where('is_archive', 0)->orderBy('name')->pluck('name as name_bn', 'id')->toArray();
            $data['regType'] = RegistrationType::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['companyType'] = CompanyType::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['investmentType'] = InvestmentType::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['industrialCategory'] = IndustrialCategory::where('status', 1)->where('is_archive', 0)->orderBy('id')->pluck('name_bn', 'id')->toArray();
            $data['industrialSector'] = IndustrialSectorSubSector::where('type', 1)->where('status', 1)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['industrialSubSector'] = IndustrialSectorSubSector::where('type', 2)->where('status', 1)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['divisions'] = Area::where('area_type', 1)->orderBy('area_nm_ban')->pluck('area_nm_ban', 'area_id')->toArray();
            //            $data['districts'] = AreaInfo::where('area_type', 2)->orderBy('area_nm', 'ASC')->pluck('area_nm_ban', 'area_id')->toArray();
            //            $data['thanas'] = AreaInfo::where('area_type', 3)->orderBy('area_nm', 'ASC')->pluck('area_nm_ban', 'area_id')->toArray();
            $data['designation'] = Designation::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();
            $data['nationality'] = Countries::where('country_status', 'Yes')->where('nationality', '!=', '')
                ->orderby('nationality', 'asc')->pluck('nationality', 'id')->toArray();

            $data['currencyBDT'] = Currencies::orderBy('code')->whereIn('code', ['BDT'])->where('is_archive', 0)->where('is_active', 1)->orderBy('code')->pluck('code', 'id')->toArray();
            $data['currencyBDT']['114'] = "টাকা";
            $data['totalFee'] = DB::table('industrial_category')->orderBy('id')->where('status', 1)->get([
                'inv_limit_start', 'inv_limit_end', 'reg_fee', 'name_bn', 'oss_fee'
            ]);
            $data['country'] = Countries::where('country_status', 'yes')->orderBy('nicename')->pluck('nicename', 'id')->toArray();
            $data['companyDirector'] = CompanyDirector::where('status', 1)->where('is_archive', 0)->orderBy('name_bn')->pluck('name_bn', 'id')->toArray();

            $data['appInfo'] = ProcessList::leftJoin('ind_reg_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('sp_payment as sfp', function ($join) use ($process_type_id) {
                    $join->on('sfp.app_id', '=', 'process_list.ref_id');
                    $join->on('sfp.process_type_id', '=', DB::raw($process_type_id));
                })
                ->where('process_list.ref_id', $applicationId)
                ->where('process_list.process_type_id', $process_type_id)
                ->first([
                    'process_list.id as process_list_id',
                    'process_list.desk_id',
                    'process_list.process_type_id',
                    'process_list.status_id',
                    'process_list.locked_by',
                    'process_list.locked_at',
                    'process_list.ref_id',
                    'process_list.tracking_no',
                    'process_list.company_id',
                    'process_list.process_desc',
                    'process_list.submitted_at',
                    'ps.status_name',
                    'ps.color',
                    'apps.*',

                    'sfp.contact_name as sfp_contact_name',
                    'sfp.contact_email as sfp_contact_email',
                    'sfp.contact_no as sfp_contact_phone',
                    'sfp.address as sfp_contact_address',
                    'sfp.pay_amount as sfp_pay_amount',
                    'sfp.vat_on_pay_amount as sfp_vat_tax',
                    'sfp.transaction_charge_amount as sfp_bank_charge',
                    'sfp.payment_status as sfp_payment_status',
                    'sfp.pay_mode as pay_mode',
                    'sfp.pay_mode_code as pay_mode_code',
                    'sfp.total_amount as sfp_total_amount',
                ]);

            $data['localRawMaterials'] = IndLocalRawMaterial::where('app_id', $data['appInfo']->id)->get();

            $data['investing_country'] = IndRegInvCountry::where('app_id', $data['appInfo']->id)
                ->first([DB::raw('group_concat(country_id) as country_id')]);
            $data['public_utility'] = UtilityService::leftJoin('public_utility_resource', 'public_utility_resource.id', '=', 'ind_reg_utility_services.utility_id')
                ->where('ind_reg_utility_services.app_id', $data['appInfo']->id)
                ->get(['ind_reg_utility_services.*', 'public_utility_resource.name_en', 'public_utility_resource.name_bn']);
            $data['loanSource'] = LoanSourceCountry::where('app_id', $data['appInfo']->id)->get();
            $data['annualProduction'] = AnnualProductionCapacity::where('app_id', $data['appInfo']->id)->get();

            $data['companyUserType'] = CommonFunction::getCompanyUserType();
            $data['process_type_id'] = $this->process_type_id;

            $public_html = (string)view("IndustryNew::application-form-edit", $data);
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            return response()->json(['responseCode' => 1, 'html' => CommonFunction::showErrorPublic($e->getMessage()) . ' [VA-1015]']);
        }
    }

    public function appFormView($appId, Request $request)
    {
        if (!$request->ajax()) {
            return 'Sorry! this is a request without proper way. [BRC-1003]';
        }

        // it's enough to check ACL for view mode only
        if (!ACL::getAccsessRight($this->acl_name, '-V-')) {
            return response()->json([
                'responseCode' => 0,
                'html' => "<h4>You have no access right! Contact with system admin for more information. [BRC-974]</h4>"
            ]);
        }

        try {
            $decodedAppId = Encryption::decodeId($appId);
            $process_type_id = $this->process_type_id;

            $data['process_type_id'] = $process_type_id;

            $data['appInfo'] = ProcessList::leftJoin('ind_reg_apps as apps', 'apps.id', '=', 'process_list.ref_id')
                ->leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                    $join->on('ps.id', '=', 'process_list.status_id');
                    $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
                })
                ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
                ->leftJoin('registration_type', 'registration_type.id', '=', 'apps.regist_type')
                ->leftJoin('company_type', 'company_type.id', '=', 'apps.org_type')
                ->leftJoin('investment_type', 'investment_type.id', '=', 'apps.invest_type')
                ->leftJoin('industrial_category', 'industrial_category.id', '=', 'apps.ind_category_id')
                ->leftJoin('ind_sector_info', 'ind_sector_info.id', '=', 'apps.ins_sector_id')
                ->leftJoin('ind_sector_info as ind_sub_sector', 'ind_sub_sector.id', '=', 'apps.ins_sub_sector_id')
                ->leftJoin('area_info as area_info_division', 'area_info_division.area_id', '=', 'apps.office_division')
                ->leftJoin('area_info as area_info_district', 'area_info_district.area_id', '=', 'apps.office_division')
                ->leftJoin('area_info as area_info_thana', 'area_info_thana.area_id', '=', 'apps.office_thana')
                ->leftJoin('area_info as f_area_info_division', 'f_area_info_division.area_id', '=', 'apps.factory_division')
                ->leftJoin('area_info as f_area_info_district', 'f_area_info_district.area_id', '=', 'apps.factory_district')
                ->leftJoin('area_info as f_area_info_thana', 'f_area_info_thana.area_id', '=', 'apps.factory_thana')
                ->leftJoin('country_info as country_info_nationality', 'country_info_nationality.id', '=', 'apps.nationality')
                ->leftJoin('designation', 'designation.id', '=', 'apps.designation')
                ->leftJoin('industrial_city_list', 'industrial_city_list.id', '=', 'apps.bscic_office_id')
                ->leftJoin('currencies as currencies_land', 'currencies_land.id', '=', 'apps.local_land_ivst_ccy')
                ->leftJoin('currencies as currencies_building', 'currencies_building.id', '=', 'apps.local_building_ivst_ccy')
                ->leftJoin('currencies as currencies_machinery', 'currencies_machinery.id', '=', 'apps.local_machinery_ivst_ccy')
                ->leftJoin('currencies as currencies_others', 'currencies_others.id', '=', 'apps.local_others_ivst_ccy')
                ->leftJoin('currencies as currencies_wc', 'currencies_wc.id', '=', 'apps.local_wc_ivst_ccy')
                ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
                ->leftJoin('sp_payment as gfp', 'gfp.id', '=', 'apps.gf_payment_id')
                ->leftJoin('industrial_city_list as reg_office', 'reg_office.id', '=', 'apps.bscic_office_id')
                ->leftJoin('company_director_type as director_type', 'director_type.id', '=', 'apps.director_type')
                ->leftJoin('ind_business_category as business_cat', 'business_cat.id', '=', 'apps.business_category_id')
                ->where('process_list.ref_id', $decodedAppId)
                ->where('process_list.process_type_id', $process_type_id)
                ->first([
                    'process_list.id as process_list_id',
                    'process_list.desk_id',
                    'process_list.process_type_id',
                    'process_list.status_id',
                    'process_list.ref_id',
                    'process_list.tracking_no',
                    'process_list.company_id',
                    'process_list.process_desc',
                    'process_list.submitted_at',
                    'ps.status_name',

                    'process_type.form_url',

                    'apps.*',

                    'registration_type.name_bn as regist_name_bn',
                    'company_type.name_bn as company_type_bn',
                    'investment_type.name_bn as investment_type_bn',
                    'industrial_category.name_bn as ind_category_bn',
                    'ind_sector_info.name_bn as ind_sector_bn',
                    'ind_sub_sector.name_bn as ind_sub_sector_bn',
                    'area_info_division.area_nm_ban as div_nm_ban',
                    'area_info_district.area_nm_ban as dis_nm_ban',
                    'area_info_thana.area_nm_ban as thana_nm_ban',
                    'f_area_info_division.area_nm_ban as f_div_nm_ban',
                    'f_area_info_district.area_nm_ban as f_dis_nm_ban',
                    'f_area_info_thana.area_nm_ban as f_thana_nm_ban',
                    'country_info_nationality.nationality as ceo_nationality',
                    'industrial_city_list.name as registration_office',
                    'currencies_land.code as currency_code_land',
                    'currencies_building.code as currency_code_building',
                    'currencies_machinery.code as currency_code_machinery',
                    'currencies_others.code as currency_others',
                    'currencies_wc.code as currencies_wc',
                    'reg_office.name as reg_office_name_bn',

                    'sfp.id as sfp_id',
                    'sfp.contact_name as sfp_contact_name',
                    'sfp.contact_email as sfp_contact_email',
                    'sfp.contact_no as sfp_contact_phone',
                    'sfp.address as sfp_contact_address',
                    'sfp.pay_amount as sfp_pay_amount',
                    'sfp.vat_on_pay_amount as sfp_vat_tax',
                    'sfp.transaction_charge_amount as sfp_bank_charge',
                    'sfp.payment_status as sfp_payment_status',
                    'sfp.pay_mode as pay_mode',
                    'sfp.pay_mode_code as pay_mode_code',
                    'sfp.total_amount as sfp_total_amount',
                    'sfp.transaction_charge_amount as sfp_transaction_charge_amount',
                    'sfp.vat_on_transaction_charge as sfp_vat_on_transaction_charge',
                    'sfp.pay_mode as sfp_pay_mode',
                    'sfp.pay_mode_code as sfp_pay_mode_code',

                    'gfp.id as gfp_id',
                    'gfp.contact_name as gfp_contact_name',
                    'gfp.contact_email as gfp_contact_email',
                    'gfp.contact_no as gfp_contact_phone',
                    'gfp.address as gfp_contact_address',
                    'gfp.pay_amount as gfp_pay_amount',
                    'gfp.vat_on_pay_amount as gfp_vat_tax',
                    'gfp.transaction_charge_amount as gfp_bank_charge',
                    'gfp.payment_status as gfp_payment_status',
                    'gfp.pay_mode as gfp_pay_mode',
                    'gfp.pay_mode_code as gf_pay_mode_code',
                    'gfp.total_amount as gfp_total_amount',
                    'gfp.transaction_charge_amount as gfp_transaction_charge_amount',
                    'gfp.vat_on_transaction_charge as gfp_vat_on_transaction_charge',
                    'director_type.name_bn as director_type',
                    'business_cat.name_bn as business_category',

                ]);

            $data['investing_country'] = IndRegInvCountry::leftJoin('country_info', 'country_info.id', '=', 'ind_reg_inv_country.country_id')
                ->where('app_id', $decodedAppId)->get([
                    'ind_reg_inv_country.*',
                    'country_info.name as country_name'
                ]);

            $data['localRawMaterial'] = IndLocalRawMaterial::leftJoin('apc_units', 'apc_units.id', '=', 'ind_reg_local_raw_material.local_raw_material_unit')
                ->where('app_id', $decodedAppId)->get();

            $data['annualProductionCapacity'] = AnnualProductionCapacity::where('app_id', $decodedAppId)->get();
            $data['utilityService'] = UtilityService::leftJoin('public_utility_resource', 'public_utility_resource.id', '=', 'ind_reg_utility_services.utility_id')
                ->where('app_id', $decodedAppId)
                ->get([
                    'ind_reg_utility_services.*',
                    'public_utility_resource.name_bn'
                ]);
            $data['loanSrcCountry'] = LoanSourceCountry::leftJoin('country_info', 'country_info.id', '=', 'ind_reg_loan_source_country.loan_country_id')
                ->where('app_id', $decodedAppId)->get([
                    'ind_reg_loan_source_country.*',
                    'country_info.name as country_name'
                ]);


            if (in_array($data['appInfo']->status_id, [15])) {
                $data['payment_step_id'] = 2;
                $data['unfixed_amounts'] = $this->unfixedAmountsForGovtApplicationFee($data['appInfo']->ind_category_id, $data['payment_step_id']);
            }

            $public_html = (string)view("IndustryNew::application-view", $data);
            // sleep(10);
            return response()->json(['responseCode' => 1, 'html' => $public_html]);
        } catch (\Exception $e) {
            return response()->json(['responseCode' => 0, 'html' => CommonFunction::showErrorPublic($e->getMessage()) . ' [VA-1016]']);
        }
    }

    public function unfixedAmountsForVendorServiceFee($ind_category_id, $payment_step_id)
    {
        $vat_percentage  = Configuration::where('caption', 'GOVT_VENDOR_VAT_FEE')->value('value');
        if (empty($vat_percentage)) {
            DB::rollback();
            Session::flash('error', 'Please, configure the value for VAT.[INR-1026]');
            return redirect()->back()->withInput();
        }

        $vendor_service_fee = $this->calculateVendorServiceFee($ind_category_id);

        return [
            1 => $vendor_service_fee, // Vendor-Service-Fee
            2 => 0, // Govt-Service-Fee
            3 => 0, // Govt. Application Fee
            4 => ($vendor_service_fee / 100) * $vat_percentage, // Vendor-Vat-Fee
            5 => 0, // Govt-Vat-Fee
            6 => 0 //govt-vendor-vat-fee
        ];
    }

    public function calculateVendorServiceFee($id)
    {
        $data = IndustrialCategory::where('id', $id)->first();
        return $data->oss_fee;
    }

    public function calculateGovtApplicationFee($id)
    {
        $data = IndustrialCategory::where('id', $id)->first();


        return $data->reg_fee;
    }

    public function Payment(Request $request)
    {
        try {
            DB::beginTransaction();
            $appId = Encryption::decodeId($request->get('app_id'));

            $appData = IndustryNew::find($appId);
            $contactInfo = ['contact_no' => !empty($request->get('gfp_contact_phone')) ? $request->gfp_contact_phone : '', 'address' => !empty($request->get('gfp_contact_address')) ? $request->gfp_contact_address : ''];
            $calculatedAmountArray = $this->unfixedAmountsForGovtApplicationFee($appData->ind_category_id, 2);

            $payment_id = $this->storeSubmissionFeeData($appData, 2, $contactInfo, $calculatedAmountArray['unfixed_amount_array'], $calculatedAmountArray);

            DB::commit();
            if ($request->get('actionBtn') == 'Submit' && $payment_id) {
                return redirect('spg/initiate-multiple/' . Encryption::encodeId($payment_id));
            }
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . "[IRN-1025]");
            return redirect()->back()->withInput();
        }
    }

    public function unfixedAmountsForGovtApplicationFee($ind_category_id, $payment_step_id)
    {
        $govt_application_fees = $this->calculateGovtApplicationFee($ind_category_id);
        $vat_percentage  = Configuration::where('caption', 'GOVT_VENDOR_VAT_FEE')->value('value');

        $unfixed_amount_array = [
            1 => 0, // Vendor-Service-Fee
            2 => 0, // Govt-Service-Fee
            3 => $govt_application_fees, // Govt. Application Fee
            4 => 0, // Vendor-Vat-Fee
            5 => ($govt_application_fees / 100) * $vat_percentage, // Govt-Vat-Fee
            6 => 0
        ];

        return $unfixed_amount_array;
    }

    public function preview()
    {
        return view("IndustryNew::preview");
    }
}
