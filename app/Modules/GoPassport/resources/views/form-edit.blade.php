@extends('layouts.admin')
@section('header-resources')
    @include('partials.form-add-edit-css', ['viewMode' => $viewMode])
    <link rel="stylesheet" href="{{ asset("assets/plugins/jquery-steps/jquery.steps.css") }}">
    <link rel="stylesheet" href="{{ asset("assets/plugins/intlTelInput/css/intlTelInput.css") }}"/>
    <style>
        .card-header {
            padding: 0.75rem 1.25rem !important;
        }

        .input-disabled {
            pointer-events: none;
        }

        .modal.fade .modal-dialog {
            margin-top: 5px;
            margin-left: 19%;
        }

        .modal-content {
            width: 190%;
        }

        #goList {
            text-align: center; /* Center align the entire table */
        }

        #goList th,
        #goList td {
            text-align: center; /* Center align header and cell content */
        }

        .page-item.active .page-link {
            background-color: #0F6849 !important;
        }

        .btn-outline-success {
            color: #0F6849 !important;
            border-color: #0F6849 !important;
        }
        .table th,
        .table td {
            border-top:none !important;
        }
        .table tr:last-child td{
            border-bottom: none !important;
        }

        .loader {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            border: 3px solid;
            border-color: #0F6849 #0F6849 transparent transparent;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        .loader {
            width: 30px;
            height: 30px;
            border: 3px dotted #0F6849;
            border-style: solid solid dotted dotted;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            box-sizing: border-box;
            animation: rotation 2s linear infinite;
        }

        .loader::after {
            content: '';
            box-sizing: border-box;
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            margin: auto;
            border: 3px dotted #FF3D00;
            border-style: solid solid dotted;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            animation: rotationBack 1s linear infinite;
            transform-origin: center center;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes rotationBack {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(-360deg);
            }
        }

        .modal.fade .modal-dialog {
            margin-top: 5px;
            margin: 1.75rem auto;
        }

        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 98%;
            }
        }

        @media (min-width: 992px) {
            .modal-dialog {
                max-width: 990px;
            }
        }
    </style>
    @include('partials.datatable-css')
@endsection

