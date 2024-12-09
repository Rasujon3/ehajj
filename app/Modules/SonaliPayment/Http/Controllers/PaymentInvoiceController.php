<?php

namespace App\Modules\SonaliPayment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\Encryption;
use App\Modules\Settings\Models\Configuration;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use App\Modules\Users\Models\CompanyInfo;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Milon\Barcode\DNS1D;
use Mpdf\Mpdf;

class PaymentInvoiceController extends Controller
{
    /**
     * Payment Voucher
     * @param null $paymentId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function paymentVoucher($paymentId = null)
    {
        if (empty($paymentId)) {
            Session::flash('error', 'Invalid payment id [PIC-113]');
            return redirect()->back();
        }

        try {
            $decodedPaymentId = Encryption::decodeId($paymentId);
            $paymentInfo = SonaliPayment::where('id', $decodedPaymentId)->first();
            $companyName = CompanyInfo::leftJoin('process_list', 'process_list.company_id', '=', 'company_info.id')
                ->where('process_list.process_type_id', $paymentInfo->process_type_id)
                ->where('process_list.ref_id', $paymentInfo->app_id)
                ->value('org_nm as company_name');
            $voucher_title = config('app.project_name');
            $voucher_subtitle = 'ICT Division';
            $voucher_logo_path = app_path('/Modules/SonaliPayment/resources/images/business_automation.png');

            $mobile_number = Configuration::where('caption', 'SP_PAYMENT_HELPLINE_VOUCHER')->first();

            $dn1d = new DNS1D();
            $trackingNo = $paymentInfo->app_tracking_no; // tracking no push on barcode.
            if (!empty($trackingNo)) {
                $barcode = $dn1d->getBarcodePNG($trackingNo, 'C39');
                $barcode_url = 'data:image/png;base64,' . $barcode;
            } else {
                $barcode_url = '';
            }

            $contents = view("SonaliPayment::paymentVoucher-pdf",
                compact('decodedPaymentId', 'paymentInfo', 'barcode_url', 'companyName',
                    'voucher_title', 'voucher_subtitle', 'voucher_logo_path'))->render();

            $mpdf = new Mpdf([
                'utf-8', // mode - default ''
                'A4', // format - A4, for example, default ''
                10, // font size - default 0
                'dejavusans', // default font family
                10, // margin_left
                10, // margin right
                10, // margin top
                10, // margin bottom
                10, // margin header
                10, // margin footer
                'P'
            ]);
            // $mpdf->Bookmark('Start of the document');
            $mpdf->useSubstitutions;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetDefaultBodyCSS('color', '#000');
            $mpdf->SetTitle(config('app.project_name'));
            $mpdf->SetSubject("Subject");
            $mpdf->SetAuthor("Business Automation Limited");
            $mpdf->autoScriptToLang = true;
            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;

            $mpdf->autoLangToFont = true;
            $mpdf->SetDisplayMode('fullwidth');
            $mpdf->SetHTMLFooter('
                    <table width="100%">
                        <tr>
                            <td width="50%"><i style="font-size: 10px;">Download time: {DATE j-M-Y h:i a}</i></td>
                            <td width="50%" align="right"><i style="font-size: 10px;">Help line: ' . $mobile_number->value . '</i></td>
                        </tr>
                    </table>');
            $stylesheet = file_get_contents(app_path('/Modules/SonaliPayment/resources/css/pdf_style.min.css'));
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';

            if ($paymentInfo->status_code == 200) {
                $mpdf->SetWatermarkImage(app_path('/Modules/SonaliPayment/resources/images/paid.png'), 1, [36, 35], [80, 65]);
                $mpdf->showWatermarkImage = true;
            }

            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($contents, 2);

            $mpdf->defaultfooterfontsize = 10;
            $mpdf->defaultfooterfontstyle = 'B';
            $mpdf->defaultfooterline = 0;

            $mpdf->SetCompression(true);
            $mpdf->Output($paymentInfo->app_tracking_no . '.pdf', 'I');

        } catch (\Exception $e) {
            dd($e->getMessage());
            Session::flash('error', 'Sorry! Something went wrong! [PIC-117]');
            return Redirect::back()->withInput();
        }
    }

    /**
     * Counter payment voucher
     * @param null $paymentId
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function counterPaymentVoucher($paymentId = null)
    {
        if (empty($paymentId)) {
            Session::flash('error', 'Invalid payment id [PIC-112]');
            return redirect()->back();
        }

        try {
            $decodedPaymentId = Encryption::decodeId($paymentId);
            $paymentInfo = SonaliPayment::where('id', $decodedPaymentId)->first();
            $companyName = CompanyInfo::leftJoin('process_list', 'process_list.company_id', '=', 'company_info.id')
                ->where('process_list.process_type_id', $paymentInfo->process_type_id)
                ->where('process_list.ref_id', $paymentInfo->app_id)
                ->value('company_name');

            $mobile_number = Configuration::where('caption', 'SP_PAYMENT_HELPLINE_VOUCHER')->first();
            $voucher_title = config('app.project_name');
            $voucher_subtitle = 'ICT Division';
            $voucher_logo_path = app_path('/modules/SonaliPayment/resources/images/business_automation.png');
            $dn1d = new DNS1D();
            $trackingNo = $paymentInfo->app_tracking_no; // tracking no push on barcode.
            if (!empty($trackingNo)) {
                $barcode = $dn1d->getBarcodePNG($trackingNo, 'C39');
                $barcode_url = 'data:image/png;base64,' . $barcode;
            } else {
                $barcode_url = '';
            }

            $contents = view("SonaliPayment::counterVoucher-pdf",
                compact('decodedPaymentId', 'paymentInfo', 'barcode_url', 'companyName',
                    'voucher_title', 'voucher_subtitle', 'voucher_logo_path'))->render();

            $mpdf = new Mpdf([
                'utf-8', // mode - default ''
                'A4', // format - A4, for example, default ''
                12, // font size - default 0
                'dejavusans', // default font family
                10, // margin_left
                10, // margin right
                10, // margin top
                15, // margin bottom
                10, // margin header
                10, // margin footer
                'P'
            ]);
            // $mpdf->Bookmark('Start of the document');
            $mpdf->useSubstitutions;
            $mpdf->SetProtection(array('print'));
            $mpdf->SetDefaultBodyCSS('color', '#000');
            $mpdf->SetTitle(config('app.project_name'));
            $mpdf->SetSubject("Subject");
            $mpdf->SetAuthor("Business Automation Limited");
            $mpdf->autoScriptToLang = true;
            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;
            $mpdf->autoArabic = true;

            $mpdf->autoLangToFont = true;
            $mpdf->SetDisplayMode('fullwidth');
            $mpdf->SetHTMLFooter('
                    <table width="100%">
                        <tr>
                            <td width="50%"><i style="font-size: 10px;">Download time: {DATE j-M-Y h:i a}</i></td>
                            <td width="50%" align="right"><i style="font-size: 10px;">Help line: ' . $mobile_number->value . '</i></td>
                        </tr>
                    </table>');
            $stylesheet = file_get_contents(app_path('/modules/SonaliPayment/resources/css/pdf_style.min.css'));
            $mpdf->setAutoTopMargin = 'stretch';
            $mpdf->setAutoBottomMargin = 'stretch';

            if ($paymentInfo->status_code == 200) {
                $mpdf->SetWatermarkImage(app_path('/modules/SonaliPayment/resources/images/paid.png'), 1, [36, 35], [80, 180]);
                $mpdf->showWatermarkImage = true;
            }

            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($contents, 2);

            $mpdf->defaultfooterfontsize = 10;
            $mpdf->defaultfooterfontstyle = 'B';
            $mpdf->defaultfooterline = 0;

            $mpdf->SetCompression(true);
            $mpdf->Output($paymentInfo->app_tracking_no . '.pdf', 'I');

        } catch (\Exception $e) {
            Session::flash('error', 'Sorry! Something went wrong! [PIC-114]');
            return Redirect::back()->withInput();
        }
    }
}
