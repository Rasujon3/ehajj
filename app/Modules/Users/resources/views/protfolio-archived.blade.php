@extends('layouts.admin')
@section('header-resources')
    @include('partials.datatable-css')
@endsection
@section('content')
    @include('partials.messages')
    <div class="hajj-profile-dashboard">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-xl-3">
                    <div class="dash-profile-menu white-box white-box-pad">
                        <div class="dash-navbar">
                            <div class="dash-menu-item active">
                                <a class="dash-menu-text" href="{{ url('/dashboard') }}"><i
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
                <div class="col-lg-8 col-xl-3">
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
                                    if ($profileInfo["basic_info"]["ref_pilgrim_id"] <= 0) {
                                        --$total;
                                    }
                                    if ($profileInfo["basic_info"]["pilgrim_type_id"] == 6) {
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
                                        @endif
                                    </div>
                                    <div>
                                        <a class="btn" title="Print Profile Info" style="padding-left: 0px !important;"
                                           target="_blank"
                                           href="/profile-pdf-generate/{{\App\Libraries\Encryption::encodeId($profileInfo["identity"]["tracking_no"])}}"><i
                                                class="fa fa-print" aria-hidden="true" style="font-size: 25px;"></i></a>
                                    </div>
                                </div>
                                <div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-xl-6">
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
                                        </div>
                                    </div>
                                    <div class="col-lg-4 dash-col">
                                        <div class="profile-info-item"><span class="info-label">Email</span>
                                            <span class="current_email"> @if(!empty($profileInfo["basic_info"]["email"]))
                                                    {{$profileInfo["basic_info"]["email"]}}
                                                @else
                                                    N/A
                                                @endif</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section-card-container p-3">
                         <span>আপনার হজের ডাটা {{$profileInfo["basic_info"]["session_caption"]}} সনে আরকাইভ অবস্থায় আছে, হজ সম্পর্কিত যে কোন তথ্যের জন্য হজ কল সেন্টার: 16136, +880 9602666707 এ যোগাযোগ করুন। </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

