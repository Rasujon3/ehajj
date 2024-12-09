@php
    if(!empty($profileInfo["basic_info"]["birth_date"])){
        $birth_date = \Carbon\Carbon::parse($profileInfo["basic_info"]["birth_date"]);
        $diffYears = \Carbon\Carbon::now()->diffInYears($birth_date);
    }
@endphp
<div class="row">
    <div class="col-lg-4">
        <div class="ehajj-profile-info white-box white-box-pad">
            <div class="profile-info-wrap">
                <div class="ehajj-profile-info-head">
                    <div class="dash-profile-pic">
                        <img src="@if(!empty($profileInfo["identity"]["picture"])){{$profileInfo["identity"]["picture"]}}@endif" alt="Images">
                    </div>
                    <h3>@if(!empty($profileInfo["basic_info"]["full_name_english"])){{$profileInfo["basic_info"]["full_name_english"]}}@endif</h3>
                    <span class="ehajj-profile-year">@if(!empty($profileInfo["basic_info"]["birth_date"])){{$diffYears}}@else N/A @endif Years</span>
                </div>
                <div class="profile-info-lists">
                    <div class="profile-info-item"><span class="info-label">PID</span> @if(!empty($profileInfo["identity"]["pid"])){{$profileInfo["identity"]["pid"]}}@else N/A @endif</div>
                    <div class="profile-info-item"><span class="info-label">Tracking ID</span> @if(!empty($profileInfo["identity"]["tracking_no"])){{$profileInfo["identity"]["tracking_no"]}}@else N/A @endif</div>
                    <div class="profile-info-item"><span class="info-label">unit</span> @if(!empty($profileInfo["identity"]["unit_no"])){{$profileInfo["identity"]["unit_no"]}}@else N/A @endif</div>
                    <div class="profile-info-item"><span class="info-label">Passport No</span> @if(!empty($profileInfo["identity"]["passport_no"])){{$profileInfo["identity"]["passport_no"]}}@else N/A @endif</div>
                    <div class="profile-info-item"><span class="info-label">Management</span> @if(!empty($profileInfo["identity"]["is_govt"])){{$profileInfo["identity"]["is_govt"]}}@else N/A @endif </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="dash-section-card card-color-6">
            <div class="section-card-title">
                @if($profileInfo["basic_info"]["pilgrim_type_id"] == 6)
                    <h3>Flight and Maktab Information</h3>
                @else <h3>Flight, Guide and Maktab Information</h3>
                @endif
            </div>
            <div class="section-card-container">
                <div class="row">
                    <div class="col-lg-4 dash-col">
                        <div class="profile-info-item"><span class="info-label">Father's Name</span>@if(!empty($profileInfo["basic_info"]["father_name_english"])){{$profileInfo["basic_info"]["father_name_english"]}}@else N/A @endif</div>
                    </div>
                    <div class="col-lg-4 dash-col">
                        <div class="profile-info-item"><span class="info-label">BD Phone Number</span>@if(!empty($profileInfo["basic_info"]["mobile"])){{$profileInfo["basic_info"]["mobile"]}}@else N/A @endif</div>
                    </div>
                    <div class="col-lg-4 dash-col">
                        <div class="profile-info-item"><span class="info-label">KSA Phone Number</span> @if(!empty($profileInfo["basic_info"]["ksa_mobile_no"])){{$profileInfo["basic_info"]["ksa_mobile_no"]}}@else N/A @endif</div>
                    </div>
                    <div class="col-lg-4 dash-col">
                        <div class="profile-info-item"><span class="info-label">PID</span> @if(!empty($profileInfo["identity"]["pid"])){{$profileInfo["identity"]["pid"]}}@else N/A @endif</div>
                    </div>
                    <div class="col-lg-4 dash-col">
                        <div class="profile-info-item"><span class="info-label">Maktab  No </span> @if(!empty($profileInfo["muallem"]["muallem_no"])){{$profileInfo["muallem"]["muallem_no"]}}@else N/A @endif</div>
                    </div>
                    <div class="col-lg-4 dash-col">
                        <div class="profile-info-item"><span class="info-label">Flight Date</span>@if(!empty($profileInfo["basic_info"]["flight_date"])){{$profileInfo["basic_info"]["flight_date"]}}@else N/A @endif</div>
                    </div>
                    <div class="col-lg-4 dash-col">
                        <div class="profile-info-item"><span class="info-label">Flight Code</span>@if(!empty($profileInfo["basic_info"]["flight_code"])){{$profileInfo["basic_info"]["flight_code"]}}@else N/A @endif</div>
                    </div>
                    <div class="col-lg-4 dash-col">
                        <div class="profile-info-item"><span class="info-label">City</span>
                            @if(isset($profileInfo["house"]["is_publish_building"]) && $profileInfo["house"]["is_publish_building"] == true)
                                {{$profileInfo["house"]["makka_house_name"]}}
                            @else N/A
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4 dash-col">
                        <div class="profile-info-item"><span class="info-label">Return Flight Date</span>
                            @if(isset($profileInfo["return_flight"]["data_found"]) && $profileInfo["return_flight"]["data_found"] == true)
                                {{$profileInfo["return_flight"]["flight_date"]}}
                            @else N/A
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
