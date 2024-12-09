<?php
use Illuminate\Support\Facades\Auth;
$user_type = Auth::user()->user_type;
?>
@extends('layouts.admin')
@section('header-resources')
    @include('partials.datatable-css')

    <style>
        .select2-container--default .select2-selection--single {
        height: 34px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
    }
    </style>
    <link rel="stylesheet" href="{{ asset("assets/plugins/select2/css/select2.min.css") }}">
@endsection
@section('content')
    @include('partials.messages')
    <div class="hajj-profile-dashboard">
        <div class="container-fluid">
            <div class="row">
                @if($user_type == '21x101')
                    <div class="col-lg-4 col-xl-3">
                        <div class="dash-profile-menu white-box white-box-pad">
                            <div class="dash-navbar">
                                <div class="dash-menu-item active">
                                    <a class="dash-menu-text" href="{{ url('/my-desk') }}"><i
                                            class="fas fa-th-large dash-menu-icon"></i> ড্যাশবোর্ড</a>
                                </div>
                                <div class="dash-menu-item">
                                    <a class="dash-menu-text" href="{{ url("/process/list") }}">
                                        <i class="fas fa-grip-vertical dash-menu-icon"></i> সেবা সমূহ
                                    </a>
                                </div>
                            </div>


                        </div>
                    </div>
                @endif
                @if($user_type == '21x101')
                <div class="col-lg-8 col-xl-3">
                @else
                <div class="col-lg-9 col-xl-4">
                @endif
                    <div class="dash-profile-left-info">
                        <div class="ehajj-profile-info white-box white-box-pad">
                            <div class="profile-info-wrap">
                                <div class="ehajj-profile-info-head">
                                    <div class="dash-profile-pic">
                                        <img
                                            src="@if(!empty($profileInfo["identity"]["picture"])){{$profileInfo["identity"]["picture"]}}@endif"
                                            alt="Images">
                                    </div>
                                    <h3>@if(!empty($profileInfo["basic_info"]["full_name_english"]))
                                            {{$profileInfo["basic_info"]["full_name_english"]}}
                                        @endif</h3>
                                    <?php
                                    if (!empty($profileInfo["basic_info"]["birth_date"])) {
                                        $birth_date = \Carbon\Carbon::parse($profileInfo["basic_info"]["birth_date"]);
                                        $diffYears = \Carbon\Carbon::now()->diffInYears($birth_date);
                                    }
                                    $count_yes_completion = 0;
                                    $total = 1;
                                    if (!empty($profileInfo["completion"])) {
                                        $total = count($profileInfo["completion"]) - 1;
                                        foreach ($profileInfo["completion"] as $key => $value) {
                                            if ($key == 'RegistrationStatus') {
                                                if ($profileInfo["basic_info"]["ref_pilgrim_id"] <= 0) {
                                                    continue;
                                                }
                                            }
                                            if ($key == 'GuideAssing') {
                                                if ($profileInfo["basic_info"]["pilgrim_type_id"] == 6) {
                                                    continue;
                                                }
                                            }
                                            if ($value == "Yes") {
                                                ++$count_yes_completion;
                                            }
                                        }
                                    }
                                    if (!empty($profileInfo["basic_info"]["ref_pilgrim_id"]) && $profileInfo["basic_info"]["ref_pilgrim_id"] <= 0) {
                                        --$total;
                                    }
                                    if (!empty($profileInfo["basic_info"]["pilgrim_type_id"]) && $profileInfo["basic_info"]["pilgrim_type_id"] == 6) {
                                        --$total;
                                    }
                                    $completion_percent = ceil(($count_yes_completion / $total) * 100);
                                    ?>
                                    <span class="ehajj-profile-year">@if(!empty($profileInfo["basic_info"]["birth_date"]))
                                            {{$diffYears}}
                                        @else
                                            N/A
                                        @endif Years</span>
                                </div>
                                <div class="profile-info-lists">
                                    <div class="profile-info-item"><span
                                            class="info-label">PID</span>@if(!empty($profileInfo["identity"]["pid"]))
                                            {{$profileInfo["identity"]["pid"]}}
                                        @else
                                            N/A
                                        @endif</div>
                                    @if($is_owner ==1)
                                        <div class="profile-info-item"><span
                                                class="info-label">Tracking ID</span>@if(!empty($profileInfo["identity"]["tracking_no"]))
                                                {{$profileInfo["identity"]["tracking_no"]}}
                                            @else
                                                N/A
                                            @endif</div>
                                    @endif
                                    <div class="profile-info-item"><span
                                            class="info-label">Passport No</span>@if(!empty($profileInfo["identity"]["passport_no"]))
                                            {{$profileInfo["identity"]["passport_no"]}}
                                        @else
                                            N/A
                                        @endif</div>
                                    <div class="profile-info-item"><span
                                            class="info-label">Management</span>@if(!empty($profileInfo["identity"]["is_govt"]))
                                            {{$profileInfo["identity"]["is_govt"]}}
                                        @else
                                            N/A
                                        @endif</div>
                                </div>
                                <div>
                                    <a class="btn" title="Print Profile Info" style="padding-left: 0px !important;"
                                       target="_blank"
                                       href="@if(!empty($profileInfo["identity"]["tracking_no"]))/profile-pdf-generate/{{\App\Libraries\Encryption::encodeId($profileInfo["identity"]["tracking_no"])}}@endif"><i
                                            class="fa fa-print" aria-hidden="true" style="font-size: 25px;"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="ehajj-profile-steps white-box white-box-pad">
                            <div class="profile-steps-wrap">
                                <div class="profile-steps-head">
                                    <div class="progress-title">
                                        <h4>Completion</h4>
                                        <span class="percentage-text">{{$completion_percent}}%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                             style="width: {{$completion_percent}}%"
                                             aria-valuenow="{{$completion_percent}}" aria-valuemin="0"
                                             aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="profile-steps-items">
                                    <ul>
                                        @if(!empty($profileInfo["basic_info"]["ref_pilgrim_id"]) && $profileInfo["basic_info"]["ref_pilgrim_id"] > 0)
                                            <li @if(!empty($profileInfo["completion"]["RegistrationStatus"]) && $profileInfo["completion"]["RegistrationStatus"] =="Yes") class="step-done" @endif>
                                                <span class="steps-icon"><img
                                                        src="{{ asset('assets/custom/images/step-icon-01.svg') }}"
                                                        alt=""></span> <span class="steps-text">Registration</span>
                                            </li>
                                        @else
                                            <li class="step-skiped">
                                                <span class="steps-icon"><img
                                                        src="{{ asset('assets/custom/images/step-icon-01.svg') }}"
                                                        alt=""></span> <span class="steps-text">Registration</span>
                                            </li>
                                        @endif
                                        <li @if(!empty($profileInfo["completion"]["PIDStatus"]) && $profileInfo["completion"]["PIDStatus"] =="Yes") class="step-done" @endif>
                                            <span class="steps-icon"><img
                                                    src="{{ asset('assets/custom/images/step-icon-02.svg') }}"
                                                    alt=""></span> <span class="steps-text">Pilgrim ID</span>
                                        </li>
                                        <li @if(!empty($profileInfo["completion"]["BiomatricStatus"]) && $profileInfo["completion"]["BiomatricStatus"] =="Yes") class="step-done" @endif>
                                            <span class="steps-icon"><img
                                                    src="{{ asset('assets/custom/images/step-icon-03.svg') }}"
                                                    alt=""></span> <span class="steps-text">Biometric</span>
                                        </li>
                                        <li @if(!empty($profileInfo["completion"]["PassportReceived"]) && $profileInfo["completion"]["PassportReceived"] =="Yes") class="step-done" @endif>
                                            <span class="steps-icon"><img
                                                    src="{{ asset('assets/custom/images/step-icon-04.svg') }}"
                                                    alt=""></span> <span class="steps-text">Passport Receive</span>
                                        </li>
                                        <li @if(!empty($profileInfo["completion"]["LodgementStatus"]) && $profileInfo["completion"]["LodgementStatus"] =="Yes") class="step-done" @endif>

                                            <span class="steps-icon"><img
                                                    src="{{ asset('assets/custom/images/step-icon-05.svg') }}"
                                                    alt=""></span> <span class="steps-text">E-hajj Lodgment</span>
                                        </li>
                                        <li @if(!empty($profileInfo["completion"]["VisaStatus"]) && $profileInfo["completion"]["VisaStatus"] =="Yes") class="step-done" @endif>
                                            <span class="steps-icon"><img
                                                    src="{{ asset('assets/custom/images/step-icon-06.svg') }}"
                                                    alt=""></span> <span class="steps-text">Visa</span>
                                        </li>
                                        <li @if(!empty($profileInfo["completion"]["VaccinStatus"]) && $profileInfo["completion"]["VaccinStatus"] =="Yes") class="step-done" @endif>
                                            <span class="steps-icon"><img
                                                    src="{{ asset('assets/custom/images/step-icon-07.svg') }}"
                                                    alt=""></span> <span class="steps-text">Vaccination</span>
                                        </li>
                                        @if(!empty($profileInfo["basic_info"]["pilgrim_type_id"]) && $profileInfo["basic_info"]["pilgrim_type_id"] == 6)
                                            <li class="step-skiped">
                                                <span class="steps-icon"><img
                                                        src="{{ asset('assets/custom/images/step-icon-08.svg') }}"
                                                        alt=""></span> <span class="steps-text">Guide Assign</span>
                                            </li>
                                        @else
                                            <li @if(!empty($profileInfo["completion"]["GuideAssing"]) && $profileInfo["completion"]["GuideAssing"] =="Yes") class="step-done" @endif>
                                                <span class="steps-icon"><img
                                                        src="{{ asset('assets/custom/images/step-icon-08.svg') }}"
                                                        alt=""></span> <span class="steps-text">Guide Assign</span>
                                            </li>
                                        @endif
                                        <li @if(!empty($profileInfo["completion"]["idCardStatus"]) && $profileInfo["completion"]["idCardStatus"] =="Yes") class="step-done" @endif>
                                            <span class="steps-icon"><img
                                                    src="{{ asset('assets/custom/images/step-icon-09.svg') }}"
                                                    alt=""></span> <span class="steps-text">ID Card Print</span>
                                        </li>
                                        <li @if(!empty($profileInfo["completion"]["HajFlightStatus"]) && $profileInfo["completion"]["HajFlightStatus"] =="Yes") class="step-done" @endif>
                                            <span class="steps-icon"><img
                                                    src="{{ asset('assets/custom/images/step-icon-10.svg') }}"
                                                    alt=""></span> <span class="steps-text">Flight</span>
                                        </li>
                                        <li @if(!empty($profileInfo["completion"]["HouseStatus"]) && $profileInfo["completion"]["HouseStatus"] =="Yes") class="step-done" @endif>
                                            <span class="steps-icon"><img
                                                    src="{{ asset('assets/custom/images/step-icon-11.svg') }}"
                                                    alt=""></span> <span class="steps-text">House</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="ehajj-profile-certificate white-box white-box-pad">
                            <div class="dash-menu-certificate">

                                @if(!empty($profileInfo["basic_info"]) && $profileInfo["basic_info"]["pilgrim_type_id"] == 1)
                                    @php
                                        $url = '';
                                        $target = '';
                                        if(isset($profileInfo["certificate"]["pre_reg_crt"]) && $profileInfo["certificate"]["pre_reg_crt"] != ''){
                                            $url = $profileInfo["certificate"]["pre_reg_crt"];
                                            $target = 'target="_blank"';
                                        }
                                    @endphp
                                    <div class="certificate-menu-item">
                                        <a {{$target}} href="{{$url}}"
                                           class="pdf-download @if(empty($url)) button-disable @endif">
                                            <span class="pdf-download-icon"></span>
                                            <span class="pdf-menu-text">Pre-Registration Certificate</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($profileInfo["basic_info"]) && $profileInfo["basic_info"]["pilgrim_type_id"] == 1)

                                    @php
                                        $url = '';
                                        $target = '';
                                        if(isset($profileInfo["certificate"]["reg_crt"]) && $profileInfo["certificate"]["reg_crt"] != ''){
                                            $url = $profileInfo["certificate"]["reg_crt"];
                                            $target = 'target="_blank"';
                                        }
                                    @endphp

                                    <div class="certificate-menu-item cmi-btn-green">
                                        <a {{$target}} href="{{$url}}"
                                           class="pdf-download @if(empty($url)) button-disable @endif">
                                            <span class="pdf-download-icon"></span>
                                            <span class="pdf-menu-text">Registration Certificate</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($profileInfo["basic_info"]) && in_array($profileInfo["basic_info"]["pilgrim_type_id"], [1,4,6]))
                                    @php
                                        $url = '';
                                        $target = '';
                                        if(isset($profileInfo["certificate"]["permission_cert"]) && $profileInfo["certificate"]["permission_cert"] != ''){
                                            $url = $profileInfo["certificate"]["permission_cert"];
                                            $target = 'target="_blank"';
                                        }
                                    @endphp
                                    <div class="certificate-menu-item cmi-btn-cream">
                                        <a {{$target}} href="{{$url}}"
                                           class="pdf-download @if(empty($url) || $profileInfo['identity']['is_publish_flight'] == 0) button-disable @endif">
                                            <span class="pdf-download-icon"></span>
                                            <span class="pdf-menu-text">Permission Letter</span>
                                        </a>
                                    </div>
                                @endif

                                <div class="certificate-menu-item cmi-btn-lightblue">
                                    @php
                                        $url = '';
                                        $target = '';
                                        if(isset($profileInfo["certificate"]["health_cert"]) && $profileInfo["certificate"]["health_cert"] != ''){
                                            $url = $profileInfo["certificate"]["health_cert"];
                                            $target = 'target="_blank"';
                                        }
                                    @endphp
                                    <a {{$target}} href="{{$url}}"
                                       class="pdf-download @if(empty($url)) button-disable @endif">
                                        <span class="pdf-download-icon"></span>
                                        <span class="pdf-menu-text">Health Certificate</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($user_type == '21x101')
                <div class="col-lg-12 col-xl-6">
                @else
                <div class="col-lg-9 col-xl-8">
                @endif
                    <div class="dash-profile-info-content">
                        <div class="dash-section-card card-color-1">
                            <div class="section-card-title">
                                <h3>Basic Information of Pilgrim </h3>
                            </div>
                            <div class="section-card-container">
                                <div class="row">
                                    @if($is_owner ==1)
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Father’s name</span> @if(!empty($profileInfo["basic_info"]["father_name_english"]))
                                                    {{$profileInfo["basic_info"]["father_name_english"]}}
                                                @else
                                                    N/A
                                                @endif</div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Mother Name</span> @if(!empty($profileInfo["basic_info"]["mother_name_english"]))
                                                    {{$profileInfo["basic_info"]["mother_name_english"]}}
                                                @else
                                                    N/A
                                                @endif</div>
                                        </div>
                                    @endif
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span
                                                class="info-label">national ID</span> @if(!empty($profileInfo["basic_info"]["national_id"]))
                                                {{$profileInfo["basic_info"]["national_id"]}}
                                            @else
                                                N/A
                                            @endif</div>
                                    </div>
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span
                                                class="info-label">Date of Birth</span>@if(!empty($profileInfo["basic_info"]["birth_date"]))
                                                {{\Carbon\Carbon::parse($profileInfo["basic_info"]["birth_date"])->format("d-M-Y")}}
                                            @else
                                                N/A
                                            @endif</div>
                                    </div>
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span
                                                class="info-label">Gender</span> @if(!empty($profileInfo["basic_info"]["gender"]))
                                                {{$profileInfo["basic_info"]["gender"]}}
                                            @else
                                                N/A
                                            @endif</div>
                                    </div>
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span
                                                class="info-label">phone Number</span> @if(!empty($profileInfo["basic_info"]["mobile"]))
                                                {{$profileInfo["basic_info"]["mobile"]}}
                                            @else
                                                N/A
                                            @endif</div>
                                    </div>
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span class="info-label">KSA Phone Number</span>
                                            <span class="current_ksa_no"> @if(!empty($profileInfo["basic_info"]["ksa_mobile_no"]))
                                                    {{$profileInfo["basic_info"]["ksa_mobile_no"]}}
                                                @else
                                                    N/A
                                                @endif</span>
                                            @if($is_owner ==1)
                                                <span class="fa fa-edit ksa_edit" style="cursor:pointer;"></span>
                                                <div class="update_ksa_mobile_panel hidden">
                                                    <input type="text" id="ksa_mobile_no" name="ksa_mobile_no"
                                                           value="@if(!empty($profileInfo["basic_info"]["ksa_mobile_no"])){{$profileInfo["basic_info"]["ksa_mobile_no"]}}@endif"
                                                           class="input-sm"/>
                                                    <button class="btn btn-sm btn-default update_ksa_number text-right"
                                                            type="button"
                                                            tracking_no="@if(!empty($profileInfo["identity"])){{$profileInfo["identity"]["tracking_no"]}}@endif">
                                                        Update
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span class="info-label">Email</span>
                                            <span class="current_email" style="text-transform: lowercase">
                                                @if(!empty($profileInfo["basic_info"]["email"]))
                                                    {{$profileInfo["basic_info"]["email"]}}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                            @if($is_owner ==1)
                                                <span class="fa fa-edit email_edit" style="cursor:pointer;"></span>
                                                <div class="update_email_panel hidden">
                                                    <input type="text" id="email" name="email"
                                                           value="@if(!empty($profileInfo["basic_info"]["email"])){{$profileInfo["basic_info"]["email"]}}@endif"
                                                           class="input-sm"/>
                                                    <button class="btn btn-sm btn-default update_email text-right"
                                                            type="button"
                                                            tracking_no="@if(!empty($profileInfo["identity"])){{$profileInfo["identity"]["tracking_no"]}}@endif">
                                                        Update
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (!empty($pilgrim_bank_info))
                            <div class="dash-section-card card-color-5">
                                <div class="section-card-title">
                                    <h3>Refundable Account Information</h3>
                                </div>
                                <div class="section-card-container">
                                    <div class="row">
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Payment Receive Type</span> @if(!empty($pilgrim_bank_info["owner_type"]))
                                                    {{$pilgrim_bank_info["owner_type"]}}
                                                @else
                                                    N/A
                                                @endif</div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Account Holder Name</span> @if(!empty($pilgrim_bank_info["account_name"]))
                                                    {{$pilgrim_bank_info["account_name"]}}
                                                @else
                                                    N/A
                                                @endif</div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Bank Name</span> @if(!empty($pilgrim_bank_info["bank_name"]))
                                                    {{$pilgrim_bank_info["bank_name"]}}
                                                @else
                                                    N/A
                                                @endif</div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Account Number</span> @if(!empty($pilgrim_bank_info["account_number"]))
                                                    {{$pilgrim_bank_info["account_number"]}}
                                                @else
                                                    N/A
                                                @endif</div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">District</span>@if(!empty($pilgrim_bank_info["dist_name"]))
                                                    {{$pilgrim_bank_info["dist_name"]}}
                                                @else
                                                    N/A
                                                @endif</div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Bank Branch Name</span> @if(!empty($pilgrim_bank_info["branch_Name"]))
                                                    {{$pilgrim_bank_info["branch_Name"]}}
                                                @else
                                                    N/A
                                                @endif</div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item">
                                                <span class="info-label">Account Change Request</span>
                                                @if (!empty($pilgrim_bank_info['existingProcess']))
                                                    <b style="color: red">In Process </b><span class="fa fa-edit" id="bankAccModalBtn" style="cursor: pointer; color: red"></span>
                                                @else
                                                    <span>N/A </span><span class="fa fa-edit" id="bankAccModalBtn" style="cursor: pointer"></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif


                        <div class="dash-section-card card-color-2">
                            <div class="section-card-title">
                                @if(!empty($profileInfo["basic_info"]) && $profileInfo["basic_info"]["pilgrim_type_id"] == 6)
                                    <h3>Maktab Information</h3>
                                @else
                                    <h3>Guide and Maktab Information</h3>
                                @endif
                            </div>
                            <div class="section-card-container">
                                @if(!empty($profileInfo["basic_info"]) && $profileInfo["basic_info"]["pilgrim_type_id"] != 6)
                                    <h3>Guide</h3>
                                    <div class="row">
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Name</span>@if(!empty($profileInfo["guide"]["guide_name"]))
                                                    {{$profileInfo["guide"]["guide_name"]}}
                                                @else
                                                    N/A
                                                @endif</div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Bd Phone Number</span>@if(!empty($profileInfo["guide"]["guide_mobile"]))
                                                    {{$profileInfo["guide"]["guide_mobile"]}}
                                                @else
                                                    N/A
                                                @endif</div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Ksa Mobile</span>@if(!empty($profileInfo["guide"]["ksa_mobile_no"]))
                                                    {{$profileInfo["guide"]["ksa_mobile_no"]}}
                                                @else
                                                    N/A
                                                @endif</div>
                                        </div>

                                        {{--                                        @if( Auth::user()->working_user_type != 'Pilgrim' && $profileInfo['identity']['is_publish_flight'])--}}
                                        {{--                                            <div class="col-lg-4 dash-col">--}}
                                        {{--                                                <div class="profile-info-item"><span class="info-label">Muallem  No </span>@if(!empty($profileInfo["guide"]["muallem_no"])){{$profileInfo["guide"]["muallem_no"]}}@else N/A @endif</div>--}}
                                        {{--                                            </div>--}}
                                        {{--                                            <div class="col-lg-4 dash-col">--}}
                                        {{--                                                <div class="profile-info-item"><span class="info-label">Flight Date</span>@if(!empty($profileInfo["haj_flight"]["flight_base_time"])){{\Carbon\Carbon::parse($profileInfo["haj_flight"]["flight_base_time"])->format("d-M-Y, H.i A")}}@else N/A @endif</div>--}}
                                        {{--                                            </div>--}}
                                        {{--                                            <div class="col-lg-4 dash-col">--}}
                                        {{--                                                <div class="profile-info-item"><span class="info-label">Flight Code</span>@if(!empty($profileInfo["haj_flight"]["flight_code"])){{$profileInfo["haj_flight"]["flight_code"]}}@else N/A @endif</div>--}}
                                        {{--                                            </div>--}}
                                        {{--                                            <div class="col-lg-4 dash-col">--}}
                                        {{--                                                <div class="profile-info-item"><span class="info-label">City</span>@if(!empty($profileInfo["house"]["city"])){{$profileInfo["house"]["city"]}}@else N/A @endif</div>--}}
                                        {{--                                            </div>--}}
                                        {{--                                            <div class="col-lg-4 dash-col">--}}
                                        {{--                                                <div class="profile-info-item"><span class="info-label">Makkah House Name</span>@if(!empty($profileInfo["house"]["makka_house_name"])){{$profileInfo["house"]["makka_house_name"]}}@else N/A @endif</div>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        @endif--}}
                                    </div>
                                    <hr>
                                @endif
                                <h3 class="pt-3">Maktab</h3>
                                <div class="row">
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span
                                                class="info-label">Name</span>@if(!empty($profileInfo["muallem"]["muallem_name"]))
                                                {{$profileInfo["muallem"]["muallem_name"]}}
                                            @else
                                                N/A
                                            @endif</div>
                                    </div>
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span
                                                class="info-label">Ksa Mobile</span>@if(!empty($profileInfo["muallem"]["muallem_mobile"]))
                                                {{$profileInfo["muallem"]["muallem_mobile"]}}
                                            @else
                                                N/A
                                            @endif</div>
                                    </div>
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span
                                                class="info-label">Maktab  No </span>@if(!empty($profileInfo["muallem"]["muallem_no"]))
                                                {{$profileInfo["muallem"]["muallem_no"]}}
                                            @else
                                                N/A
                                            @endif</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="dash-section-card card-color-3">
                            <div class="section-card-title">
                                <h3>Visa Information</h3>
                            </div>
                            <div class="section-card-container">
                                <div class="row">
                                    <div class="col-lg-6 dash-col">
                                        <div class="profile-info-item"><span
                                                class="info-label">MOFA Application Number</span>
                                            @if(!empty($profileInfo["visa"]["mofa_id"]))
                                                {{$profileInfo["visa"]["mofa_id"]}}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span
                                                class="info-label">Visa Status</span>
                                            @if(!empty($profileInfo["visa"]["status"]))
                                                {{$profileInfo["visa"]["status"]}}
                                            @else
                                                N/A
                                            @endif</div>
                                    </div>
                                    <div class="col-lg-2 dash-col">
                                        <div class="profile-info-item"><span
                                                class="info-label">Name</span>
                                            @if(!empty($profileInfo["visa"]["name"]))
                                                {{$profileInfo["visa"]["name"]}}
                                            @else
                                                N/A
                                            @endif</div>
                                    </div>
                                    @if(!empty($profileInfo) && !empty($profileInfo["visa"]["e_visa_link"]))
                                        <div class="col-lg-6 dash-col">
                                            <div class="profile-info-item"><span class="info-label">E Visa Link</span>
                                                <a style="text-decoration: underline!important;color: #0f6674;padding-left: 0px !important;"
                                                   class="btn btn-sm" target="_blank" title="ক্লিক করুন"
                                                   href="@if(!empty($profileInfo["visa"]["e_visa_download_link"])) {{$profileInfo["visa"]["e_visa_download_link"]}} @elseif(!empty($profileInfo["visa"]["e_visa_link"])) {{$profileInfo["visa"]["e_visa_link"]}} @else '#' @endif ">
                                                    @if(!empty($profileInfo["visa"]["e_visa_download_link"]))
                                                        ই-ভিসা ডাউনলোড করুন
                                                    @elseif(!empty($profileInfo["visa"]["e_visa_link"]))
                                                        ই-ভিসা লিংক
                                                    @else
                                                        N/A
                                                    @endif
                                                </a></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="dash-section-card card-color-4">
                            <div class="section-card-title">
                                <h3>Flight Information</h3>
                            </div>
                            @if(!empty($profileInfo["identity"]) && $profileInfo['identity']['is_publish_flight'])
                                <div class="section-card-container">
                                    <h3>Haj Flight</h3>
                                    <div class="row">
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span class="info-label">Flight Date</span>
                                                @if(isset($profileInfo["haj_flight"]["data_found"]) && $profileInfo["haj_flight"]["data_found"] == true)
                                                    {{$profileInfo["haj_flight"]["flight_date"]}}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span class="info-label">Flight Code</span>
                                                @if(isset($profileInfo["haj_flight"]["data_found"]) && $profileInfo["haj_flight"]["data_found"] == true)
                                                    {{$profileInfo["haj_flight"]["flight_code"]}}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span class="info-label">Remains</span>
                                                @if(isset($profileInfo["haj_flight"]["data_found"]) && $profileInfo["haj_flight"]["data_found"] == true)
                                                    {{$profileInfo["haj_flight"]["remaining_days"]}}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Reporting Date</span>
                                                @if(isset($profileInfo["reporting"]["date"]))
                                                    {{$profileInfo["reporting"]["date"]}}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span class="info-label">Location </span> Haj
                                                Office, Dhaka
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <h3 class="pt-3">Return Flight</h3>
                                    <div class="row">
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span class="info-label">Flight Date</span>
                                                @if(isset($profileInfo["return_flight"]["data_found"]) && $profileInfo["return_flight"]["data_found"] == true)
                                                    {{$profileInfo["return_flight"]["flight_date"]}}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span class="info-label">Flight Code</span>
                                                @if(isset($profileInfo["return_flight"]["data_found"]) && $profileInfo["return_flight"]["data_found"] == true)
                                                    {{$profileInfo["return_flight"]["flight_code"]}}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-lg-4 dash-col">
                                            <div class="profile-info-item"><span class="info-label">Remains</span>
                                                @if(isset($profileInfo["return_flight"]["data_found"]) && $profileInfo["return_flight"]["data_found"] == true)
                                                    {{$profileInfo["return_flight"]["remaining_days"]}}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="p-3 m-0 text-center profile-info-item">Flight not published yet!!</p>
                            @endif
                        </div>

                        <div class="dash-section-card card-color-5">
                            <div class="section-card-title">
                                <h3>Mecca Housing Information</h3>
                            </div>
                            @if(!empty($profileInfo["identity"]) && $profileInfo['identity']['is_publish_building'])
                                <div class="section-card-container">
                                    <div class="row">
                                        <div class="col-lg-6 dash-col">
                                            <div class="profile-info-item"><span
                                                    class="info-label">Makkah House Name</span>
                                                @if(isset($profileInfo["house"]["is_publish_building"]) && $profileInfo["house"]["is_publish_building"] == true)
                                                    {{$profileInfo["house"]["makka_house_name"]}}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="p-3 m-0 text-center profile-info-item">House not published yet!!</p>
                            @endif
                        </div>

                        <div style="display: none" class="dash-section-card card-color-6">
                            <div class="section-card-title">
                                <h3>Reporting Information</h3>
                            </div>
                            <div class="section-card-container">
                                <div class="row">
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span class="info-label">Reporting Date</span>
                                            @if(isset($profileInfo["reporting"]["date"]))
                                                {{$profileInfo["reporting"]["date"]}}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span class="info-label">Location </span> Haj
                                            Office, Dhaka
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($is_owner ==1)
                            <div class="dash-section-card">
                                <div class="section-card-title print-card-title">
                                    <h3>List of group members</h3>
                                    <a class="print-btn" target="_blank" title="Print Group Members"
                                       href="@if(!empty($profileInfo["identity"]))/list-of-group-members-pdf-generate/{{\App\Libraries\Encryption::encodeId($profileInfo["identity"]["tracking_no"])}}@endif"><i
                                            class="fas fa-print"></i></a>
                                </div>
                                <div class="section-gmb-lists">
                                    <div class="dash-list-table">
                                        <div class="table-responsive">
                                            <table class="table dash-table">
                                                <thead>
                                                <tr>
                                                    @if(!empty($profileInfo["basic_info"]) && $profileInfo["basic_info"]["pilgrim_type_id"] == 6)
                                                        <th scope="col">PID</th>
                                                    @else
                                                        <th scope="col">Unit</th>
                                                        <th scope="col">PID</th>
                                                    @endif
                                                    <th scope="col">Contact Details</th>
                                                    {{--                                                    <th scope="col" nowrap>Name</th>--}}
                                                    {{--                                                    <th scope="col" nowrap>Phone Number</th>--}}
                                                    @if(!empty($profileInfo["basic_info"]) && $profileInfo["basic_info"]["pilgrim_type_id"] == 6)
                                                        <th scope="col" nowrap>Flight</th>
                                                        {{--                                                        <th scope="col" nowrap>Flight Date</th>--}}
                                                    @endif
                                                    <th scope="col">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr class="table-row-space">
                                                    <td colspan="5">&nbsp;</td>
                                                </tr>
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
                                                            @if(!empty($profileInfo["basic_info"]) && $profileInfo["basic_info"]["pilgrim_type_id"] == 6)
                                                                <td scope="row"><span class="gmb-lists-img">
                                                            <img
                                                                src="@if(!empty($single_group["picture"])){{$single_group["picture"]}}@endif"
                                                                width="50" height="50" alt="Images"></span><br>
                                                                    {{$single_group["pid"]}}
                                                                </td>
                                                            @else
                                                                <td scope="row"><span class="gmb-lists-img">
                                                            <img
                                                                src="@if(!empty($single_group["picture"])){{$single_group["picture"]}}@endif"
                                                                width="30" height="30" alt="Images"></span>
                                                                    {{$single_group["unit_no"]}}
                                                                </td>
                                                                <td>{{$single_group["pid"]}}</td>
                                                            @endif
                                                            <td><span class="gmb-contact-desc">@if(!empty($single_group["full_name_english"]))
                                                                        {{$single_group["full_name_english"]}}
                                                                    @else
                                                                        N/A
                                                                    @endif <br> @if(!empty($single_group["mobile"]))
                                                                        {{$single_group["mobile"]}}
                                                                    @else
                                                                        N/A
                                                                    @endif</span></td>

                                                            {{--                                                            <td nowrap>@if(!empty($single_group["full_name_english"])){{$single_group["full_name_english"]}}@else N/A @endif <span style="color: blue; font-weight: bold">{{$gender_age}}</span></td>--}}
                                                            {{--                                                            <td>@if(!empty($single_group["mobile"])){{$single_group["mobile"]}}@else N/A @endif</td>--}}

                                                            @if(!empty($profileInfo["basic_info"]) && $profileInfo["basic_info"]["pilgrim_type_id"] == 6)
{{--                                                                <td scope="col" nowrap>--}}
{{--                                                                    @if($single_group["flight_code"] != null || $single_group["flight_code"] != '')--}}
{{--                                                                        {{$single_group["flight_code"]}}--}}
{{--                                                                    @else--}}
{{--                                                                        NA--}}
{{--                                                                    @endif--}}
{{--                                                                </td>--}}
{{--                                                                <td scope="col" nowrap>--}}
{{--                                                                    @if($single_group["flight_date"] != null || $single_group["flight_date"] != '')--}}
{{--                                                                        {{$single_group["flight_date"]}}--}}
{{--                                                                    @else--}}
{{--                                                                        NA--}}
{{--                                                                    @endif--}}
{{--                                                                </td>--}}
                                                                <td><span class="gmb-contact-desc">
                                                                        @if($single_group["flight_code"] != null || $single_group["flight_code"] != '')
                                                                            {{$single_group["flight_code"]}}
                                                                        @else
                                                                            NA
                                                                        @endif
                                                                            <br>
                                                                        @if($single_group["flight_date"] != null || $single_group["flight_date"] != '')
                                                                            {{$single_group["flight_date"]}}
                                                                        @else
                                                                            NA
                                                                        @endif</span></td>

                                                            @endif
                                                            <td nowrap><a
                                                                    class="btn btn-listview-round btn-outline-primary"
                                                                    href="{{url("/users/get-single-pilgrim", Encryption::encodeId($unique_tracking_no))}}"
                                                                    aria-label="Info"><i class="fa fa-eye"
                                                                                         aria-hidden="true"></i>
                                                                    View</a></td>
                                                        </tr>
                                                        <tr class="table-row-space">
                                                            <td colspan="5">&nbsp;</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($is_owner ==1)
        <div class="modal ba-modal fade" id="ehajjInfoModal" tabindex="-1" aria-labelledby="ehajjInfoModal"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <div class="modal-body">
                        <div class="modal-info-block">
                            <h3 class="modal-title">Group Members Information</h3>
                            <div id="single_pilgrim_list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (!empty($pilgrim_bank_info))
        <div class="modal ba-modal fade" id="pilgrimBankInfo" aria-labelledby="pilgrimBankInfo"
        aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <button type="button" class="close modal-close " data-dismiss="modal" aria-label="Close" style="background: #43CE81 !important;">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <div class="modal-body">
                        <div class="modal-info-block">
                            <h3 class="modal-title">Pilgrim Refundable Account Information</h3>
                            @if (!empty($pilgrim_bank_info['existingProcess']))
                                <p style="color: red">আপনার একটি আবেদন মন্ত্রনালয়ের অনুমোদন এর অপেক্ষায় আছে</p>
                            @endif
                            <div id="">
                                {!! Form::open(['url' => url('/users/pilgrim-profile/store-pilgrim-refund-bank'), 'id' => 'pilgrimBankAccForm']) !!}
                                {{-- <input type="hidden" id="existingProcess" value="{{count($pilgrim_bank_info['existingProcess'])}}"> --}}
                                @if (!empty($pilgrim_bank_info['existingProcess']))
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!! Form::label('paymentReceiveType', 'Payment Receive Type:') !!}
                                                {!! Form::select('paymentReceiveType', $pilgrim_bank_info['paymentReceiveType'], $pilgrim_bank_info["existingProcess"]["ownerType"], ['class' => 'form-control', 'disabled']) !!}
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('accountNumber', 'Account Number:') !!}
                                                {!! Form::text('accountNumber', $pilgrim_bank_info["existingProcess"]["accountNumber"], ['class' => 'form-control', 'required', 'disabled']) !!}
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('refund_district_id', 'District:') !!}
                                                {!! Form::select('refund_district_id',  $pilgrim_bank_info["district_list"], $pilgrim_bank_info["existingProcess"]["distCode"], ['class' => 'form-control', 'placeholder'=>'Select One', 'required', 'disabled']) !!}
                                            </div>
                                            <div class="form-group" style="display: {{ $pilgrim_bank_info["existingProcess"]["ownerType"] == 'Nearest Relative' ? 'block' : 'none' }}">
                                                {!! Form::label('relation', 'Relation:') !!}
                                                {!! Form::text('relation', $pilgrim_bank_info["existingProcess"]["relation"], ['class' => 'form-control', 'id' => 'relation', 'disabled']) !!}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!! Form::label('accountHolderName', 'Account Holder Name:') !!}
                                                {!! Form::text('accountHolderName', $pilgrim_bank_info["existingProcess"]["accountName"], ['class' => 'form-control', 'required', 'disabled']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('refund_bank_id', 'Bank Name:') !!}
                                                {!! Form::select('refund_bank_id', $pilgrim_bank_info['bank_list'], $pilgrim_bank_info["existingProcess"]["bankId"], ['class' => 'form-control', 'placeholder'=>'Select One', 'required', 'disabled']) !!}
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('refund_branch_id', 'Bank Branch Name:') !!}
                                                {!! Form::select('refund_branch_id', [], '', ['class' => 'form-control','placeholder'=>'Select One', 'id' => 'refund_branch_id', 'required', 'disabled']) !!}
                                                <input type="hidden" id="hidden_branch_routing" name="hidden_branch_routing" value="{{ $pilgrim_bank_info["existingProcess"]["branchRoutingNo"] }}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close">
                                            Close
                                        </button>

                                        {!! Form::submit('Submit', ['class' => 'btn btn-primary', 'id' => 'refundBankInfoSubmitBtn', 'disabled', 'style' => 'cursor: not-allowed']) !!}
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!! Form::label('paymentReceiveType', 'Payment Receive Type:') !!}
                                                {!! Form::select('paymentReceiveType', $pilgrim_bank_info['paymentReceiveType'], $pilgrim_bank_info["owner_type"], ['class' => 'form-control select2Dropdown']) !!}
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('accountNumber', 'Account Number:') !!}
                                                {!! Form::text('accountNumber', $pilgrim_bank_info["account_number"], ['class' => 'form-control', 'required']) !!}
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('refund_district_id', 'District:') !!}
                                                {!! Form::select('refund_district_id',  $pilgrim_bank_info["district_list"], $pilgrim_bank_info["dist_code"], ['class' => 'form-control select2Dropdown', 'required', 'placeholder'=>'Select One']) !!}
                                            </div>
                                            <div class="form-group" style="display: {{ $pilgrim_bank_info['owner_type'] == 'Nearest Relative' ? 'block' : 'none' }}">
                                                {!! Form::label('relation', 'Relation:') !!}
                                                {!! Form::text('relation', $pilgrim_bank_info['relation'], ['class' => 'form-control', 'id' => 'relation']) !!}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                {!! Form::label('accountHolderName', 'Account Holder Name:') !!}
                                                {!! Form::text('accountHolderName', $pilgrim_bank_info["account_name"], ['class' => 'form-control', 'required']) !!}
                                            </div>

                                            <div class="form-group">
                                                {!! Form::label('refund_bank_id', 'Bank Name:') !!}
                                                {!! Form::select('refund_bank_id', $pilgrim_bank_info['bank_list'], $pilgrim_bank_info['bank_id'], ['class' => 'form-control select2Dropdown required', 'placeholder'=>'Select One', 'required']) !!}
                                            </div>
                                            <div class="form-group">
                                                {!! Form::label('refund_branch_id', 'Bank Branch Name:') !!}
                                                {!! Form::select('refund_branch_id', [], '', ['class' => 'form-control','placeholder'=>'Select One', 'id' => 'refund_branch_id', 'required']) !!}
                                                <input type="hidden" id="hidden_branch_routing" name="hidden_branch_routing" value="{{ $pilgrim_bank_info['id'] }}"/>
                                                @php
                                                    $trackingNo = $profileInfo['identity']['tracking_no'];
                                                    $encodedTrackingNo = Encryption::encodeId($trackingNo);
                                                @endphp
                                                <input type="hidden" id="tracking_no" name="tracking_no" value="{{$encodedTrackingNo}}"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close">
                                            Close
                                        </button>

                                        {!! Form::submit('Submit', ['class' => 'btn btn-primary', 'id' => 'refundBankInfoSubmitBtn']) !!}
                                    </div>
                                @endif
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('footer-script')
    @include('partials.datatable-js')
    @include('partials.image-upload')

    <script src="{{ asset("assets/plugins/select2.min.js") }}"></script>
    <script>
        function ehajjSingleGroup(tracking_no) {
            $.ajax({
                url: "<?php echo env('APP_URL') . '/users/get-single-pilgrim' ?>",
                type: "POST",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {tracking_no: tracking_no},
                success: function (response) {
                    if (response.responseCode == 1) {
                        $('#single_pilgrim_list').html(response.html);
                        $('#ehajjInfoModal').modal('show');
                    } else {
                        $('#single_pilgrim_list').html("");
                        $('#ehajjInfoModal').modal('show');
                    }
                },
                error: function (data) {
                    // console.log((data.responseJSON.errors));
                }
            })
        }

        $(document).ready(function () {
            $(".select2Dropdown").select2({
                tags: true,
                width: '100%',
            });

            // basic information KSA mobile no udpate
            $(".ksa_edit").on('click', function () {
                $(".update_ksa_mobile_panel").removeClass('hidden');
            });

            $(".update_ksa_number").on('click', function () {

                var ksa_mobile_no = $('#ksa_mobile_no').val();
                if (!ksa_mobile_no) {
                    alert('KSA Mobile Number should be given');
                    return false;
                }

                btn = $(this);
                btn_content = btn.html();
                btn.html('<i class="fa fa-spinner fa-spin"></i> &nbsp;' + btn_content);

                var tracking_no = $(this).attr('tracking_no');
                var _token = $('input[name="_token"]').val();
                var flag_column = 'ksa_mobile_no';

                $.ajax({
                    url: "<?php echo env('APP_URL') . '/users/pilgrim-profile/update-pilgrim-profile-by-single-column' ?>",
                    type: 'post',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        _token: _token,
                        tracking_no: tracking_no,
                        ksa_mobile_no: ksa_mobile_no,
                        flag_column: flag_column
                    },
                    success: function (response) {
                        if (response.responseCode == 0) {
                            alert(response.message);
                        } else {
                            $(".update_ksa_mobile_panel").addClass('hidden');
                            $(".current_ksa_no").html(ksa_mobile_no);
                        }
                        btn.html(btn_content);
                    }
                });


            });


            // basic information email update
            $(".email_edit").on('click', function () {
                $(".update_email_panel").removeClass('hidden');
            });

            $(".update_email").on('click', function () {

                var email = $('#email').val();
                if (!email) {
                    alert('Email should be given');
                    return false;
                }

                btn = $(this);
                btn_content = btn.html();
                btn.html('<i class="fa fa-spinner fa-spin"></i> &nbsp;' + btn_content);

                var tracking_no = $(this).attr('tracking_no');
                var _token = $('input[name="_token"]').val();
                var flag_column = 'email';

                $.ajax({
                    url: "<?php echo env('APP_URL') . '/users/pilgrim-profile/update-pilgrim-profile-by-single-column' ?>",
                    type: 'post',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        _token: _token,
                        tracking_no: tracking_no,
                        email: email,
                        flag_column: flag_column
                    },
                    success: function (response) {
                        if (response.responseCode == 0) {
                            alert(response.message);
                        } else {
                            $(".update_email_panel").addClass('hidden');
                            $(".current_email").html(email);
                        }
                        btn.html(btn_content);
                    }
                });


            });

            var maharamGurdianBankid = 0;

            @if (!empty($pilgrim_bank_info))
                $(document).on('change', '#paymentReceiveType', function () {
                    const payment_receive_type = $('#paymentReceiveType').val();
                    const initial_dependent_id = {{$profileInfo["basic_info"]["dependent_id"]}} + '';
                    const initial_maharam_id = {{$profileInfo["basic_info"]["maharam_id"]}} + '';
                    let initial_payment_receive_type = '{{ $pilgrim_bank_info["owner_type"] }}';

                    switch(payment_receive_type) {
                        case 'Pilgrim own':
                            if(initial_payment_receive_type == 'Pilgrim own') {
                                $('#accountNumber').val('{{$pilgrim_bank_info["account_number"]}}').prop('readonly', false);
                                $('#refund_district_id').val('{{$pilgrim_bank_info["dist_code"]}}');
                                $('#refund_bank_id').val('{{$pilgrim_bank_info['bank_id']}}');
                            } else {
                                $('#accountNumber').val('').prop('readonly', false);
                                $('#refund_district_id').val('');
                                $('#refund_bank_id').val('');
                            }
                            $('#relation').parent().hide();
                            $('#relation').attr('required', false).prop('disabled', true);
                            $('#accountHolderName').val('{{$profileInfo["basic_info"]["full_name_english"]}}').prop('readonly', true);
                            $('#refund_bank_id').trigger('change');
                            break;
                        case 'Nearest Relative':
                            if(initial_payment_receive_type == 'Nearest Relative') {
                                $('#accountHolderName').val('{{$pilgrim_bank_info["account_name"]}}').prop('readonly', false);
                                $('#accountNumber').val('{{$pilgrim_bank_info["account_number"]}}').prop('readonly', false);
                                $('#refund_district_id').val('{{$pilgrim_bank_info["dist_code"]}}');
                                $('#refund_bank_id').val('{{$pilgrim_bank_info['bank_id']}}');
                                $('#refund_branch_id').val('');
                            } else {
                                $('#accountHolderName').val('').prop('readonly', false);
                                $('#accountNumber').val('').prop('readonly', false);
                                $('#refund_district_id').val('');
                                $('#refund_bank_id').val('');
                                $('#refund_branch_id').val('');
                            }
                            $('#relation').parent().show();
                            $('#relation').val('{{$pilgrim_bank_info["relation"]}}').attr('required', true).addClass('required').prop('disabled', false);
                            $('#refund_bank_id').trigger('change');
                            break;
                        case 'Guardian':
                            if(initial_payment_receive_type == 'Guardian') {
                                $('#accountHolderName').val('{{$pilgrim_bank_info["account_name"]}}').prop('readonly', false);
                                $('#accountNumber').val('{{$pilgrim_bank_info["account_number"]}}').prop('readonly', false);
                                $('#refund_district_id').val('{{$pilgrim_bank_info["dist_code"]}}');
                                $('#refund_bank_id').val('{{$pilgrim_bank_info['bank_id']}}');
                                $('#refund_branch_id').val('');
                                $('#refund_bank_id').trigger('change');
                            } else {
                                getMaharamBankInfo(0, initial_dependent_id);
                            }
                            $('#relation').parent().hide();
                            $('#relation').attr('required', false).prop('disabled', true);
                            break;
                        case 'Maharam':
                            if(initial_payment_receive_type == 'Maharam') {
                                $('#accountHolderName').val('{{$pilgrim_bank_info["account_name"]}}').prop('readonly', false);
                                $('#accountNumber').val('{{$pilgrim_bank_info["account_number"]}}').prop('readonly', false);
                                $('#refund_district_id').val('{{$pilgrim_bank_info["dist_code"]}}');
                                $('#refund_bank_id').val('{{$pilgrim_bank_info['bank_id']}}');
                                $('#refund_branch_id').val('');
                                $('#refund_bank_id').trigger('change');
                            } else {
                                getMaharamBankInfo(initial_maharam_id, initial_dependent_id);
                            }
                            $('#relation').parent().hide();
                            $('#relation').attr('required', false).prop('disabled', true);
                            break;
                        default:
                            $('#relation').parent().hide();
                            $('#relation').attr('required', false).removeClass('required');
                            $('#relation').val('').prop('disabled', true);
                            $('#accountNumber').val('');
                            $('#refund_district_id').val('');
                            $('#accountHolderName').val('');
                            $('#refund_bank_id').val('');
                            $('#refund_branch_id').val('');
                            break;
                    }
                });

                if( {{count($pilgrim_bank_info['existingProcess'])}} <= 0) {
                    $('#paymentReceiveType').trigger('change');
                }

                $('#bankAccModalBtn').on('click', function() {
                    $('#pilgrimBankInfo').modal('show');
                });

                $('#refund_district_id').on('change', function() {
                    var self = $(this);
                    var districtId = $('#refund_district_id').val();
                    var bankId = $('#refund_bank_id').find(":selected").val();
                    $("#refund_branch_id").val('');
                    let initial_payment_receive_type = '{{ $pilgrim_bank_info["owner_type"] }}';
                    let payment_receive_type = $('#paymentReceiveType').val();

                    let hidden_branch_routing = $("#hidden_branch_routing").val();
                    if (districtId !== '') {
                        $(this).after('<span class="loading_data">Loading...</span>');
                        $('#paymentReceiveType').prop('disabled', true);
                        $.ajax({
                            type: "POST",
                            url: "<?php echo env('APP_URL') . '/users/pilgrim-profile/get-pilgrim-refund-bank-branch' ?>",
                            data: {
                                districtId: districtId,
                                bankId: bankId
                            },
                            success: function (response) {
                                var option = `<option value="">Select One</option>`;
                                if (response.responseCode == 1) {
                                    $.each(response.data, function (id, value) {
                                        if((payment_receive_type == 'Guardian' || payment_receive_type == 'Maharam') && (initial_payment_receive_type != 'Guardian' || initial_payment_receive_type != 'Maharam')) {
                                            if(maharamGurdianBankid == id) {
                                                option += `<option value="${id}" selected>${value}</option>`;
                                            } else
                                                option += `<option value="${id}" >${value}</option>`;
                                        } if(id == hidden_branch_routing) {
                                            option += `<option value="${id}" selected>${value}</option>`;
                                        } else
                                            option += `<option value="${id}" >${value}</option>`;

                                    });
                                    refund_branch_id = null;
                                }
                                $("#refund_branch_id").html(option);
                                $('#refund_branch_id').select2({
                                    tags: true,
                                    width: '100%',
                                });
                                if(Object.keys(response.data).length < 1) {
                                    if($('#refund_branch_id').data('select2')) {
                                        $('#refund_branch_id').select2('destroy');
                                    }
                                }
                                self.next().hide();
                                if( {{count($pilgrim_bank_info['existingProcess'])}} <= 0) {
                                    $('#paymentReceiveType').prop('disabled', false);
                                }
                            }
                        });
                    } else {
                        if($('#refund_branch_id').data('select2')) {
                            $('#refund_branch_id').select2('destroy');
                        }
                        $("#refund_branch_id").val('').html(`<option value="">Select One</option>`);
                    }
                });

                $('#refund_district_id').trigger('change');

                $('#refund_bank_id').on('change', function() {
                    $('#refund_district_id').trigger('change');
                });

                function getMaharamBankInfo(maharam_id, dependent_id) {
                    $('#paymentReceiveType').prop('disabled', true);
                    $.ajax({
                        type: "POST",
                        url: "<?php echo env('APP_URL') . '/users/pilgrim-profile/get-pilgrim-maharam-bank-information' ?>",
                        data: {
                            maharamId: maharam_id,
                            dependentId: dependent_id,
                        },
                        success: function (response) {
                            if (response.responseCode == 1) {
                                $('#accountHolderName').val(response.data.account_name).prop('readonly', true);
                                $('#accountNumber').val(response.data.account_number).prop('readonly', false);
                                $('#relation').parent().hide();
                                $('#relation').attr('required', false).removeClass('required').prop('disabled', true);
                                $('#refund_district_id').val(response.data.dist_code);
                                $('#refund_bank_id').val(response.data.bank_id);
                                // $('#refund_branch_id').val(response.data.id).html(`<option>${response.data.branch_Name} (Routing Number: ${response.data.routing_no})</option>`);
                                $('#refund_branch_id').val(response.data.id);
                                maharamGurdianBankid = response.data.id;
                            } else {
                                $('#accountHolderName').val('').prop('readonly', false);
                                $('#accountNumber').val('').prop('readonly', false);
                                $('#relation').parent().hide();
                                $('#relation').attr('required', false).removeClass('required').prop('disabled', true);
                                $('#refund_district_id').val('');
                                $('#refund_bank_id').val('');
                                $('#refund_branch_id').val('').html(`<option value="">Select One</option>`);
                                // $('#refund_branch_id').val('');
                                // $('#refund_bank_id').trigger('change');
                            }
                            if( {{count($pilgrim_bank_info['existingProcess'])}} <= 0) {
                                $('#paymentReceiveType').prop('disabled', false);
                            }
                            $('#refund_bank_id').trigger('change');
                        }
                    });
                };

                $('#pilgrimBankAccForm').on('submit', function() {
                    const payment_receive_type = $('#paymentReceiveType').val();
                    const accountHolderName = $.trim($('#accountHolderName').val());
                    const accountNumber = $.trim($('#accountNumber').val());
                    const refund_bank_id = $('#refund_bank_id').val();
                    const refund_district_id = $('#refund_district_id').val();
                    const refund_branch_id = $('#refund_branch_id').val();
                    const relation = $.trim($('#relation').val());
                    const trackingNO = $('#tracking_no').val();
                    const nameRegex = /^[a-zA-Z0-9\s.()]+$/;
                    const numericRegex = /^[0-9]+$/;

                    if (!nameRegex.test(accountHolderName)) {
                        $('#accountHolderName').focus();
                        return false;
                    }
                    if(payment_receive_type == 'Nearest Relative') {
                        if (!nameRegex.test(relation)) {
                            $('#relation').focus();
                            return false;
                        }
                    }
                    if (!numericRegex.test(accountNumber)) {
                        $('#accountNumber').next().hide();
                        $('#accountNumber').after('<span style="color: red; font-size: 13px">Account number must be number</span>');
                        $('#accountNumber').focus();
                        return false;
                    } else {
                        $('#accountNumber').next().hide();
                    }

                    if(accountNumber.length < 13 || accountNumber.length > 17) {
                        $('#accountNumber').next().hide();
                        $('#accountNumber').after('<span style="color: red; font-size: 13px">Account number must be between 13 and 17 characters</span>');
                        $('#accountNumber').focus();
                        return false;
                    } else {
                        $('#accountNumber').next().hide();
                    }
                    if(payment_receive_type == 'Nearest Relative' && (!accountHolderName || !accountNumber || accountNumber.length < 13 || !refund_bank_id || !refund_district_id || !refund_branch_id || !relation || !trackingNO)) {
                        console.log('Nearest Relative not valid')
                        return false;
                    } else if(payment_receive_type !== 'Nearest Relative' && (!accountHolderName || !accountNumber || accountNumber.length < 13 || !refund_bank_id || !refund_district_id || !refund_branch_id || !trackingNO)) {
                        console.log('other type not valid')
                        return false;
                    }
                    return true;
                });

            @endif



            // $('#paymentReceiveType').trigger('change');









        });
    </script>
@endsection
