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

    <title>E-HajjBD | Haj Profile pdf</title>
</head>

<body class="body" style="position: relative; padding:30px 0 20px !important; margin:0 !important; display:block !important; background:#ffffff; -webkit-text-size-adjust:none; font-family: Arial; color: #000;">

<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
    <tbody>
    <tr>
        <td width="10" style="width:10px;padding: 0;">&nbsp;</td>
        <td align="center" valign="top">
            <table width="1080" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="background: #ffffff;width: 1080px;">
                <tbody>
                <tr>
                    <td valign="top" style="width: 40%;padding: 10px 20px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                            <tbody>
                            <tr>
                                <td style="text-align: center; background: #ffffff;border: 1px solid rgba(0, 0, 0, 0.12);border-radius: 6px;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                        <tbody>
                                        <tr>
                                            <td style="text-align: center;">
                                                <div style="height: 100px;width: 100%;border-radius: 50%;margin-bottom: 10px;"><img src="@if(!empty($profileInfo["identity"]["picture"])){{$profileInfo["identity"]["picture"]}}@endif" width="100" height="100" alt="Images"></div>
                                                <h3 style="font-size: 16px;color: #112219;font-weight: bold;margin: 0 0 5px;padding: 0;text-transform: uppercase;">@if(!empty($profileInfo["basic_info"]["full_name_english"])){{$profileInfo["basic_info"]["full_name_english"]}}@endif</h3>
                                                <?php
                                                if(!empty($profileInfo["basic_info"]["birth_date"])){
                                                    $birth_date = \Carbon\Carbon::parse($profileInfo["basic_info"]["birth_date"]);
                                                    $diffYears = \Carbon\Carbon::now()->diffInYears($birth_date);
                                                }
                                                $count_yes_completion = 0;
                                                $total = 1;
                                                if(!empty($profileInfo["completion"])){
                                                    $total = count($profileInfo["completion"])-1;
                                                    foreach ($profileInfo["completion"] as $key=> $value){
                                                        if($key == 'RegistrationStatus'){
                                                            if($profileInfo["basic_info"]["ref_pilgrim_id"] <= 0){
                                                                continue;
                                                            }
                                                        }
                                                        if($key == 'GuideAssing'){
                                                            if($profileInfo["basic_info"]["pilgrim_type_id"] == 6){
                                                                continue;
                                                            }
                                                        }
                                                        if($value == "Yes"){
                                                            ++$count_yes_completion;
                                                        }
                                                    }
                                                }
                                                if($profileInfo["basic_info"]["ref_pilgrim_id"] <= 0){
                                                    --$total;
                                                }
                                                if($profileInfo["basic_info"]["pilgrim_type_id"] == 6 ){
                                                    --$total;
                                                }
                                                $completion_percent = ceil(($count_yes_completion/$total)*100);
                                                ?>
                                                <span style="display: inline-block;color: black;font-size: 14px;">@if(!empty($profileInfo["basic_info"]["birth_date"])){{$diffYears}}@else N/A @endif Years</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left; padding-bottom: 20px;">
                                                <table width="100%" border="1" cellspacing="0" cellpadding="0" style="width: 100%;">
                                                    <tbody>
                                                    <tr>
                                                        <td style="color: black">PID:</td>
                                                        <td>@if(!empty($profileInfo["identity"]["pid"])){{$profileInfo["identity"]["pid"]}}@else N/A @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Passport No:</td>
                                                        <td>@if(!empty($profileInfo["identity"]["passport_no"])){{$profileInfo["identity"]["passport_no"]}}@else N/A @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Management:</td>
                                                        <td>@if(!empty($profileInfo["identity"]["is_govt"])){{$profileInfo["identity"]["is_govt"]}}@else N/A @endif</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="text-align:left; padding-bottom: 20px;">
                                                <table width="100%" border="1" cellspacing="0" cellpadding="0" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="2" style="font-size: 16px;line-height: 22px;font-weight: bold;">Completion: {{$completion_percent}}%</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(\Illuminate\Support\Facades\Auth::user()->working_user_type != 'Guide')
                                                    <tr>
                                                        <td>REGISTRATION</td>
                                                        <td style="width: 30%;"> @if(!empty($profileInfo["completion"]["RegistrationStatus"]) && $profileInfo["completion"]["RegistrationStatus"] =="Yes") Complete @else Pending @endif </td>
                                                    </tr>
                                                    @endif
                                                    <tr>
                                                        <td>PILGRIM ID</td>
                                                        <td>@if(!empty($profileInfo["completion"]["PIDStatus"]) && $profileInfo["completion"]["PIDStatus"] =="Yes") Complete @else Pending @endif </td>
                                                    </tr>
                                                    <tr>
                                                        <td>BIOMETRIC</td>
                                                        <td>@if(!empty($profileInfo["completion"]["BiomatricStatus"]) && $profileInfo["completion"]["BiomatricStatus"] =="Yes") Complete @else Pending @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td>PASSPORT RECEIVE</td>
                                                        <td>@if(!empty($profileInfo["completion"]["PassportReceived"]) && $profileInfo["completion"]["PassportReceived"] =="Yes") Complete @else Pending @endif </td>
                                                    </tr>
                                                    <tr>
                                                        <td>E-HAJJ LODGMENT</td>
                                                        <td>@if(!empty($profileInfo["completion"]["LodgementStatus"]) && $profileInfo["completion"]["LodgementStatus"] =="Yes") Complete @else Pending @endif </td>
                                                    </tr>
                                                    <tr>
                                                        <td>VISA</td>
                                                        <td>@if(!empty($profileInfo["completion"]["VisaStatus"]) && $profileInfo["completion"]["VisaStatus"] =="Yes") Complete @else Pending @endif </td>
                                                    </tr>
                                                    <tr>
                                                        <td>VACCINATION</td>
                                                        <td>@if(!empty($profileInfo["completion"]["VaccinStatus"]) && $profileInfo["completion"]["VaccinStatus"] =="Yes") Complete @else Pending @endif </td>
                                                    </tr>
                                                    @if(\Illuminate\Support\Facades\Auth::user()->working_user_type != 'Guide')
                                                    <tr>
                                                        <td>GUIDE ASSIGN</td>
                                                        <td>@if(!empty($profileInfo["completion"]["GuideAssing"]) && $profileInfo["completion"]["GuideAssing"] =="Yes") Complete @else Pending @endif </td>
                                                    </tr>
                                                    @endif
                                                    <tr>
                                                        <td>ID CARD PRINT</td>
                                                        <td>@if(!empty($profileInfo["completion"]["idCardStatus"]) && $profileInfo["completion"]["idCardStatus"] =="Yes") Complete @else Pending @endif </td>
                                                    </tr>
                                                    <tr>
                                                        <td>FLIGHT</td>
                                                        <td>@if(!empty($profileInfo["completion"]["HajFlightStatus"]) && $profileInfo["completion"]["HajFlightStatus"] =="Yes") Complete @else Pending @endif </td>
                                                    </tr>
                                                    <tr>
                                                        <td>HOUSE</td>
                                                        <td>@if(!empty($profileInfo["completion"]["HouseStatus"]) && $profileInfo["completion"]["HouseStatus"] =="Yes") Complete @else Pending @endif </td>
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

                    <td valign="top" style="width: 60%;padding: 10px 20px;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                            <tbody>
                            <tr>
                                <td style="text-align: center; background: #ffffff;border: 1px solid rgba(0, 0, 0, 0.12);border-radius: 6px;">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                        <tbody>
                                        <tr>
                                            <td style="text-align:left;padding-bottom: 20px;">
                                                <table width="100%" border="1" cellspacing="0" cellpadding="0" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="2" style="font-size: 16px;line-height: 22px;font-weight: bold;">Basic Information of Pilgrim</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td style="color: black;width:40%;">Father’s Name:</td>
                                                        <td>@if(!empty($profileInfo["basic_info"]["father_name_english"])){{$profileInfo["basic_info"]["father_name_english"]}}@else N/A @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Mother Name:</td>
                                                        <td>@if(!empty($profileInfo["basic_info"]["mother_name_english"])){{$profileInfo["basic_info"]["mother_name_english"]}}@else N/A @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Date Of Birth:</td>
                                                        <td>@if(!empty($profileInfo["basic_info"]["birth_date"])){{\Carbon\Carbon::parse($profileInfo["basic_info"]["birth_date"])->format("d-M-Y")}}@else N/A @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">National ID:</td>
                                                        <td>@if(!empty($profileInfo["basic_info"]["national_id"])){{$profileInfo["basic_info"]["national_id"]}}@else N/A @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Gender:</td>
                                                        <td>@if(!empty($profileInfo["basic_info"]["gender"])){{$profileInfo["basic_info"]["gender"]}}@else N/A @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Phone Number:</td>
                                                        <td>@if(!empty($profileInfo["basic_info"]["mobile"])){{$profileInfo["basic_info"]["mobile"]}}@else N/A @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">KSA Phone Number:</td>
                                                        <td>@if(!empty($profileInfo["basic_info"]["ksa_mobile_no"])){{$profileInfo["basic_info"]["ksa_mobile_no"]}}@else N/A @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Email Address:</td>
                                                        <td>@if(!empty($profileInfo["basic_info"]["email"])){{$profileInfo["basic_info"]["email"]}}@endif</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>

                                        @if($archived_pilgrim == 'true')
                                        <tr>
                                            <td style="text-align:left;padding-bottom: 20px;">
                                                <table width="100%" border="1" cellspacing="0" cellpadding="0" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="2" style="font-size: 16px;line-height: 22px;font-weight: bold;">@if($profileInfo["basic_info"]["pilgrim_type_id"] == 6)
                                                                Maktab Information
                                                            @else Guide and Maktab Information
                                                            @endif</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if($profileInfo["basic_info"]["pilgrim_type_id"] != 6)
                                                        <tr>
                                                            <td colspan="2" style="font-size:16px;font-weight:bold;">Guide</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="color: black;width:40%;">Name:</td>
                                                            <td>@if(!empty($profileInfo["guide"]["guide_name"])){{$profileInfo["guide"]["guide_name"]}}@else N/A @endif</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="color: black">Bd Phone Number:</td>
                                                            <td>@if(!empty($profileInfo["guide"]["guide_mobile"])){{$profileInfo["guide"]["guide_mobile"]}}@else N/A @endif</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="color: black">Ksa Mobile:</td>
                                                            <td>@if(!empty($profileInfo["guide"]["ksa_mobile_no"])){{$profileInfo["guide"]["ksa_mobile_no"]}}@else N/A @endif</td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td colspan="2" style="font-size:16px;font-weight:bold;">Maktab</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Name:</td>
                                                        <td>@if(!empty($profileInfo["muallem"]["muallem_name"])){{$profileInfo["muallem"]["muallem_name"]}}@else N/A @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">KSA Mobile :</td>
                                                        <td>@if(!empty($profileInfo["muallem"]["muallem_mobile"])){{$profileInfo["muallem"]["muallem_mobile"]}}@else N/A @endif</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Maktab No:</td>
                                                        <td>@if(!empty($profileInfo["muallem"]["muallem_no"])){{$profileInfo["muallem"]["muallem_no"]}}@else N/A @endif</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="text-align:left;padding-bottom: 20px;">
                                                <table width="100%" border="1" cellspacing="0" cellpadding="0" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="2" style="font-size: 16px;line-height: 22px;font-weight: bold;">Visa Information</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td style="color: black;width:40%;">MOFA Application Number:
                                                        </td>
                                                        <td>@if(!empty($profileInfo["visa"]["mofa_id"]))
                                                                {{$profileInfo["visa"]["mofa_id"]}}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Visa Status:</td>
                                                        <td>
                                                            @if(!empty($profileInfo["visa"]["status"]))
                                                                {{$profileInfo["visa"]["status"]}}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Name:</td>
                                                        <td>
                                                            @if(!empty($profileInfo["visa"]["name"]))
                                                                {{$profileInfo["visa"]["name"]}}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="text-align:left;padding-bottom: 20px;">
                                                <table width="100%" border="1" cellspacing="0" cellpadding="0" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="2" style="font-size: 16px;line-height: 22px;font-weight: bold;">Flight Information</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if($profileInfo['identity']['is_publish_flight'])
                                                    <tr>
                                                        <td colspan="2">Haj Flight</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black;width:40%;">Flight Date:</td>
                                                        <td>@if(isset($profileInfo["haj_flight"]["data_found"]) && $profileInfo["haj_flight"]["data_found"] == true)
                                                                {{$profileInfo["haj_flight"]["flight_date"]}}
                                                            @else N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Flight Code:</td>
                                                        <td>@if(isset($profileInfo["haj_flight"]["data_found"]) && $profileInfo["haj_flight"]["data_found"] == true)
                                                                {{$profileInfo["haj_flight"]["flight_code"]}}
                                                            @else N/A
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td style="color: black">Remains:</td>
                                                        <td>@if(isset($profileInfo["haj_flight"]["data_found"]) && $profileInfo["haj_flight"]["data_found"] == true)
                                                                {{$profileInfo["haj_flight"]["remaining_days"]}}
                                                            @else N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">Return Flight</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black;width:40%;">Flight Date:</td>
                                                        <td>@if(isset($profileInfo["return_flight"]["data_found"]) && $profileInfo["return_flight"]["data_found"] == true)
                                                                {{$profileInfo["return_flight"]["flight_date"]}}
                                                            @else N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Flight Code:</td>
                                                        <td>@if(isset($profileInfo["return_flight"]["data_found"]) && $profileInfo["return_flight"]["data_found"] == true)
                                                                {{$profileInfo["return_flight"]["flight_code"]}}
                                                            @else N/A
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td style="color: black">Remains:</td>
                                                        <td>@if(isset($profileInfo["return_flight"]["data_found"]) && $profileInfo["return_flight"]["data_found"] == true)
                                                                {{$profileInfo["return_flight"]["remaining_days"]}}
                                                            @else N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @else
                                                        <tr>
                                                            <td colspan="2" style="text-align:center;">Flight not published yet!!</td>
                                                        </tr>
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="text-align:left;padding-bottom: 20px;">
                                                <table width="100%" border="1" cellspacing="0" cellpadding="0" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="2" style="font-size: 16px;line-height: 22px;font-weight: bold;">House Information</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if($profileInfo['identity']['is_publish_building'])
                                                    <tr>
                                                        <td style="color: black;width:40%;">Makkah House Name:</td>
                                                        <td>@if(isset($profileInfo["house"]["is_publish_building"]) && $profileInfo["house"]["is_publish_building"] == true)
                                                                {{$profileInfo["house"]["makka_house_name"]}}
                                                            @else N/A
                                                            @endif</td>
                                                    </tr>
                                                    @else
                                                        <tr>
                                                            <td colspan="2" style="text-align:center;">House  not published yet!!</td>
                                                        </tr>
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="text-align:left;padding-bottom: 20px;">
                                                <table width="100%" border="1" cellspacing="0" cellpadding="0" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="2" style="font-size: 16px;line-height: 22px;font-weight: bold;">Reporting Information</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td style="color: black;width:40%;">Reporting Date:</td>
                                                        <td>@if(isset($profileInfo["reporting"]["date"]))
                                                                {{$profileInfo["reporting"]["date"]}}
                                                            @else N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="color: black">Location:</td>
                                                        <td>Haj Office, Dhaka</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
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
        <td width="10" style="width:10px;padding: 0;">&nbsp;</td>
    </tr>
    </tbody>
</table>

</body>
</html>
