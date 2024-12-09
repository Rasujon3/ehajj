
<style>
    /**
     * Hajj Agency Info
     */
    .form-searchbar{
        position: relative;
        width: 100%;
        max-width: 520px;
        text-align: left;
        display: block;
        margin: 0 auto 20px;
    }
    .form-searchbar label{
        font-size: 15px;
        font-weight: normal;
    }
    .form-searchbar .inline-src-group{
        display: flex;
        column-gap: 10px;
        align-items: center;
    }
    .form-searchbar .inline-src-group .btn{
        min-width: 110px;
        font-weight: normal;
        display: flex;
        column-gap: 8px;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .form-searchbar .inline-src-group .btn,
    .form-searchbar .inline-src-group .form-control{
        height: 40px;
    }

    .border-card-block.inner-card{
        border-color: #D9EDF7;
        box-shadow: none;
    }
    .border-card-block.inner-card .bd-card-head{
        background-color: #D9EDF7;
        color: #000000;
    }

    .row-gap-1{
        row-gap: 10px;
    }


    .bs_checkbox_ag [type="radio"]:not(:checked) + label:after,
    .bs_checkbox_ag [type="radio"]:checked + label:after,
    .bs_checkbox_ag [type="checkbox"]:not(:checked) + label:after,
    .bs_checkbox_ag [type="checkbox"]:checked + label:after {
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M19 0H5.00002C2.23997 0.00332812 0.00332812 2.23997 0 5.00002V19C0.00332812 21.76 2.23997 23.9967 5.00002 24H19C21.76 23.9967 23.9967 21.76 24 19V5.00002C23.9967 2.23997 21.76 0.00332812 19 0ZM20 8.079L10.746 17.333C9.96511 18.1142 8.69878 18.1145 7.91756 17.3336C7.91738 17.3334 7.91719 17.3332 7.917 17.333L3.99998 13.417C3.60867 13.0257 3.60867 12.3913 3.99998 12C4.3913 11.6088 5.0257 11.6087 5.41697 12L9.33295 15.916L18.588 6.66202C18.9793 6.27211 19.6125 6.27323 20.0025 6.6645C20.3924 7.05581 20.3913 7.68909 20 8.079Z" fill="%23374957"/></svg>');
        background-size: 100%;
        background-repeat: no-repeat;
        background-position: center;
    }

    .current-status{
        font-size: 15px;
        font-weight: bold;
        color: #0F6849;
    }
    .current-status.cst-inactive{
        color: #EC1C24;
    }

    .hajj-agency-info .ehajj-list-table .table td{
        padding-top: 15px;
        padding-bottom: 15px;
    }
    .hajj-agency-info .bs_checkbox_ag{
        margin: 0;
    }
    .hajj-agency-info .bs_checkbox_ag [type="radio"]:not(:checked) + label,
    .hajj-agency-info .bs_checkbox_ag [type="radio"]:checked + label,
    .hajj-agency-info .bs_checkbox_ag [type="checkbox"]:not(:checked) + label,
    .hajj-agency-info .bs_checkbox_ag [type="checkbox"]:checked + label{
        font-size: 14px;
    }

    .hajj-agency-info .ehajj-list-table .table{
        min-width: 100%;
    }
    .bootstrap-datetimepicker-widget{
        min-width:300px;
    }
    @media (max-width: 767px){
        .hajj-agency-info .ehajj-list-table .table{
            min-width: 100%;
        }
        .hajj-agency-info .ehajj-list-table .table > thead{
            display: none;
        }
        .hajj-agency-info .ehajj-list-table .table > tbody > tr > td{
            display: block;
            width: 100%;
            text-align: left;
        }
        .table-striped tbody tr:nth-of-type(2n+1){
            background-color: #f2f2f2;
        }
    }
    @media (max-width: 575px){
        .hajj-agency-info .flight-info-lists{
            margin: 0;
        }
        .hajj-agency-info .flight-info-lists ul li{
            flex-direction: column;
        }
        .hajj-agency-info .flight-info-lists ul li .flight-info-list-title{
            width: 100%;
            max-width: 100%;
            -ms-flex: 0 0 100%;
            flex: 0 0 100%;
        }
        .bootstrap-datetimepicker-widget table th,
        .bootstrap-datetimepicker-widget table td{
            padding: 5px !important;
            font-size:12px;
        }
        .bootstrap-datetimepicker-widget{
            min-width:280px;
        }
    }

</style>
<div class="border-card-block hajj-agency-info">
    {!! Form::open([
             'url' => url('process/action/store/'.$process_type_id),
             'method' => 'post',
             'class' => 'form-horizontal',
             'id' => 'agency_info_update',
             'enctype' => 'multipart/form-data',
             'files' => 'true'
         ])
    !!}

    {!! Form::hidden('process_type_id', $process_type_id,['id' => 'processTypeId']) !!}
    {!! Form::hidden('haj_license_no', '',['class' => 'haj_license_no']) !!}
    {!! Form::hidden('user_name', '',['class' => 'user_name']) !!}
    {!! Form::hidden('agency_name', '',['class' => 'agency_name']) !!}
    {!! Form::hidden('user_email', '',['class' => 'user_email']) !!}
    {!! Form::hidden('agency_id', '',['class' => 'agency_id']) !!}
    {!! Form::hidden('user_phone', '',['class' => 'user_phone']) !!}
    {!! Form::hidden('chk_current_agency_status', '',['class' => 'chk_current_agency_status']) !!}
    {!! Form::hidden('chk_current_user_email', '',['class' => 'chk_current_user_email']) !!}
    {!! Form::hidden('chk_current_pre_reg_agency_status', '',['class' => 'chk_current_pre_reg_agency_status']) !!}
    {!! Form::hidden('chk_current_user_status', '',['class' => 'chk_current_user_status']) !!}
    <div class="bd-card-head">
        <div class="bd-card-title">
            <span class="title-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 24 18" fill="none">
                    <path d="M23.1018 3.3276L22.8657 1.98857C22.675 0.909681 21.5203 0.211331 20.2859 0.428854L4.22559 3.26095H22.418C22.6527 3.26095 22.8812 3.2846 23.1018 3.3276Z" fill="white"/>
                    <path d="M22.4183 4.93359H1.57999C0.70825 4.93359 -0.000976562 5.50649 -0.000976562 6.2104V16.3343C-0.000976562 17.0382 0.70825 17.6111 1.57999 17.6111H22.4183C23.2898 17.6111 23.999 17.0382 23.999 16.3343V6.2104C23.9989 5.50624 23.2898 4.93359 22.4183 4.93359ZM7.43186 16.3109H5.75921V14.8714H7.43186V16.3109ZM7.43186 13.4317H5.75921V11.9921H7.43186V13.4317ZM7.43186 10.5525H5.75921V9.11282H7.43186V10.5525ZM7.43186 7.67325H5.75921V6.23355H7.43186V7.67325ZM19.7217 9.40635L18.4345 10.6104L19.5942 14.4841L18.9446 15.1337C18.8733 15.2047 18.7579 15.2047 18.6865 15.1337L16.9579 11.9918L15.3679 13.56L15.6211 14.7959L15.2165 15.2007C15.1628 15.2544 15.0755 15.2544 15.0223 15.2009L12.7321 12.9107C12.6783 12.857 12.6783 12.77 12.7321 12.7165L13.1367 12.3114L14.3499 12.5589L15.9191 10.9629L12.7992 9.24561C12.7279 9.17479 12.7279 9.05932 12.7987 8.988L13.4489 8.33846L17.2969 9.49032L18.5096 8.19404C18.864 7.83968 19.4226 7.82412 19.7572 8.15888C20.0913 8.49338 20.0754 9.05211 19.7217 9.40635Z" fill="white"/>
                </svg>
            </span>
            <h3>Haj Agency Information Update Form</h3>
        </div>
    </div>
    <div class="bd-card-content">

        <div class="form-searchbar">
            <label>Search by Licence  Number</label>
            <div class="inline-src-group">
                <input class="form-control" type="text" id="licenceNo" placeholder="0002">
                <button type="button" id="searchBtn" class="btn btn-green" onclick="searchAgencyLicenceSearch()">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M13.4998 12.7346L10.1078 9.34266C10.9889 8.26507 11.4221 6.89004 11.3178 5.50201C11.2136 4.11397 10.5798 2.81911 9.54754 1.88527C8.51532 0.951435 7.16366 0.450059 5.77215 0.484851C4.38063 0.519643 3.05572 1.08794 2.07147 2.0722C1.08721 3.05646 0.518911 4.38137 0.484119 5.77288C0.449326 7.1644 0.950703 8.51605 1.88454 9.54827C2.81838 10.5805 4.11324 11.2143 5.50127 11.3186C6.88931 11.4229 8.26433 10.9897 9.34193 10.1086L12.7338 13.5005L13.4998 12.7346ZM5.91643 10.2505C5.05937 10.2505 4.22157 9.99635 3.50896 9.5202C2.79634 9.04404 2.24093 8.36727 1.91295 7.57545C1.58497 6.78364 1.49915 5.91235 1.66636 5.07177C1.83356 4.23118 2.24627 3.45906 2.8523 2.85303C3.45832 2.247 4.23045 1.83429 5.07104 1.66709C5.91162 1.49989 6.78291 1.5857 7.57472 1.91368C8.36654 2.24166 9.04331 2.79707 9.51946 3.50969C9.99562 4.2223 10.2498 5.06011 10.2498 5.91716C10.2485 7.06604 9.79151 8.16749 8.97913 8.97986C8.16675 9.79224 7.0653 10.2492 5.91643 10.2505Z" fill="white"/>
                        </svg>
                    </span>
                    <span>Search</span>
                </button>
                <button type="button" id="loadingBtn" class="btn btn-green" style="display: none">
                    <span>Loading...</span>
                </button>
            </div>
        </div>

        <div class="border-card-block inner-card mt-4 info">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <h3>Agency Information</h3>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="flight-info-lists">
                            <ul>
                                <li>
                                    <span class="flight-info-list-title">Haj License No</span>
                                    <span class="flight-info-list-desc " id="haj_license_no"></span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">User Name</span>
                                    <span class="flight-info-list-desc " id="user_name"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="flight-info-lists">
                            <ul>
                                <li>
                                    <span class="flight-info-list-title">Agency</span>
                                    <span class="flight-info-list-desc " id="agency_name"></span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">User email</span>
                                    <span class="flight-info-list-desc " id="user_email"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-card-block inner-card mb-0 info">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <h3>Change Information</h3>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="ehajj-list-table">
                    <div class="table-responsive" style="overflow: visible" >
                        <table class="table table-striped" style="border: 1px solid #D9D9D9;">
                            <thead>
                                <tr>
                                    <th>Label Name</th>
                                    <th>Current Status</th>
                                    <th>Changeable Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="text-left">
                                            <div class="bs_checkbox_ag">
                                                <input type="checkbox" id="hajj_agency_status" name="agency_section" value="0">
                                                <label for="hajj_agency_status">Agency Status</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="current-status cst-active" id="current_agency_status"></span>
                                    </td>
                                    <td id="check_agency" style="display: none">
                                        <div class="status-from-group">
                                            <div class="row row-gap-1 m-0">
                                                <div class="col-md-6 px-1">
                                                    {!! Form::select('agency_status',["" => 'Select'] + $status, null, ['class' => 'form-control', 'id' => 'agency_status' ]) !!}
                                                </div>
                                                <div class="col-md-6 px-1">
                                                    {!! Form::select('inactive_reason',["" => 'Select Inactive Reason'] + $inactive_reason, null, ['class' => 'form-control', 'id' => 'inactive_reason' ]) !!}
                                                </div>

                                                <div class="col-md-12 px-1" id="date" style="display:none">
                                                    <div class="input-group">
                                                        <div class="input-group" id="datepicker1" data-target-input="nearest">
                                                            {!! Form::text('inactive_date','',['class' => 'form-control datepicker','id'=>'inactive_date', 'placeholder'=>'DD/MM/YYYY']) !!}
                                                            <div class="input-group-append">
                                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="text-left">
                                            <div class="bs_checkbox_ag">
                                                <input id="hajj_pre-reg-status" type="checkbox" name="pre_reg_section" value="0" >
                                                <label for="hajj_pre-reg-status">Pre-Registration Status</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="current-status cst-active" id="current_pre_reg_status"></span>
                                    </td>
                                    <td id="check_pre_reg"  style="display: none">
                                        <div class="status-from-group">
                                            <div class="row row-gap-1 m-0">
                                                <div class="col-md-12 px-1">
                                                    {!! Form::select('pre_reg_status',["" => 'Select'] + $status, null, ['class' => 'form-control', 'id' => 'pre_reg_status' ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="text-left">
                                            <div class="bs_checkbox_ag">
                                                <input type="checkbox" name="agency_user_section" id="hajj-agency-user-status" value="0">
                                                <label for="hajj-agency-user-status">User Status</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="current-status cst-active" id="current_user_status"></span>
                                    </td>
                                    <td id="check_user" style="display: none">
                                        <div class="status-from-group">
                                            <div class="row row-gap-1 m-0">
                                                <div class="col-md-6 px-1">
                                                    {!! Form::select('active_user_id',["" => 'Select'] + [], null, ['class' => 'form-control', 'id' => 'email' ]) !!}
                                                </div>
                                                <div class="col-md-6 px-1">
                                                    {!! Form::select('user_status',["" => 'Select'] + $status, null, ['class' => 'form-control', 'id' => 'user_status' ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bd-card-footer info">
        <div class="flex-center flex-btn-group">
            <button class="btn btn-default"><span>Close</span></button>
            <button type="submit" class="btn btn-green"><span>Submit</span></button>
        </div>
    </div>
    {!! Form::close() !!}
</div>
<script src="{{ asset("assets/scripts/moment.js") }}"></script>
<script src="{{ asset("assets/scripts/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset("assets/plugins/datepicker-oss/js/bootstrap-datetimepicker.js") }}"></script>
<script>
    $(document).on('focus', ".datepicker", function () {
        $(this).datetimepicker({
            format: 'DD-MMM-YYYY',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-chevron-left",
                next: "fa fa-chevron-right",
                today: "fa fa-clock-o",
                clear: "fa fa-trash-o"
            }
        });
    });

    $(document).ready(function() {
        $('.info').css('display', 'none');
        $('#date').css('display', 'none');

        $('#agency_info_update').on('keypress', function(event) {
            if (event.which === 13) { // 13 is the key code for "Enter"
                event.preventDefault(); // Prevent form submission
            }
        });

        $('#inactive_reason').on('change', function() {
            let selectedValue = $(this).val();
            if(selectedValue){
                $('#date').css('display', 'block');
            } else {
                $('#date').css('display', 'none');
            }
        });

        $('#hajj_agency_status').on('change', function() {
            if ($(this).is(':checked')) {
                $('#hajj_agency_status').val(1);
                $('#check_agency').show();

            } else {
                $('#hajj_agency_status').val(0);
                $('#check_agency').hide();
            }
        });
        $('#hajj_pre-reg-status').on('change', function() {
            if ($(this).is(':checked')) {
                $('#hajj_pre-reg-status').val(1);
                $('#check_pre_reg').show();
            } else {
                $('#hajj_pre-reg-status').val(0);
                $('#check_pre_reg').hide();
            }
        });
        $('#hajj-agency-user-status').on('change', function() {
            if ($(this).is(':checked')) {
                $('#hajj-agency-user-status').val(1);
                $('#check_user').show();
            } else {
                $('#hajj-agency-user-status').val(0);
                $('#check_user').hide();
            }
        });
        $('#agency_status').on('change', function() {
            let agencyStatus = $(this).val();
            if(agencyStatus === '0'){
                $('#inactive_reason').css('display', 'block');
            } else {
                $('#inactive_reason').css('display', 'none');
                $('#date').css('display', 'none');
            }
        });

        $('#email').on('change', function() {
            let email  = $('#email option:selected').text();
            $('.user_email').val(email);
        });

        $('#agency_info_update').on('submit', function(e) {
            e.preventDefault();  // Prevent form submission to validate first
            let isValid = true;
            let errorMessage = '';

            // Checking condition here
            let sections = {
                agency: {
                    sectionValue: $('#hajj_agency_status').val(),
                    currentValue: $('.chk_current_agency_status').val(),
                    changedValue: $('#agency_status').val(),
                    errorMsg: 'আপনি এজেন্সির কোন তথ্য পরিবর্তন করেন নি.'
                },
                preReg: {
                    sectionValue: $('#hajj_pre-reg-status').val(),
                    currentValue: $('.chk_current_pre_reg_agency_status').val(),
                    changedValue: $('#pre_reg_status').val(),
                    errorMsg: 'আপনি প্রাক নিবন্ধনের কোন তথ্য পরিবর্তন করেন নি.'
                },
                user: {
                    sectionValue: $('#hajj-agency-user-status').val(),
                    currentValue: $('.chk_current_user_status').val(),
                    changedValue: $('#user_status').val(),
                    errorMsg: 'আপনি ইউজার এর কোন তথ্য পরিবর্তন করেন নি.'
                }
            };

            // Check if no sections have been modified
            if (sections.agency.sectionValue === '0' && sections.preReg.sectionValue === '0' && sections.user.sectionValue === '0') {
                isValid = false;
                errorMessage = 'আপনি কোন তথ্য পরিবর্তন করেন নি.';
            }

            // Validate individual sections
            $.each(sections, function(key, section) {
                if (section.sectionValue === '1' && section.currentValue === section.changedValue) {
                    isValid = false;
                    errorMessage = section.errorMsg;
                    return false;
                }
            });

            if (!isValid) {
                alert(errorMessage);
            } else {
                this.submit();
            }
        });


    });

    function searchAgencyLicenceSearch() {
        $('#searchBtn').css('display','none');
        $('#loadingBtn').css('display','block');
        let licence_no = $('#licenceNo').val();
        let process_type_id = $('#processTypeId').val();
        $.ajax({
            url: '/search-by-agency-licence',
            type: 'GET',
            data: {
                licence_no: licence_no,
                process_type_id: process_type_id,
            },
            success: function(response) {
                if(response.responseCode === 1) {
                    $('.info').css('display', 'block');
                    const agencyInfo = response.data?.agency_info;
                    const agencyUserInfo = response.data?.agency_user_info;
                    const userInfoEmail = response.data?.userInfoEmail;

                    $('#haj_license_no').text(agencyInfo?.license_no);
                    $('#agency_name').text(agencyInfo?.name);
                    $('#user_email').text(agencyUserInfo?.user_email);
                    $('.chk_current_user_email').val(agencyUserInfo?.user_email);
                    $('#user_name').text(agencyUserInfo?.user_full_name);

                    $('.haj_license_no').val(agencyInfo?.license_no);
                    $('.agency_name').val(agencyInfo?.name);
                    $('.user_email').val(agencyUserInfo?.user_email);
                    $('.user_name').val(agencyUserInfo?.user_full_name);
                    $('.user_phone').val(agencyUserInfo?.user_phone);
                    $('.agency_id').val(agencyInfo?.id);
                    $('#inactive_reason').val(agencyInfo?.inactive_reason);
                    $('#inactive_date').val(agencyInfo?.inactive_deadline);

                    if (agencyInfo?.inactive_deadline) {
                        const formattedDate = formatDateToDDMMMYYYY(agencyInfo.inactive_deadline);
                        $('#inactive_date').val(formattedDate);
                        $('#date').css('display', 'block');
                    } else {
                        $('#date').css('display', 'none');
                    }

                    if (agencyInfo?.is_active === 1) {
                        $('#current_agency_status').text('Active');
                        $('#inactive_reason').css('display', 'none');
                        $('#agency_status').val(1);
                        $('.chk_current_agency_status').val(1);
                    } else {
                        $('#current_agency_status').text('Inactive');
                        $('.chk_current_agency_status').val(0);
                        $('#inactive_reason').css('display', 'block');
                        $('#agency_status').val(0);
                    }
                    if (agencyInfo?.pre_reg_status === 1) {
                        $('#current_pre_reg_status').text('Active');
                        $('.chk_current_pre_reg_agency_status').val(1);
                        $('#pre_reg_status').val(1);
                    } else {
                        $('#current_pre_reg_status').text('Inactive');
                        $('.chk_current_pre_reg_agency_status').val(0);
                        $('#pre_reg_status').val(0);
                    }
                    if(agencyUserInfo?.user_status === 'active'){
                        $('#current_user_status').text('Active');
                        $('.chk_current_user_status').val(1);
                        $('#user_status').val(1);
                    } else {
                        $('#current_user_status').text('Inactive');
                        $('.chk_current_user_status').val(0);
                        $('#user_status').val(0);
                    }
                    if(agencyUserInfo?.user_status == 'active'){
                        $('#email').html('<option value="' + agencyUserInfo?.id + '">' + agencyUserInfo?.user_email + '</option>');
                    }else{
                        let userEmail = '';
                        userInfoEmail.forEach(function(user) {
                            userEmail += '<option value="' + user.id + '">' + user.user_email + '</option>';
                        });

                        $('#email').append(userEmail);
                        //$('#email').html('<option value="' + agencyUserInfo?.id + '">' + agencyUserInfo?.user_email + '</option>');

                    }

                    $('#loadingBtn').css('display','none');
                    $('#searchBtn').css('display','block');

                } else {
                    alert(response.message);
                    $('.info').css('display', 'none');
                    resetFormFields();
                    $('#loadingBtn').css('display','none');
                    $('#searchBtn').css('display','block');
                }

            },
            error: function(xhr, status, error) {
                resetFormFields();
                $('.info').css('display', 'none');
                $('#loadingBtn').css('display','none');
                $('#searchBtn').css('display','block');
            }
        });
    }
    function formatDateToDDMMMYYYY(dateString) {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const month = monthNames[date.getMonth()];
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
    }
    // Function to reset form fields
    function resetFormFields() {
        $('#haj_license_no').text('');
        $('#agency_name').text('');
        $('#user_email').text('');
        $('#user_name').text('');
        $('#inactive_reason').val('');
        $('#inactive_date').val('');
        $('#date').css('display', 'none');

        $('#current_agency_status').text('Inactive');
        $('#agency_status').val(0);

        $('#current_pre_reg_status').text('Inactive');
        $('#pre_reg_status').val(0);

        $('#current_user_status').text('Inactive');
        $('#user_status').val(0);

        $('#email').html('');
    }

</script>
