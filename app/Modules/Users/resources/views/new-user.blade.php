<?php
$accessMode = ACL::getAccsessRight('user');
if (!ACL::isAllowed($accessMode, 'V')) {
    abort('400', 'You have no access right!. Contact with system admin for more information.');
}

$nationality_type = session('nationality_type');
$identity_type = session('identity_type');


?>

@extends('layouts.admin')

@section('header-resources')
    <link rel="stylesheet" href="{{ asset('assets/plugins/intlTelInput/css/intlTelInput.min.css') }}" />
{{--    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>--}}
    <link rel="stylesheet" href="{{ asset("assets/plugins/datepicker-oss/css/bootstrap-datetimepicker.min.css") }}" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">

@endsection

@section('content')
    @include('partials.messages')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><strong><i class="fa fa-user-plus" aria-hidden="true"></i>
                            {{ trans('Users::messages.new_user_form_title') }}</strong>
                    </h5>
                </div>

                {!! Form::open(['url' => '/users/store-new-user', 'method' => 'patch', 'class' => 'form-horizontal', 'id' => 'create_user_form', 'enctype' => 'multipart/form-data', 'files' => 'true']) !!}

                <div class="card-body">




                    @if ($identity_type == 'nid')
                        <div id="NIDInfoArea">

                            <div class="row">



                            </div>

                        </div>
                    @elseif($identity_type == 'tin')
                        <div id="ETINInfoArea">

                            <div class="row">


                                <div class="form-group col-md-6">
                                    <div class="row">
                                        {!! Form::label('user_DOB', trans('Users::messages.user_dob'), ['class' => 'col-md-4 col-form-label']) !!}
                                        <div class="col-md-8">
                                            <div class="input-group-append">
                                                {!! Form::text('user_DOB',null, ['class' => 'form-control required datepicker', 'placeholder' => 'Pick from calender', !empty(Request::segment(3)) ? (Encryption::decodeId(Request::segment(3)) == 'skip' ? '' : 'readonly') : 'readonly']) !!}
                                                <span class="input-group-text" id="basic-addon2"><i
                                                        class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                        {!! $errors->first('user_DOB', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                    @elseif($identity_type == 'passport')
                        <div id="PassportInfoArea">
                            <div class="row">


                                <div class="form-group col-md-6">
                                    <div class="row">
                                        {!! Form::label('user_DOB', trans('Users::messages.user_dob'), ['class' => 'col-md-4 col-form-label']) !!}
                                        <div class="col-md-8">
                                            <div class="input-group-append">
                                                {!! Form::text('user_DOB',null, ['class' => 'form-control required datepicker', 'placeholder' => 'Pick from calender', !empty(Request::segment(3)) ? (Encryption::decodeId(Request::segment(3)) == 'skip' ? '' : 'readonly') : 'readonly']) !!}
                                                <span class="input-group-text" id="basic-addon2"><i
                                                        class="fa fa-calendar"></i></span>
                                            </div>
                                        </div>
                                        {!! $errors->first('user_DOB', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-4 required-star">First Name</label>
                                <div class="col-md-8">
                                    <div class="input-group ">
                                        {!! Form::text('user_first_name', null, $attributes = ['class' => 'form-control required', 'placeholder' => 'Enter First Name', 'id' => 'user_first_name']) !!}
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2"><i
                                                    class="fa fa-user"></i></span>
                                        </div>
                                    </div>
                                    {!! $errors->first('user_first_name', '<span class="text-danger">:message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('user_gender', trans('Users::messages.user_gender'), ['class' => 'text-left required-star col-md-4', 'id' => 'user_gender']) !!}
                                <div class="col-sm-8">
                                    <label class="identity_hover">
                                        {!! Form::radio('user_gender', 'Male', true , ['class' => 'required', 'id' => 'user_gender_male']) !!}
                                        Male
                                    </label>
                                    &nbsp;&nbsp;
                                    <label class="identity_hover">
                                        {!! Form::radio('user_gender', 'Female', false, ['class' => 'required', 'id' => 'user_gender_female']) !!}
                                        Female
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label
                                    class="col-md-4 required-star">Middle Name</label>
                                <div class="col-md-8">
                                    <div class="input-group ">
                                        {!! Form::text('user_middle_name', $value = null, $attributes = ['class' => 'form-control required ', 'data-rule-maxlength' => '200', 'placeholder' => 'Enter Middle Name', 'id' => 'user_middle_name']) !!}

                                    </div>
                                    {!! $errors->first('user_middle_name', '<span class="text-danger">:message</span>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-4 required-star">{{ trans('Users::messages.user_mobile') }}</label>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        {!! Form::text('user_mobile',null, $attributes = ['class' => 'form-control required bd_mobile', 'placeholder' => 'Enter your Number', 'id' => 'user_mobile']) !!}
                                    </div>
                                    {!! $errors->first('user_mobile', '<span class="text-danger">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @if ($logged_user_type == '1x101' || $logged_user_type == '8x808')
                            {{-- For System Admin & IT cell --}}
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label
                                        class="col-md-4 required-star">Last Name</label>
                                    <div class="col-md-8">
                                        <div class="input-group ">
                                            {!! Form::text('user_last_name', $value = null, $attributes = ['class' => 'form-control required ', 'data-rule-maxlength' => '200', 'placeholder' => 'Enter Last Name', 'id' => 'user_last_name']) !!}

                                        </div>
                                        {!! $errors->first('user_last_name', '<span class="text-danger">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-6">
                            <div
                                class="form-group row has-feedback {{ $errors->has('user_email') ? 'needs-validation' : '' }}">
                                <label class="col-md-4 required-star">Email Address</label>
                                <div class="col-sm-8">
                                    <div class="input-group ">
                                        {!! Form::text('user_email', $value = null, $attributes = ['class' => 'form-control email required', 'data-rule-maxlength' => '40', 'placeholder' => 'Enter your Email Address', 'id' => 'user_email']) !!}
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon2"><i
                                                    class="fa fa-envelope"></i></span>
                                        </div>
                                    </div>
                                    {!! $errors->first('user_email', '<span class="text-danger">:message</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            @if ($logged_user_type == '1x101' || $logged_user_type == '8x808')
                                {{-- For System Admin & IT cell --}}
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label
                                            class="col-md-4 required-star">{{ trans('Users::messages.user_type') }}</label>
                                        <div class="col-md-8">
                                            {!! Form::select('user_type', $user_types, '', $attributes = ['class' => 'form-control required', 'data-rule-maxlength' => '40', 'placeholder' => 'Select One', 'id' => 'user_type']) !!}
                                            {!! $errors->first('user_type', '<span class="text-danger">:message</span>') !!}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <div class="form-group row has-feedback {{ $errors->has('user_nid') ? 'needs-validation' : '' }}">
                                    <label class="col-md-4 ">National ID No.</label>
                                    <div class="col-sm-8">
                                        <div class="input-group ">
                                            {!! Form::number('user_nid', $value = null, $attributes = ['class' => 'form-control  ', 'data-rule-maxlength' => '40', 'placeholder' => 'Enter your NID (if any)', 'id' => 'user_nid']) !!}

                                        </div>
                                        {!! $errors->first('user_nid', '<span class="text-danger">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">

                            <div class="form-group col-md-6">
                                <div class="row">
                                    {!! Form::label('user_DOB', trans('Users::messages.user_dob'), ['class' => 'col-md-4 col-form-label']) !!}
                                    <div class="col-md-8">
                                        <div class="input-group-append">
                                            {!! Form::text('user_DOB',null, ['class' => 'form-control required datepicker', 'placeholder' => 'Pick from calender', ''  ]) !!}
                                            <span class="input-group-text" id="basic-addon2"><i
                                                    class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>
                                    {!! $errors->first('user_DOB', '<span class="help-block">:message</span>') !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row has-feedback {{ $errors->has('contact_address') ? 'needs-validation' : '' }}">
                                    <label class="col-md-4 ">Address</label>
                                    <div class="col-sm-8">
                                        <div class="input-group ">
                                            {!! Form::text('contact_address', $value = null, $attributes = ['class' => 'form-control  ', 'placeholder' => 'Enter your Address', 'id' => 'contact_address']) !!}

                                        </div>
                                        {!! $errors->first('contact_address', '<span class="text-danger">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>




                    <div class="row">

                        <div class="col-md-1"></div>



                    </div>

                </div>

                <div class="card-footer">
                    <div class="float-left">
                        <a href="{{ url('users/lists') }}" class="btn btn-default btn-sm"><i class="fa fa-times"></i>
                            <b>Close</b></a>
                    </div>
                    <div class="float-right">
                        @if (ACL::getAccsessRight('user', 'A'))
                            <button type="submit" class="btn btn-block btn-sm btn-primary" id="submit"><b>Submit</b>
                            </button>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <!--/panel-body-->
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset("assets/scripts/moment.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/datepicker-oss/js/bootstrap-datetimepicker.js") }}"></script>
    <script src="{{ asset("assets/scripts/jquery.validate.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/intlTelInput/js/intlTelInput.js") }}"  type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/intlTelInput/js/utils.js") }}"  type="text/javascript"></script>
@include('partials.image-upload')

    <script>

                        $('.datepicker').datetimepicker({
                           viewMode: 'years',
                           format: 'DD-MMM-YYYY',
                                });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#submit').click(function() {
            var _token = $('input[name="_token"]').val();
            $("#create_user_form").validate({
                errorPlacement: function() {
                    return false;
                },
                submitHandler: function(form) { // <- pass 'form' argument in
                    $("#submit").attr("disabled", true);
                    form.submit(); // <- use 'form' argument here.
                }
            });
        })

        // remove laravel error message start
        @if ($errors->any()) $('form input[type=text]').on('keyup', function (e) {
            if ($(this).val() && e.which != 32) {

                if ($(this).parent().parent().hasClass('has-error')) {
                    $(this).parent().parent().removeClass('has-error');
                    $(this).siblings(".help-block").hide();
                } else {
                    $(this).parent().parent().parent().removeClass('has-error');
                    $(this).parent().siblings(".help-block").hide();
                }

            }
        });

        $('form select').on('change', function (e) {
            if ($(this).val()) {
                $(this).siblings(".help-block").hide();
                $(this).parent().parent().removeClass('has-error');
            }
        }); @endif
        // remove laravel error message end


        /**
         * Convert an image to a base64 url
         * @param  {String}   url
         * @param  {String}   [outputFormat=image/png]
         */
        function convertImageToBase64(img, outputFormat) {
            var originalWidth = img.style.width;
            var originalHeight = img.style.height;

            img.style.width = "auto";
            img.style.height = "auto";
            img.crossOrigin = "Anonymous";

            var canvas = document.createElement("canvas");

            canvas.width = img.width;
            canvas.height = img.height;

            var ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0);

            img.style.width = originalWidth;
            img.style.height = originalHeight;

            // Get the data-URL formatted image
            // Firefox supports PNG and JPEG. You could check img.src to
            // guess the original format, but be aware the using "image/jpg"
            // will re-encode the image.
            var dataUrl = canvas.toDataURL(outputFormat);

            //return dataURL.replace(/^data:image\/(png|jpg);base64,/, "");
            return dataUrl;
        }

        function convertImageUrlToBase64(url, callback, outputFormat) {
            var img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function() {
                callback(convertImageToBase64(this, outputFormat));
            };
            img.src = url;
        }


        // Convert NID image URL to base64 format
        var user_image = $("#user_pic_preview").attr('src');
        convertImageUrlToBase64(user_image, function(url) {
            $("#user_pic_base64").val(url);
        });
    </script>
@endsection
