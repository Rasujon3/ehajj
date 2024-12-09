<?php
$osspid = new \App\Libraries\Osspid([
    'client_id' => config('osspid.osspid_client_id'),
    'client_secret_key' => config('osspid.osspid_client_secret_key'),
    'callback_url' => config('app.PROJECT_ROOT') . '/osspid-callback',
]);
$redirect_url = $osspid->getRedirectURL();
$signUp_link = env('osspid_base_url') . '/user/create';
?>
<div class="col-lg-4">
    <div class="home-user-login">
        <div class="login-info-box">
            <h3>User Access</h3>
            @include('partials.messages')
            <button class="btn btn-success btn-sm w-100 p-1 rounded" type="button" onclick="location.href ='<?php echo @$redirect_url; ?>';">Log In</button>
{{--            <a class="fpass-text" href="<?php echo @$redirect_url; ?>">Forget Password ?</a>--}}
            <a class="fpass-text" href="https://osspid.org/user/forget-password">Forget Password ?</a>
            <div class="hr_or"><span class="or-text">or</span></div>
            <p>New User ? <a href="{{$signUp_link}}">Sign Up</a></p>

            <hr style="width: 100% !important;">
            <button type="button" class="btn btn-success btn-sm w-100 p-1 rounded" data-toggle="modal" data-target="#staticBackdrop">
                সরকারী হজ্বযাত্রীর লগইন
            </button>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <div id="fetchTracking_no">
                    <h5 class="text-center">Enter Tracking No.</h5>
                    <input class="form-control col-md-6 mx-auto" type="text" id="tracking_no">
                </div>

                <div id="fetchAccessCode" style="display: none;">
                    <h5 class="text-center">Enter Your Access Code</h5>
                    <input class="form-control col-md-6 mx-auto" type="text" id="accessCode">
                </div>
                <p id="errMsg" style="display: none; color:orangered; text-align: center; font-size: 12px; margin-top:5px;"></p>

                <hr>
                <div class="text-center">
                    <button type="button" id="srchPilgrimBtn" class="btn btn-success btn-sm">Next</button>
                    <button type="button" id="checkAccessCode" style="display: none;" class="btn btn-success btn-sm">Next</button>
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

    let accessCode = 0;
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
                    $('#fetchAccessCode').show();

                    $('#srchPilgrimBtn').hide();
                    $('#checkAccessCode').show();
                    accessCode = data.accessToken;
                }
                else{
                    $('#errMsg').html(data.msg).show();
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


    $(document).on('click','#checkAccessCode',function(){
        let btnObj = $(this);
        let btnContent = btnObj.html();
        btnObj.html('<i class="fa fa-spinner fa-pulse"></i> &nbsp;'+ btnContent);
        btnObj.prop('disabled', true);

        let givenAccessCode = $('#accessCode').val();

        $('#errMsg').html('').hide();

        if(!givenAccessCode){
            $('#errMsg').html('Please provide valid access code.').show();
            btnObj.html(btnContent);
            btnObj.prop('disabled', false);
            return false;
        }

        if(givenAccessCode != accessCode){
            $('#accessCode').val('');
            $('#errMsg').html('Invalid access code.').show();
            btnObj.html(btnContent);
            btnObj.prop('disabled', false);
            return false;
        }

        $('#staticBackdrop').modal('hide');
        alert('Successfully validated. Welcome!!!');
        btnObj.html(btnContent);
        btnObj.prop('disabled', false);
    });

</script>
