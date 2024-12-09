@extends('layouts.admin')
@section('header-resources')
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

        .with-margin {
            margin: 5px;
        }


    </style>
    @include('partials.datatable-css')
@endsection

@section('content')
    @include('partials.messages')

    <div class="dash-content-main">
        <div class="card card-magenta">
            <div class="card-header" style="background-color: #0F6849 !important">
                <h3 class="card-title pt-2 pb-2" style="color: white !important">পাসপোর্ট ফেরতের তথ্য</h3>
            </div>
        </div>
        <div class="dash-section-content pt-1">
            <div class="dash-sec-head">
                <div class="container m-0 p-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3" ></div>
                                <div class="col-sm-8" >
                                    {!! Form::open([
                                            'url' => url('go-passport/add/delivery-passport/'.($encoded_process_type_id)),
                                            'method' => 'post',
                                            'class' => 'form-horizontal',
                                            'id' => 'deliver-passport-add',
                                            'enctype' => 'multipart/form-data',
                                            'files' => 'true'
                                    ])!!}

                                    <div class="form-group row">
                                        <div class="col-md-8">
                                            {!! Form::label('', 'পাসপোর্ট নাম্বার সার্চ করুন', ['class' => '']) !!}
                                            <div style="display: flex; align-items: center;">
                                                {!! Form::text('passport_no', '', ['class' => 'form-control required', 'placeholder' => 'Enter', 'id' => '']) !!}
                                                <button type="submit" class="btn btn-default" id="add_passport" style="margin-left: 5px; background-color: #0F6849 !important;color: white !important; width: 50% !important;">
                                                    <i class="fa fa-plus-circle" style="color: white;"></i>&nbsp;Add Passport
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {!! Form::close() !!}
                                </div>
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
                                    <div class="col-sm-3" style="color: black !important">পাসপোর্ট এর তালিকা
                                    </div>
                                    <div class="col-sm-9">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table dt-responsive">
                                    <thead>
                                    <tr style="background-color: lightgrey;">
                                        <th class="align-content-center">ক্রমিক নং</th>
                                        <th class="align-content-center">জিও ক্রমিক নাম্বার</th>
                                        <th class="align-content-center">ট্র্যাকিং নাম্বার</th>
                                        <th class="align-content-center">পাসপোর্টের ধরণ</th>
                                        <th class="align-content-center">পাসপোর্ট নং</th>
                                        <th class="align-content-center">পাসপোর্টের জন্মতারিখ</th>
                                        <th class="align-content-center">অ্যাকশন</th>
                                    </tr>
                                    </thead>
                                    <tbody id="delivery_list">
                                    @php $serialNumber = 1; @endphp
                                    @if ($returnPassports)
                                        @forelse($returnPassports as $data)
                                            {!! Form::open(array('url' => 'go-passport/remove/delivery-passport/','method' => 'post','id' => 'passport-info-remove')) !!}
                                            <input type="hidden" name="passport_no" value="{{ App\Libraries\Encryption::encodeId($data['passport_no'])}}">
                                            <tr id="member">
                                                <td class="align-content-center">{{ $serialNumber++ }}</td>
                                                <td class="align-content-center">@if(isset($data['go_serial_no'])){{$data['go_serial_no']}}@endif</td>
                                                <td class="align-content-center">@if(isset($data['tracking_no'])){{$data['tracking_no']}}@endif</td>
                                                <td class="align-content-center">@if(isset($data['passport_type'])){{$data['passport_type']}}@endif</td>
                                                <td class="align-content-center">@if(isset($data['passport_no'])){{$data['passport_no']}}@endif</td>
                                                <td class="align-content-center">@if(isset($data['passport_dob'])){{$data['passport_dob']}}@endif</td>
                                                <td class="align-content-center">
                                                    <button class="btn btn-danger" type="submit">
                                                        <i class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            {!! Form::close() !!}
                                        @empty
                                            <tr id="noRecordRow">
                                                <td colspan="6" style="text-align: center">No Pilgrims Found</td>
                                            </tr>
                                        @endforelse
                                    @endif
                                    </tbody>
                                </table>
                                @if ($returnPassports)
                                    @if(count($returnPassports) > 0)
                                        <br><br>
                                        {!! Form::open([
                                                 'url' => url('go-passport/save/delivery-passport'),
                                                 'method' => 'post',
                                                 'class' => 'form-horizontal',
                                                 'id' => 'save-delivery-passport',
                                                 'enctype' => 'multipart/form-data',
                                                 'files' => 'true'
                                       ])!!}
                                        <div class="form-group row">
                                            {!! Form::label('', 'পাসপোর্ট রিটার্নের কারণ', ['class' => 'col-md-4 with-margin required-star']) !!}
                                            <div class="col-md-6 with-margin">
                                                <div style="display: flex; align-items: center;">
                                                    {!! Form::text('purpose', '', ['class' => 'form-control required', 'placeholder' => 'Enter', 'id' => '']) !!}
                                                </div>
                                            </div>
                                            <br>
                                            {!! Form::label('', 'রিটার্নের তারিখ', ['class' => 'col-md-4 with-margin required-star']) !!}

                                            <div class="col-md-6 with-margin">
                                                <div>
                                                    <div class="input-group" id="datepicker1" data-target-input="nearest">
                                                        {!! Form::text('return_date','',['class' => 'form-control datepicker','placeholder'=>'MM/DD/YYYY', 'data-rule-maxlength'=>'20', 'value'=>"12:00 AM", 'id' => '']) !!}
                                                        <div class="input-group-append" data-target="#datepicker1" data-toggle="datetimepicker">
                                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {!! Form::label('', 'মন্তব্য', ['class' => 'col-md-4 with-margin']) !!}
                                            <div class="col-md-6 with-margin">
                                                <div style="display: flex; align-items: center;">
                                                    {!! Form::textarea('comment', '', ['class' => 'form-control', 'placeholder' => 'Enter', 'id' => '', 'style' => 'height: 70px;']) !!}
                                                </div>
                                            </div>

                                        </div>
                                    <div class="form-group row">
                                        <div class="col-md-5"></div>
                                        <div class="col-md-4"></div>
                                        <div class="col-md-3">
                                            <button type="submit" id="save_passport" class="btn btn-default float-right" style="background-color: #0F6849 !important;color: white !important; width: 35% !important;">
                                                <i class="fa fa-save" style="color: white;"></i>&nbsp; Save
                                            </button>
                                        </div>
                                    </div>
                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="process_type_id" value="{{$encoded_process_type_id}}">

                                        {!! Form::close() !!}
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="float-left" style="padding-left: 1em;">
                                <a href="{{ url('go-passport/list/'. $encoded_process_type_id )}}" id="closeForm"
                                   style="cursor: pointer;" class="btn btn-default btn-md"
                                   value="close" name="close">Close
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <br><br><br>
            </div>
        </div>

    @endsection

@section('footer-script')
    <script src="{{ asset("assets/plugins/intlTelInput/js/intlTelInput.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/intlTelInput/js/utils.js") }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ mix('assets/scripts/common_form_script.min.js') }}"></script>

    @include('partials.datatable-js')
    <script src="{{ asset("assets/scripts/moment.js") }}"></script>
    <script src="{{ asset("assets/scripts/bootstrap-datetimepicker.js") }}"></script>
    <script src="{{ asset("assets/plugins/datepicker-oss/js/bootstrap-datetimepicker.js") }}"></script>

    <script>

        $(document).ready(function() {
                $('#save-delivery-passport').submit(function(event) {
                    let form = $(this);
                    let isValid = true;

                    form.find('.required').each(function() {
                        if (!$(this).val()) {
                            isValid = false;
                            $(this).addClass('is-invalid');
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    });

                    if (!isValid) {
                        event.preventDefault();
                        return false;
                    }
                    $('#save_passport').prop('disabled', true);
                    return true;
                });

            $('#add_passport').on('click', function() {
                $('#deliver-passport-add').submit()
                $(this).prop('disabled', true);
            });

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
        });
    </script>
@endsection




