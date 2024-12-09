@extends('public_home.front')
<?php
$userData = session('oauth_data');
$type= ['' => 'Select One'] + ['1' => 'Desk User', '2' => 'Agency User'];
?>

@section('header-resources')
    <link rel="stylesheet" href="{{ asset("assets/plugins/intlTelInput/css/intlTelInput.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/plugins/datepicker-oss/css/bootstrap-datetimepicker.min.css") }}" />
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset("assets/custom/css/custom-style.css") }}" />
    <link rel="stylesheet" href="{{ asset("assets/custom/css/custom-responsive.css") }}" />
@endsection
<style>
    .site-header nav{
        margin: 0px;
    }
    #navbarCollapse ul{
        float: right;
    }

</style>
@section('body')
    <div class="ëhajj-signup-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="ehajj-signup-content">
                        <div class="ehajj-form-title">
                            <h3 class="text-center"> Sign Up Process</h3>
                        </div>
                        @if(Session::has('success'))
                            <div class="alert alert-info">
                                {!!Session::get('success') !!}
                                {!! link_to('signup/resend-mail?tmp='.Input::get('tmp'), 'Resend Verification Email', array('class' => 'btn btn-primary btn-sm')) !!}
                            </div>
                        @endif
                        @if(Session::has('verifyNo'))
                            <div class="alert alert-warning text-center">
                                {{ Session::get('verifyNo') }}
                                {!! link_to('signup/resend-mail?tmp='.Input::get('tmp'), 'Resend Email', array('class' => 'btn btn-primary btn-sm')) !!}
                            </div>
                        @endif
                        @if(Session::has('verifyYes'))
                            <div class="alert alert-danger text-center">
                                {{ Session::get('verifyYes') }}
                                {!! link_to('forget-password', 'Recover Password', array('class' => 'btn btn-primary btn-sm')) !!}
                            </div>
                        @endif
                        @if(Session::has('error'))
                            <div class="alert alert-warning">
                                {{ Session::get('error') }}
                            </div>
                        @endif
                        <div style="height: 3vh;"></div>
                        <div class="form-content">
                            {!! Form::open(array('url' => url('/client/signup/identity-verify'),'method' => 'post', 'class' => 'form-horizontal', 'id' => 'OSSSignUpForm',
                            'enctype' =>'multipart/form-data', 'files' => 'true')) !!}
                            <input type="hidden" name="from_prp" value="{{ (!empty($userData->from_prp)) ? $userData->from_prp : 0 }}">

                            <fieldset>
                                <div class="form-block has-feedback {{ $errors->has('user_first_name') ? 'has-error' : ''}}">
                                    <label  class="form-label-left text-left required-star">User Name</label>
                                    <div class="form-input">
                                        {!! Form::text('user_first_name', ($userData) ? $userData->user_full_name : '', $attributes = array('class'=>'form-control  required', 'data-rule-maxlength'=>'40',
                                        'placeholder'=>'Enter your Name', 'id'=>"user_first_name")) !!}

                                        {!! $errors->first('user_first_name','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>

                                <div class="form-block has-feedback {{ $errors->has('designation') ? 'has-error' : ''}}">
                                    <label  class="form-label-left text-left required-star">Designation</label>
                                    <div class="form-input">
                                        {!! Form::text('designation', '', $attributes = array('class'=>'form-control  required', 'data-rule-maxlength'=>'40',
                                        'placeholder'=>'Enter your designation', 'id'=>"designation")) !!}

                                        {!! $errors->first('designation','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>

                                <div class="form-block has-feedback {{ $errors->has('user_nid') ? 'has-error' : ''}}">
                                    <label  class="form-label-left text-left">NID (if any)</label>
                                    <div class="form-input">
                                        {!! Form::number('user_nid', (!empty($userData->user_nid)) ? $userData->user_nid : '', $attributes = array('class'=>'form-control ', 'data-rule-maxlength'=>'40',
                                        'placeholder'=>'Enter your NID', 'id'=>"user_nid")) !!}

                                        {!! $errors->first('user_nid','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>


                                <div class="form-block has-feedback {{$errors->has('user_DOB') ? 'has-error' : ''}}">
                                    {!! Form::label('user_DOB','Date of Birth',['class'=>'form-label-left text-left required-star']) !!}
                                    <div class="form-input">
                                        <div class=" input-group date datepicker" data-date="12-03-2015" data-date-format="dd-mm-yyyy">
{{--                                            {!! Form::text('user_DOB', (!empty($userData->user_DOB)) ? date('d-M-Y', strtotime($userData->user_DOB)) : '', ['class'=>'form-control datepicker required', 'placeholder' => 'Pick a date', 'data-rule-maxlength'=>'40']) !!}--}}
{{--                                            <span class="input-group-addon ehajj-icon-calender"></span>--}}
{{--                                            {!! $errors->first('user_DOB','<span class="help-block">:message</span>') !!}--}}
                                            <input type="date" id="user_DOB" name="user_DOB" value="<?php echo (!empty($userData->user_DOB)) ? date('Y-m-d', strtotime($userData->user_DOB)) : '';?>" class="form-control datepicker" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-block has-feedback {{ $errors->has('district') ? 'has-error' : ''}}">
                                    <label class="form-label-left text-left required-star">District</label>
                                    <div class="form-input">
                                        {!! Form::select('district', $districts, (!empty($userData->sess_district)) ? $userData->sess_district : '', $attributes = array('class'=>'form-control required',
                                        'id'=>"district")) !!}

                                    </div>
                                </div>

                                <div class="form-block has-feedback {{ $errors->has('thana') ? 'has-error' : ''}}">
                                    <label class="form-label-left text-left required-star">{!! trans('messages.thana') !!}</label>
                                    <div class="form-input">
                                        {!! Form::select('thana', [], (!empty($userData->sess_thana)) ? $userData->sess_thana : '', $attributes = array('class'=>'form-control required',
                                        'placeholder' => 'Select One', 'id'=>"thana")) !!}

                                    </div>
                                </div>

                                <div class="form-block has-feedback {{ $errors->has('user_mobile') ? 'has-error' : ''}}">
                                    <label  class="form-label-left text-left required-star">Mobile Number</label>
                                    <div class="form-input">
                                        {!! Form::text('user_mobile', ($userData) ? $userData->mobile : '', $attributes = array('class'=>'form-control  phone required',
                                        'maxlength'=>"20", 'data-rule-maxlength'=>'40', 'placeholder'=>'Enter your Mobile Number','id'=>"user_mobile")) !!}
                                        <span class="text-danger mobile_number_error"></span>

                                        {!! $errors->first('user_mobile','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>

                                <div class="form-block has-feedback {{ $errors->has('user_email') ? 'has-error' : ''}}">
                                    <label  class="form-label-left text-left  required-star">Email Address</label>
                                    <div class="form-input">
                                        {!! Form::text('user_email', ($userData) ? $userData->user_email : '', $attributes = array('class'=>'form-control email required', 'readonly', 'data-rule-maxlength'=>'40',
                                        'placeholder'=>'Enter your Email Address','id'=>"user_email")) !!}

                                        {!! $errors->first('user_email','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>

                                <div class="form-block has-feedback {{$errors->has('user_gender') ? 'has-error': ''}}">
                                    {!! Form::label('user_gender','Gender',['class'=>'text-left required-star form-label-left', 'id' => 'user_gender']) !!}
                                    <div class="form-input">
                                        <div class="bs_radio">
                                            {!! Form::radio('user_gender', 'Male', true, ['class'=>'required','id'=>'user_gender_male']) !!}
                                            <label for="user_gender_male">Male</label>
                                        </div>
                                        <div class="bs_radio">
                                            {!! Form::radio('user_gender', 'Female', false, ['class'=>'required', 'id'=>'user_gender_female']) !!}
                                            <label for="user_gender_female">Female</label>
                                        </div>
                                    </div>
                                </div>



    {{--                            <div class="form-group pull-right  {{$errors->has('g-recaptcha-response') ? 'has-error' : ''}}">--}}
    {{--                                <div class="col-md-12">--}}
    {{--                                    {!! Recaptcha::render() !!}--}}
    {{--                                    {!! $errors->first('g-recaptcha-response','<span class="help-block">:message</span>') !!}--}}
    {{--                                </div>--}}
    {{--                            </div>--}}

                                <div class='clearfix'></div>
                                <div class="form-block">
                                    <div class="form-label-left"></div>
                                    <div class="form-input">
                                        <button type="submit" class="btn btn-block btn-accenct" ><b>Submit</b></button>
                                    </div>
                                </div>


                            </fieldset>

                            {!! Form::close() !!}
                            <div class="clearfix"></div>
                        </div>



                        <div class="clearfix">

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

<!--<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>-->

@section('footer-script')
    <!--<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>-->
    <!-- Location picker -->
    <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>

    <script type="text/javascript" src='https://maps.google.com/maps/api/js?key={{config('services.google_map.cliend_id')}}&libraries=places'></script>

    <script src="{{ asset("assets/scripts/moment.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/datepicker-oss/js/bootstrap-datetimepicker.js") }}"></script>
    <script src="{{ asset("assets/scripts/jquery.validate.min.js") }}"></script>


    <script>

        function updateControls(addressComponents) {
            $('#road_no').val(addressComponents.addressLine1);
            // $('#house_no').val(addressComponents.addressLine1);
            $('#state').val(addressComponents.city);
            $('#province').val(addressComponents.stateOrProvince);
            $('#post_code').val(addressComponents.postalCode);
            $("#country option[value=" + addressComponents.country +"]").attr("selected","selected");
        }



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        $(document).ready(function () {

            $(function () {
                var _token = $('input[name="_token"]').val();
                $("#OSSSignUpForm").validate({
                    errorPlacement: function () {
                        return false;
                    }
                });
            });
            // var today = new Date();
            // var yyyy = today.getFullYear();
            // $('.datepicker').datetimepicker({
            //     viewMode: 'years',
            //     format: 'DD-MMM-YYYY',
            //     // maxDate: (new Date()),
            //     // minDate: '01/01/' + (yyyy - 70)
            // });



            // get district by dstrictID
            $("#district").change(function () {
                var self = $(this);
                var districtId = $('#district').val();
                if (districtId !== '') {
                    $(this).after('<span class="loading_data">Loading...</span>');
                    $("#loaderImg").html("<img style='margin-top: -15px;' src='<?php echo url('/public/assets/images/ajax-loader.gif'); ?>' alt='loading' />");
                    $.ajax({
                        type: "GET",
                        url: "<?php echo url('/users/get-thana-by-district-id'); ?>",
                        data: {
                            districtId: districtId
                        },
                        success: function (response) {
                            var option = '<option value="">Select One</option>';
                            if (response.responseCode == 1) {
                                $.each(response.data, function (id, value) {
                                        if (id.trim() == '{{ $userData->sess_thana??'' }}') {
                                            option += '<option value="' + id.trim() + '" selected>' + value + '</option>';
                                        } else {
                                            option += '<option value="' + id.trim() + '">' + value + '</option>';
                                        }

                                });
                            }
                            $("#thana").html(option);
                            self.next().hide();
                        }
                    });
                }
            });

            @if(!empty($userData->sess_district))
                $("#district").trigger('change');
            @endif
        });
    </script>

    {{--initial- input plugin--}}
    <script src="{{ asset("assets/plugins/intlTelInput/js/intlTelInput.js") }}"  type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/intlTelInput/js/utils.js") }}"  type="text/javascript"></script>
    <script>
        $("#user_mobile").intlTelInput({
            hiddenInput: ["user_mobile"],
            // onlyCountries: ["bd"],
            initialCountry: "BD",
            placeholderNumberType: "MOBILE",
            separateDialCode: true,
        });

    </script>
@endsection
