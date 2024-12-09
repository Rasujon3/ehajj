<?php

namespace App\Modules\CertificateGeneration\Http\Controllers;

use App\Libraries\Encryption;
use App\Modules\CertificateGeneration\Services\IndustryReRegistration;
use App\Modules\IndustryNew\Models\AnnualProductionCapacity;
use App\Modules\IndustryNew\Models\InvestorList;
use App\Modules\IndustryNew\Models\LoanSourceCountry;
use App\Modules\IndustryNew\Models\MachineryImported;
use App\Modules\IndustryNew\Models\MachineryLocal;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\PdfPrintRequestQueue;
use App\Modules\Users\Models\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use Milon\Barcode\DNS1D;
use DB;
use Milon\Barcode\DNS2D;
use App\Modules\CertificateGeneration\helper;


class CertificateGenerationController extends Controller
{
//    use IndustryReRegistration;
    private $offset = 0;//default offset
    private $limit = 5;//default limit

    public function generateCertificate($index='-1'){

        /*  *** initial start must be -1
            *** second parameter will be set 0
            *** and others parameter will be set like 1,2,3....15
         # multi thread start */
        if ($index > 15) {exit;}
        elseif ($index == '-1'){
            $this->offset = 0;
        }else{
            $this->offset = ($index * $this->limit) + $this->limit;
        }
        /* # multi thread end */

        $pdfDataTake = PdfPrintRequestQueue::where("job_receiving_status", 0)
            ->whereIn('process_type_id', ['1','2']) // = registration, 2= registration, and others certificate can do usign python
            ->where("no_of_try_job_sending",'<', 3)
            ->skip($this->offset)
            ->take($this->limit);
        $pdfData = $pdfDataTake->get();
        $pdfDataTake->update(["job_receiving_status"=>'-1', "no_of_try_job_sending"=>DB::raw("no_of_try_job_sending+1")]);

        if($pdfData->isEmpty()){
            echo '<br/>No PDF in queue to send! ' . date("j F, Y, g:i a");
        }
        $dn1d = new DNS1D();
        $dn1dx = new DNS2D();


        foreach ($pdfData as $row){
            try{
                if($row->process_type_id == 1){
                    $unique_id = generateUniqueId();

                    $appInfo =  $this->industryNewData($row->process_type_id, $row->app_id, $row->signatory);

//                    $mpdf = new \Mpdf\Mpdf();
//                    $mpdf->WriteHTML('<h1>Hello world!</h1>');
//                    $mpdf->Output();

                    $barcode = $dn1d->getBarcodePNG($appInfo['appInfo']->tracking_no, 'C39', 2, 60);
                    $appInfo['qrCode'] =$dn1dx->getBarcodePNG(URL::to('/').'/docs/'.$unique_id, 'QRCODE');
                    $img = '<img src="data:image/png;base64,'.$barcode.'" height="30"  alt="barcode" />';
                    $content = view("CertificateGeneration::industry_new", $appInfo)
                        ->render();



                    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults(); // extendable default Configs
                    $fontDirs = $defaultConfig['fontDir'];

                    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults(); // extendable default Fonts
                    $fontData = $defaultFontConfig['fontdata'];

                    $mpdf = new \Mpdf\Mpdf([
                        'tempDir'       => storage_path(),
                        'fontDir' => array_merge($fontDirs, [
                            public_path('assets/fonts'), // to find like /public/fonts/SolaimanLipi.ttf
                        ]),
                        'fontdata' => $fontData + [
                                'kalpurush' => [
                                    'R' => 'kalpurush-kalpurush.ttf', 'I' => 'kalpurush-kalpurush.ttf', 'useOTL' => 0xFF, 'useKashida' => 75,

                                ],
                            ],
                        'default_font' => 'kalpurush',
                        'setAutoTopMargin' => 'pad',
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'default_font_size' => 11,
                    ]);

//            $mpdf->useSubstitutions;
                    $mpdf->SetProtection(array('print'));
                    $mpdf->SetDefaultBodyCSS('color', '#000');
                    $mpdf->SetTitle("One Stop Service");
                    $mpdf->SetSubject("Subject");
                    $mpdf->SetAuthor("Business Automation Limited");
                    //$mpdf->autoScriptToLang = true;
                    $mpdf->baseScript = 1;
                    $mpdf->autoVietnamese = true;
                    $mpdf->SetDisplayMode('fullwidth');
                    $mpdf->SetHTMLHeader('<div class="header" style="margin:auto;width: 100%">
          <div class="logo_image" style="float: left; width: 100px">
                <img src="assets/images/govt.png" alt="" height="85%">
            </div>
            <div style="text-align: center;width: 460px;float: left;">
                <h4 style=" margin: 0px;padding: 0px">
                গণপ্রজাতন্ত্রী বাংলাদেশ সরকার
                </h4>

                <h3 style="font-weight: bolder ;margin: 0px;padding: 0px">
               বিজনেস অটোমেশন লিমিটেড
                </h3>
               <span style="font-size: 14px;">৩৯৮ কাওরান বাজার, ঢাকা - ১২০৮</span>

            </div>
            <div class="logo_image" style="float: right; width: 100px;" >
                <img src="assets/images/oss.png" alt="" width="100%">
            </div>
        <div class="barcode" style="float: none; margin: 0px 235px  auto;width: 200px; text-align: center;">

      '.$img.' <span style="margin-left: 15%; font-size:16px">'.$appInfo['appInfo']->tracking_no.'</span>
</div>

    </div>');

                    $mpdf->SetHTMLFooter('
<div class="text-center">
<span style="   font-size: 11px;">৩৯৮ কাওরান বাজার, ঢাকা - ১২০৮, ফোন: ৯৫৫৬১৯১-২, ফ্যাক্স: ৯৫৫০৭০৪, ইমেইল: info@ba-system.com অনুমোদিত কপির সত্যতা যাচাই করতে QR কোড স্ক্যান করুন অথবা লগইন করুন https://www.ba-systems.com </span>
</div>', true);
                    $stylesheet = file_get_contents('assets/stylesheets/appviewPDF.css');

                    $mpdf->setAutoTopMargin = 'stretch';
                    $mpdf->setAutoBottomMargin = 'stretch';
                    $mpdf->showWatermarkImage = true;
//                    if(in_array(config('app.APP_ENV'), ['local','dev','uat','training'])){
//                        $mpdf->SetWatermarkImage('assets/images/pdf_watermark_test.png',
//                            0.6,'D','F'
//                        );
//                    }else{
//                        $mpdf->SetWatermarkImage('assets/images/BSCIC_logo-01.png',
//                            0.1,'F','F'
//                        );
//                    }

//                    $mpdf->SetWatermarkImage('assets/images/BSCIC_logo-01.png',
//                        0.1,'F','F'
//                    );
                    $mpdf->WriteHTML($stylesheet, 1);
                    $mpdf->WriteHTML($content, 2);
//                    $mpdf->defaultfooterfontstyle = 'B';
                    $mpdf->defaultfooterline = 0;
                    $mpdf->SetCompression(true);
                    $baseURL = "certificate/";
                    $directoryName = $baseURL . date("Y/m");
                    $directoryNameYear = $baseURL . date("Y");

                    directoryFunction($directoryName, $directoryNameYear);
                    $certificateName = uniqid("certificate_", true);
                    $pdfFilePath = $directoryName . "/" . $certificateName . '.pdf';
                    $mpdf->Output($pdfFilePath, 'F'); // Saving pdf *** F for Save only, I for view only.
//exit();

                    $fullPath = URL::to('/').'/'.$pdfFilePath;
                    saveCertificate($row, $fullPath, $unique_id);
                    echo "certificate generate successfully ".PHP_EOL;

                }


                elseif($row->process_type_id == 2){ // re-registration
                    $this->certificateGenerate($row, $row->app_id, $row->signatory);
                } else{
                    dd("the process type was not found!");
                }

            }catch (\Exception $e){
                echo $e->getFile().' '.$e->getMessage().' '.$e->getLine();
//                $row->status = 0;
                $row->job_receiving_status = -1;
                $row->job_receiving_response = $e->getMessage().', line: '.$e->getLine();
                $row->save();
            }

        }

    }


    public function industryNewData($process_type_id, $decodedAppId, $signatory){
        $data['appInfo'] = ProcessList::leftJoin('ind_reg_apps as apps', 'apps.id', '=', 'process_list.ref_id')
            ->leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
            ->leftJoin('process_status as ps', function ($join) use ($process_type_id) {
                $join->on('ps.id', '=', 'process_list.status_id');
                $join->on('ps.process_type_id', '=', DB::raw($process_type_id));
            })
            ->leftJoin('user_desk', 'user_desk.id', '=', 'process_list.desk_id')
            ->leftJoin('users', 'process_list.updated_by', '=', 'users.id')
            ->leftJoin('registration_type', 'registration_type.id', '=', 'apps.regist_type')
            ->leftJoin('company_type', 'company_type.id', '=', 'apps.org_type')
            ->leftJoin('investment_type', 'investment_type.id', '=', 'apps.invest_type')
            ->leftJoin('industrial_category', 'industrial_category.id', '=', 'apps.ind_category_id')
            ->leftJoin('ind_sector_info', 'ind_sector_info.id', '=', 'apps.ins_sector_id')
            ->leftJoin('ind_sector_info as ind_sub_sector', 'ind_sub_sector.id', '=', 'apps.ins_sub_sector_id')
            ->leftJoin('area_info as area_info_division', 'area_info_division.area_id', '=', 'apps.office_division')
            ->leftJoin('area_info as area_info_district', 'area_info_district.area_id', '=', 'apps.office_district')
            ->leftJoin('area_info as area_info_thana', 'area_info_thana.area_id', '=', 'apps.office_thana')
            ->leftJoin('area_info as f_area_info_division', 'f_area_info_division.area_id', '=', 'apps.factory_division')
            ->leftJoin('area_info as f_area_info_district', 'f_area_info_district.area_id', '=', 'apps.factory_district')
            ->leftJoin('area_info as f_area_info_thana', 'f_area_info_thana.area_id', '=', 'apps.factory_thana')
            ->leftJoin('country_info as country_info_nationality', 'country_info_nationality.id', '=', 'apps.nationality')
//            ->leftJoin('designation', 'designation.id', '=', 'apps.designation')
//            ->leftJoin('area_info as ceo_area_div', 'ceo_area_div.area_id', '=', 'apps.ceo_division')
//            ->leftJoin('area_info as ceo_area_dis', 'ceo_area_dis.area_id', '=', 'apps.ceo_district')
//            ->leftJoin('area_info as ceo_area_thana', 'ceo_area_thana.area_id', '=', 'apps.ceo_thana')
//            ->leftJoin('designation as ent_designation', 'ent_designation.id', '=', 'apps.entrepreneur_designation')
            ->leftJoin('industrial_city_list', 'industrial_city_list.id', '=', 'apps.bscic_office_id')
            ->leftJoin('area_info as industrial_city_dis', 'industrial_city_dis.area_id', '=', 'industrial_city_list.district')
            ->leftJoin('currencies as currencies_land', 'currencies_land.id', '=', 'apps.local_land_ivst_ccy')
            ->leftJoin('currencies as currencies_building', 'currencies_building.id', '=', 'apps.local_building_ivst_ccy')
            ->leftJoin('currencies as currencies_machinery', 'currencies_machinery.id', '=', 'apps.local_machinery_ivst_ccy')
            ->leftJoin('currencies as currencies_others', 'currencies_others.id', '=', 'apps.local_others_ivst_ccy')
            ->leftJoin('currencies as currencies_wc', 'currencies_wc.id', '=', 'apps.local_wc_ivst_ccy')
            ->leftJoin('sp_payment as sfp', 'sfp.id', '=', 'apps.sf_payment_id')
            ->leftJoin('sp_payment as gfp', 'gfp.id', '=', 'apps.gf_payment_id')
            ->leftJoin('registration_office as reg_office', 'reg_office.id', '=', 'apps.bscic_office_id')
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
                'process_list.completed_date',
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
//                'designation.name_bn as ceo_designation',
//                'ceo_area_div.area_nm_ban as ceo_area_div',
//                'ceo_area_dis.area_nm_ban as ceo_area_dis',
//                'ceo_area_thana.area_nm_ban as ceo_area_thana',
//                'ent_designation.name_bn as ent_desg_bn',
                'industrial_city_list.name as registration_office',
                'industrial_city_dis.area_nm_ban as industrial_city_dis_name',
                'currencies_land.code as currency_code_land',
                'currencies_building.code as currency_code_building',
                'currencies_machinery.code as currency_code_machinery',
                'currencies_others.code as currency_others',
                'currencies_wc.code as currencies_wc',
                'reg_office.name_bn as reg_office_name_bn',

                'sfp.contact_name as sfp_contact_name',
                'sfp.contact_email as sfp_contact_email',
                'sfp.contact_no as sfp_contact_phone',
                'sfp.address as sfp_contact_address',
                'sfp.pay_amount as sfp_pay_amount',
//                'sfp.vat_amount as sfp_vat_tax',
                'sfp.transaction_charge_amount as sfp_bank_charge',
                'sfp.payment_status as sfp_payment_status',
                'sfp.pay_mode as pay_mode',
                'sfp.pay_mode_code as pay_mode_code',

                'gfp.contact_name as gfp_contact_name',
                'gfp.contact_email as gfp_contact_email',
                'gfp.contact_no as gfp_contact_phone',
                'gfp.address as gfp_contact_address',
                'gfp.pay_amount as gfp_pay_amount',
//                'gfp.vat_amount as gfp_vat_tax',
                'gfp.transaction_charge_amount as gfp_bank_charge',
                'gfp.payment_status as gfp_payment_status',
                'gfp.pay_mode as gfp_pay_mode',
                'gfp.pay_mode_code as gf_pay_mode_code',
                'users.user_first_name',
                'users.designation as user_designation',
                'users.user_mobile',
                'users.user_email',
            ]);

        $data['annualProductionCapacity'] = AnnualProductionCapacity::leftJoin('apc_units', 'apc_units.id', '=', 'ind_reg_annual_prod_capacity.unit')
            ->where('app_id', $decodedAppId)->get();
        $data['loanSrcCountry'] = LoanSourceCountry::leftJoin('country_info', 'country_info.id', '=', 'ind_reg_loan_source_country.loan_country_id')
            ->where('app_id', $decodedAppId)->get([
                'ind_reg_loan_source_country.*',
                'country_info.name as country_name'
            ]);

        $data['signatory'] = Users::where('id', $signatory)->first([
            'user_first_name',
            'designation',
            'user_mobile',
            'user_email',
            'signature',
        ]);


        return $data;
    }
}
