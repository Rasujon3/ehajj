@extends('layouts.admin')
@section('header-resources')
    <style>
        .card-header {
            padding: 0.75rem 1.25rem !important;
        }
        .input-disabled{
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

        .page-item.active .page-link{
            background-color: #0F6849  !important;
        }
        .btn-outline-success{
            color: #0F6849  !important;
            border-color: #0F6849  !important;
        }

        .pagination .page-item.active .page-link,
        .pagination .page-item .page-link:focus,
        .pagination .page-item .page-link:active{
            color: #ffffff !important;
        }
    </style>

    @include('partials.datatable-css')

@endsection

@section('content')

    @include('partials.messages')

    <div class="dash-content-main">
        <div class="dash-section-content">
            <div class="dash-content-inner">
                <div class="card card-magenta border border-magenta">
                    <div class="card-header" style="background-color: #0F6849 !important">
                        <h3 class="card-title pt-2 pb-2" style="color: white !important">জিও পাসপোর্ট এর তালিকা</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-default float-right btn-sm" style="margin-left: 5px !important;">
                                <a href="/go-passport/delivery-passport/{{ $process_type_id }}" style="color: #0F6849 !important;">
                                    <i class="fa fa-minus-circle" style="color: #0F6849 !important;"></i>&nbsp;&nbsp; Delivery
                                </a>
                            </button>

                            <button type="button" class="btn btn-default float-right btn-sm" style="margin-right: 5px !important;"
                                    onclick="showModalForGOCreate()"> <i class="fa fa-plus-circle" style="color: #0F6849 "> </i>&nbsp; Received
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-table-head flex-space-btw">
                            <div class="list-tabmenu" role="tablist">
                                <div class="nav nav-tabs" role="presentation">
                                    <button class="tab-btn nav-link active" data-toggle="tab" data-target="#tabReceivedPassport" type="button" role="tab">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none">
                                            <path d="M7 1V7" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5 5L7 7L9 5" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9.5 2.5H13C13.2761 2.5 13.5 2.72386 13.5 3V11C13.5 11.2761 13.2761 11.5 13 11.5H1C0.723858 11.5 0.5 11.2761 0.5 11V3C0.5 2.72386 0.723858 2.5 1 2.5H4.5" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6 11.5L5 14" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8 11.5L9 14" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M4 14H10" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                        <span>Received Passport</span>
                                    </button>
                                    <button class="tab-btn nav-link" data-toggle="tab" data-target="#tabDeliveryDone" type="button" role="tab">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none">
                                            <path d="M7 1V7" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5 5L7 7L9 5" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9.5 2.5H13C13.2761 2.5 13.5 2.72386 13.5 3V11C13.5 11.2761 13.2761 11.5 13 11.5H1C0.723858 11.5 0.5 11.2761 0.5 11V3C0.5 2.72386 0.723858 2.5 1 2.5H4.5" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6 11.5L5 14" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8 11.5L9 14" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M4 14H10" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                        <span>Delivery Passport</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane show active fade" id="tabReceivedPassport">
                                <div class="lists-tab-content">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="goList"  style="width: 100%">
                                            <thead>
                                            <tr>
                                                <th>SL NO</th>
                                                <th>GO Number</th>
                                                <th>GO Date</th>
                                                <th>Application Type</th>
                                                <th>Application Sub Type</th>
                                                <th>Pilgrim</th>
                                                <th>Modified</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tabDeliveryDone">
                                <div class="lists-tab-content">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="delivery_list" style="width: 100%">
                                            <thead>
                                            <tr>
                                                <th>SL NO</th>
                                                <th>Purpose</th>
                                                <th>Passport</th>
                                                <th>Pilgrim</th>
                                                <th>Return Date</th>
                                                <th>Modified</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="stickerGOCreate" data-backdrop="static" data-keyboard="false" tabindex="-1"
             role="dialog" aria-labelledby="stickerVisaMemberEntryLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="font-size: 16px">
                    <div class="modal-header" style="background-color: #0F6849 !important">
                        <h5 class="modal-title" id="gridSystemModalLabel" style="color: white !important">জিও পাসপোর্ট এন্ট্রি করুন</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true" style="font-size: 25px;color: white !important">&times;</span></button>
                    </div>

                    <div class="modal-body">
                        {!! Form::open([
                                  'url' => url('go-passport/store/'.($process_type_id)),
                                  'method' => 'post',
                                  'class' => 'form-horizontal',
                                  'id' => 'go_passport_form',
                                  'enctype' => 'multipart/form-data',
                                  'files' => 'true'
                          ])!!}
                        <div class="card">

                            <div class="card-body">
                                <div class="row mt-4 mb-2" id="stickerVisaInfo">
                                    <div class="col-sm-3">
                                        <div class="form-group row">
                                            <label for="team_type" class="col-sm-4">দলের ধরণ</label>
                                            {!! Form::select('team_type',["" => 'Select'] + $team_type, null, ['class' => 'form-control col-sm-8 required', 'id' => 'team_type', 'onchange' => 'getSubTeamByTeamId("team_type",this.value,"sub_team_type",0)' ]) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-3" id="sub_team_type_container">
                                        <div class="form-group row">
                                            <label for="sub_team_type" class="col-sm-4">দলের প্রকার</label>
                                            {!! Form::select('sub_team_type',[], null, ['class' => 'form-control col-sm-8 required', 'id' => 'sub_team_type']) !!}

                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group row">
                                            <label for="go_number" class="col-sm-4">জিও নাম্বার</label>
                                            {!! Form::text('go_number','',['class'=>'form-control col-sm-8 required','placeholder' =>'','id'=>'go_number']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group row">
                                            <label for="go_member" class="col-sm-6">জিওতে সদস্য সংখ্যা </label>
                                            {!! Form::number('go_member','',['class'=>'form-control col-sm-6 required','placeholder' =>'','id'=>'go_member']) !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4 mb-2">
                                    <div class="col-sm-4">
                                        <div class="form-group row">
                                            <div class="input-group" id="datepicker1" data-target-input="nearest">
                                                <label for="go_date" class="col-sm-4">জিও তারিখ </label>
                                                {!! Form::text('go_date','',['class' => 'form-control datepicker','placeholder'=>'MM/DD/YYYY', 'data-rule-maxlength'=>'20', 'value'=>"12:00 AM", 'id' => '']) !!}
                                                <div class="input-group-append" data-target="#datepicker1" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 is_fee_applicable">
                                        <label for="is_fee_applicable" class="col-sm-6">ফি প্রযোজ্য</label>
                                        <div class="form-check form-check-inline ">
                                            {!! Form::radio('fee_applicable', 'yes', false, ['class' => 'form-check-input']) !!}
                                            {!! Form::label('fee_yes', 'Yes', ['class' => 'form-check-label']) !!}
                                        </div>
                                        <div class="form-check form-check-inline ">
                                            {!! Form::radio('fee_applicable', 'no', true, ['class' => 'form-check-input']) !!}
                                            {!! Form::label('fee_no', 'No', ['class' => 'form-check-label']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-4" id="payableAmountSection" style="display: none">
                                        <div class="form-group row">
                                            <label for="payable_amount" class="col-sm-4">পরিশোধযোগ্য এমাউন্ট </label>
                                            {!! Form::number('payable_amount','',['class'=>'form-control col-sm-8','placeholder' =>'Enter','id'=>'payable_amount']) !!}
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-1">
                                    <div class=" col-sm-12 d-flex justify-content-between" id="addToList">
                                        <button type="button" id="goClose"  class="btn btn-danger float-right" style="margin-left: 5px !important;">
                                            <i class="fa fa-times" style="color: white "> </i>&nbsp;Close</button>
                                        <button type="button" id="goSubmit" style="background-color: #0F6849  !important; margin-right: 5px !important;" class="btn btn-primary float-right">
                                            <i class="fa fa-save" style="color: white "> </i>&nbsp;Create</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

@endsection

@section('footer-script')

    @include('partials.datatable-js')
    <script src="{{ asset("assets/scripts/moment.js") }}"></script>
    <script src="{{ asset("assets/scripts/bootstrap-datetimepicker.js") }}"></script>
    <script src="{{ asset("assets/plugins/datepicker-oss/js/bootstrap-datetimepicker.js") }}"></script>
    <script>
        $(document).ready(function () {
            let teamTypeElement = $('#team_type');
            let subTeamTypeElement = $('#sub_team_type');
            let goNumberElement = $('#go_number');
            let goMemberElement = $('#go_member');
            let fee_applicable = $('input[name="fee_applicable"]:checked').val();
            let payable_amount = $('#payable_amount').val();

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

            $('#goSubmit').on('click', function() {

                if (validateRequiredFields('#go_passport_form input, #go_passport_form select')) {
                    alert("Fill Input Field");
                    return false;
                }

                if(fee_applicable === 'yes' && payable_amount === null){
                    $('#payable_amount').addClass('error');
                    return false;
                }else{
                    $('#payable_amount').removeClass('error');
                }
                $('#go_passport_form').submit();
            });

            $('#goClose').on('click', function() {
                // Reset the form
                teamTypeElement.removeClass('input_disabled');
                subTeamTypeElement.removeClass('input_disabled');
                goNumberElement.prop('readOnly', false);
                goMemberElement.prop('readOnly', false);
                $('#payable_amount').removeClass('input_disabled');
                $('#payable_amount').removeClass('error');
                $('#team_type').val('');
                $('#sub_team_type').val('');
                $('#go_number').val('');
                $('#go_member').val('');
                $('#payable_amount').val('');
                $('#stickerGOCreate').modal('hide');
            });

            $('input[name="fee_applicable"]').on('change', function(){
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

        });
        $(document).ready(function() {

            $('#goList').DataTable({
                processing: true,
                serverSide: true,
                "bDestroy": true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: '/go-passport/{{$type_key}}/getGoList/{{ $process_type_id }}',

                    method: 'get',
                    data: function(d) {
                        d.process_type_id = "{{ $process_type_id }}";
                        d.type_key = "{{ $type_key }}";
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'go_number',
                        name: 'go_number',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'go_date',
                        name: 'go_date',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'team_type',
                        name: 'team_type',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'team_sub_type',
                        name: 'team_sub_type',
                        orderable: false,
                        searchable: true
                    },

                    {
                        data: 'pilgrims',
                        name: 'pilgrims',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            $('#delivery_list').DataTable({
                processing: true,
                serverSide: true,
                "bDestroy": true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: '/go-passport/{{$type_key}}/get-delivery-List/{{ $process_type_id }}',

                    method: 'get',
                    data: function(d) {
                        d.process_type_id = "{{ $process_type_id }}";
                        d.type_key = "{{ $type_key }}";
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'purpose',
                        name: 'purpose',
                        orderable: false,
                        searchable: true,
                        render: function(data, type, row, meta) {
                            if (type === 'display') {
                                var maxLength = 9;
                                if (data.length > maxLength) {
                                    return data.substr(0, maxLength) + ' ...';
                                } else {
                                    return data;
                                }
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: 'passport',
                        name: 'passport',
                        orderable: false,
                        searchable: true,
                        render: function(data, type, row, meta) {
                            if (type === 'display') {
                                var maxLength = 9;
                                if (data.length > maxLength) {
                                    return data.substr(0, maxLength) + ' ...';
                                } else {
                                    return data;
                                }
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: 'no_of_pilgrim',
                        name: 'no_of_pilgrim',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'return_date',
                        name: 'return_date',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });
        });


        function showModalForGOCreate() {
            $("#stickerGOCreate").modal('show');
        }

        function validateRequiredFields(selector) {
            const requiredClientFields = $(selector);
            let hasErrors = false;

            requiredClientFields.each(function () {
                const elem = $(this);

                if (elem.hasClass('required') && !elem.val()) {
                    elem.addClass('error');
                    hasErrors = true;
                } else {
                    elem.removeClass('error');
                }
            });

            return hasErrors;
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
                                $('#sub_team_type_container').css('display', 'block');
                            } else {
                                document.getElementById(sub_team_type).classList.remove('required');
                                $('#sub_team_type_container').css('display', 'none');
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
            }
        }
    </script>

@endsection
