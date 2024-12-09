<?php
$osspid = new \App\Libraries\Osspid([
    'client_id' => config('osspid.osspid_client_id'),
    'client_secret_key' => config('osspid.osspid_client_secret_key'),
    'callback_url' => config('app.PROJECT_ROOT') . '/osspid-callback',
]);
$redirect_url = $osspid->getRedirectURL();
$signUp_link = env('osspid_base_url') . '/user/create';
?>
<style>
    /* Add this style to prevent hover color change */
    .btn:hover {
        color: inherit !important;
        /*background-color: inherit !important;*/
    }
    /* prp login modal design */
    #prpLoginBtn:hover {
        color: #f6f3f3 !important;
    }
</style>
{{-- <div class="col-lg-4">
    <div class="home-user-login">
        <div class="login-info-box">
            <h3>User Access</h3>
            @include('partials.messages')
            <button class="btn w-100" type="button" data-toggle="modal" data-target="#staticBackdrop">হজযাত্রী ও গাইড লগইন</button>
            <hr style="width: 100% !important;">

            <button class="btn w-100" type="button" data-toggle="modal" data-target="#prpLoginModal">PRP Login</button>
            <hr style="width: 100% !important;">
            <a class="btn w-100" type="button" href="{{$data['authorize_uri']}}" >লগইন</a>

            <hr style="width: 100% !important;">
            <button class="btn w-100" aria-label="Site Login" onclick="location.href ='<?php echo @$redirect_url; ?>';">OSSPID Login</button>
            <a class="fpass-text" href="https://osspid.org/user/forget-password" aria-label="Forget Password Link">Forget Password ?</a>
            <div class="hr_or"><span class="or-text">or</span></div>
            <p>New User ? <a href="{{$signUp_link}}" aria-label="Site Signup Link">Sign Up</a></p>
        </div>
    </div>
</div> --}}

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <div id="fetchTracking_no">
                    <h5 class="text-center">আপনার ট্র্যাকিং নম্বরটি প্রদান করুন </h5>
                    <input class="form-control col-md-6 mx-auto" type="text" id="tracking_no">
                </div>

                <div id="fetchAccessCode" style="display: none;">
                    <h5 class="text-center">আপনার এক্সেস কোডটি প্রদান করুন </h5>
                    <input class="form-control col-md-6 mx-auto" type="text" placeholder="Enter Your Access Code" id="accessCode">
                </div>

                <div id="fetchMobile" style="display: none;">
                    <h5 class="text-center">আপনার মোবাইল নম্বরটি প্রদান করুন </h5>
                    <p id="masked_mobile" style="display: none; color:green; text-align: center; font-size: 14px; margin: 5px 0px;"></p>
                    <input class="form-control col-md-6 mx-auto" type="text" id="mobile_no">
                </div>
                <br>
                <p id="errMsg" style="display: none; color:orangered; text-align: center; font-size: 12px;"></p>
                <div class="text-center">
                    <button type="button" id="srchPilgrimBtn" class="btn btn-success btn-sm">পরবর্তী</button>
                    {{--                    <button type="button" id="resendOtpbtn" style="display: none;" class="btn btn-success btn-sm">আপনার এক্সেস কোডটি পুনরায় পেতে ক্লিক করুন</button>--}}
                    <button type="button" id="checkAccessCode" style="display: none;" class="btn btn-success btn-sm">পরবর্তী</button>
                    <button type="button" id="checkMobileBtn" style="display: none;" class="btn btn-success btn-sm">পরবর্তী</button>
                </div>
                <hr>
                <div class="text-center">
                    <button type="button" id="resendOtpbtn" style="display: none; color:orangered; text-align: center; font-size: 14px; margin-top:5px;">আপনার এক্সেস কোডটি পুনরায় পেতে ক্লিক করুন</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="resendOtpModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="resendOtpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <div>
                    <h5 class="text-center">আপনার মোবাইল নম্বরটি প্রদান করুন </h5>
                    <p id="masked_mobile_resend" style="color:green; text-align: center; font-size: 14px; margin: 5px 0px;"></p>
                    <input class="form-control col-md-6 mx-auto" type="text" id="resend_mobile_no">
                </div>

                <p id="errMsgResendOtp" style="display: none; color:orangered; text-align: center; font-size: 12px; margin-top:5px;"></p>

                <hr>
                <div class="text-center">
                    <button type="button" id="checkMobileAndResendOtp" class="btn btn-success btn-sm">পরবর্তী</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal ba-modal fade" id="prpLoginModal"  data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="prpLoginModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="icon-close-modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <g clip-path="url(#clip0_2216_3720)">
                                <path d="M0.292786 0.292105C0.480314 0.104634 0.734622 -0.000681838 0.999786 -0.000681838C1.26495 -0.000681838 1.51926 0.104634 1.70679 0.292105L7.99979 6.5851L14.2928 0.292105C14.385 0.196594 14.4954 0.120412 14.6174 0.0680032C14.7394 0.0155942 14.8706 -0.011992 15.0034 -0.0131458C15.1362 -0.0142997 15.2678 0.0110021 15.3907 0.0612829C15.5136 0.111564 15.6253 0.185817 15.7192 0.27971C15.8131 0.373602 15.8873 0.485254 15.9376 0.608151C15.9879 0.731047 16.0132 0.862727 16.012 0.995506C16.0109 1.12829 15.9833 1.25951 15.9309 1.38151C15.8785 1.50351 15.8023 1.61386 15.7068 1.7061L9.41379 7.9991L15.7068 14.2921C15.8889 14.4807 15.9897 14.7333 15.9875 14.9955C15.9852 15.2577 15.88 15.5085 15.6946 15.6939C15.5092 15.8793 15.2584 15.9845 14.9962 15.9868C14.734 15.9891 14.4814 15.8883 14.2928 15.7061L7.99979 9.4131L1.70679 15.7061C1.51818 15.8883 1.26558 15.9891 1.00339 15.9868C0.741189 15.9845 0.490376 15.8793 0.304968 15.6939C0.11956 15.5085 0.0143908 15.2577 0.0121124 14.9955C0.00983399 14.7333 0.110628 14.4807 0.292786 14.2921L6.58579 7.9991L0.292786 1.7061C0.105315 1.51858 0 1.26427 0 0.999105C0 0.73394 0.105315 0.479632 0.292786 0.292105Z" fill="black"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_2216_3720">
                                    <rect width="16" height="16" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
            </button>

            <div class="modal-body">
                <div class="loginContent">
                    <h3 style="font-weight: bold !important;">Enter PRP Login Credential</h3>
                    <form action="#">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="prp_login_email">Email</label>
                                <div class="has-input-icon">
                                    <input type="text" class="form-control" name="prp_login_email" id="prp_email" placeholder="Enter Your Email">
                                    <span class="input-left-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <g opacity="0.3">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.5 5.25L2.25 4.5H21.75L22.5 5.25V18.75L21.75 19.5H2.25L1.5 18.75V5.25ZM3 6.8025V18H21V6.804L12.465 13.35H11.55L3 6.8025ZM19.545 6H4.455L12 11.8035L19.545 6Z" fill="#333333"/>
                                                </g>
                                            </svg>
                                        </span>
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="prp_login_password">Password</label>
                                <div class="has-input-icon">
                                    <input type="password" class="form-control" name="prp_login_password" id="prp_password" placeholder="Enter Your Password">
                                    <span class="input-left-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <g opacity="0.3">
                                                    <path d="M20 12C20 10.897 19.103 10 18 10H17V7C17 4.243 14.757 2 12 2C9.243 2 7 4.243 7 7V10H6C4.897 10 4 10.897 4 12V20C4 21.103 4.897 22 6 22H18C19.103 22 20 21.103 20 20V12ZM9 7C9 5.346 10.346 4 12 4C13.654 4 15 5.346 15 7V10H9V7Z" fill="#333333"/>
                                                </g>
                                            </svg>
                                    </span>

                                    <span class="show-pass-icon">
                                            <svg id="showPasswordIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.6666 6.00004C10.6666 6.70728 10.3856 7.38556 9.88554 7.88566C9.38544 8.38576 8.70716 8.66671 7.99992 8.66671C7.29267 8.66671 6.6144 8.38576 6.1143 7.88566C5.6142 7.38556 5.33325 6.70728 5.33325 6.00004C5.33325 5.2928 5.6142 4.61452 6.1143 4.11442C6.6144 3.61433 7.29267 3.33337 7.99992 3.33337C8.70716 3.33337 9.38544 3.61433 9.88554 4.11442C10.3856 4.61452 10.6666 5.2928 10.6666 6.00004ZM9.33325 6.00004C9.33325 6.35366 9.19278 6.6928 8.94273 6.94285C8.69268 7.1929 8.35354 7.33337 7.99992 7.33337C7.6463 7.33337 7.30716 7.1929 7.05711 6.94285C6.80706 6.6928 6.66659 6.35366 6.66659 6.00004C6.66659 5.64642 6.80706 5.30728 7.05711 5.05723C7.30716 4.80718 7.6463 4.66671 7.99992 4.66671C8.35354 4.66671 8.69268 4.80718 8.94273 5.05723C9.19278 5.30728 9.33325 5.64642 9.33325 6.00004Z" fill="#8C8C8C"/>
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.99995 0C11.7273 0 14.86 2.54933 15.748 6C14.86 9.45067 11.728 12 7.99995 12C4.27195 12 1.13995 9.45067 0.251953 6C1.13995 2.54933 4.27195 0 7.99995 0ZM7.99995 10.6667C5.01595 10.6667 2.48729 8.70533 1.63795 6C2.48729 3.29467 5.01595 1.33333 7.99995 1.33333C10.984 1.33333 13.5126 3.29467 14.362 6C13.5126 8.70533 10.984 10.6667 7.99995 10.6667Z" fill="#8C8C8C"/>
                                            </svg>
                                    </span>
                                </div>

                            </div>
                            <div class="login-fp-text">
                                <a class="fp-text-link" id="forgotPasswordLink" href="" target="_blank">Forgot password?</a>
                            </div>
                            <p id="prp_errMsg" style="display: none; color:orangered; text-align: center; font-size: 12px; margin-top:5px;"></p>
                            <button class="btn btn-login" id="prpLoginBtn">Login</button>
                        </div>
                        <div class="login-text">
                            <p class="m-0">Dont't have an account? <span id="prp-sign-up">Sign Up</span></p>
                        </div>
                        <div class="text-center mt-3" style="display: none" id="contact-call-center">
                            <span style="color: orangered">হজ সম্পর্কিত তথ্য জানার জন্য অনুগ্রহ করে 16136 বা +8809602666707 নম্বরে যোগাযোগ করুন।</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script>
    const base_url = "{{ url('/') }}";
    const errorMsg = $('.error-msg');
    const captchaDiv = $('#captchaDiv');

    let hit = "{{ Session::get('hit') }}";
    if (hit >= 3) {
        captchaDiv.css('display', 'block');
        grecaptcha.reset();
    }

    $(document).bind('keypress', function(e) {
        if (e.keyCode == 13) {
            checkUserInformation()
        }
    });

    function checkUserInformation() {
        if ($("#email").val() == '' || $("#passowrd").val() == '') {
            errorMsg.html("Please enter your email and password properly!");
            return false;
        }

        $("#btnSignIn").prop('disabled', true); // disable button
        $("#btnSignIn").html('<i class="fa fa-cog fa-spin"></i> Loading...');
        errorMsg.html("");
        $.ajax({
            url: '/login/check',
            type: 'POST',
            data: {
                email: $('input[name="email"]').val(),
                password: $('input[name="password"]').val(),
                g_recaptcha_response: $('#g-recaptcha-response').val(),
                _token: $('input[name="_token"]').val()
            },
            datatype: 'json',
            success: function(response) {
                if (response.responseCode === 1) {
                    window.location = base_url + response.redirect_to;
                } else {
                    if (response.hit >= 3) {
                        captchaDiv.css('display', 'block');
                        grecaptcha.reset();
                    }
                    errorMsg.html(response.msg);
                }
                $("#btnSignIn").prop('disabled', false); // disable button
                $("#btnSignIn").html('Sign In');
            },
            error: function(jqHR, textStatus, errorThrown) {
                // Reset error message div and put the message inside
                errorMsg.html(jqHR.responseJSON.message);
                // console.log(jqHR.responseJSON.message)
                console.log(jqHR, textStatus, errorThrown);
                $("#btnSignIn").prop('disabled', false); // disable button
                $("#btnSignIn").html('Sign In');
            }
        });
    }

    $(document).on('click','.Next1',function(e){
        // var project_id = $('#project_name').val();
        var email_address = $('#email_address').val();
        if( email_address == '')
        {
            $("#email_address").addClass('error');
            $(".error-message-nid").text("Please enter your email address");
            return false;
        }
        else {
            $("#email_address").removeClass('error');
            $(".error-message-nid").text("");
        }

        if( !validateEmail(email_address)) {
            $("#email_address").addClass('error');
            $(".error-message-nid").text("Please enter valid email address");
            return false;
        }
        btn = $(this);
        btn_content = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> &nbsp;'+btn_content);
        // btn.prop('disabled', true);

        $("#otpnext1").prop('disabled', true); // disable button
        $.ajax({
            url: '/login/otp-login-validation-with-token-provide',
            type: 'post',
            data: {
                _token: $('input[name="_token"]').val(),
                'email_address': email_address,
                // 'project_id': project_id,
                // 'otp' : $('#otpForm').find('input[name=otp]:checked').val()
            },
            success: function (response) {
                btn.prop('disabled', false);
                btn.html(btn_content);

                if(response.responseCode == 1)
                {
                    timerCounter= setInterval('secondPassed()', 1000);
                    seconds.value = seconds.defaultValue;
                    $(".error-message-message-login").hide();
                    // $("#email_address").prop("disabled", true)
                    $('#otp_step_1').css("display", "none");
                    $('#otpnext1').css("display", "none");
                    $('#otp_step_2').css("display", "block");
                    $('#otpnext2').css("display", "block");
                    $('#otpnext3').css("display", "block");

                    $('#loading_send_sms').html('Sending OTP <i class="fa fa-spinner fa-spin"></i>');
                    ///// ajax call
                    $('.modal-title').html('<span class="text-bold">Your email Address:  '+response.user_email+'</span>');
                    $('.modal-title').addClass('text-center');
                    checksmsStatus(response.queue_id);
                }
                else
                {
                    $(".error-message-message-login").show();
                    $(".error-message-message-login").text(response.msg);
                    // alert('Invalid Credentials');
                    return false;
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);

            },
            beforeSend: function (xhr) {
                console.log('before send');
            },
            complete: function () {
                //completed
            }
        });

    });

    function validateEmail($email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        return emailReg.test( $email );
    }

    $(document).on('click','.Next2',function(e){
        btn.prop('disabled', true);
        var login_token = $('#login_token').val();
        var project_id = $('#project_name').val();
        var email_address = $('#email_address').val();
        if(!login_token)
        {
            alert('OTP should be given');
            return false;
        }

        if(!email_address)
        {
            alert('Data has mismatch');
            return false;
        }


        btn = $(this);
        btn_content = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> &nbsp;'+btn_content);

        $.ajax({
            url: '/login/otp-login-check',
            type: 'post',
            data: {
                _token: $('input[name="_token"]').val(),
                'email_address': email_address,
                'login_token' : login_token,
                'project_id' : project_id
            },
            success: function (response) {

                btn.html(btn_content);
                btn.prop('disabled', false);

                if(response.responseCode == 1)
                {
                    window.location.href = response.redirect_to;
                }
                else if(response.msg == 'OTP Time Expired!.Please Try again'){
                    $('#resend_link').css("display", "block");
                }
                else
                {
                    $(".error-message-message-login").show();
                    $(".error-message-message-login").text(response.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);

            },
            beforeSend: function (e) {
                const loading_sign = '...<i class="fa fa-spinner fa-spin"></i>';
                e.innerText = loading_sign;
            },
            complete: function () {
                //completed
            }
        });
    });

    $(document).on('click','.resend_otp',function(e){
        // btn.prop('disabled', true);
        $('#login_token').val('');
        var project_id = $('#project_name').val();

        var email_address = $('#email_address').val();

        if(!email_address)
        {
            alert('Data has mismatch');
            return false;
        }


        btn = $(this);
        btn_content = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> &nbsp;'+btn_content);

        $.ajax({
            url: '/login/otp-resent',
            type: 'post',
            data: {
                _token: $('input[name="_token"]').val(),
                'email_address': email_address,
            },
            success: function (response) {

                btn.html(btn_content);
                // btn.prop('disabled', false);

                //console.log(response);
                //alert(response);


                if(response.responseCode == 1)
                {
                    $('#resend_link').css("display", "none");
                    $('#loading_send_sms').css("display", "block");
                    $('#loading_send_sms').html('Sending OTP <i class="fa fa-spinner fa-spin"></i>');
                    $('#resend_link').css("display", "none");
                    checksmsStatus(response.queue_id);

                    // checksmsStatus(response.sms_id,response.otp_expired);
                }
                else
                {
                    $(".success-message-message-login").hide();
                    $(".error-message-message-login").show();
                    $(".error-message-message-login").text(response.msg);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);

            },
            beforeSend: function (xhr) {
                console.log('before send');
            },
            complete: function () {
                //completed
                if(seconds == 0){
                    seconds = 180;
                }

                timerCounter= setInterval('secondPassed()', 1000);
            }
        });
    });

    $('.onlyNumber').on('keydown', function (e) {
        //period decimal
        if ((e.which >= 48 && e.which <= 57)
            //numpad decimal
            || (e.which >= 96 && e.which <= 105)
            // Allow: backspace, delete, tab, escape, enter and .
            || $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1
            // Allow: Ctrl+A
            || (e.keyCode == 65 && e.ctrlKey === true)
            // Allow: Ctrl+C
            || (e.keyCode == 67 && e.ctrlKey === true)
            // Allow: Ctrl+V
            || (e.keyCode == 86 && e.ctrlKey === true)
            // Allow: Ctrl+X
            || (e.keyCode == 88 && e.ctrlKey === true)
            // Allow: home, end, left, right
            || (e.keyCode >= 35 && e.keyCode <= 39))
        {

            var thisVal = $(this).val();
            if (thisVal.indexOf(".") != -1 && e.key == '.') {
                return false;
            }
            $(this).removeClass('error');
            return true;
        }
        else
        {
            $(this).addClass('error');
            return false;
        }
    }).on('paste', function (e) {
        var $this = $(this);
        setTimeout(function () {
            $this.val($this.val().replace(/[^0-9]/g, ''));
        }, 4);
    }).on('keyup', function (e) {
        var $this = $(this);
        setTimeout(function () {
            $this.val($this.val().replace(/[^0-9]/g, ''));
        }, 4);
    });

    $(document).on('keypress',  function (e) {
        if($('#otp_step_1').is(':visible')) {
            var key = e.which;
            if (key == 13) { //This is an ENTER
                $('#otpnext1').click();
            }
        }
    });

    $(document).on('keypress',  function (e) {
        if($('#otp_step_2').is(':visible')) {
            var key = e.which;
            if (key == 13) { //This is an ENTER
                $('#otpnext3').click();
            }
        }
    });

    var timerCounter;
    var seconds = 180; //**change 180 for any number you want, it's the seconds **//
    function secondPassed() {

            $('#resend_link').css("display", "none");
            var minutes = Math.round((seconds - 30)/60);
            var remainingSeconds = seconds % 60;
            if (remainingSeconds < 10) {
                remainingSeconds = "0" + remainingSeconds;
            }

            document.getElementById('show_cowndown').innerHTML = minutes + ":" + remainingSeconds;
            if (seconds == 0) {
                clearInterval(timerCounter);
                document.getElementById('show_cowndown').innerHTML = "Expired!";

                $(".success-message-message-login").hide();
                $(".error-message-message-login").hide();
                $('#display_before').css("display", "none");
                $('#resend_link').css("display", "block");

            } else {
                seconds--;
            }

    }

    function checksmsStatus(email_id,expiredtime=null) {
        var currenttime = new Date();
        var currenttimemilisecond = currenttime.getTime();
        var after10 = (currenttimemilisecond+(10*1000));
        var after20 = (currenttimemilisecond+(20*1000));
        var expiredtimemilisecond = new Date(expiredtime).getTime();

        var x = setInterval(function() {
            var currenttime2 = new Date().getTime();
            $.ajax({
                url: '/login/check-sms-send-status',
                type: 'post',
                data: {
                    _token: $('input[name="_token"]').val(),
                    'email_id': email_id,
                },
                success: function (response) {

                    if(response.responseCode == 1) {
                        if(response.sms_status ==1){
                            clearInterval(x);
                            $('#loading_send_sms').css("display", "none");
                            $(".error-message-message-login").hide();
                            $('.success-message-message-login').show().text(response.msg).delay(5000).fadeOut(300);
                            setTimeout("$('#display_before').css('display', 'block')", 5000);
                            $('#otp_step_1').css("display", "none");
                            $('#otpnext1').css("display", "none");
                             remainingtime(expiredtime);
                        }else{
                            if (currenttime2 >expiredtimemilisecond){
                                clearInterval(x);
                                $('#loading_send_sms').html('Please Try after some times');
                            }else
                                if(currenttime2 >after20){
                                $('#loading_send_sms').html('Please wait, sending SMS <i class="fa fa-spinner fa-spin"></i>');
                            }else if(currenttime2 > after10){
                                $('#loading_send_sms').html('Sending SMS <i class="fa fa-spinner fa-spin"></i>');
                            }


                        }
                    }
                    else
                    {
                        clearInterval(x);
                        $('#loading_send_sms').html('Please Try after some times');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);

                },
                beforeSend: function (xhr) {
                    console.log('before send');
                },
                complete: function () {
                    //completed
                    timerCounter=  setInterval('secondPassed()', 1000);
                }
            });

        }, 2000);
    }

    $(document).on('click','#resendOtpbtn',function(){
        let btnObj = $(this);
        let btnContent = btnObj.html();
        btnObj.html('<i class="fa fa-spinner fa-pulse"></i> &nbsp;'+ btnContent);
        btnObj.prop('disabled', true);

        $('#staticBackdrop').modal('hide');
        $('#resendOtpModal').modal('show');
    });



    $(document).on('click','#checkMobileAndResendOtp',function(){
        let btnObj = $(this);
        let btnContent = btnObj.html();
        btnObj.html('<i class="fa fa-spinner fa-pulse"></i> &nbsp;'+ btnContent);
        btnObj.prop('disabled', true);

        let tracking_no = $('#tracking_no').val();
        let resend_mobile_no = $('#resend_mobile_no').val();

        $.ajax({
            url: 'resend-otp-to-user',
            type: "ajax",
            method: "post",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                tracking_no: tracking_no,
                resend_mobile_no: resend_mobile_no,
            },
            success: function (data) {


                if(data.responseCode == 1){
                    $('#resendOtpModal').modal('hide');

                    var url ="{{ url('/') }}"; //the url I want to redirect to
                    $(location).attr('href', url);
                }
                else{
                    $('#errMsgResendOtp').show().text(data.msg);
                    // location.reload();
                }
                btnObj.html(btnContent);
                btnObj.prop('disabled', false);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // btnObj.html(btnContent);
                // btnObj.prop('disabled', false);
                // alert('error');
            }
        });
    });


    $(document).on('click','#srchPilgrimBtn',function(){
        let btnObj = $(this);
        let btnContent = btnObj.html();
        btnObj.html('<i class="fa fa-spinner fa-pulse"></i> &nbsp;'+ btnContent);
        btnObj.prop('disabled', true);

        let tracking_no = $('#tracking_no').val();

        $('#errMsg').html('').hide();

        if(!tracking_no){
            $('#errMsg').html('Please provide valid tracking no.').show();
            btnObj.html(btnContent);
            btnObj.prop('disabled', false);
            return false;
        }

        $.ajax({
            url: 'search-pilgrim-by-tracking-no',
            type: "ajax",
            method: "post",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                tracking_no: tracking_no,
            },
            success: function (data) {

                if(data.responseCode == 1){
                    $('#fetchTracking_no').hide();
                    $('#fetchAccessCode').hide();
                    $('#resendOtpbtn').hide();
                    $('#srchPilgrimBtn').hide();
                    $('#checkAccessCode').hide();

                    $('#fetchMobile').show();
                    $('#checkMobileBtn').show();
                    $('#masked_mobile').show().html(data.maskedMobile);
                }
                else if(data.responseCode == 2){
                    $('#fetchTracking_no').hide();
                    $('#srchPilgrimBtn').hide();
                    $('#fetchMobile').hide();
                    $('#checkMobileBtn').hide();

                    $('#fetchAccessCode').show();
                    $('#checkAccessCode').show();

                    if(data.buttonResendShow){
                        $('#resendOtpbtn').show();
                        $('#masked_mobile_resend').show().html(data.maskedMobile);
                    }
                    else {
                        $('#resendOtpbtn').hide();
                    }
                }
                else{
                    $('#staticBackdrop').modal('hide');
                    $('#fetchTracking_no').hide();
                    $('#srchPilgrimBtn').hide();
                    $('#fetchAccessCode').hide();
                    $('#checkAccessCode').hide();
                    $('#resendOtpbtn').hide();
                    $('#fetchMobile').hide();
                    $('#checkMobileBtn').hide();
                }
                $('#errMsg').html(data.msg).show();
                btnObj.html(btnContent);
                btnObj.prop('disabled', false);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                btnObj.html(btnContent);
                btnObj.prop('disabled', false);
                alert('error');
            }
        });
    });

    $(document).on('click','#checkAccessCode',function(){
        let btnObj = $(this);
        let btnContent = btnObj.html();
        btnObj.html('<i class="fa fa-spinner fa-pulse"></i> &nbsp;'+ btnContent);
        btnObj.prop('disabled', true);

        let tracking_no = $('#tracking_no').val();
        let givenAccessCode = $('#accessCode').val();

        $('#errMsg').html('').hide();

        if(!givenAccessCode){
            $('#errMsg').html('Please provide valid access code.').show();
            btnObj.html(btnContent);
            btnObj.prop('disabled', false);
            return false;
        }

        $.ajax({
            url: 'search-pilgrim-by-access-token',
            type: "ajax",
            method: "post",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                tracking_no: tracking_no,
                token: givenAccessCode,
            },
            success: function (data) {
                $('#tracking_no').val('');
                $('#accessCode').val('');
                // alert(data.msg);
                $('#staticBackdrop').modal('hide');

                if(data.responseCode == 3){
                    $('#fetchTracking_no').hide();
                    $('#fetchAccessCode').hide();
                    $('#srchPilgrimBtn').hide();
                    $('#checkAccessCode').hide();
                    $('#resendOtpbtn').hide();

                    var url ="{{ url('/dashboard') }}"; //the url I want to redirect to
                    $(location).attr('href', url);
                }
                else{
                    alert(data.msg);
                    location.reload();
                }
                btnObj.html(btnContent);
                btnObj.prop('disabled', false);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                btnObj.html(btnContent);
                btnObj.prop('disabled', false);
                alert('error');
            }
        });
    });

    $(document).on('click','#checkMobileBtn',function(){
        let btnObj = $(this);
        let btnContent = btnObj.html();
        btnObj.html('<i class="fa fa-spinner fa-pulse"></i> &nbsp;'+ btnContent);
        btnObj.prop('disabled', true);

        let tracking_no = $('#tracking_no').val();
        let mobile_no = $('#mobile_no').val();

        $('#errMsg').html('').hide();

        if(!mobile_no){
            $('#errMsg').html('আপনার বৈধ মোবাইল নম্বরটি প্রদান করুন।').show();
            btnObj.html(btnContent);
            btnObj.prop('disabled', false);
            return false;
        }

        $.ajax({
            url: 'save-and-search-pilgrim',
            type: "ajax",
            method: "post",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                tracking_no: tracking_no,
                mobile_no: mobile_no,
            },
            success: function (data) {
                $('#mobile_no').val('');
                $('#tracking_no').val('');
                $('#accessCode').val('');

                $('#staticBackdrop').modal('hide');
                location.reload();

                btnObj.html(btnContent);
                btnObj.prop('disabled', false);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                btnObj.html(btnContent);
                btnObj.prop('disabled', false);
                alert('error');
            }
        });
    });

    $(document).on('click','#prpLoginBtn',function(){
        let btnObj = $(this);
        let btnContent = btnObj.html();
        btnObj.html('<i class="fa fa-spinner fa-pulse"></i> &nbsp;'+ btnContent);
        btnObj.prop('disabled', true);

        let prp_email       = $('#prp_email').val();
        let prp_password    = $('#prp_password').val();

        $('#prp_errMsg').html('').hide();

        if(!prp_email || !prp_password){
            $('#errMsg').html('আপনার সঠিক লগইন তথ্য প্রদান করুন').show();
            btnObj.html(btnContent);
            btnObj.prop('disabled', false);
            return false;
        }

        $.ajax({
            url: 'prp-login',
            type: "ajax",
            method: "post",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                prp_email: prp_email,
                prp_password: prp_password,
            },
            success: function (data) {
                $('#prpLoginModal').modal('hide');
                if(data.responseCode == 3){
                    var url ="{{ url('/dashboard') }}"; //the url I want to redirect to
                    $(location).attr('href', url);
                }
                if(data.responseCode == -2){
                    var url ="{{ url('/client/signup/identity-verify') }}"; // the url for collectng new user data
                    $(location).attr('href', url);
                }

                if(data.responseCode == -1){
                    location.reload();
                }

                btnObj.html(btnContent);
                btnObj.prop('disabled', false);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                btnObj.html(btnContent);
                btnObj.prop('disabled', false);
                alert('error');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        let forgotPasswordLink = document.getElementById('forgotPasswordLink');
        $.ajax({
            url: 'get-pilgrimdb-path',
            type: "ajax",
            method: "get",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },

            success: function (data) {
                if (data.responseCode === 1) {
                    forgotPasswordLink.href = data.data + 'forget-password';
                }
                if (data.responseCode === -1 || data.data == "") {
                    forgotPasswordLink.href = '#';
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                forgotPasswordLink.href = '#';
            }
        });
    });

    $("#prp-sign-up").click(function () { // Use click event handler directly on the button
        let callCenterDiv = $("#contact-call-center");
        if (callCenterDiv.length) { // Check if the call center div exists before showing
            callCenterDiv.removeClass("hidden"); // Remove any initial "hidden" class (optional)
            callCenterDiv.show(); // Show the call center div
            callCenterDiv.fadeIn(500); // Add fade-in animation with a duration of 500 milliseconds
        } else {
            console.error("Something went wrong."); // Inform user if the div is missing
        }
    });

    $(document).ready(function() {
        $('.show-pass-icon').click(function() {
            let passwordInput = $('#prp_password');
            let showPasswordIcon = $('#showPasswordIcon');
            if (passwordInput.attr('type') === 'password') { // Toggle input type between 'password' and 'text'
                passwordInput.attr('type', 'text');
                //showPasswordIcon.find('path').attr('fill', 'lightgray'); // Change icon color
            } else {
                passwordInput.attr('type', 'password');
                // showPasswordIcon.find('path').removeAttr('fill'); // Reset icon color
                //showPasswordIcon.find('path').attr('fill', '#212529'); // Reset icon color
            }
        });
    });

</script>
