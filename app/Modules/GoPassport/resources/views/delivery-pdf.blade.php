<?php
$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
<table style="width: 100%">
    <tr>
        <td style="width: 20%"></td>
        <td style="width: 60%; text-align: center;">
            <h2 style="font-size: 25px;">Passport Delivery Slip</h2>
            <br>
            <p style="font-size: 15px;">Government of the people's Republic of Bangladesh</p>
            <p style="font-size: 15px;">Ministry of Religious affairs</p>
        </td>
        {{-- QR code --}}
        <td style="width: 20%; text-align: right;">
            <?php
            $qrcode=QrCode::size(100)->generate($qrCodeUrl);
            $code = (string)$qrcode;
            echo substr($code,38);
            ?>
        </td>
    </tr>

    <tr style="padding-top: 20px;">
        <td style="width: 20%; padding-top: 30px;">
            <p><b>Voucher No: {{ $StickerPassportReturn->tracking_no }}</b></p>
        </td>
        {{-- BARCODE --}}
        <td style="width: 60%; text-align: center; padding-top: 30px;">
            <img
                src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($StickerPassportReturn->tracking_no, $generatorPNG::TYPE_CODE_128)) }}"
                alt="barcode"
                width="200"
                height="30"
                style="max-width: 100%; padding: 5px;"
            />
        </td>
        <td style="width: 20%; text-align: right; padding-top: 30px;">
            <p><b>Date: </b>{{ $current_time }}</p>
        </td>
    </tr>
</table>
<br>
<div style="width: 100%;">
    <table style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
        <tr style="background-color: #EEEEEE;">
            <th style="padding: 15px 0px; border: 2px solid #EEEEEE; width: 8%;">SL No.</th>
            <th style="padding: 15px 0px; border: 2px solid #EEEEEE; width: 25%;">Tracking Number</th>
            <th style="padding: 15px 0px; border: 2px solid #EEEEEE; width: 25%;">Passport Number</th>
            <th style="padding: 15px 0px; border: 2px solid #EEEEEE; width: 42%;">Name</th>
        </tr>
        </thead>
        <tbody>
        @if (count($pilgrimData) > 0)
            @foreach ($pilgrimData as $key => $pilgrim)
                <tr style="">
                    <td style="border: 2px solid #EEEEEE; padding: 15px 0px; width: 8%;">{{ $key + 1 }}</td>
                    <td style="border: 2px solid #EEEEEE; padding: 15px 0px; width: 25%;">{{ $pilgrim->pilgrim_tracking_no }}</td>
                    <td style="border: 2px solid #EEEEEE; padding: 15px 0px; width: 25%;">{{ $pilgrim->passport_no }}</td>
                    <td style="border: 2px solid #EEEEEE; padding: 15px 0px; width: 42%;">{{ $pilgrim->name }}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>

<div style="padding-top: 100px; page-break-inside: avoid;">
    <table style="width: 100%">
        <tr>
            <td colspan="2" style="text-align: center; width: 35%; border-top: 2px solid #000; padding-top: 5px;"><b>Name and Signature of Deliveryer</b></td>
            <td style="width: 30%;"></td>
            <td colspan="2" style="text-align: center; width: 35%; border-top: 2px solid #000; padding-top: 5px;"><b>Recipient's Name and Signature</b></td>
        </tr>
    </table>
</div>
</body>
</html>
