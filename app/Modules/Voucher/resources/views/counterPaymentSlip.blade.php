<?php
$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
<section class="content" id="applicationForm">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <!-- Basic Information start -->
                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td width="20%" class="text-left">
                            <img
                                src="{{ public_path('assets/images/mora.png') }}"
                                style="height: 70px;"
                                alt="Ministry Logo"
                            />
                        </td>
                        <td width="60%" style="text-align: center; vertical-align: middle;">
                            <p>
                                <span style="font-weight: bold; font-size: 16px;" >Online Payment Slips of haj Pilgrims</span> <br>
                                <span style="font-weight: bold;" >(For preservation by pilgrims)</span> <br>
                            </p>
                            <p>
                                <img
                                    src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($apiResponseDataArr->paymentInfo->hpg_pay_unique_id ?? '', $generatorPNG::TYPE_CODE_128)) }}"
                                    alt="barcode"
                                    width="125"
                                    height="30"
                                    style="display: block; margin: 0 auto; max-width: 100%; padding: 5px;"
                                />
                            </p>
                        </td>
                        <td width="20%" class="text-right">
                            <p style="border: 1px solid black;">&nbsp;&nbsp; Applicant Copy &nbsp;&nbsp;</p>
                        </td>
                    </tr>
                </table>
                <table class="mt-2" cellspacing="0" width="100%">
                    <tr>
                        <td class="text-center">
                            <h5 class="text-center">Payment Information</h5>
                        </td>
                    </tr>
                </table>
                <!-- Basic Information End -->
                <!-- Payment Information start -->
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 5px 5px 5px 10px; font-size: 11px; width: 14%; vertical-align: top;">Tracking No</td>
                        <td style="padding: 5px 0px; font-size: 11px; width: 1%; vertical-align: top;">:</td>
                        <td style="padding: 5px 10px 5px 5px; font-size: 11px; width: 34%; vertical-align: top;">{{ $apiResponseDataArr->paymentInfo->tracking_no ?? '' }}</td>
                        <td style="width: 2%;">&nbsp;</td>
                        <td style="padding: 5px 5px 5px 10px; font-size: 11px; width: 10%; vertical-align: top; text-align: right;"></td>
                        <td style="padding: 5px 0px; font-size: 11px; width: 1%; vertical-align: top; text-align: right;"></td>
                        <td style="padding: 5px 10px 5px 5px; font-size: 11px; width: 10%; vertical-align: top; text-align: right;"></td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 5px 5px 10px; font-size: 11px; width: 14%; vertical-align: top; text-align: left;">Payment ID</td>
                        <td style="padding: 5px 0px; font-size: 11px; width: 1%; vertical-align: top; text-align: left;">:</td>
                        <td style="padding: 5px 10px 5px 5px; font-size: 11px; width: 34%; vertical-align: top; text-align: left;">{{ $apiResponseDataArr->paymentInfo->hpg_pay_unique_id ?? '' }}</td>
                        <td style="width: 2%;">&nbsp;</td>
                        <td style="padding: 5px 5px 5px 10px; font-size: 11px; width: 10%; vertical-align: top; text-align: right;">Date</td>
                        <td style="padding: 5px 0px; font-size: 11px; width: 1%; vertical-align: top; text-align: right;">:</td>
                        <td style="padding: 5px 10px 5px 5px; font-size: 11px; width: 10%; vertical-align: top; text-align: left;">{{ $current_time }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 5px 5px 10px; font-size: 11px; width: 14%; vertical-align: top;">Bank Name</td>
                        <td style="padding: 5px 0px; font-size: 11px; width: 1%; vertical-align: top;">:</td>
                        <td style="padding: 5px 10px 5px 5px; font-size: 11px; width: 34%; vertical-align: top;">Sonali Bank Limited</td>
                        <td style="width: 2%;">&nbsp;</td>
                        <td style="padding: 5px 5px 5px 10px; font-size: 11px; width: 10%; vertical-align: top; text-align: right;">Payment Mode</td>
                        <td style="padding: 5px 0px; font-size: 11px; width: 1%; vertical-align: top; text-align: right;">:</td>
                        <td style="padding: 5px 10px 5px 5px; font-size: 11px; width: 10%; vertical-align: top; text-align: left;">{{ $apiResponseDataArr->payMode->pay_mode ?? '' }}</td>
                    </tr>
                </table>
                <!-- Payment Information end -->
                <!-- Payment Summary Start -->
                <table style="width: 100%;">
                    <thead>
                    <tr style="">
                        <th style="text-align: left; padding: 5px 2px; border: 2px solid #EEEEEE; width: 8%;">Payment Summary</th>
                        <th style="text-align: center; padding: 5px 2px; border: 2px solid #EEEEEE; width: 15%;">Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="">
                        <td style="border: 2px solid #EEEEEE; padding: 5px 2px; width: 8%;">Pay Amount</td>
                        <td style="text-align: center; border: 2px solid #EEEEEE; padding: 5px 0px; width: 15%;">{{ $apiResponseDataArr->paymentInfo->amount ?? '' }}</td>
                    </tr>
                    <tr style="">
                        <td style="border: 2px solid #EEEEEE; padding: 5px 2px; width: 8%;">Transaction Charge</td>
                        <td style="text-align: center; border: 2px solid #EEEEEE; padding: 5px 0px; width: 15%;">{{ $apiResponseDataArr->paymentInfo->commission ?? '' }}</td>
                    </tr>
                    <tr style="">
                        <td style="border: 2px solid #EEEEEE; padding: 5px 2px; width: 8%;">VAT on transaction Charge</td>
                        <td style="text-align: center; border: 2px solid #EEEEEE; padding: 5px 0px; width: 15%;">{{ $apiResponseDataArr->paymentInfo->vat ?? '' }}</td>
                    </tr>
                    <tr style="">
                        <td style="border: 2px solid #EEEEEE; padding: 5px 2px; width: 8%;">Total fees</td>
                        <td style="text-align: center; border: 2px solid #EEEEEE; padding: 5px 0px; width: 15%;">{{ $apiResponseDataArr->paymentInfo->withCharge ?? '' }}</td>
                    </tr>
                    </tbody>
                </table>
                <!-- Payment Summary end -->
                <!-- Amount in word start -->
                <table class="" cellspacing="0" width="100%" style="font-size: 12px">
                    <tr>
                        <td class="text-left">
                            <p style="padding: 5px;">Amount in Words (Taka) : {{ $apiResponseDataArr->paymentInfo->withCharge ? ucwords(CommonFunction::convert_number_to_words($apiResponseDataArr->paymentInfo->withCharge)) : '' }} Taka only </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">
                            <p style="padding: 5px;">Depositor Name : {{  $apiResponseDataArr->paymentInfo->depositor_name ?? ''}} </p>
                        </td>
                        <td style="text-align: right;">
                            <p style="padding: 5px; ">Depositor Mobile Number : {{  $apiResponseDataArr->paymentInfo->depositor_mobile ?? ''}} </p>
                        </td>
                    </tr>
                </table>
                <!-- Amount in word end -->
            </div>
        </div>
    </div>
</section>
</body>
</html>
