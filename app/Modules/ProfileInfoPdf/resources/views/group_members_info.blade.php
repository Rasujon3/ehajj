<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <!--[if gte mso 9]><xml>
    <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
    </o:OfficeDocumentSettings>
    </xml><![endif]-->

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="date=no">
    <meta name="format-detection" content="address=no">
    <meta name="format-detection" content="telephone=no">

    <title>E-HajjBD | Haj Profile Group Member pdf</title>

    <style>

    </style>
</head>

<body class="body" style="position: relative; padding:40px 0 20px !important; margin:0 !important; display:block !important; background:#ffffff; -webkit-text-size-adjust:none; font-family: Arial; color: #000;">

<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
    <tbody>
    <tr>
        <td width="10" style="width:10px;padding: 0;">&nbsp;</td>
        <td align="center" valign="top">
            <table width="800" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="background: #ffffff;width: 800px;">
                <tbody>
                <tr>
                    <td valign="top" style="width: 100%;padding: 10px 20px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                            <tbody>
                            <tr>
                                <td style="text-align: center; background: #ffffff;border: 1px solid rgba(0, 0, 0, 0.12);border-radius: 6px;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                        <tbody>
                                        <tr>
                                            <td style="text-align:center; padding-bottom: 20px;">
                                                <table width="100%" border="1" cellspacing="0" cellpadding="0" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="5" style="font-size: 16px;line-height: 22px;font-weight: bold;text-align:left;">List of group members</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Photo</th>
                                                        <th>PID No</th>
                                                        <th>Name</th>
                                                        {{--                                                        <th>Tracking No</th>--}}
                                                        <th>Phone Number</th>
                                                        <th>Flight Code</th>
                                                        <th>Flight Date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(!empty($profileInfo["group_list"]))
                                                        @foreach($profileInfo["group_list"] as $single_group)
                                                            @php
                                                                $unique_tracking_no = $single_group["tracking_no"];
                                                                if(!empty($single_group["birth_date"])){
                                                                       $single_birth_date = \Carbon\Carbon::parse($single_group["birth_date"]);
                                                                       $single_age = \Carbon\Carbon::now()->diffInYears($single_birth_date);
                                                                       $gender_age = "";
                                                                       if ($single_group["gender"] == "female" && $single_age<18){
                                                                           $gender_age = "(Female, Age ".$single_age.")";
                                                                       }elseif($single_group["gender"] == "female"){
                                                                           $gender_age = "(Female)";
                                                                       }elseif ($single_age<18){
                                                                           $gender_age = "(Age ".$single_age.")";
                                                                       }
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td scope="row"><img src="@if(!empty($single_group["picture"])){{$single_group["picture"]}}@endif" width="30" height="30" alt="Images"></td>
                                                                <td>@if(!empty($single_group["pid"])){{$single_group["pid"]}}@else N/A @endif</td>
                                                                <td>@if(!empty($single_group["full_name_english"])){{$single_group["full_name_english"]}}@else N/A @endif <span style="color: blue; font-weight: bold">{{$gender_age}}</span></td>
                                                                {{--                                                                <td>@if(!empty($single_group["tracking_no"])){{$single_group["tracking_no"]}}@else N/A @endif</td>--}}
                                                                <td>@if(!empty($single_group["mobile"])){{$single_group["mobile"]}}@else N/A @endif</td>
                                                                <td> @if($single_group["flight_code"] != null || $single_group["flight_code"] != '')
                                                                        {{$single_group["flight_code"]}}
                                                                    @else NA
                                                                    @endif
                                                                </td>
                                                                <td> @if($single_group["flight_date"] != null || $single_group["flight_date"] != '')
                                                                        {{$single_group["flight_date"]}}
                                                                    @else NA
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif

                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
        <td width="10" style="width:10px;padding: 0;">&nbsp;</td>
    </tr>
    </tbody>
</table>

</body>
</html>
