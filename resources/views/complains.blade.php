@extends('public_home.front')
@section('header-resources')

@endsection
@section('body')
    <style>
        * {
            margin: 0;
            padding: 0
        }

        html {
            height: 100%;
        }

        .footer-top p {
            color: #ffffff;
        }

        #heading {
            text-transform: uppercase;
            color: #673AB7;
            font-weight: normal
        }

        #msform {
            text-align: center;
            position: relative;
            margin-top: 20px
        }

        #msform fieldset {
            background: white;
            border: 0 none;
            border-radius: 0.5rem;
            box-sizing: border-box;
            width: 100%;
            margin: 0;
            padding-bottom: 20px;
            position: relative
        }

        .form-card {
            text-align: left
        }

        #msform fieldset:not(:first-of-type) {
            display: none
        }

        #msform input,
        #msform textarea {
            padding: 8px 15px 8px 15px;
            border: 1px solid #ccc;
            border-radius: 0px;
            margin-bottom: 25px;
            margin-top: 2px;
            width: 100%;
            box-sizing: border-box;
            font-family: montserrat;
            color: #2C3E50;
            background-color: #ECEFF1;
            font-size: 16px;
            letter-spacing: 1px
        }

        #msform input:focus,
        #msform textarea:focus {
            -moz-box-shadow: none !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            border: 1px solid #673AB7;
            outline-width: 0
        }

        #msform .action-button {
            width: 100px;
            background: #673AB7;
            font-weight: bold;
            color: white;
            border: 0 none;
            border-radius: 0px;
            cursor: pointer;
            padding: 10px 5px;
            margin: 10px 20px 10px 5px;
            float: right
        }

        #msform .action-button:hover,
        #msform .action-button:focus {
            background-color: #311B92
        }

        #msform .action-button-previous {
            width: 100px;
            background: #616161;
            font-weight: bold;
            color: white;
            border: 0 none;
            border-radius: 0px;
            cursor: pointer;
            padding: 10px 5px;
            margin: 10px 20px 10px 0px;
            float: left
        }

        #msform .action-button-previous:hover,
        #msform .action-button-previous:focus {
            background-color: #000000
        }

        .card {
            z-index: 0;
            border: none;
            position: relative
        }

        .fs-title {
            font-size: 25px;
            color: #673AB7;
            margin-bottom: 15px;
            font-weight: normal;
            text-align: left
        }

        .purple-text {
            color: #673AB7;
            font-weight: normal
        }

        .steps {
            font-size: 25px;
            color: gray;
            margin-bottom: 10px;
            font-weight: normal;
            text-align: right
        }

        .fieldlabels {
            color: black;
            text-align: left
        }

        .fit-image {
            width: 100%;
            object-fit: cover
        }

        .form-container {
            display: flex;
            justify-content: center;
            /*align-items: center;*/
            /*height: 100vh; !* Adjust this value to control the vertical centering *!*/
        }

        .form-container form {
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 4px;
            max-width: 100%;
            width: 540px; /* Adjust this value to control the form's width */
        }

        #msform .complain-form-step .checkbox,
        #msform .input-form-block {
            display: inline-block;
            width: 100%;
            position: relative;
            margin-bottom: 15px;
        }

        #msform .complain-form-step .checkbox label,
        #msform .input-form-block .radio-inline {
            display: inline-block;
            position: relative;
            padding-left: 30px;
            text-align: left;
        }

        #msform .complain-form-step .checkbox label input,
        #msform .input-form-block .radio-inline input {
            width: 25px;
            position: absolute;
            left: 0;
            top: 3px;
            margin-top: 0;
        }

        .ehajj-complain-form .form-group {
            display: block;
            width: 100%;
            margin: 0 auto;
            max-width: 300px;
        }

        .ehajj-complain-form .form-card .form-title-h4 {
            margin-top: 30px;
            text-align: center;
        }

        .ehajj-complain-form .complain-form-footer {
            display: flex;
            width: 100%;
            padding: 20px;
            position: relative;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            column-gap: 20px;
        }

        .ehajj-complain-form .complain-form-step {
            padding: 20px;
        }

        .loading-indicator {
            display: none; /* Initially hide the loading indicator */
            width: 40px;
            height: 40px;
            background-color: #ffffff;
            border-radius: 50%;
            border: 4px solid #333333;
            border-top-color: #777777;
            animation: spin 1s infinite linear; /* Apply a spinning animation */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }


    </style>
    <section class="home-intro-section">
        <div class="container">
            <div class="ehajj-complain-form ">
                <h2 id="heading" style="text-align: center">অভিযোগ ফর্ম </h2>
                <div class="form-container ehajj-complain-form-content">
                    <form id="msform" action="/submit-complain" method="POST" enctype="multipart/form-data">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!}
                        {!! Session::has('error') ? '<div class="alert alert-danger alert-special-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!}

                        <!-- progressbar -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <ul id="progressbar" style="display: none">
                            <li class="active" id="account"><strong>Account</strong></li>
                            <li id="personal"><strong>Personal</strong></li>
                            <li id="saudi"><strong></strong></li>
                        </ul>
                        <div class="progress" style="display: none">
                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                 role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <br> <!-- fieldsets -->
                        {{-- country selection--}}
                        <fieldset>
                            <div class="form-card">
                                <div class="row">
                                    <div class="col-5" style="display: none">
                                        <h2 class="steps">Step 1 - 4</h2>
                                    </div>
                                </div>
                                {{--                                <div id="loadingIndicator" class="loading-indicator"></div>--}}

                                <h4 class="form-title-h4">আপনার বর্তমান অবস্থান নির্বাচন করুন *</h4>
                                <div class="form-group">
                                    <div class="input-form-block">
                                        <label class="radio-inline">
                                            <input type="radio" class="country_selection" name="country" id="bangladesh"
                                                   value="ban" disabled> বাংলাদেশ
                                        </label>
                                    </div>
                                    <div class="input-form-block">
                                        <label class="radio-inline">
                                            <input type="radio" class="country_selection" name="country" id="saudi_arab"
                                                   value="soudi" disabled> সৌদি আরব
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <input type="button" name="next" class="next action-button" id="next1" value="পরবর্তী"/>

                        </fieldset>

                        {{-- pilgrim info collection--}}
                        <fieldset>
                            <div class="form-card">
                                <div class="complain-form-step">
                                    <div class="form-group" style="max-width: 100%">
                                        <label class="fieldlabels">অভিযোগকারী হজ যাত্রী নিজে অথবা পক্ষে ?*</label>
                                        <div class="input-form-block">
                                            <label class="radio-inline">
                                                <input type="radio" class="self_pilgrim_yes_selection"
                                                       name="self_pilgrim_yes" id="self_pilgrim_yes" value="1"> নিজে
                                            </label>
                                        </div>
                                        <div class="input-form-block">
                                            <label class="radio-inline">
                                                <input type="radio" class="self_pilgrim_yes_selection"
                                                       name="self_pilgrim_yes" id="self_pilgrim_no" value="0"> পক্ষে
                                            </label>
                                        </div>
                                    </div>

                                    <div id="tracking_no_div" style="display: none;">
                                        <label class="fieldlabels">প্রাক-নিবন্ধিত হজ যাত্রীর ট্র্যাকিং নং/ পিআইডি নং /
                                            পাসপোর্ট নং : *</label>
                                        <input type="text" name="tracking_no" id="tracking_no" placeholder="Tracking No"
                                        />
                                    </div>
                                    <div id="pid_div2" style="display: none;">
                                        <label class="fieldlabels">পিলগ্রিম আইডি নম্বর / ট্র্যাকিং নং /পাসপোর্ট
                                            নং: </label>
                                        <input type="text" name="pid2" id="pid2"/>
                                    </div>
                                    <div style="display: flex; justify-content: center;" id="loader">
                                        <img src="http://i.stack.imgur.com/FhHRx.gif">
                                    </div>
                                </div>
                            </div>

                            <input type="button" name="next" class="next action-button" id="next2" value="পরবর্তী"/>
                            <input type="button" name="previous" class="previous action-button-previous"
                                   value="পূর্বে"/>
                        </fieldset>

                        <fieldset class="complain-form-step">
                            <div id="pid_div" class="form-card" style="display: none;">
                                <label class="fieldlabels">পিলগ্রিম আইডি নম্বর: </label>
                                <input type="text" name="pid" id="pid"/>
                            </div>


                            <div id="license_no_div" class="form-card" style="display: none;">
                                <label class="fieldlabels">হজ এজেন্সি এর লাইসেন্স নম্বর *</label>
                                <input type="text" name="license_no" id="license_no"/>
                            </div>

                            <div id="agency_name_div" class="form-card" style="display: none;">
                                <label class="fieldlabels">হজ এজেন্সির নাম </label>
                                <input type="text" name="agency_name" id="agency_name"/>
                            </div>
                            <div class="form-card" id="tracking_no_div2" style="display: none;">
                                <label class="fieldlabels"> ট্র্যাকিং নং : *</label>
                                <input type="text" name="tracking_no2" id="tracking_no2" placeholder="Tracking No"/>
                            </div>
                            <label class="fieldlabels" id="on_behalf_text"> অভিযোগকারী হজ যাত্রীর পক্ষে ব্যক্তির
                                তথ্য </label>
                            <div class="form-card">
                                <label class="fieldlabels">নাম </label>
                                <input type="text" id="pilgrim_name" name="pilgrim_name" placeholder="name"/>
                            </div>
                            <div class="form-card">
                                <label class="fieldlabels">এন আই ডি নম্বর * </label>
                                <input type="number" id="pilgrim_nid" name="pilgrim_nid" onchange="getCountNID()"
                                       placeholder="nid"/>
                            </div>

                            <div class="form-card">
                                <label class="fieldlabels">মোবাইল </label>
                                <input type="tel" id="pilgrim_mobile" onchange="validateMobile()" name="pilgrim_mobile"
                                       placeholder="Mobile"/>
                            </div>
                            <div class="form-card">
                                <label class="fieldlabels">ই-মেইল </label>
                                <input type="email" id="pilgrim_email" name="pilgrim_email" placeholder="Email"/>
                            </div>

                            <div class="form-card">

                                <label class="fieldlabels">অভিযোগের কারণ (টিক চিহ্ন দিতে হবে ) </label>


                                {{--                                <div class="checkbox myCheckbox">--}}
                                {{--                                    <label>--}}
                                {{--                                        <input type="checkbox" name="not_registered" id="reason">--}}
                                {{--                                        নিবন্ধন না করা--}}
                                {{--                                    </label>--}}
                                {{--                                </div>--}}
                                @foreach($complain_reasons as $reason)
                                    <div class="checkbox myCheckbox" id="{{"select_".$reason->country}}">
                                        <label>
                                            <input type="checkbox" name="selected_data[]" value="{{$reason->id}}">
                                            {{$reason->title}}
                                        </label>
                                    </div>

                                @endforeach

                            </div>
                            <div class="form-card">
                                <label class="fieldlabels">মন্তব্য </label>
                                {{--                                <textarea class="form-control" name="comment" id="exampleFormControlTextarea1"--}}
                                {{--                                          rows="3"></textarea>--}}
                                <textarea id="txtarea" class="form-control" spellcheck="false" name="comment"
                                          rows="3"></textarea>

                            </div>

                            <div class="form-card">
                                <label class="fieldlabels">উপরোক্ত বিষয়ে আমার বক্তব্য আলাদা পৃষ্ঠায় সংযুক্ত করা হইল
                                    <span class="text-muted" style="font-size: 12px; color: red !important;">(Maximum PDF file Upload Size {{\App\Modules\Settings\Models\Configuration::where('caption',"PDF_Upload_Limit")->pluck("value")->first()}} KB) </span>
                                </label>
                                <input type="file" name="pdf_file" placeholder="pdf"/>
                                <input type="hidden" name="is_govt" id="is_govt"/>
                            </div>
                            <div class="form-card">
                                <div class="captcha">
                                    <span id="captcha_image">{!! captcha_img() !!}</span>
                                    <button type="button" class="btn btn-primary btn-refresh"
                                            onclick="refreshCaptcha()">Refresh
                                    </button>
                                </div>
                                <input id="captcha" type="text" class="form-control" onchange="validateCapctha()"
                                       placeholder="Enter Captcha" name="captcha">
                            </div>

                            <input type="button" name="next" class="next action-button" id="next3"
                                   onclick="submit_form()" value="সাবমিট"/>
                            <input type="button" name="next" style="background: #673AB70D" class="btn btn-secondary action-button" id="next4"
                                   value="সাবমিট"/>
                            <input type="button" name="previous" class="previous action-button-previous"
                                   value="পূর্বে"/>

                        </fieldset>


                    </form>
                </div>


            </div>
        </div>
    </section>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {

            var current_fs, next_fs, previous_fs; //fieldsets
            var opacity;
            var current = 1;
            var steps = $("fieldset").length;

            setProgressBar(current);

            $(".next").click(function () {

                current_fs = $(this).parent();
                next_fs = $(this).parent().next();


                $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
                next_fs.show();
                current_fs.animate({opacity: 0}, {
                    step: function (now) {// for making fielset appear animation
                        opacity = 1 - now;

                        current_fs.css({
                            'display': 'none',
                            'position': 'relative'
                        });
                        next_fs.css({'opacity': opacity});
                    },
                    duration: 500
                });
                setProgressBar(++current);
            });

            $(".previous").click(function () {

                current_fs = $(this).parent();
                previous_fs = $(this).parent().prev();

//Remove class active
                $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

//show the previous fieldset
                previous_fs.show();

//hide the current fieldset with style
                current_fs.animate({opacity: 0}, {
                    step: function (now) {
// for making fielset appear animation
                        opacity = 1 - now;

                        current_fs.css({
                            'display': 'none',
                            'position': 'relative'
                        });
                        previous_fs.css({'opacity': opacity});
                    },
                    duration: 500
                });
                setProgressBar(--current);
            });

            function setProgressBar(curStep) {
                var percent = parseFloat(100 / steps) * curStep;
                percent = percent.toFixed();
                $(".progress-bar")
                    .css("width", percent + "%")
            }

            $(".submit").click(function () {
                return false;
            })

        });
    </script>
    <script>
        $(window).on('load', function () {
            // Enable the radio button after the page loads
            $('#bangladesh').prop('disabled', false);
            $('#saudi_arab').prop('disabled', false);

        });
        $(document).ready(function () {
            $('#loader').hide();

            $('#next1').prop('disabled', true);
            // $('#next2').prop('disabled', true);
            $('#next2').hide();
            $('#next3').hide();
            $('#next4').show();
            $('.country_selection').change(function () {
                let country_name = $(this).val();
                $('#tracking_no_div').hide();
                $('#pid_div').hide();
                $('#pid_div2').hide();
                $('#license_no_div').hide();
                $('#agency_name_div').hide();
                $('#self_pilgrim_yes_div').hide();
                $('#tracking_no_div2').hide();

                // $('#pilgrim_tracking_no').css("display", "none");

                if (country_name == 'ban') {
                    $("[id='select_2']").hide();
                    $("[id='select_1']").show();
                    $('#tracking_no_div').show();
                    $('#pid_div').show();
                    $('#license_no_div').show();
                    $('#agency_name_div').show();
                    $('#self_pilgrim_yes_div').show();
                    $('#next1').prop('disabled', false);
                    $('.self_pilgrim_yes_selection').change(function () {

                        let self_pilgrim_yes = $(this).val();
                        $('#pilgrim_name').attr('readonly', false);
                        $('#pilgrim_nid').attr('readonly', false);
                        $('#pilgrim_mobile').attr('readonly', false);
                        $('#pilgrim_email').attr('readonly', false);
                        if (self_pilgrim_yes == '1') {
                            $('#tracking_no').val('');
                            $('#on_behalf_text').hide();
                            $('#next2').hide();

                        } else {
                            $('#tracking_no').val('');
                            $('#on_behalf_text').show();
                            $('#next2').hide();

                        }
                    });
                } else {
                    $("[id='select_1']").hide();
                    $("[id='select_2']").show();
                    $('#pid_div2').show();
                    $('#self_pilgrim_yes_div').show();
                    $('#tracking_no_div2').show();
                    $('#next1').prop('disabled', false);
                    $('.self_pilgrim_yes_selection').change(function () {
                        let self_pilgrim_yes = $(this).val();
                        if (self_pilgrim_yes == '1') {
                            $('#pid2').val('');
                            $('#on_behalf_text').hide();
                            $('#next2').hide();
                        } else {
                            $('#pid2').val('');
                            $('#on_behalf_text').show();
                            $('#next2').hide();
                        }
                    });
                }
            });

            // $('#tracking_no').on('input', function() {
            //     var inputValue = $(this).val();
            //     if (inputValue.length >= 7) {
            //         $(this).trigger('change');
            //     }
            // });
            var typingTimer;

            $('#tracking_no').on('keyup', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(fetchData, 2500);
            });

            $('#tracking_no').on('keydown', function () {
                clearTimeout(typingTimer);
            });


            // $('#pid2').on('input', function() {
            //     var inputValue = $(this).val();
            //     if (inputValue.length >= 7) {
            //         $(this).trigger('change');
            //     }
            // });
            $('#pid2').on('keyup', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(fetchDataByPid, 2500);
            });

            $('#pid2').on('keydown', function () {
                clearTimeout(typingTimer);
            });

        });

        function getCountNID() {
            let total_length_nid = $('#pilgrim_nid').val().length;
            if (total_length_nid < 10) {
                alert("Invalid Nid");
                $('#pilgrim_nid').val('');

            }
            if (total_length_nid > 10 && total_length_nid <= 12) {
                alert("Invalid Nid");
                $('#pilgrim_nid').val('');
            }
            if (total_length_nid > 13 && total_length_nid <= 16) {
                alert("Invalid Nid");
                $('#pilgrim_nid').val('');
            }
            if (total_length_nid > 17) {
                alert("Invalid Nid");
                $('#pilgrim_nid').val('');
            }
        }

        function validateMobile() {
            console.log("qqq");
            let pilgrim_phone_number = $('#pilgrim_mobile').val();
            if (!validateBangladeshiPhoneNumber(pilgrim_phone_number)) {
                alert("Phone Number Invalid");
                $('#pilgrim_mobile').val('');
            }
        }

        function validateBangladeshiPhoneNumber(phonenumber) {
            var mobileRegex = /^(?:\+?88|0088)?01[3-9]\d{8}$/;
            var mobileNumber = phonenumber;
            if (mobileRegex.test(mobileNumber)) {
                return true;
            } else {
                return false;
            }
        }

        function fetchData() {

            self_pilgrim_yes = getIsSelfSelectedValue();
            if (self_pilgrim_yes == '') {
                alert(" প্রথমে নিজ অথবা পক্ষে সিলেক্ট করুন । ");
                $('#next2').prop('diseabled', true);
                $('#tracking_no').val('');
                return false;
            }
            var inputField = document.getElementById("tracking_no");
            var value = inputField.value;
            $('#loader').show();

            $.ajax({
                url: '<?php echo env('APP_URL') . '/ajax-fetch-data-by-tracking_no' ?>',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    tracking_no: value
                },
                success: function (response) {
                    $('#loader').hide();
                    var response1 = response;
                    var response = response.data.data;
                    if (response1.data.status == 400) {
                        alert(response1.data.msg);
                        $('#next2').hide();
                        return 0;
                    } else if (response1.data.status == 200 && response != null) {
                        $('#next2').show();
                    } else if (response1.data.status == 200 && response == null) {
                        alert(response1.data.msg);
                        $('#next2').hide();
                        return 0;
                    }


                    if (self_pilgrim_yes == '1') {
                        $('#pilgrim_name').attr('readonly', true);
                        $('#pilgrim_mobile').attr('readonly', true);
                        $('#pilgrim_email').attr('readonly', false);
                        $('#pilgrim_nid').attr('readonly', true);
                        $('#pilgrim_name').val(response.full_name_english);
                        $('#pilgrim_mobile').val(response.mobile);
                        $('#pilgrim_email').val(response.email);
                        $('#pilgrim_nid').val(response.national_id);
                        $('#pid').val(response.pid);
                        $('#license_no').val(response.license_no);
                        $('#agency_name').val(response.name);
                        $('#pid').attr('readonly', true);
                        $('#license_no').attr('readonly', true);
                        $('#agency_name').attr('readonly', true);
                        $('#is_govt').val(response.is_govt);

                    } else {
                        $('#pilgrim_name').val('');
                        $('#pilgrim_mobile').val('');
                        $('#pilgrim_email').val('');
                        $('#pilgrim_nid').val('');
                        $('#pid').val('');
                        $('#license_no').val('');
                        $('#agency_name').val('');
                        $('#pilgrim_nid').val('');
                        $('#pid').val(response.pid);
                        $('#license_no').val(response.license_no);
                        $('#agency_name').val(response.name);
                        $('#pilgrim_name').attr('readonly', false);
                        $('#pilgrim_nid').attr('readonly', false);
                        $('#pilgrim_mobile').attr('readonly', false);
                        $('#pilgrim_email').attr('readonly', false);
                        $('#pid').attr('readonly', true);
                        $('#license_no').attr('readonly', true);
                        $('#agency_name').attr('readonly', true);
                        $('#is_govt').val(response.is_govt);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#loader').hide();
                    alert(errorThrown);
                    console.log(errorThrown);
                },
                beforeSend: function (xhr) {

                }
            });
        }

        function getIsSelfSelectedValue() {
            var selectedValue = "";

            if (document.getElementById("self_pilgrim_yes").checked) {
                selectedValue = document.getElementById("self_pilgrim_yes").value;
            } else if (document.getElementById("self_pilgrim_no").checked) {
                selectedValue = document.getElementById("self_pilgrim_no").value;
            }
            return selectedValue;
        }


        function fetchDataByPid() {
            var inputField = document.getElementById("pid2");
            var value = inputField.value;
            self_pilgrim_yes = getIsSelfSelectedValue();
            if (self_pilgrim_yes == '') {
                alert(" প্রথমে নিজ অথবা পক্ষে সিলেক্ট করুন । ");
                $('#next2').hide()
                $('#pid2').val('');
                return false;
            }
            $('#loader').show();

            $.ajax({
                url: '<?php echo env('APP_URL') . '/ajax-fetch-data-by-pid' ?>',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    pid: value
                },
                success: function (response) {
                    $('#loader').hide();

                    var response1 = response;
                    if (response1.data.status == 400) {
                        alert(response1.data.msg);
                        $('#next2').hide();
                        return 0;
                    } else if (response1.data.status == 200 && response != null) {
                        $('#next2').show();
                    } else if (response1.data.status == 200 && response == null) {
                        if ($('#pid2').val().length > 7) {
                            alert(response1.data.msg);
                        }
                        $('#next2').hide();
                        return 0;
                    }
                    var response = response.data.data;
                    $('#pid').val(response.pid);
                    $('#license_no').val(response.license_no);
                    $('#agency_name').val(response.name);

                    if (self_pilgrim_yes == '1') {
                        $('#license_no_div').show();
                        $('#agency_name_div').show();
                        $('#tracking_no2').attr('readonly', true);
                        $('#pilgrim_name').attr('readonly', true);
                        $('#license_no').attr('readonly', true);
                        $('#agency_name').attr('readonly', true);
                        $('#pilgrim_nid').attr('readonly', false);
                        $('#pilgrim_mobile').attr('readonly', true);
                        $('#pilgrim_email').attr('readonly', false);
                        $('#pilgrim_name').val(response.full_name_english);
                        $('#pilgrim_mobile').val(response.mobile);
                        $('#pilgrim_email').val(response.email);
                        $('#pilgrim_nid').val(response.national_id);
                        $('#tracking_no2').val(response.tracking_no);
                        $('#license_no').val(response.license_no);
                        $('#agency_name').val(response.name);
                        $('#is_govt').val(response.is_govt);
                    } else {
                        $('#license_no_div').show();
                        $('#agency_name_div').show();
                        $('#tracking_no2').attr('readonly', true);
                        $('#license_no').attr('readonly', true);
                        $('#agency_name').attr('readonly', true);
                        $('#tracking_no2').val(response.tracking_no);
                        $('#license_no').val(response.license_no);
                        $('#agency_name').val(response.name);
                        $('#pilgrim_name').val('');
                        $('#pilgrim_email').val('');
                        $('#pilgrim_nid').val('');
                        $('#pilgrim_mobile').val('');
                        $('#is_govt').val(response.is_govt);
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#loader').hide();

                    alert(errorThrown);
                    console.log(errorThrown);
                },
                beforeSend: function (xhr) {

                }
            });
        }

        function validateCapctha() {
            var captchaResponse = $('#captcha').val();
            // Send an AJAX request to the server for CAPTCHA validation
            $.ajax({
                url: '{{ route("captcha.validate") }}', // Replace with your Laravel route URL
                type: 'POST',
                data: {
                    captcha: captchaResponse,
                    _token: '{{ csrf_token() }}' // Include CSRF token for Laravel protection
                },
                success: function (response) {
                    // CAPTCHA validation successful, submit the form
                    var numChecked = $('input[type="checkbox"]:checked').length;
                    if (numChecked > 0) {
                        let pilgrim_name = $('#pilgrim_name').val();
                        let pilgrim_mobile = $('#pilgrim_mobile').val();
                        let pilgrim_nid = $('#pilgrim_nid').val();
                        let status = 1;
                        if (pilgrim_name == null) {
                            alert("Please Enter Name !");
                            $('#next3').hide()
                            $('#next4').show()
                            status = 0;
                        }
                        if (pilgrim_mobile == null) {
                            alert("Please Enter Mobile No !");
                            $('#next3').hide()
                            $('#next4').show()
                            status = 0;

                        }
                        if (pilgrim_nid == null) {
                            alert("Please Enter Nid No !");
                            $('#next3').hide()
                            $('#next4').show()
                            status = 0;

                        }
                        if (status == 1) {
                            $('#next3').show();
                            $('#next4').hide();
                            $('#next1').prop('disabled', false);
                        } else {
                            refreshCaptcha();
                        }
                    } else {
                        alert("Please Select Reason!");
                        $('#next3').hide();
                        $('#next4').show();
                        refreshCaptcha();
                    }
                    // Output the number of checked checkboxes

                },
                error: function (xhr) {
                    // CAPTCHA validation failed, show error message
                    alert('CAPTCHA validation failed');
                    $('#next3').hide();
                    $('#next4').show();
                    refreshCaptcha();
                }
            });
        }

        function refreshCaptcha() {
            $('#next3').hide();
            $('#next4').show();
            $('#captcha').val('');
            $.ajax({
                url: '{{ route("refresh_captcha") }}', // Replace with your Laravel route URL
                type: 'GET',
                success: function (response) {
                    console.log(response);
                    $('#captcha_image').html(response.captcha);
                },
                error: function (xhr) {
                    // Handle error if CAPTCHA refresh fails
                    alert('CAPTCHA refresh failed');
                }
            });

        }


        function submit_form() {
            var form = document.getElementById("msform");
            form.submit();
        }
    </script>
    <script>
        function expandTextarea(id) {
            document.getElementById(id).addEventListener('keyup', function () {
                this.style.overflow = 'hidden';
                this.style.height = 0;
                this.style.height = this.scrollHeight + 'px';
            }, false);
        }

        expandTextarea('txtarea');
    </script>
    @section('footer-script')

        <script type="text/javascript" src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>


        <script>


            $('#haji_search').click(function (e) {
                e.preventDefault();
                var searchval = $('#haji_search_val').val();
                if (!searchval) {
                    alert('Please provide valid tracking no, passport no or hajj agency license number.');
                    return false;
                }
                if (searchval.length == 4 && ($.isNumeric(searchval) == true)) {
                    // window.location.href = 'https://prp.pilgrimdb.org/agencies/'+ searchval ;
                    window.open('https://prp.pilgrimdb.org/agencies/' + searchval, '_blank')
                } else {
                    // window.location.href = 'https://prp.pilgrimdb.org/web/pilgrim-search?q='+ searchval ;
                    window.open('https://prp.pilgrimdb.org/web/pilgrim-search?q=' + searchval, '_blank')
                }

            });

        </script>
    @endsection
@endsection
