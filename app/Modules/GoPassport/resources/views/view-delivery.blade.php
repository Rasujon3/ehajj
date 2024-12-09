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
{{--        <div class="card card-magenta">--}}
{{--            <div class="card-header" style="background-color: #0F6849 !important">--}}
{{--                <h3 class="card-title pt-2 pb-2" style="color: white !important">পাসপোর্ট ফেরতের তথ্য</h3>--}}
{{--            </div>--}}
{{--        </div>--}}
        <div class="dash-section-content pt-1">
{{--            <div class="dash-sec-head">--}}
{{--                <div class="container m-0 p-0">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-sm-2" ></div>--}}
{{--                                <div class="col-sm-8" >--}}
{{--                                    {!! Form::open([--}}
{{--                                            'url' => url('go-passport/add/delivery-passport/'.($encoded_process_type_id)),--}}
{{--                                            'method' => 'post',--}}
{{--                                            'class' => 'form-horizontal',--}}
{{--                                            'id' => 'deliver-passport-store',--}}
{{--                                            'enctype' => 'multipart/form-data',--}}
{{--                                            'files' => 'true'--}}
{{--                                    ])!!}--}}

{{--                                    <div class="form-group row">--}}
{{--                                        <div class="col-md-8">--}}
{{--                                            {!! Form::label('', 'পাসপোর্ট নাম্বার সার্চ করুন', ['class' => '']) !!}--}}
{{--                                            <div style="display: flex; align-items: center;">--}}
{{--                                                {!! Form::text('passport_no', '', ['class' => 'form-control required', 'placeholder' => 'Enter', 'id' => '']) !!}--}}
{{--                                                <button type="submit" class="btn btn-default btn-sm" style="margin-left: 5px;color: #0F6849 !important;">--}}
{{--                                                    <i class="fa fa-plus-circle" style="color: #0F6849;"></i>&nbsp;Add Passport--}}
{{--                                                </button>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                    {!! Form::close() !!}--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="dash-content-main">
                <div class="dash-section-content">
                    <div class="dash-content-inner" id="stickerVisaList">
                        <div class="card card-magenta">
                            <div class="card-header" style="background-color: #D9EDF7 !important">
                                <div class="row">
                                    <div class="col-sm-6" style="color: black !important"> <i class="fa fa-star"></i> Voucher
                                        No: @if(isset($voucher_no)){{$voucher_no}}@endif
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
                                    <tbody id="view_delivery_list">
                                    @php $serialNumber = 1; @endphp
                                    @forelse($getReturnData as $data)
                                    <tr id="">
                                        <td class="align-content-center">{{ $serialNumber++ }}</td>
                                        <td class="align-content-center">@if(isset($data['go_serial_no'])){{$data['go_serial_no']}}@endif</td>
                                        <td class="align-content-center">@if(isset($data['pilgrim_tracking_no'])){{$data['pilgrim_tracking_no']}}@endif</td>
                                        <td class="align-content-center">@if(isset($data['passport_type'])){{$data['passport_type']}}@endif</td>
                                        <td class="align-content-center">@if(isset($data['passport_no'])){{$data['passport_no']}}@endif</td>
                                        <td class="align-content-center">@if(isset($data['passport_dob'])){{ date('d-M-Y', strtotime($data['passport_dob'])) }}@endif</td>
                                        <td class="align-content-center">
                                            @if(isset($data['pilgrim_id']))
                                                {!! Form::open([
                                                    'url' => url('go-passport/delivery/genarate-pdf/' .\App\Libraries\Encryption::encodeId($data['pilgrim_id']).'/' .($encoded_process_type_id)),
                                                    'method' => 'post',
                                                    'class' => 'form-horizontal',
                                                    'id' => 'single-delivery-passport',
                                                    'enctype' => 'multipart/form-data',
                                                    'files' => 'true',
                                                    'target' => '_blank',
                                                    ])
                                                !!}

                                                <button class="btn btn-primary print-button" type="submit">
                                                    <i class="fa fa-print" style="cursor: pointer"
                                                       data-toggle="tooltip" title=""
                                                       aria-describedby="tooltip"></i>
                                                </button>
                                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                <input type="hidden" name="flag" value="yes">
                                                {!! Form::close() !!}
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                        <tr id="noRecordRow">
                                            <td colspan="6" style="text-align: center">No Pilgrims Found</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                            @if(count($getReturnData)>0)
                                {!! Form::open([
                                    'url' => url('go-passport/delivery/genarate-pdf/' .($encodedId).'/'.($encoded_process_type_id)),
                                    'method' => 'post',
                                    'class' => 'form-horizontal',
                                    'id' => 'pdf-full',
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
                                        Delivery & Generate Slip
                                    </button>
                                </div>
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                <input type="hidden" name="flag" value="no">

                                {!! Form::close() !!}
                            @endif
                            <div class="float-right" style="padding-left: 1em;">
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

@endsection




