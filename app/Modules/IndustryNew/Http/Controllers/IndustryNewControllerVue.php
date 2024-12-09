<?php

namespace App\Modules\IndustryNew\Http\Controllers;

use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\IndustryNew\Models\AnnualProductionCapacity;
use App\Modules\IndustryNew\Models\IndLocalRawMaterial;
use App\Modules\IndustryNew\Models\IndRegInvCountry;
use App\Modules\IndustryNew\Models\LoanSourceCountry;
use App\Modules\IndustryNew\Models\UtilityService;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\SonaliPayment\Services\SPAfterPaymentManager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\CompanyProfile\Models\IndustrialCategory;
use App\Modules\Settings\Models\Configuration;
use App\Modules\SonaliPayment\Services\SPPaymentManager;
use Illuminate\Support\Facades\DB;

class IndustryNewControllerVue extends Controller
{
    use SPPaymentManager;
    use SPAfterPaymentManager;

    protected $process_type_id;
    protected $acl_name;

    public function __construct()
    {
        $this->process_type_id = 1; // 1 is for IndustryNew
        $this->acl_name = 'IndustryNew';
    }

    public function appFormView($appId)
    {

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

            // $public_html = (string)view("IndustryNew::application-view", $data);
            // sleep(10);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['responseCode' => 0, 'html' => CommonFunction::showErrorPublic($e->getMessage()) . ' [VA-1016]']);
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

    public function calculateGovtApplicationFee($id)
    {
        $data = IndustrialCategory::where('id', $id)->first();

        return $data->reg_fee;
    }
}