@section('content')
    @include('partials.messages')

    <div class="dash-content-main">
        <div class="card card-magenta">
            <div class="card-header" style="background-color: #0F6849 !important">
                @if($processListCheck != null)
                    <h3 class="card-title pt-2 pb-2" style="color: white !important"><i class="fa fa-star"></i> Voucher
                        No: {{$processListCheck->tracking_no}} </h3>
                @else
                    <h3 class="card-title pt-2 pb-2" style="color: white !important">জিও পাসপোর্ট এর তালিকা </h3>
                @endif
            </div>
        </div>
        <div class="dash-section-content pt-1">
            <div class="dash-sec-head">
                <div class="container m-0 p-0">
                    <div class="card" style="overflow-x: auto;">
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th>দলের ধরণ</th>
                                    <td>: {{ $team_type[$stickerVisaData->team_type] }}</td>
                                    <th>দলের প্রকার</th>
                                    <td>: {{ (($team_sub_type != 'no') ? $team_sub_type[0]: 'N/A')}}</td>
                                </tr>
                                <tr>
                                    <th>জিও ক্রমিক নং</th>
                                    <td>
                                        : {{ (isset($stickerVisaData->go_number) ? $stickerVisaData->go_number: '')}}</td>
                                    <th>জিও সদস্য সংখ্যা</th>
                                    <td>: {{ $stickerVisaData->go_member}}</td>
                                </tr>
                                <tr>
                                    <th>ফি প্রযোজ্য</th>
                                    <td>
                                        : {{ (!empty($stickerVisaData->fee_applicable) ? $stickerVisaData->fee_applicable: 'N/A')}}</td>
                                    <th>পরিশোধযোগ্য এমাউন্ট</th>
                                    <td>
                                        : {{ (!empty($stickerVisaData->payable_amount) ? $stickerVisaData->payable_amount: 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>জিও তারিখ</th>
                                    <td> : {{ (!empty($goMemberCheck->go_date) && ($goMemberCheck->go_date != null)) ? (\Carbon\Carbon::createFromFormat('Y-m-d', $goMemberCheck->go_date)->format('d-M-Y')): 'N/A' }} </td>
                                </tr>
                            </table>
                            <div class="card-tools">
                                {{--  @if((count($stickerPilgrimsData) == 0))--}}
                                <button type="button" class="btn btn-default float-right btn-sm"
                                        style="background-color: #0F6849; color: white; margin-right: 5px !important;"
                                        onclick="showModalForGOEdit()"><i class="fa fa-edit"
                                        style="color: white !important;"> </i>&nbsp;Edit
                                </button>
                                {{--  @endif--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dash-content-main">
                <div class="dash-section-content">
                    <div class="dash-content-inner" id="stickerVisaList">
                        <div class="card card-magenta">
                            <div class="card-header" style="background-color: #D9EDF7 !important">
                                <div class="row">
                                    <div class="col-sm-3" style="color: black !important">স্টিকার ভিসার হজযাত্রীদের
                                        তালিকা
                                    </div>
                                    <div class="col-sm-9">
                                        @if(count($stickerPilgrimsData) < $goMemberCheck->go_members)
                                            <button type="button" class="btn btn-default float-right btn-sm"
                                                    style="color: white; background-color: #0F6849; margin-right: 5px !important;"
                                                    onclick="showModalForAddMember()"><i class="fa fa-plus-circle" style="color: white !important;"></i>&nbsp;পাসপোর্ট এন্ট্রি করুন
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" style="overflow-x: auto;">
                                <table class="table dt-responsive">
                                    <thead>
                                    <tr style="background-color: lightgrey;">
                                        <th class="align-content-center">ক্রমিক নং</th>
                                        <th class="align-content-center">পিআইডি নং</th>
                                        <th class="align-content-center">নাম</th>
                                        <th class="align-content-center">পাসপোর্টের ধরণ</th>
                                        <th class="align-content-center">পাসপোর্ট নং</th>
                                        <th class="align-content-center">পাসপোর্টের জন্মতারিখ</th>
                                        <th class="align-content-center">মোবাইল নং</th>
                                        <th class="align-content-center">জিও সিরিয়াল নাম্বার</th>
                                        <th class="align-content-center">অ্যাকশন</th>
                                    </tr>
                                    </thead>
                                    <tbody id="stickerVisaMembersList">
                                    @if(count($stickerPilgrimsData) != 0)
                                    @forelse($stickerPilgrimsData as $index => $item)
                                        <tr id="member_{{$index}}">
                                            <td class="align-content-center justify-content-center">{{$index+1}}</td>
                                            <td class="align-content-center justify-content-center">{{$item->pid}}</td>
                                            <td class="align-content-center justify-content-center">{{$item->name}}</td>
                                            <td class="align-content-center justify-content-center"> {{$item->passport_type}}</td>
                                            <td class="align-content-center justify-content-center">{{$item->passport_no}}</td>
                                            <td class="align-content-center justify-content-center"> {{ !empty($item->passport_dob)? \App\Libraries\CommonFunction::changeDateFormat($item->passport_dob) : ''  }}</td>
                                            <td class="align-content-center justify-content-center">{{$item->mobile_no}}</td>
                                            <td class="align-content-center justify-content-center">{{$item->go_serial_no}}</td>
                                            <td class="align-content-center justify-content-center">
                                                @if($processListCheck === null)
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            @if($item->id)
                                                                <button class="btn btn-primary edit-button"
                                                                        data-encoded-id="{{ \App\Libraries\Encryption::encodeId($item->id) }}">
                                                                    <i class="fa fa-edit"
                                                                       style="cursor: pointer"
                                                                       data-toggle="tooltip"
                                                                       title=""
                                                                       aria-describedby="tooltip">
                                                                    </i>
                                                                </button>
                                                            @endif
                                                        </div>

                                                        <div class="col-md-2" style="margin: 0px 24px !important">
                                                            @if($item->id)
                                                                {!! Form::open([
                                                                    'url' => url('go-passport/genarate-pdf/' .($encode_ref_id).'/'.($encode_process_type_id).'/'.\App\Libraries\Encryption::encodeId($item->id)),
                                                                    'method' => 'post',
                                                                    'class' => 'form-horizontal',
                                                                    'id' => 'process-go-passport',
                                                                    'enctype' => 'multipart/form-data',
                                                                    'files' => 'true',
                                                                    'target' => '_blank',
                                                                    ])
                                                                !!}
                                                                <button class="btn btn-primary print-button" type="submit">
                                                                    <i class="fa fa-print"
                                                                       style="cursor: pointer"
                                                                       data-toggle="tooltip"
                                                                       title=""
                                                                       aria-describedby="tooltip">
                                                                    </i>
                                                                </button>
                                                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                                                                {!! Form::close() !!}
                                                            @endif
                                                        </div>
                                                        <div class="col-md-2" >
                                                            <button type="button" class="btn btn-danger delete-button"
                                                                    @if($item->id)
                                                                        data-encoded-id="{{ \App\Libraries\Encryption::encodeId($item->id) }}"
                                                                   @endif >
                                                                <i class="fa fa-trash" style="cursor: pointer"
                                                                   data-toggle="tooltip" title=""
                                                                   aria-describedby="tooltip"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                  <!--  <button type="button" class="btn btn-primary">
                                                        <i class=""
                                                           style="cursor: pointer"
                                                           data-toggle="tooltip"
                                                           title=""
                                                           aria-describedby="tooltip">
                                                        </i>
                                                        Processing
                                                    </button> -->
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="noRecordRow">
                                            <td colspan="6" style="text-align: center">No Pilgrims Found</td>
                                        </tr>
                                    @endforelse
                                    @else
                                        <tr id="noRecordRow">
                                        <td colspan="6" style="text-align: center">No Pilgrims Found</td>
                                    </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                            @if($processListCheck === null && count($stickerPilgrimsData) != 0)
                                {!! Form::open([
                                    'url' => url('go-passport/genarate-pdf/' .($encode_ref_id).'/'.($encode_process_type_id)),
                                    'method' => 'post',
                                    'class' => 'form-horizontal',
                                    'id' => 'process-go-passport',
                                    'enctype' => 'multipart/form-data',
                                    'files' => 'true',
                                    'target' => '_blank',
                                    ])
                                !!}

                                @csrf
                               <div class="float-right" style="padding-left: 1em;">
                                   <button type="submit" id="submitForm" style="cursor: pointer"
                                           class="btn btn-primary btn-md"
                                           value="submit" name="actionBtn"><i class="fa fa-file-pdf"></i> &nbsp;
                                       Receive & Generate Slip
                                   </button>
                               </div>
                            @endif
                            <div class="float-right" style="padding-left: 1em;">
                                <a href="{{ url('go-passport/list/'. $encode_process_type_id )}}" id="closeForm"
                                   style="cursor: pointer;" class="btn btn-default btn-md"
                                   value="close" name="close">Close
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <br><br><br>
            </div>
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

            {!! Form::close() !!}


            {{--Here Add edit GO modal code include --}}
            @include('GoPassport::modal.edit-go-modal')
            {{--Here Add and edit member entry modal code include --}}
            @include('GoPassport::modal.edit-member-entry-modal-template')
            @include('GoPassport::modal.member-entry-modal')
            {{--Here Add and Delete member entry modal code include --}}
            @if(count($stickerPilgrimsData) != 0)
            @include('GoPassport::modal.passport-member-delete')
            @endif
        </div>

@endsection

@section('footer-script')
<script src="{{ asset("assets/plugins/intlTelInput/js/intlTelInput.min.js") }}"
            type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/intlTelInput/js/utils.js") }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ mix('assets/scripts/common_form_script.min.js') }}"></script>

    @include('partials.form-add-edit-js', ['viewMode' => $viewMode])

    @include('partials.datatable-js')
    <script src="{{ asset("assets/scripts/moment.js") }}"></script>
    <script src="{{ asset("assets/scripts/bootstrap-datetimepicker.js") }}"></script>
    <script src="{{ asset("assets/plugins/datepicker-oss/js/bootstrap-datetimepicker.js") }}"></script>
    <script>
        let teamTypeElement = $('#team_type');
        let subTeamTypeElement = $('#sub_team_type');
        let goNumberElement = $('#go_number');
        let goMemberElement = $('#go_member');
        let addToList = $('#addToList');
        let fee_applicable = $('input[name="fee_applicable"]:checked').val();
        let payable_amount = $('#payable_amount').val();
        let _token = $('input[name="_token"]').val();
        var timeoutID;
        $(document).ready(function () {
            //resetStickerVisaMemberForm();
            $('.passportError').html('');
            @if(isset($goMemberCheck->team_type) && isset($goMemberCheck->sub_team_type))
            getSubTeamByTeamId('team_type', {{$goMemberCheck->team_type}}, 'sub_team_type', {{$goMemberCheck->sub_team_type}});
            @endif

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

            $('input[name="fee_applicable"]').on('change', function () {
                var value = $(this).val();
                if (value === 'yes') {
                    $('#payable_amount').removeClass('input_disabled');
                    $('#payable_amount').addClass('required');
                    $('#payableAmountSection').show();

                } else {
                    $('input[name="payable_amount"]').val('');
                    $('#payable_amount').removeClass('required');
                    $('#payableAmountSection').hide();
                }
            });
            $('input[name="fee_applicable"]:checked').trigger('change');

            $('#team_type').on('change', function () {
                $('#sub_team_type_hidden').addClass('d-none');
                $('#sub_team_type').removeClass('d-none');
            });

            $('#goUpdate').on('click', function () {
                if (validateRequiredFields('#go_passport_form_update input, #go_passport_form_update select')) {
                    alert("Fill Input Field");
                    return false;
                }

                if (fee_applicable === 'yes' && payable_amount === null) {
                    $('#payable_amount').addClass('error');
                    return false;
                } else {
                    $('#payable_amount').removeClass('error');
                }
                $('#go_passport_form_update').submit();
            });

            $('input[name="passport_type"]').on('change', function () {
                $('input[name="selected_passport_type"]').val($(this).val());
            });
            $('input[name="passport_type"]:checked').trigger('change');

            // Passport Verify section
            $('#verifyPassport').on('click', function () {
                let passport_type = $('input[name="passport_type"]:checked').val();
                let passport_no = $("#passport_no").val();
                let dob = $("#passport_dob").val();
                submit_time = new Date();
                if (passport_type === 'E-PASSPORT' && (!passport_no.match('^[A-Z]{1}[0-9]{8}$') || !passport_no.length == 9)) {
                    hasError = true;
                    $('#passport_no_error').text('Please enter valid Passport No');
                    return false;
                } else if (passport_type === 'MRP' && (!passport_no.match('^[A-Z]{2}[0-9]{7}$') || !passport_no.length == 9)) {
                    hasError = true;
                    $('#passport_no_error').text('Please enter valid Passport No');
                    return false;
                } else {
                    $('#passport_no_error').text('');
                }
                if ($('#passport_dob').val() == '') {
                    alert('Birth ID can not be null');
                    $('#passport_dob_error').text('Please enter valid Passport Birth date');
                    return false;
                }
                //$("#check").after('<span class="loader" ></span>');
                $("#check").after('<span style="color: #0a5328; font-size:18px; display: flex" ">দয়া করে অপেক্ষা করুন, এরজন্য কিছু সময় লাগতে পারে। &nbsp;<i class="loader"></i></span>');
                $('#verifyPassport').addClass('d-none');
                $('#passport_no').addClass('input_disabled');
                $('#passport_dob').addClass('input_disabled');
                $('#father_name').addClass('input_disabled');
                $('#gender').addClass('input_disabled');
                $('input[name="pass_name"]').addClass('input_disabled');
                $('.calendar').addClass('d-none');
                $('input[name="passport_type"]').prop('disabled', true);
                passportVerification(passport_type, passport_no, dob, submit_time);
            });

            // pilgrim add
            $('#updateToList').on('click', function () {
                let hasError = false;
                if (validateRequiredFields('#stickerVisaMemberEntryDiv input, #stickerVisaMemberEntryDiv select')) {
                    hasError = true;
                }

                let passport_type = document.querySelector('input[name="passport_type"]:checked').value;
                let passport_no = document.getElementById('passport_no').value;
                let mobile_no = document.getElementById('mobile_no').value;
                const stickerVisaMembersListElement = document.getElementById('stickerVisaMembersList');

                if (passport_type === 'E-PASSPORT' && (!passport_no.match('^[A-Z]{1}[0-9]{8}$') || !passport_no.length == 9)) {
                    hasError = true;
                    $('#passport_no_error').text('Please enter valid Passport No');
                } else if (passport_type === 'MRP' && (!passport_no.match('^[A-Z]{2}[0-9]{7}$') || !passport_no.length == 9)) {
                    hasError = true;
                    $('#passport_no_error').text('Please enter valid Passport No');
                } else {
                    $('#passport_no_error').text('');
                }

                if (!BdMobileValidation(mobile_no)) {
                    hasError = true;
                    $('#mobile_no_error').text('Please enter valid Mobile No');
                } else {
                    $('#mobile_no_error').text('');
                }
                if (hasError) return false;
                $(this).prop('disabled', true);
                $('#go_passport_member_add').submit();
            });

            $('.edit-button').on('click', function () {

                let encodedId = $(this).data('encoded-id');
                $('#edit-modal-content').html('');
                $("#edit-member-loader").after('<span style="color: #0a5328; margin-left: 20px; font-size:17px" "> দয়া করে অপেক্ষা করুন <i class="fa fa-spinner fa-spin"></i></span>');
                editMembers(encodedId);
            });

            $('.delete-button').on('click', function () {
                let encodedId = $(this).data('encoded-id');
                $('#delete-modal-content').html('');
                $("#delete-member-loader").after('<span style="color: #0a5328; margin-left: 20px; font-size:17px" "> দয়া করে অপেক্ষা করুন <i class="fa fa-spinner fa-spin"></i></span>');
                deleteMembers(encodedId);
            });

            $('#cancelDeleteButton').click(function () {
                $("#deleteConfirmationModal").modal('hide');
                return false;
            });

            $('#close_passport_member_add_modal').on('click', function() {
                // Reset the form
                $('.memberInfo').addClass('d-none');
                $('#pass_name').val('');
                $('#father_name').val('');
                $('#gender').val('');
                $('.passport-img').val('');
                $('#passport_dob').val('');
                $('#passport_no').val('');
                $('#go_serial_no').val('');
                $('#mobile_no').val('');
                $('#amount').val('');
                $('#referance_no').val('');
                $('#taka_received_date').val('');

                $('#verifyPassport').removeClass('d-none');
                $('#passport_no').removeClass('input_disabled');
                $('#passport_dob').removeClass('input_disabled');
                $('#father_name').removeClass('input_disabled');
                $('#gender').removeClass('input_disabled');
                $('input[name="pass_name"]').removeClass('input_disabled');
                $('.calendar').removeClass('d-none');
                $('input[name="passport_type"]').prop('disabled', false);
                $('#passport_no_error').text('');
                $('#passport_dob_error').text('');
                $('#updateToList').addClass('d-none');
                $('#stickerVisaMemberEntryModal .passportError').html('');
                clearTimeout(timeoutID);
                $("#stickerVisaMemberEntryModal").modal('hide');
            });

        });

        function validateRequiredFields(selector) {
            const requiredClientFields = document.querySelectorAll(selector);
            let hasErrors = false;
            for (const elem of requiredClientFields) {
                if (elem.classList.contains('required') && !elem.value) {
                    elem.classList.add('error');
                    hasErrors = true;
                } else {
                    elem.classList.remove('error');
                }
            }
            return hasErrors;
        }

        function showModalForGOEdit() {
            $("#stickerGOEdit").modal('show');
        }

        function showModalForAddMember() {
            if (document.getElementById('noRecordRow')) {
                $("#noRecordRow").remove();
            }
            let total_members = document.getElementById('stickerVisaMembersList').children.length
            let go_member = <?php echo json_encode($stickerVisaData->go_member); ?>;

            if (total_members >= go_member) {
                alert('You can no longer add member')
                return false;
            }
            $("#stickerVisaMemberEntryModal").modal('show');
        }

        function getSubTeamByTeamId(team_type, team_id, sub_team_type, selected_value = 0) {
            let _token = $('input[name="_token"]').val();
            if (team_id !== '') {
                $("#" + team_type).after('<span style="color: #0a5328" "><i class="fa fa-spinner fa-spin"></i> Loading...</span>');
                $.ajax({
                    type: "POST",
                    url: "/getSubTeamData",
                    data: {_token, team_id},
                    dataType: 'json',
                    success: function (response) {
                        let option = '<option value="">Selected</option>';
                        if (response.status == 200) {
                            if (response.data.length > 0) {
                                document.getElementById(sub_team_type).classList.add('required');
                                $('#subTypeCheck').css('display', 'block');
                            } else {
                                document.getElementById(sub_team_type).classList.remove('required');
                                $('#subTypeCheck').css('display', 'none');
                            }
                            $.each(response.data, function (index, item) {
                                if (item.id == selected_value) {
                                    option += '<option value="' + item.id + '" selected>' + item.name + '</option>';
                                } else {
                                    option += '<option value="' + item.id + '">' + item.name + '</option>';
                                }
                            });
                        }
                        $("#" + sub_team_type).html(option);
                        $("#" + team_type).next().hide('slow');
                    }
                });
            } else {
                // console.log('Please select a valid district');
            }
        }

        $(function () {
            var today = new Date();
            var yyyy = today.getFullYear();
            $('.datetimepicker4').datetimepicker({
                format: 'DD-MMM-YYYY',
                maxDate: 'now',
                minDate: '01/01/' + (yyyy - 110),
                ignoreReadonly: true,
            });
        });


        function BdMobileValidation(phone) {
            var reg = /(^(\+88|0088)?(01){1}[3456789]{1}(\d){8})$/;
            if (reg.test(phone)) {
                return true;
            }
            return false;
        }

        function showDeleteConfirmation() {
            $("#deleteConfirmationModal").modal('show');
        }

        function editMembers(encodedId) {
            $.ajax({
                type: "GET",
                url: '/go-passport/edit-members/' + encodedId,
                data: {
                    _token: '<?php echo csrf_token(); ?>',
                },
                success: function (response) {
                    $("#edit-member-loader").next().hide('slow');
                    if (response.responseCode === 1) {
                        $('#edit-modal-content').html(response.html);
                    } else {
                        alert("Error!!!!");
                    }
                },
            });

            $("#stickerVisaMemberEditModal").modal('show');
        }

        function deleteMembers(encodedId) {
            $.ajax({
                type: "GET",
                url: '/go-passport/delete-members/' + encodedId,
                data: {
                    _token: '<?php echo csrf_token(); ?>',
                },
                success: function (response) {
                    $("#delete-member-loader").next().hide('slow');
                    if (response.responseCode === 1) {
                        $('#delete-modal-content').html(response.html);
                    } else {
                        alert("Error!!");
                    }
                },
            });

            $("#deleteConfirmationModal").modal('show');
        }


        function passportVerification(passport_type, passport_no, dob, submit_time) {
            let passportSubmissionTime = Math.floor((new Date() - submit_time) / 1000);
            let _token = $('input[name="_token"]').val();
            $.ajax({
                url: '/go-passport/passport_verification',
                type: 'POST',
                data: {_token, passport_type, passport_no, dob},
                dataType: 'json',
                success: function (response) {
                    $("#check").next().hide('slow');
                    let msg = '';
                    if (response.responseCode == 1) {
                        $('.memberInfo').removeClass('d-none');
                        $('#updateToList').removeClass('d-none');
                        $('#requestData').val(response.data.requestData);
                        $('input[name="pass_name"]').val(response.data.passportData.pass_name);
                        $('input[name="father_name"]').val(response.data.passportData.father_name_english);
                        $('input[name="gender"]').val(response.data.passportData.gender);
                        $('img.passport-img').attr('src', 'data:image/webp;base64,' + response.data.passportData.passportPhoto);

                    } else {
                        msg = response.msg;
                        if (passportSubmissionTime > 60 * 3) {
                            msg = 'Its been 3 minute already!<br />We will try to verify for you, when server is available<br /><b>You can try again after some time...</b>'
                        } else if (passportSubmissionTime > 60 * 2) {
                            msg = 'ইতিমধ্যে ২ মিনিট পার হয়েছে! কিছুক্ষণ পরে আবার চেষ্টা করুন...';
                        } else if (passportSubmissionTime > 60) {
                            msg = 'এক মিনিট ইতিমধ্যে হয়েছে! আপনি একটু বেশি অপেক্ষা করতে পারেন..';
                        } else if (passportSubmissionTime > 30) {
                            msg = 'দয়াকরে অপেক্ষা করুন, এরজন্য কিছু সময় লাগতে পারে।';
                        }
                        if (response.status === 1 || response.status === 0) {
                            $('#stickerVisaMemberEntryModal .passportError').html(msg + '&nbsp;<i class="fa fa-spinner fa-spin"></i>').css({
                                'color': '#0F6849',
                                'font-weight': 'bold',
                                'font-size': '15px'
                            });
                            timeoutID = setTimeout(function() {
                                passportVerification(passport_type, passport_no, dob, submit_time);
                            }, 8000);
                        } else {
                            $('#stickerVisaMemberEntryModal .passportError').html(msg).css({
                                'color': 'red',
                                'font-weight': 'bold'
                            });
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#check").next().hide('slow');
                    console.log(errorThrown);
                }
            });
        }
    </script>
@endsection




