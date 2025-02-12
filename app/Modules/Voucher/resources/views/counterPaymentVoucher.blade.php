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
                                <span>হজে গমনেচ্ছুদের অনলাইন পেমেন্ট ভাউচার</span> <br>
                                <span>(হজযাত্রী কর্তৃক সংরক্ষণের জন্য)</span> <br>
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
                            <p style="border: 1px solid black;">&nbsp;&nbsp; Bank Copy &nbsp;&nbsp;</p>
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
                            <td style="text-align: center; border: 2px solid #EEEEEE; padding: 5px 0px; width: 15%;">{{ $apiResponseDataArr->paymentInfo->amount ?? '' }}</td>
                        </tr>
                    </tbody>
                </table>
                <!-- Payment Summary end -->
                <!-- Amount in word start -->
                <table class="" cellspacing="0" width="100%" style="font-size: 12px">
                    <tr>
                        <td class="text-left">
                            <p style="padding: 5px;">Amount in Words (Taka) : {{ $apiResponseDataArr->paymentInfo->amount ? ucwords(CommonFunction::convert_number_to_words($apiResponseDataArr->paymentInfo->withCharge)) : '' }} Taka only </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">
                            <p style="padding: 5px;">Depositor Name : {{  $apiResponseDataArr->paymentInfo->depositor_name ?? ''}} </p>
                        </td>
                        <td style="text-align: right;">
                            <p style="padding: 5px;">Depositor Mobile Number : {{  $apiResponseDataArr->paymentInfo->depositor_mobile ?? ''}} </p>
                        </td>
                    </tr>
                </table>
                <!-- Amount in word end -->
                <!-- Signature Part start -->
                <br>
                <br>
                <table class="" cellspacing="0" width="100%" style="font-size: 12px">
                    <tr>
                        <td width="45%">
                            <span>_____________________</span><br/>
                            <span>Depositor signature</span>
                        </td>
                        <td width="40%">
                            <span>____________</span><br/>
                            <span>Received By</span>
                        </td>
                        <td width="15%">
                            <span class="text-right">______________</span><br/>
                            <span class="text-right">Approved By</span>
                        </td>
                    </tr>
                </table>
                <!-- Signature Part end -->
                <br>
                <!-- Divider Part start -->
                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td width="100%" class="text-left">
                            <img
                                src="{{ public_path('assets/images/divider.png') }}"
                                style="height: 70px;"
                                alt="page divider"
                            />
                        </td>
                    </tr>
                </table>
                <!-- Divider Part end -->
            </div>
        </div>
    </div>
</section>
<br>
<br>
<section class="content" id="applicationForm2">
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
                                alt="ministry logo"
                            />
                        </td>
                        <td width="60%" style="text-align: center; vertical-align: middle;">
                            <p>
                                <span>হজে গমনেচ্ছুদের অনলাইন পেমেন্ট ভাউচার</span> <br>
                                <span>(হজযাত্রী কর্তৃক সংরক্ষণের জন্য)</span> <br>
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
                <!-- Basic Information end -->
                <!-- Payment Information start -->
                <table style="width: 100%;">
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
                        <td style="text-align: center; border: 2px solid #EEEEEE; padding: 5px 0px; width: 15%;">{{ $apiResponseDataArr->paymentInfo->amount ?? '' }}</td>
                    </tr>
                    </tbody>
                </table>
                <!-- Payment Summary end -->
                <!-- Amount in word start -->
                <table class="" cellspacing="0" width="100%" style="font-size: 12px">
                    <tr>
                        <td class="text-left">
                            <p style="padding: 5px;">Amount in Words (Taka) : {{ $apiResponseDataArr->paymentInfo->amount ? ucwords(CommonFunction::convert_number_to_words($apiResponseDataArr->paymentInfo->withCharge)) : '' }} </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">
                            <p style="padding: 5px;">Depositor Name : {{  $apiResponseDataArr->paymentInfo->depositor_name ?? ''}} </p>
                        </td>
                        <td style="text-align: right;">
                            <p style="padding: 5px;">Depositor Mobile Number : {{  $apiResponseDataArr->paymentInfo->depositor_mobile ?? ''}} </p>
                        </td>
                    </tr>
                </table>
                <!-- Amount in word end -->
                <!-- Signature Part start -->
                <br>
                <br>
                <table class="" cellspacing="0" width="100%" style="font-size: 12px">
                    <tr>
                        <td width="45%">
                            <span>_____________________</span><br/>
                            <span>Depositor signature</span>
                        </td>
                        <td width="40%">
                            <span>____________</span><br/>
                            <span>Received By</span>
                        </td>
                        <td width="15%">
                            <span class="text-right">______________</span><br/>
                            <span class="text-right">Approved By</span>
                        </td>
                    </tr>
                </table>
                <!-- Signature Part end -->
                <br>
                <!-- Divider Part start -->
                <table class="" cellspacing="0" width="100%">
                    <tr>
                        <td width="100%" class="text-left">
                            <p style="font-weight: bold;">এই ভাউচারটি সোনালী ব্যাংকের যেকোনো ব্রাঞ্চে গিয়ে সরাসরি টাকা প্রদান করতে হবে, এবং টাকা জমাদানের পরে ই-হজ সাইট থেকে লগইন করে প্রাকনিবন্ধন ভাউচার থেকে Payment Verify বাটনে ক্লিক করে পেমেন্ট নিশ্চিত করে প্রাকনিবন্ধন সনদ ডাউনলোড করতে হবে।
                            </p>
                        </td>
                    </tr>
                </table>
                <!-- Divider Part end -->
            </div>
        </div>
    </div>
</section>
</body>
</html>
