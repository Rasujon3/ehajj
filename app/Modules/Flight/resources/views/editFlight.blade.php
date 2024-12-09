<?php date_default_timezone_set("Asia/Dhaka"); ?>
@extends('layouts.admin')
@section('header-resources')
    <!-- DateTimePicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    {{-- CK editor --}}
    {{-- <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> --}}
@endsection

@section('content')
<style>
    /* Custom styles for datetimepicker buttons */
    .datetimepicker .btn-picker {
        background-color: #007bff;
        color: #fff;
        border: 1px solid #007bff;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 14px;
    }

    .datetimepicker .btn-picker:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>
    {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!}
    {!! Session::has('error') ? '<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!}

    <div class="dash-content-main">
        <div class="border-card-block">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <span class="title-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 26 26" fill="none">
                            <path d="M20.9041 15.0465L17.4667 11.3003L16.6981 10.473C16.5849 10.3345 16.5431 10.0856 16.6169 9.92139L17.8699 7.12973C18.2631 6.25391 18.0822 4.92313 17.4778 4.16954C17.2655 3.90984 16.9371 3.76242 16.6019 3.77642C15.6463 3.82975 14.5276 4.58816 14.1345 5.46397L12.8814 8.25563C12.8077 8.41985 12.594 8.5541 12.4153 8.56157L6.21182 8.46254C5.53059 8.44175 4.74181 8.95363 4.46334 9.574L3.92279 10.7782C3.57471 11.5537 3.9866 12.199 4.84655 12.2123L10.3017 12.2933C10.7272 12.2979 10.9331 12.6206 10.757 13.0129L10.3025 14.0255L9.56536 15.6677C9.47117 15.8775 9.21743 16.1253 9.00594 16.2058L5.93524 17.3814C5.61801 17.5021 5.32519 17.8858 5.28631 18.241L5.13992 19.6661C5.06253 20.278 5.55911 20.8078 6.17554 20.7776L9.16585 19.9276C9.62122 19.7922 10.1595 20.0338 10.361 20.4641L11.7132 23.2633C12.1045 23.7349 12.8263 23.7629 13.2503 23.3066L14.2178 22.2501C14.4533 21.9941 14.5495 21.5111 14.4288 21.1939L13.2664 18.1182C13.1769 17.9026 13.1934 17.5483 13.2876 17.3385L14.4792 14.6836C14.6553 14.2913 15.02 14.2358 15.3194 14.5456L19.0056 18.5678C19.5871 19.2015 20.3429 19.0804 20.691 18.305L21.2316 17.1007C21.51 16.4803 21.3683 15.5508 20.9041 15.0465Z" fill="white"/>
                        </svg>
                    </span>
                    <h3>Edit Flight</h3>
                </div>
            </div>
            <div class="bd-card-content">
                {!! Form::open(array('url' => 'flight/update-flight/'.Encryption::encodeId($returnData['flightDetails'][0]['id']),'method' => 'post', 'class' => 'form-horizontal', 'id' => 'flightForm',
            'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
                <div class="new-flight-form">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card-form-group">
                                <div class="card-form-block {{$errors->has('airlines') ? 'has-error' : ''}}">
                                    {!! Form::label('airlines','Airlines: ',['class'=>'required-star']) !!}
                                        {!! Form::select('airlines', $returnData['airlines'], $returnData['flightDetails'][0]['airlines_id'], ['class'=>'form-control required', 'data-rule-maxlength'=>'40']) !!}
                                        {!! $errors->first('airlines','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block {{$errors->has('flight_code') ? 'has-error' : ''}}">
                                    {!! Form::label('flight_code','Flight Code: ',['class'=>'required-star']) !!}
                                        {!! Form::text('flight_code', $returnData['flightDetails'][0]['flight_code'], ['class'=>'form-control required','placeholder'=>'Flight Code',
                                        'data-rule-maxlength'=>'40']) !!}
                                        {!! $errors->first('flight_code','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block {{$errors->has('flight_capacity') ? 'has-error' : ''}}">
                                    {!! Form::label('flight_capacity','Flight Capacity: ',['class'=>'required-star']) !!}
                                        {!! Form::text('flight_capacity', $returnData['flightDetails'][0]['flight_capacity'], ['class'=>'form-control number required input-sm','placeholder'=>'Flight Capacity',
                                        'data-rule-maxlength'=>'11']) !!}
                                        {!! $errors->first('flight_capacity','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block input-has-icon {{$errors->has('departure_time') ? 'has-error' : ''}}">
                                    {!! Form::label('departure_time','Departure Time: ',['class'=>'required-star']) !!}
                                    <div class="">
                                        {!! Form::text('departure_time', $returnData['flightDetails'][0]['departure_time'], ['class'=>'form-control bnEng required departure_time datetimepicker',
                                            'placeholder'=>'MM/DD/YYYY HH:MM', 'data-rule-maxlength'=>'20']) !!}
                                        <span class="form-input-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <g clip-path="url(#clip0_2139_2246)">
                                                    <path d="M14.75 2H14V1.25C14 1.05109 13.921 0.860322 13.7803 0.71967C13.6397 0.579018 13.4489 0.5 13.25 0.5C13.0511 0.5 12.8603 0.579018 12.7197 0.71967C12.579 0.860322 12.5 1.05109 12.5 1.25V2H6.5V1.25C6.5 1.05109 6.42098 0.860322 6.28033 0.71967C6.13968 0.579018 5.94891 0.5 5.75 0.5C5.55109 0.5 5.36032 0.579018 5.21967 0.71967C5.07902 0.860322 5 1.05109 5 1.25V2H4.25C3.2558 2.00119 2.30267 2.39666 1.59966 3.09966C0.896661 3.80267 0.501191 4.7558 0.5 5.75L0.5 14.75C0.501191 15.7442 0.896661 16.6973 1.59966 17.4003C2.30267 18.1033 3.2558 18.4988 4.25 18.5H14.75C15.7442 18.4988 16.6973 18.1033 17.4003 17.4003C18.1033 16.6973 18.4988 15.7442 18.5 14.75V5.75C18.4988 4.7558 18.1033 3.80267 17.4003 3.09966C16.6973 2.39666 15.7442 2.00119 14.75 2ZM2 5.75C2 5.15326 2.23705 4.58097 2.65901 4.15901C3.08097 3.73705 3.65326 3.5 4.25 3.5H14.75C15.3467 3.5 15.919 3.73705 16.341 4.15901C16.7629 4.58097 17 5.15326 17 5.75V6.5H2V5.75ZM14.75 17H4.25C3.65326 17 3.08097 16.7629 2.65901 16.341C2.23705 15.919 2 15.3467 2 14.75V8H17V14.75C17 15.3467 16.7629 15.919 16.341 16.341C15.919 16.7629 15.3467 17 14.75 17Z" fill="#474D49"/>
                                                    <path d="M9.5 12.875C10.1213 12.875 10.625 12.3713 10.625 11.75C10.625 11.1287 10.1213 10.625 9.5 10.625C8.87868 10.625 8.375 11.1287 8.375 11.75C8.375 12.3713 8.87868 12.875 9.5 12.875Z" fill="#474D49"/>
                                                    <path d="M5.75 12.875C6.37132 12.875 6.875 12.3713 6.875 11.75C6.875 11.1287 6.37132 10.625 5.75 10.625C5.12868 10.625 4.625 11.1287 4.625 11.75C4.625 12.3713 5.12868 12.875 5.75 12.875Z" fill="#374957"/>
                                                    <path d="M13.25 12.875C13.8713 12.875 14.375 12.3713 14.375 11.75C14.375 11.1287 13.8713 10.625 13.25 10.625C12.6287 10.625 12.125 11.1287 12.125 11.75C12.125 12.3713 12.6287 12.875 13.25 12.875Z" fill="#374957"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_2139_2246">
                                                        <rect width="18" height="18" fill="white" transform="translate(0.5 0.5)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <div class="card-form-block {{$errors->has('departure_city') ? 'has-error' : ''}}">
                                    {!! Form::label('departure_city','Departure City: ',['class'=>'required-star']) !!}
                                        {!! Form::select('departure_city', [], '', ['class'=>'form-control required',
                                        'data-rule-maxlength'=>'20', 'id'=> 'departure_city', 'placeholder' => 'Select One']) !!}
                                        {!! $errors->first('departure_city','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block input-has-icon {{$errors->has('pnl_time') ? 'has-error' : ''}}" id="pnl_div">
                                    {!! Form::label('pnl_time','PNL Time & Date: ') !!}
                                    {!! Form::text('pnl_time', $returnData['flightDetails'][0]['pnl_time'], ['class'=>'form-control bnEng datetimepicker',
                                        'placeholder'=>'MM/DD/YYYY HH:MM', 'data-rule-maxlength'=>'20']) !!}

                                    <span class="form-input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                            <g clip-path="url(#clip0_2139_2701)">
                                                <path d="M17.7498 8.75C17.5509 8.75 17.3601 8.82902 17.2195 8.96967C17.0788 9.11032 16.9998 9.30109 16.9998 9.5C17.0059 11.244 16.4061 12.9359 15.3029 14.2866C14.1998 15.6373 12.6617 16.563 10.9517 16.9054C9.24168 17.2477 7.46583 16.9856 5.92768 16.1636C4.38954 15.3417 3.1846 14.0111 2.51882 12.3993C1.85304 10.7874 1.76775 8.9943 2.27753 7.32651C2.78731 5.65871 3.86052 4.21974 5.3137 3.25555C6.76689 2.29136 8.50984 1.86182 10.2447 2.04033C11.9795 2.21884 13.5984 2.99433 14.8248 4.23425C14.8001 4.24098 14.7751 4.24624 14.7498 4.25H12.4998C12.3009 4.25 12.1101 4.32902 11.9695 4.46967C11.8288 4.61032 11.7498 4.80109 11.7498 5C11.7498 5.19891 11.8288 5.38968 11.9695 5.53033C12.1101 5.67098 12.3009 5.75 12.4998 5.75H14.7498C15.3465 5.75 15.9188 5.51295 16.3408 5.09099C16.7627 4.66903 16.9998 4.09674 16.9998 3.5V1.25C16.9998 1.05109 16.9208 0.860322 16.7801 0.71967C16.6395 0.579018 16.4487 0.5 16.2498 0.5C16.0509 0.5 15.8601 0.579018 15.7195 0.71967C15.5788 0.860322 15.4998 1.05109 15.4998 1.25V2.79875C13.9642 1.4259 12.004 0.621625 9.94668 0.520327C7.88938 0.419029 5.85963 1.02684 4.1966 2.2422C2.53358 3.45756 1.33802 5.20684 0.809711 7.19773C0.281404 9.18862 0.45235 11.3005 1.29398 13.1805C2.13562 15.0605 3.59696 16.5947 5.4338 17.5268C7.27065 18.4589 9.37174 18.7323 11.386 18.3014C13.4002 17.8706 15.2055 16.7615 16.5003 15.1595C17.7951 13.5576 18.5009 11.5598 18.4998 9.5C18.4998 9.30109 18.4208 9.11032 18.2801 8.96967C18.1395 8.82902 17.9487 8.75 17.7498 8.75Z" fill="#474D49"/>
                                                <path d="M9.5 5C9.30109 5 9.11032 5.07902 8.96967 5.21967C8.82902 5.36032 8.75 5.55109 8.75 5.75V9.5C8.75004 9.6989 8.82909 9.88963 8.96975 10.0303L11.2197 12.2803C11.3612 12.4169 11.5507 12.4925 11.7473 12.4908C11.9439 12.489 12.1321 12.4102 12.2711 12.2711C12.4102 12.1321 12.489 11.9439 12.4908 11.7473C12.4925 11.5507 12.4169 11.3612 12.2802 11.2198L10.25 9.1895V5.75C10.25 5.55109 10.171 5.36032 10.0303 5.21967C9.88968 5.07902 9.69891 5 9.5 5Z" fill="#474D49"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_2139_2701">
                                                    <rect width="18" height="18" fill="white" transform="translate(0.5 0.5)"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </span>
                                    {!! $errors->first('pnl_time','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block {{$errors->has('flight_status') ? 'has-error' : ''}}">
                                    {!! Form::label('flight_status','Flight Status: ',['class'=>'required-star']) !!}
                                        {!! Form::select('flight_status', $returnData['flight_status'], $returnData['flightDetails'][0]['status'], ['class'=>'form-control required',
                                        'data-rule-maxlength'=>'20', 'id'=> 'flight_status']) !!}
                                        {!! $errors->first('flight_status','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block input-has-icon hidden {{$errors->has('actual_departed_time') ? 'has-error' : ''}}" id="actual_departed_time_container">
                                    {!! Form::label('actual_departed_time','Actual Departed Time: ',['class'=>'required-star']) !!}
                                    <div class="">

                                       {!! Form::text('actual_departed_time', $returnData['flightDetails'][0]['departure_actual_time'], ['class'=>'form-control bnEng required datetimepicker',
                                       'placeholder'=>'MM/DD/YYYY HH:MM', 'data-rule-maxlength'=>'20']) !!}

                                        <span class="form-input-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <g clip-path="url(#clip0_2139_2701)">
                                                    <path d="M17.7498 8.75C17.5509 8.75 17.3601 8.82902 17.2195 8.96967C17.0788 9.11032 16.9998 9.30109 16.9998 9.5C17.0059 11.244 16.4061 12.9359 15.3029 14.2866C14.1998 15.6373 12.6617 16.563 10.9517 16.9054C9.24168 17.2477 7.46583 16.9856 5.92768 16.1636C4.38954 15.3417 3.1846 14.0111 2.51882 12.3993C1.85304 10.7874 1.76775 8.9943 2.27753 7.32651C2.78731 5.65871 3.86052 4.21974 5.3137 3.25555C6.76689 2.29136 8.50984 1.86182 10.2447 2.04033C11.9795 2.21884 13.5984 2.99433 14.8248 4.23425C14.8001 4.24098 14.7751 4.24624 14.7498 4.25H12.4998C12.3009 4.25 12.1101 4.32902 11.9695 4.46967C11.8288 4.61032 11.7498 4.80109 11.7498 5C11.7498 5.19891 11.8288 5.38968 11.9695 5.53033C12.1101 5.67098 12.3009 5.75 12.4998 5.75H14.7498C15.3465 5.75 15.9188 5.51295 16.3408 5.09099C16.7627 4.66903 16.9998 4.09674 16.9998 3.5V1.25C16.9998 1.05109 16.9208 0.860322 16.7801 0.71967C16.6395 0.579018 16.4487 0.5 16.2498 0.5C16.0509 0.5 15.8601 0.579018 15.7195 0.71967C15.5788 0.860322 15.4998 1.05109 15.4998 1.25V2.79875C13.9642 1.4259 12.004 0.621625 9.94668 0.520327C7.88938 0.419029 5.85963 1.02684 4.1966 2.2422C2.53358 3.45756 1.33802 5.20684 0.809711 7.19773C0.281404 9.18862 0.45235 11.3005 1.29398 13.1805C2.13562 15.0605 3.59696 16.5947 5.4338 17.5268C7.27065 18.4589 9.37174 18.7323 11.386 18.3014C13.4002 17.8706 15.2055 16.7615 16.5003 15.1595C17.7951 13.5576 18.5009 11.5598 18.4998 9.5C18.4998 9.30109 18.4208 9.11032 18.2801 8.96967C18.1395 8.82902 17.9487 8.75 17.7498 8.75Z" fill="#474D49"/>
                                                    <path d="M9.5 5C9.30109 5 9.11032 5.07902 8.96967 5.21967C8.82902 5.36032 8.75 5.55109 8.75 5.75V9.5C8.75004 9.6989 8.82909 9.88963 8.96975 10.0303L11.2197 12.2803C11.3612 12.4169 11.5507 12.4925 11.7473 12.4908C11.9439 12.489 12.1321 12.4102 12.2711 12.2711C12.4102 12.1321 12.489 11.9439 12.4908 11.7473C12.4925 11.5507 12.4169 11.3612 12.2802 11.2198L10.25 9.1895V5.75C10.25 5.55109 10.171 5.36032 10.0303 5.21967C9.88968 5.07902 9.69891 5 9.5 5Z" fill="#474D49"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_2139_2701">
                                                        <rect width="18" height="18" fill="white" transform="translate(0.5 0.5)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <div class="card-form-block hidden {{$errors->has('total_departed_pilgrims') ? 'has-error' : ''}}" id="total_departed_pilgrims_container">
                                    {!! Form::label('total_departed_pilgrims','Total Passenger Departed: ',['class'=>'required-star']) !!}
                                        {!! Form::text('total_departed_pilgrims', $returnData['flightDetails'][0]['total_passenger_departed'], ['class'=>'form-control required','placeholder'=>'Flight Code',
                                        'data-rule-maxlength'=>'40']) !!}
                                        {!! $errors->first('total_departed_pilgrims','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block input-has-icon hidden {{$errors->has('actual_arrival_time') ? 'has-error' : ''}}" id="actual_arrival_time_container">
                                    {!! Form::label('actual_arrival_time','Actual Arrival Time: ',['class'=>'required-star']) !!}
                                    <div class="">

                                        {!! Form::text('actual_arrival_time', $returnData['flightDetails'][0]['arrival_actual_time'], ['class'=>'form-control bnEng required datetimepicker',
                                            'placeholder'=>'MM/DD/YYYY HH:MM', 'data-rule-maxlength'=>'20']) !!}
                                        <span class="form-input-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <g clip-path="url(#clip0_2139_2701)">
                                                    <path d="M17.7498 8.75C17.5509 8.75 17.3601 8.82902 17.2195 8.96967C17.0788 9.11032 16.9998 9.30109 16.9998 9.5C17.0059 11.244 16.4061 12.9359 15.3029 14.2866C14.1998 15.6373 12.6617 16.563 10.9517 16.9054C9.24168 17.2477 7.46583 16.9856 5.92768 16.1636C4.38954 15.3417 3.1846 14.0111 2.51882 12.3993C1.85304 10.7874 1.76775 8.9943 2.27753 7.32651C2.78731 5.65871 3.86052 4.21974 5.3137 3.25555C6.76689 2.29136 8.50984 1.86182 10.2447 2.04033C11.9795 2.21884 13.5984 2.99433 14.8248 4.23425C14.8001 4.24098 14.7751 4.24624 14.7498 4.25H12.4998C12.3009 4.25 12.1101 4.32902 11.9695 4.46967C11.8288 4.61032 11.7498 4.80109 11.7498 5C11.7498 5.19891 11.8288 5.38968 11.9695 5.53033C12.1101 5.67098 12.3009 5.75 12.4998 5.75H14.7498C15.3465 5.75 15.9188 5.51295 16.3408 5.09099C16.7627 4.66903 16.9998 4.09674 16.9998 3.5V1.25C16.9998 1.05109 16.9208 0.860322 16.7801 0.71967C16.6395 0.579018 16.4487 0.5 16.2498 0.5C16.0509 0.5 15.8601 0.579018 15.7195 0.71967C15.5788 0.860322 15.4998 1.05109 15.4998 1.25V2.79875C13.9642 1.4259 12.004 0.621625 9.94668 0.520327C7.88938 0.419029 5.85963 1.02684 4.1966 2.2422C2.53358 3.45756 1.33802 5.20684 0.809711 7.19773C0.281404 9.18862 0.45235 11.3005 1.29398 13.1805C2.13562 15.0605 3.59696 16.5947 5.4338 17.5268C7.27065 18.4589 9.37174 18.7323 11.386 18.3014C13.4002 17.8706 15.2055 16.7615 16.5003 15.1595C17.7951 13.5576 18.5009 11.5598 18.4998 9.5C18.4998 9.30109 18.4208 9.11032 18.2801 8.96967C18.1395 8.82902 17.9487 8.75 17.7498 8.75Z" fill="#474D49"/>
                                                    <path d="M9.5 5C9.30109 5 9.11032 5.07902 8.96967 5.21967C8.82902 5.36032 8.75 5.55109 8.75 5.75V9.5C8.75004 9.6989 8.82909 9.88963 8.96975 10.0303L11.2197 12.2803C11.3612 12.4169 11.5507 12.4925 11.7473 12.4908C11.9439 12.489 12.1321 12.4102 12.2711 12.2711C12.4102 12.1321 12.489 11.9439 12.4908 11.7473C12.4925 11.5507 12.4169 11.3612 12.2802 11.2198L10.25 9.1895V5.75C10.25 5.55109 10.171 5.36032 10.0303 5.21967C9.88968 5.07902 9.69891 5 9.5 5Z" fill="#474D49"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_2139_2701">
                                                        <rect width="18" height="18" fill="white" transform="translate(0.5 0.5)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <div class="card-form-block hidden {{$errors->has('total_arrived_pilgrims') ? 'has-error' : ''}}" id="total_arrived_pilgrims_container">
                                    {!! Form::label('total_arrived_pilgrims','Total Passenger Arrived: ',['class'=>'required-star']) !!}
                                        {!! Form::text('total_arrived_pilgrims', $returnData['flightDetails'][0]['total_passenger_arrived'], ['class'=>'form-control required','placeholder'=>'Flight Code',
                                        'data-rule-maxlength'=>'40']) !!}
                                        {!! $errors->first('total_arrived_pilgrims','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block {{$errors->has('route_to_makkah') ? 'has-error' : ''}}" id="r2m">
                                    {!! Form::label('route_to_makkah','Route to Makkah: ',['class'=>'required-star']) !!}

                                    <span style="margin-right: 10px;">
                                        {!! Form::radio('route_to_makkah', 'Yes', ($returnData['flightDetails'][0]['rout_to_makkah'] == 'Yes'), ['class' => 'required', 'id' => 'makkah_yes']) !!} Yes
                                    </span>
                                    <span>
                                        {!! Form::radio('route_to_makkah', 'No', ($returnData['flightDetails'][0]['rout_to_makkah'] == 'No'), ['class' => 'required', 'id' => 'makkah_no']) !!} No
                                    </span>

                                    {!! $errors->first('route_to_makkah','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card-form-group">
                                <div class="card-form-block {{$errors->has('aircraft') ? 'has-error' : ''}}">
                                    {!! Form::label('aircraft','Aircraft: ',['class'=>'required-star']) !!}
                                        {!! Form::select('aircraft', $returnData['aircrafts'], $returnData['flightDetails'][0]['aircraft_id'], ['class'=>'form-control required', 'data-rule-maxlength'=>'20']) !!}
                                        {!! $errors->first('aircraft','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block {{$errors->has('type') ? 'has-error' : ''}}">
                                    {!! Form::label('type','Flight Type: ',['class'=>'required-star']) !!}
                                    {!! Form::select('type', $returnData['flight_type'], $returnData['flightDetails'][0]['type'], ['class'=>'form-control required',
                                    'data-rule-maxlength'=>'40', 'id' => 'type']) !!}
                                    {!! $errors->first('type','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block input-has-icon {{$errors->has('flight_duration') ? 'has-error' : ''}}">
                                    {!! Form::label('flight_duration','Flight Duration: ',['class'=>'required-star']) !!}
                                    {!! Form::text('flight_duration', $returnData['flightDetails'][0]['flight_duration'],['class'=>'form-control required input-sm', 'data-rule-maxlength'=>'40',
                                    'placeholder'=>'Select Time',]) !!}
                                    {!! $errors->first('flight_duration','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block input-has-icon {{$errors->has('arrival_time') ? 'has-error' : ''}}">
                                    {!! Form::label('arrival_time','Arrival Time: ',['class'=>'required-star']) !!}
                                    <div class="">
                                        {!! Form::text('arrival_time', $returnData['flightDetails'][0]['arrival_time'], ['class'=>'form-control bnEng required datetimepicker',
                                        'placeholder'=>'MM/DD/YYYY HH:MM', 'data-rule-maxlength'=>'20']) !!}
                                        <span class="form-input-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <g clip-path="url(#clip0_2139_2701)">
                                                    <path d="M17.7498 8.75C17.5509 8.75 17.3601 8.82902 17.2195 8.96967C17.0788 9.11032 16.9998 9.30109 16.9998 9.5C17.0059 11.244 16.4061 12.9359 15.3029 14.2866C14.1998 15.6373 12.6617 16.563 10.9517 16.9054C9.24168 17.2477 7.46583 16.9856 5.92768 16.1636C4.38954 15.3417 3.1846 14.0111 2.51882 12.3993C1.85304 10.7874 1.76775 8.9943 2.27753 7.32651C2.78731 5.65871 3.86052 4.21974 5.3137 3.25555C6.76689 2.29136 8.50984 1.86182 10.2447 2.04033C11.9795 2.21884 13.5984 2.99433 14.8248 4.23425C14.8001 4.24098 14.7751 4.24624 14.7498 4.25H12.4998C12.3009 4.25 12.1101 4.32902 11.9695 4.46967C11.8288 4.61032 11.7498 4.80109 11.7498 5C11.7498 5.19891 11.8288 5.38968 11.9695 5.53033C12.1101 5.67098 12.3009 5.75 12.4998 5.75H14.7498C15.3465 5.75 15.9188 5.51295 16.3408 5.09099C16.7627 4.66903 16.9998 4.09674 16.9998 3.5V1.25C16.9998 1.05109 16.9208 0.860322 16.7801 0.71967C16.6395 0.579018 16.4487 0.5 16.2498 0.5C16.0509 0.5 15.8601 0.579018 15.7195 0.71967C15.5788 0.860322 15.4998 1.05109 15.4998 1.25V2.79875C13.9642 1.4259 12.004 0.621625 9.94668 0.520327C7.88938 0.419029 5.85963 1.02684 4.1966 2.2422C2.53358 3.45756 1.33802 5.20684 0.809711 7.19773C0.281404 9.18862 0.45235 11.3005 1.29398 13.1805C2.13562 15.0605 3.59696 16.5947 5.4338 17.5268C7.27065 18.4589 9.37174 18.7323 11.386 18.3014C13.4002 17.8706 15.2055 16.7615 16.5003 15.1595C17.7951 13.5576 18.5009 11.5598 18.4998 9.5C18.4998 9.30109 18.4208 9.11032 18.2801 8.96967C18.1395 8.82902 17.9487 8.75 17.7498 8.75Z" fill="#474D49"/>
                                                    <path d="M9.5 5C9.30109 5 9.11032 5.07902 8.96967 5.21967C8.82902 5.36032 8.75 5.55109 8.75 5.75V9.5C8.75004 9.6989 8.82909 9.88963 8.96975 10.0303L11.2197 12.2803C11.3612 12.4169 11.5507 12.4925 11.7473 12.4908C11.9439 12.489 12.1321 12.4102 12.2711 12.2711C12.4102 12.1321 12.489 11.9439 12.4908 11.7473C12.4925 11.5507 12.4169 11.3612 12.2802 11.2198L10.25 9.1895V5.75C10.25 5.55109 10.171 5.36032 10.0303 5.21967C9.88968 5.07902 9.69891 5 9.5 5Z" fill="#474D49"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_2139_2701">
                                                        <rect width="18" height="18" fill="white" transform="translate(0.5 0.5)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <div class="card-form-block {{$errors->has('arrival_city') ? 'has-error' : ''}}">
                                    {!! Form::label('arrival_city','Arrival city: ',['class'=>'required-star']) !!}
                                    {!! Form::select('arrival_city', [], '', ['class'=>'form-control required',
                                     'data-rule-maxlength'=>'20', 'placeholder' => 'Select One']) !!}
                                    {!! $errors->first('arrival_city','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block {{$errors->has('gaca_reservation_no') ? 'has-error' : ''}}">
                                    {!! Form::label('gaca_reservation_no','GACA Reservation No: ') !!}
                                    {!! Form::text('gaca_reservation_no', $returnData['flightDetails'][0]['gaca_reservation_no'], ['class'=>'form-control text','placeholder'=>'Enter GACA No',
                                    'data-rule-maxlength'=>'40']) !!}
                                    {!! $errors->first('gaca_reservation_no','<span class="help-block">:message</span>') !!}
                                </div>

                                <div class="card-form-block {{$errors->has('pilgrim_type') ? 'has-error' : ''}}">
                                    {!! Form::label('pilgrim_type','Pilgrim Type: ',['class'=>'required-star']) !!}
                                    {!! Form::select('pilgrim_type', $returnData['pilgrim_type'], $returnData['flightDetails'][0]['is_ballottee'], ['class'=>'form-control required',
                                    'data-rule-maxlength'=>'20', 'id'=> 'pilgrim_type']) !!}
                                    {!! $errors->first('pilgrim_type','<span class="help-block">:message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 {{$errors->has('description') ? 'has-error' : ''}}">
                            <div class="form-group">
                                {!! Form::label('description','Remarks: ',['class'=>'']) !!}
                                {!! Form::textarea('description', $returnData['flightDetails'][0]['description'], ['class'=>'form-control','placeholder'=>'Enter the remarks (if any)',
                                'data-rule-maxlength'=>'255', 'size' => '30x5']) !!}
                                {!! $errors->first('description','<span class="help-block">:message</span>') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bd-card-footer">
                <div class="flex-space-btw info-btn-group">
                    <a href="{{ url('/flight/flight-details/'.Encryption::encodeId($returnData['flightDetails'][0]['id'])) }}">
                        {!! Form::button('Close', array('type' => 'button', 'class' => 'btn btn-default')) !!}
                    </a>
                    <button type="submit" class="btn btn-green"><span>Update</span></button>
                </div>
            </div>
            {!! Form::close() !!}<!-- /.form end -->
        </div>
    </div>
@endsection

@section('footer-script')
    <script src="{{ asset("assets/scripts/moment.js") }}"></script>
    <script src="{{ asset("assets/scripts/jquery.validate.min.js") }}"></script>
    <!-- DateTimePicker JS -->
    <script src="{{ asset("assets/scripts/bootstrap-datetimepicker.js") }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            const departureCity = `{{ $returnData['flightDetails'][0]['departure_city_id'] }}`;
            const arrivalCity = `{{ $returnData['flightDetails'][0]['arrival_city_id'] }}`;

            var today = new Date();
            var yyyy = today.getFullYear();
            var mm = today.getMonth() + 1;
            var dd = today.getDate();

            $('.datetimepicker').datetimepicker({
                viewMode: 'months',
                sideBySide: true,
                format: 'MM/DD/YYYY HH:mm',
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

            $("#flightForm").validate({
                errorPlacement: function () {
                    return false;
                }
            });

            $('#airlines').change(function (e) {
                if (this.value == '3') {
                    $('#airlines_other_div').removeClass('hidden');
                    $('#airlines_other').addClass('required');
                }
                else {
                    $('#airlines_other_div').addClass('hidden');
                    $('#airlines_other').removeClass('required');
                }
            });
            $('#airlines').trigger('change');

            $('#flight_status').on('change', function(){
                if(this.value == 'departed') {
                    $('#actual_departed_time_container').removeClass('hidden');
                    $('#total_departed_pilgrims_container').removeClass('hidden');
                    $('#actual_departed_time').addClass('required');
                    $('#total_departed_pilgrims').addClass('required');

                    $('#actual_arrival_time_container').addClass('hidden');
                    $('#total_arrived_pilgrims_container').addClass('hidden');
                    $('#actual_arrival_time').removeClass('required');
                    $('#total_arrived_pilgrims').removeClass('required');
                } else if(this.value == 'arrived') {
                    $('#actual_arrival_time_container').removeClass('hidden');
                    $('#total_arrived_pilgrims_container').removeClass('hidden');
                    $('#actual_arrival_time').addClass('required');
                    $('#total_arrived_pilgrims').addClass('required');

                    $('#actual_departed_time_container').addClass('hidden');
                    $('#total_departed_pilgrims_container').addClass('hidden');
                    $('#actual_departed_time').removeClass('required');
                    $('#total_departed_pilgrims').removeClass('required');
                } else {
                    $('#actual_departed_time_container').addClass('hidden');
                    $('#total_departed_pilgrims_container').addClass('hidden');
                    $('#actual_departed_time').removeClass('required');
                    $('#total_departed_pilgrims').removeClass('required');

                    $('#actual_arrival_time_container').addClass('hidden');
                    $('#total_arrived_pilgrims_container').addClass('hidden');
                    $('#actual_arrival_time').removeClass('required');
                    $('#total_arrived_pilgrims').removeClass('required');
                }
            });

            $('#flight_status').trigger('change');


            // CKEDITOR.replace('description');

            $('#description').on('input', function() {
                var lines = $(this).val().split("\n").length;

                if(lines > 10) {
                    $(this).attr('rows', 10);
                } else if (lines > 5) {
                    $(this).attr('rows', lines);
                } else {
                    $(this).attr('rows', 5);
                }
            });

            $('#description').trigger('input');

            $('#type').on('change', function() {
                if (this.value == 'arrival') {
                    $('#r2m').addClass('hidden');
                    $('#pnl_div').addClass('hidden');
                    $('#makkah_yes').removeClass('required');
                    $('#makkah_no').removeClass('required');
                }else{
                    $('#r2m').removeClass('hidden');
                    $('#pnl_div').removeClass('hidden');
                    $('#makkah_yes').addClass('required');
                    $('#makkah_no').addClass('required');
                }
            });

            $('#type').on('change', function() {
                const flightType = $(this).val();
                $(this).after('<span class="loading">Loading...</span>');
                $.ajax({
                    url: "{{url('/flight/get-flight-citys')}}",
                    type: 'GET',
                    data: {flightType: flightType},
                    success: function(response) {
                        if(response.responseCode == 1) {
                            const departureCitys = response.data.departureCitys;
                            const arraivalCitys = response.data.arraivalCitys;
                            let departureOption = '';
                            let arraivalOption = '';
                            for(let i in departureCitys) {
                                if(departureCity == i) {
                                    departureOption += `<option value="${i}" selected>${departureCitys[i]}</option>`;
                                } else
                                departureOption += `<option value="${i}">${departureCitys[i]}</option>`;
                            }
                            for(let i in arraivalCitys) {
                                if(arrivalCity == i) {
                                    arraivalOption += `<option value="${i}" selected>${arraivalCitys[i]}</option>`;
                                } else
                                arraivalOption += `<option value="${i}">${arraivalCitys[i]}</option>`;
                            }
                            $('#departure_city').html(departureOption);
                            $('#arrival_city').html(arraivalOption);
                        }
                        $('#type').next().hide();
                    }
                });
            });
            $('#type').trigger('change');
        });
    </script>
@endsection
