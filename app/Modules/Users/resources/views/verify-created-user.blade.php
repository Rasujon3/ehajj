@extends('public_home.front')

@section('header-resources')
    <link rel="stylesheet" href="{{ asset("assets/plugins/password-strength/password_strength.css") }}">
@endsection

@section ('body')

    <div class="container" style="margin-top:30px;">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" >
                        <h3 class="text-center">Verification Process</h3>
                    </div>
                    <div class="card-body">

                        @include('partials.messages')
                        <hr/>
                        <div class="col-md-12 col-sm-12">
                            {!! Form::open(array('url' => '/users/created-user-verification/'.$encrypted_token,'method' => 'patch', 'class' => 'form-horizontal',
                            'id'=> 'vreg_form')) !!}
                            <div class="col-md-12 col-sm-12">
                                <h3>Terms of Usage of OSS System</h3>
                                Terms and conditions to use this system can be briefed as -
                                <ol>
                                    <li>You must follow any policies made available to you within the Services.</li>
                                    <li>You have to fill all the given fields with correct information and take responsibility if any wrong or misleading information has been given</li>
                                    <li>You are responsible for the activity that happens on or through your account. So, keep your password confidential.</li>
                                    <li>We may modify these terms or any additional terms that apply to a Service to, for example,
                                        reflect changes to the law or changes to our Services. You should look at the terms regularly.</li>
                                </ol>
                            </div>


                            <br>
                            <div class="col-md-12">
                                <label>
                                    {!! Form::checkbox('user_agreement', 1, null,  ['class'=>'required']) !!}
                                    &nbsp;
                                    I have read and agree to terms and conditions.
                                </label>
                            </div>

                            <div class="form-group">
                                <div class="col-lg-3 float-right col-lg-offset-3">
                                    <button type="submit" class="btn btn-block btn-primary btn-large"  id="update_pass_btn"><b>Save and Continue</b></button>
                                </div>
                            </div>
                            <div class="col-md-8"><br/></div>
                            <div class="form-group">
                                <div class="col-lg-3 float-right">
                                    Already have an account? <b>{!! link_to('users/login', 'Login', array('class' => '')) !!}</b>
                                </div>
                            </div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            {!! Form::close() !!}
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-script')
    <script src="{{ asset("assets/scripts/jquery.validate.min.js") }}"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="{{ asset("assets/plugins/password-strength/password_strength.js") }}" type="text/javascript"></script>

    <script>


        // Show password validation check
        $(document).ready(function(){
            $("#enable_show").on("input", function(){
                var show_pass_value= document.getElementById('enable_show').value;
                checkRegularExp(show_pass_value);
            });
        });

        function enableSavePassBtn(){
            var password_input_value= document.getElementById('user_new_password').value;
            checkRegularExp(password_input_value);
        }

        function checkRegularExp(password){
            var submitbtn=$('#update_pass_btn');
            var user_password= $('#user_new_password');
            var enable_show = $('#enable_show');
            var regularExp = /^(?!\S*\s)(?=.*\d)(?=.*[~`!@#$%^&*()--+={}\[\]|\\:;"'<>,.?/_₹])(?=.*[A-Z]).{6,20}$/;

            if(regularExp.test(password)==true ) {
                user_password.removeClass('is-invalid');
                user_password.addClass('is-valid');
                enable_show.removeClass('is-invalid');
                submitbtn.prop("disabled", false);
                submitbtn.removeClass("disabled");
            }
            else {
                enable_show.addClass('is-invalid');
                user_password.addClass('is-invalid');
                submitbtn.prop("disabled", true);
                submitbtn.addClass("disabled");
            }

        }

        $(document).ready(function($) {
            $('#myPassword').strength_meter();
        });
        $('#myPassword').strength_meter({

            //  CSS selectors
            strengthWrapperClass: 'strength_wrapper',
            inputClass: 'strength_input',
            strengthMeterClass: 'strength_meter',
            toggleButtonClass: 'button_strength',

            // text for show / hide password links
            showPasswordText: 'Show Password',
            hidePasswordText: 'Hide Password'

        });


        // $('#myPassword').strength_meter();
        //
        // $('#user_confirm_password').on('keyup', function () {
        //     if ($('#user_confirm_password').val().length >= 6 && $('#user_confirm_password').val().length <= 20 &&
        //         $('#user_new_password').val() == $('#user_confirm_password').val()) {
        //         $('#user_new_password').removeClass("error");
        //     } else {
        //         $('#user_new_password').addClass("error");
        //     }
        //
        // });

        $(function () {
            var _token = $('input[name="_token"]').val();
            $("#vreg_form").validate({
                rules: {
                    user_confirm_password: {
                        equalTo: "#user_new_password"
                    }
                },
                errorPlacement: function () {
                    return false;
                }
            });
        });
    </script>

    <style>
        input[type="checkbox"].error{
            outline: 1px solid red
        }
    </style>
@endsection
