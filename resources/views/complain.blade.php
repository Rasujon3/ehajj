<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0
        }

        html {
            height: 100%;
        }

        p {
            color: grey
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
            margin: 10px 0px 10px 5px;
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
            margin: 10px 5px 10px 0px;
            float: right
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
            color: gray;
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
            width: 400px; /* Adjust this value to control the form's width */
        }

    </style>
</head>
<body>
    <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
    <h2 id="heading" style="text-align: center">অভিযোগ ফর্ম </h2>
        <div class="form-container">
            <form id="msform" action="/submit-complain" method="POST" enctype="multipart/form-data" style="width: 50%;">
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

                <div class="form-group">
                    <label class="fieldlabels">আপনার বর্তমান অবস্থান নির্বাচন করুন *</label>
                    <div class="col-sm-8">
                        <label class="radio-inline">
                            <input type="radio" style="margin-left: -52px;" class="country_selection" name="country" id="bangladesh" value="ban" disabled > বাংলাদেশ
                        </label>
                        <label class="radio-inline" style="margin-left: 30px;">
                            <input type="radio" style="margin-left: -54px;" class="country_selection" name="country" id="saudi_arab" value="soudi" disabled > সৌদি আরব
                        </label>

                    </div>
                </div>
            </div>
            <input type="button" name="next" class="next action-button" id="next1"value="Next"/>
        </fieldset>

        {{-- pilgrim info collection--}}
        <fieldset>
            <div class="form-card">
                <div>
                    <div id="tracking_no_div" style="display: none;">
                        <label class="fieldlabels">প্রাক-নিবন্ধিত/হজ যাত্রীর ট্র্যাকিং নং : *</label>
                        <input type="text" name="tracking_no" id="tracking_no" placeholder="Tracking No" onchange="fetchData()"/>
                    </div>

                    <div id="pid_div" style="display: none;">
                        <label class="fieldlabels">পিলগ্রিম আইডি নাম্বার: </label>
                        <input type="text" name="pid" id="pid"/>
                    </div>
                    <div id="pid_div2" style="display: none;">
                        <label class="fieldlabels">পিলগ্রিম আইডি নাম্বার: </label>
                        <input type="text" name="pid2" id="pid2" onchange="fetchDataByPid()"/>
                    </div>

                    <div id="license_no_div" style="display: none;">
                        <label class="fieldlabels">হাজ্ এজেন্সি এর লাইসেন্স নাম্বার *</label>
                        <input type="text" name="license_no" id="license_no"/>
                    </div>

                    <div id="agency_name_div" style="display: none;">
                        <label class="fieldlabels">হজ এজেন্সির নাম </label>
                        <input type="text" name="agency_name" id="agency_name"/>
                    </div>

                    <div class="form-group">
                        <label class="fieldlabels">অভিযোগকারী হজযাত্রী নিজে কি না ?*</label>
                        <div class="col-sm-8">
                            <label class="radio-inline">
                                <input type="radio" style="margin-left: -33px;" class="self_pilgrim_yes_selection" name="self_pilgrim_yes" id="self_pilgrim_yes" value="1"> হ্যা
                            </label>
                            <label class="radio-inline">
                                <input type="radio" style="margin-left: -31px;" class="self_pilgrim_yes_selection" name="self_pilgrim_yes" id="self_pilgrim_no" value="0"> পক্ষে
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <input type="button" name="next" class="next action-button" id="next2"  value="Next"/>
            <input type="button" name="previous" class="previous action-button-previous"
                   value="Previous"/>
        </fieldset>

        <fieldset>
            <div class="form-card" id="tracking_no_div2" style="display: none;">
                <label class="fieldlabels"> ট্র্যাকিং নং : *</label>
                <input type="text" name="tracking_no2" id="tracking_no2" placeholder="Tracking No"/>
            </div>
            <div class="form-card">
                <label class="fieldlabels">নাম  </label>
                <input type="text" id="pilgrim_name" name="pilgrim_name" placeholder="name"/>
            </div>
            <div class="form-card">
                <label class="fieldlabels">এন আই ডি নম্বর * </label>
                <input type="text" id="pilgrim_nid" name="pilgrim_nid" placeholder="nid"/>
            </div>

            <div class="form-card">
                <label class="fieldlabels">মোবাইল </label>
                <input type="text" id="pilgrim_mobile" name="pilgrim_mobile" placeholder="Mobile"/>
            </div>
            <div class="form-card">
                <label class="fieldlabels">ই-মেইল  </label>
                <input type="email" id="pilgrim_email"  name="pilgrim_email" placeholder="Email"/>
            </div>

            <div class="form-card">

                <label class="fieldlabels">অভিযোগের কারণ (টিক চিহ্ন দিতে হবে ) </label>


                <div class="checkbox myCheckbox">
                    <label>
                        <input type="checkbox" name="not_registered"  id="reason">
                        নিবন্ধন না করা
                    </label>
                </div>

                <div class="checkbox myCheckbox">
                    <label>
                        <input type="checkbox" name="not_shifted" id="reason">
                        অন্য হজ এজেন্সিতে স্থানান্তর না করা
                    </label>
                </div>
                <div class="checkbox myCheckbox">
                    <label>
                        <input type="checkbox" name="not_return_refund" id="not_return_refund">
                        রিফান্ড কৃত টাকা ফেরত না দেওয়া
                    </label>
                </div>
                <div class="checkbox myCheckbox">
                    <label>
                        <input type="checkbox" name="training_related" id="training_related">

                        প্রশিক্ষণ সংক্রান্ত
                    </label>
                </div>
                <div class="checkbox myCheckbox">
                    <label>
                        <input type="checkbox" name="flight_related" id="flight_related">

                        ফ্লাইট সংক্রান্ত
                    </label>
                </div>
                <div class="checkbox myCheckbox">
                    <label>
                        <input type="checkbox" name="replacement_related" id="replacement_related">

                        রিপ্লেসমেন্ট সংক্রান্ত
                    </label>
                </div>
                <div class="checkbox myCheckbox">
                    <label>
                        <input type="checkbox" name="others" id="others">

                        অন্যান্য
                    </label>
                </div>
            </div>
            <div class="form-card">
                <label class="fieldlabels">উপরোক্ত বিষয়ে আমার বক্তব্য আলাদা পৃষ্ঠায় সংযুক্ত করা হইল </label>
                <input type="file" name="pdf_file" placeholder="pdf"/>
            </div>

            <input type="button" name="next" class="next action-button" id="next3" onclick="submit_form()" value="Submit"/>
            <input type="button" name="previous" class="previous action-button-previous"
                   value="Previous"/>
        </fieldset>


    </form>
        </div>
</div>
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
                    step: function (now) {
// for making fielset appear animation
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
        $(window).on('load', function() {
            // Enable the radio button after the page loads
            $('#bangladesh').prop('disabled', false);
            $('#saudi_arab').prop('disabled', false);
        });
        $(document).ready(function(){
            $('#next1').prop('disabled',true);
            $('#next2').prop('disabled',true);
            $('#next3').prop('disabled',true);
            $('.country_selection').change(function(){
                let country_name = $(this).val();
                $('#tracking_no_div').hide();
                $('#pid_div').hide();
                $('#pid_div2').hide();
                $('#license_no_div').hide();
                $('#agency_name_div').hide();
                $('#self_pilgrim_yes_div').hide();
                $('#tracking_no_div2').hide();

                // $('#pilgrim_tracking_no').css("display", "none");

                if(country_name == 'ban'){
                    $('#tracking_no_div').show();
                    $('#pid_div').show();
                    $('#license_no_div').show();
                    $('#agency_name_div').show();
                    $('#self_pilgrim_yes_div').show();
                    $('#next1').prop('disabled',false);
                }else{
                    $('#pid_div2').show();
                    $('#self_pilgrim_yes_div').show();
                    $('#tracking_no_div2').show();
                    $('#next1').prop('disabled',false);
                }
            });
            $('.self_pilgrim_yes_selection').change(function(){

                let self_pilgrim_yes = $(this).val();
                $('#pilgrim_name').attr('readonly',false);
                $('#pilgrim_nid').attr('readonly',false);
                $('#pilgrim_mobile').attr('readonly',false);
                $('#pilgrim_email').attr('readonly',false);


                if(self_pilgrim_yes == '1'){
                    $('#pilgrim_name').attr('readonly',true);
                    $('#pilgrim_nid').attr('readonly',false);
                    $('#pilgrim_mobile').attr('readonly',true);
                    $('#pilgrim_email').attr('readonly',true);
                    $('#next2').prop('disabled',false);
                }else{
                    $('#next2').prop('disabled',false);
                }
            });
            $('.myCheckbox').on('change', function() {
                var anyUnchecked = $('.myCheckbox:not(:checked)').length > 0;
                // Disable or enable the submit button based on selection
                if(anyUnchecked){
                    $('#next3').prop('disabled', false);

                }else{
                    $('#next3').prop('disabled', true);

                }
            });

        });
        function fetchData() {
            var inputField = document.getElementById("tracking_no");
            var value = inputField.value;
            $.ajax({
                url: '<?php echo env('APP_URL').'/ajax-fetch-data-by-tracking_no'?>',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    tracking_no: value
                },
                success: function (response) {
                    console.log(response.data.data);
                    var response = response.data.data;
                    $('.self_pilgrim_yes_selection').change(function(){
                        let self_pilgrim_yes = $(this).val();

                        $('#pilgrim_name').attr('readonly',false);
                        $('#pilgrim_nid').attr('readonly',false);
                        $('#pilgrim_mobile').attr('readonly',false);
                        $('#pilgrim_email').attr('readonly',false);


                        if(self_pilgrim_yes == '1'){
                            $('#pilgrim_name').attr('readonly',true);
                            $('#pilgrim_nid').attr('readonly',false);
                            $('#pilgrim_mobile').attr('readonly',true);
                            $('#pilgrim_email').attr('readonly',true);
                            $('#pilgrim_name').val(response.full_name_english);
                            $('#pilgrim_mobile').val(response.mobile);
                            $('#pilgrim_email').val(response.email);
                            $('#pilgrim_nid').val(response.national_id);
                        }else{
                            $('#pilgrim_name').val('');
                            $('#pilgrim_mobile').val('');
                            $('#pilgrim_email').val('');
                            $('#pilgrim_nid').val('');
                        }

                    });

                    $('#pid').val(response.pid);
                    $('#license_no').val(response.license_no);
                    $('#agency_name').val(response.name);
                    // return false;

                    // check_medical_cert_generator(tracking_no, 'generate_mr');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                    console.log(errorThrown);
                },
                beforeSend: function (xhr) {

                }
            });
        }
        function fetchDataByPid() {
            var inputField = document.getElementById("pid2");
            var value = inputField.value;
            $.ajax({
                url: '<?php echo env('APP_URL').'/ajax-fetch-data-by-pid'?>',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    pid: value
                },
                success: function (response) {
                    console.log(response);
                    var response = response.data.data;
                    $('.self_pilgrim_yes_selection').change(function(){
                        let self_pilgrim_yes = $(this).val();

                        $('#pilgrim_name').attr('readonly',false);
                        $('#pilgrim_nid').attr('readonly',false);
                        $('#pilgrim_mobile').attr('readonly',false);
                        $('#pilgrim_email').attr('readonly',false);


                        if(self_pilgrim_yes == '1'){
                            $('#pilgrim_name').attr('readonly',true);
                            $('#pilgrim_nid').attr('readonly',false);
                            $('#pilgrim_mobile').attr('readonly',true);
                            $('#pilgrim_email').attr('readonly',true);
                            $('#pilgrim_name').val(response.full_name_english);
                            $('#pilgrim_mobile').val(response.mobile);
                            $('#pilgrim_email').val(response.email);
                            $('#pilgrim_nid').val(response.national_id);
                            $('#tracking_no2').val(response.tracking_no);
                        }else{
                            $('#pilgrim_name').val('');
                            $('#pilgrim_mobile').val('');
                            $('#pilgrim_email').val('');
                            $('#pilgrim_nid').val('');
                            $('#tracking_no2').val('');
                        }

                    });

                    $('#pid').val(response.pid);
                    $('#license_no').val(response.license_no);
                    $('#agency_name').val(response.name);
                    // return false;

                    // check_medical_cert_generator(tracking_no, 'generate_mr');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                    console.log(errorThrown);
                },
                beforeSend: function (xhr) {

                }
            });
        }
        function submit_form(){
            var form = document.getElementById("msform");
            form.submit();
        }
    </script>
</body>
</html>
