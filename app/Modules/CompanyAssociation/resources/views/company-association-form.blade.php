<?php

$divisions = getDivision();
$regType = getCompanyRegistrationType();
$companyType = getCompanyType();
?>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<style type="text/css">
    /* Latest compiled and minified CSS included as External Resource*/

    /* Optional theme */

    /*@import url('//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css');*/

    .addressField {
        width: 79.5%;
        float: right;
    }
    .stepwizard-step p {
        margin-top: 0px;
        color:#666;
    }
    .stepwizard-row {
        display: table-row;
    }
    .stepwizard {
        display: table;
        width: 100%;
        position: relative;
    }
    .stepwizard-step button[disabled] {
        /*opacity: 1 !important;
        filter: alpha(opacity=100) !important;*/
    }
    .stepwizard .btn.disabled, .stepwizard .btn[disabled], .stepwizard fieldset[disabled] .btn {
        opacity:1 !important;
        color:#bbb;
    }
    .stepwizard-row:before {
        top: 14px;
        bottom: 0;
        position: absolute;
        content:" ";
        width: 100%;
        height: 1px;
        background-color: #ccc;
        z-index: 0;
    }
    .stepwizard-step {
        font-weight: bold;
        display: table-cell;
        text-align: center;
        position: relative;
    }
    .btn-circle {
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        line-height: 1.428571429;
        border-radius: 15px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <strong>Company Association</strong>
            </div>
            <div class="card-body">
                <div class="stepwizard">
                    <div class="stepwizard-row setup-panel">
                        <div class="stepwizard-step col-xs-6">
                            <a href="#step-1" type="button" class="btn btn-success btn-circle step1">1</a>
                            <p><small>Step 1</small></p>
                        </div>
                        <div class="stepwizard-step col-xs-6 ">
                            <a href="#step-2" type="button" class="btn btn-default btn-circle step2" disabled="disabled">2</a>
                            <p><small>Step 2</small></p>
                        </div>
                    </div>
                </div>

                <form role="form" method="post" action="/client/company-association/store">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
                    <input type="hidden" name="org_company_id" id="org_company_id" value=""/>
                    <div class="setup-content" id="step-1">
                        {{--		            <div class="panel-heading">--}}
                        {{--		                 <h3 class="panel-title">প্রতিষ্ঠানের তথ্য</h3>--}}
                        {{--		            </div>--}}
                        {{--		            <div class="panel-body">--}}
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 form-input row">
                                    {!! Form::label('company_name_bangla', trans('CompanyProfile::messages.company_name_bangla'),
                                    ['class'=>'col-md-5 required-star']) !!}
                                    <div class="col-md-7 {{$errors->has('company_name_bangla') ? 'has-error': ''}}">
                                        {!! Form::text('company_name_bangla', '', ['placeholder' => trans("CompanyProfile::messages.write_company_name_bangla"),
                                       'class' => 'form-control input-md required bnEng','id'=>'company_name_bangla','required'=>'required']) !!}
                                        {!! $errors->first('company_name_bangla','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-6 form-input row">
                                    {!! Form::label('company_type_id', trans('CompanyProfile::messages.company_type'),
                                    ['class'=>'col-md-5 required-star']) !!}
                                    <div class="col-md-7 {{$errors->has('company_type') ? 'has-error': ''}}">
                                        {!! Form::select('company_type_id', $companyType, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'company_type_id','required'=>'required']) !!}
                                        {!! $errors->first('company_type_id','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 form-input row">
                                    {!! Form::label('company_name_english', trans('CompanyProfile::messages.company_name_english'),
                                    ['class'=>'col-md-5 required-star']) !!}
                                    <div class="col-md-7 {{$errors->has('company_name_english') ? 'has-error': ''}}">
                                        {!! Form::text('company_name_english','', ['placeholder' => trans("CompanyProfile::messages.write_company_name_english"),
                                       'class' => 'form-control input-md required bnEng','id'=>'company_name_english','required'=>'required']) !!}
                                        {!! $errors->first('company_name_english','<span class="help-block">:message</span>') !!}
                                        <span id="name_validation" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 form-input row">
                                    {!! Form::label('reg_type_id', trans('CompanyProfile::messages.reg_type'),
                                    ['class'=>'col-md-5 required-star']) !!}
                                    <div class="col-md-7 {{$errors->has('reg_type_id') ? 'has-error': ''}}">
                                        {!! Form::select('reg_type_id', $regType, '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select"),'id'=>'reg_type_id','required'=>'required']) !!}
                                        {!! $errors->first('reg_type_id','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 form-input row">
                                    {!! Form::label('company_office_division_id', trans('CompanyProfile::messages.division'),
                                    ['class'=>'col-md-5 required-star']) !!}
                                    <div class="col-md-7 {{$errors->has('company_office_division_id') ? 'has-error': ''}}">
                                        {!! Form::select('company_office_division_id', $divisions, '', ['class' =>'form-control input-md required','required'=>'required','placeholder'=> trans("CompanyProfile::messages.select_division"),
'id'=>'company_office_division_id', 'onchange'=>"getDistrictByDivisionId('company_office_division_id', this.value, 'company_office_district_id')"]) !!}
                                        {!! $errors->first('company_office_division_id','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="col-md-6 form-input row">
                                    {!! Form::label('company_office_district_id', trans('CompanyProfile::messages.district'),
                                    ['class'=>'col-md-5 required-star']) !!}
                                    <div class="col-md-7 {{$errors->has('company_office_district_id') ? 'has-error': ''}}">
                                        {!! Form::select('company_office_district_id', [], '', ['class' =>'form-control input-md required','required'=>'required','placeholder'=> trans("CompanyProfile::messages.select_district"),
'id'=>'company_office_district_id', 'onchange'=>"getThanaByDistrictId('company_office_district_id', this.value, 'company_office_thana_id')"]) !!}
                                        {!! $errors->first('company_office_district_id','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 form-input row">

                                </div>

                                <div class="col-md-6 form-input row">
                                    {!! Form::label('company_office_thana_id', trans('CompanyProfile::messages.thana'),
                                    ['class'=>'col-md-5 required-star']) !!}
                                    <div class="col-md-7 {{$errors->has('company_office_thana_id') ? 'has-error': ''}}">
                                        {!! Form::select('company_office_thana_id', [], '', ['class' =>'form-control input-md required','placeholder'=> trans("CompanyProfile::messages.select_thana"),'id'=>'company_office_thana_id','required'=>'required']) !!}
                                        {!! $errors->first('company_office_thana_id','<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div id="warning_message" class="alert alert-dismissible" role="alert"></div>

                        <button style="margin-right: 30px" class="btn btn-primary nextBtn float-right" type="button">Search & Next</button>
                    </div>
                    {{--		        </div>--}}



                    <div class="panel panel-primary setup-content" id="step-2">
                        {{--		            <div class="panel-heading">--}}
                        {{--		                 <h3 class="panel-title">ব্যবহার কারীর তথ্য</h3>--}}
                        {{--		            </div>--}}
                        <div class="panel-body">
                            <div id="status_message" class="alert alert-dismissible" role="alert"></div>

                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6 row">
                                                {!! Form::label('company_name_bangla_data', trans('CompanyProfile::messages.company_name_bangla'),
                                                ['class'=>'col-md-5']) !!}
                                                <div class="col-md-7">
                                                    : <span id="company_name_bangla_data"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-input row">
                                                {!! Form::label('company_name_data', trans('CompanyProfile::messages.company_name_english'),
                                                ['class'=>'col-md-5']) !!}
                                                <div class="col-md-7">
                                                    : <span id="company_name_english_data"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6 row">
                                                {!! Form::label('company_type', trans('CompanyProfile::messages.company_type'),
                                                ['class'=>'col-md-5']) !!}
                                                <div class="col-md-7">
                                                    : <span id="company_type"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-input row">
                                                {!! Form::label('reg_type', trans('CompanyProfile::messages.reg_type'),
                                                ['class'=>'col-md-5']) !!}
                                                <div class="col-md-7">
                                                    : <span id="reg_type"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6 row">
                                                {!! Form::label('division', trans('CompanyProfile::messages.division'),
                                                ['class'=>'col-md-5']) !!}
                                                <div class="col-md-7">
                                                    : <span id="division"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-input row">
                                                {!! Form::label('district', trans('CompanyProfile::messages.district'),
                                                ['class'=>'col-md-5']) !!}
                                                <div class="col-md-7">
                                                    : <span id="district"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6 row">
                                                {!! Form::label('thana', trans('CompanyProfile::messages.thana'),
                                                ['class'=>'col-md-5']) !!}
                                                <div class="col-md-7">
                                                    : <span id="thana"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6 form-input user_name hidden row">
                                                {!! Form::label('user_name', trans('CompanyProfile::messages.user_name'),
                                                ['class'=>'col-md-5']) !!}
                                                <div class="col-md-7">
                                                    : <span id="user_name"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-input email hidden row">
                                                {!! Form::label('email', trans('CompanyProfile::messages.email'),
                                                ['class'=>'col-md-5']) !!}
                                                <div class="col-md-7">
                                                    : <span id="email"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6 form-input phone hidden row">
                                                {!! Form::label('email', trans('CompanyProfile::messages.mobile_no'),
                                                ['class'=>'col-md-5']) !!}
                                                <div class="col-md-7">
                                                    : <span class="input_ban" id="mobile"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-input last_update hidden row">
                                                {!! Form::label('last_update', trans('CompanyProfile::messages.last_login'),
                                                ['class'=>'col-md-5']) !!}
                                                <div class="col-md-7">
                                                    : <span class="" id="last_update"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                           
                            

                            <div class="form-group" id="save_company" style="display:none;">
                                <div class="checkbox form-input">
                                    <label style="font-weight: 600;">
                                        {!! Form::checkbox('save_company_yes',1,null, array('id'=>'save_company_yes', 'class'=>'', 'checked' => false)) !!}
                                        আপনি কি নতুন কোম্পানি অন্তর্ভুক্তি করতে চান?
                                    </label>
                                </div>
                            </div>


                            <button class="btn btn-primary float-right" type="submit">Submit</button>
                            <button class="btn btn-success float-right previousBtn" style="margin-right:10px;" type="button">Previous</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
<script src="{{ asset("assets/scripts/moment.min.js") }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.nextBtn ').attr('disabled', true);

        var navListItems = $('div.setup-panel div a'),
            allWells = $('.setup-content'),
            allNextBtn = $('.nextBtn'),
            allPreviousBtn = $('.previousBtn')
        ;

        allWells.hide();

        navListItems.click(function (e) {
            e.preventDefault();
            var $target = $($(this).attr('href')),
                $item = $(this);

            if (!$item.hasClass('disabled')) {
                navListItems.removeClass('btn-success').addClass('btn-default');
                $item.addClass('btn-success');
                allWells.hide();
                $target.show();
                $target.find('input:eq(0)').focus();
            }
        });

        allPreviousBtn.click(function(){
            var curStep = $(this).closest(".setup-content"),
                curStepBtn = curStep.attr("id"),
                previousStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

            previousStepWizard.trigger('click');
            $('#step-2').hide();
            $('.step2').removeClass('btn-success');
            $('#step-1').show();
            $('.step1').addClass('btn-success');
        });

        allNextBtn.click(async function () {
            var curStep = $(this).closest(".setup-content"),
                curStepBtn = curStep.attr("id"),
                nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                curInputs = curStep.find("input[type='text'],input[type='checkbox'],input[type='radio'],input[type='url'],select"),
                isValid = true;



            $(".form-input").removeClass("has-error");
            for (var i = 0; i < curInputs.length; i++) {
                if (!curInputs[i].validity.valid) {
                    isValid = false;
                    $(curInputs[i]).closest(".form-input").addClass("has-error");
                }
            }

            if(isValid && curStepBtn == 'step-1'){

                $.ajax({
                    url: "{{ url("client/company-association/company-info")}}",
                    type: "POST",
                    data: {
                        companyname: $('#company_name_english').val(),
                        companyThana: $('#company_office_thana_id').val(),
                        companyType: $('#company_type_id').val(),
                        _token : $('input[name="_token"]').val()
                    },
                    success: function(response){
                        if (response.responseCode == 1){

                            $('#step-1').hide();
                            $('.step1').removeClass('btn-success');
                            $('#step-2').show();
                            $('.step2').addClass('btn-success');
                            $("#save_company_yes").prop('checked',false)
                            if (isValid) nextStepWizard.removeAttr('disabled').trigger('click');

                            var message = '';
                            var cl = ""
                            $("#status_message").removeClass('alert alert-danger alert-success');
                            if(response.status == 1){
                                cl = "alert alert-success"
                                message = "আপনার কোম্পানিটি পাওয়া গিয়েছে। এপ্রুভাল এর জন্য নিচের ব্যবহারকারীকে এসাইন করুন।";
                                $("#company_name_bangla_data").html(response.company_data.org_nm_bn);
                                $("#company_name_english_data").html(response.company_data.org_nm);
                                $("#company_type").html(response.company_data.company_type_bn);
                                $("#reg_type").html(response.company_data.regist_type_bn);
                                $("#division").html(response.company_data.division);
                                $("#district").html(response.company_data.district);
                                $("#thana").html(response.company_data.thana);
                                $("#user_name").html(response.last_login_data.user_name);
                                $('#last_update').html(response.last_login_data.time);

                                // Hide mobile number charecter
                                var number = response.last_login_data.user_mobile;
                                var numberLength = number.length;
                                if (numberLength % 2 == 0){
                                    var middleIndex = numberLength/2;
                                }else{
                                    var middleIndex = (numberLength-1)/2;
                                }
                                var startIndexOfMiddle = middleIndex-1;
                                var endIndexOfMiddle = middleIndex+2;
                                var hidden_number = number.substring(0, startIndexOfMiddle) + "***" + number.substring(endIndexOfMiddle);
                                $("#mobile").html(hidden_number);

                                // Hide email charecter
                                var email = response.last_login_data.user_email;
                                var parts = email.split("@"), len = parts[0].length;
                                if (len % 2 == 0){
                                    var middleIndex = len/2;
                                }else{
                                    var middleIndex = (len-1)/2;
                                }
                                var startIndexOfMiddle = middleIndex-1;
                                var endIndexOfMiddle = middleIndex+2;
                                var hidden_email = email.substring(0, startIndexOfMiddle) + "***" + email.substring(endIndexOfMiddle);
                                $("#email").html(hidden_email);

                                $(".last_update").removeClass('hidden');
                                $(".email").removeClass('hidden');
                                $(".phone").removeClass('hidden');
                                $(".user_name").removeClass('hidden');

                                $("#user_type_section").show();
                                $("#bscic_user_section").hide()
                                $("#company_user_section").hide();
                                $("#save_company").hide();
                                $("#org_company_id").val(response.company_id);
                                $("#save_company_yes").prop('required',false);
                                $(".user_type").prop('required',true);
                                html = '<option value="">নির্বাচন করুন</option>';

                                $.each(response.companyusers, function(index, value) {
                                    html += '<option value="' + index+ '" >' + value + '</option>';
                                });
                                $('#company_user').html(html);
                            } else{
                                $("#company_name_bangla_data").html($("#company_name_bangla").val());
                                $("#company_name_english_data").html($("#company_name_english").val());
                                $("#company_type").html($("#company_type_id option:selected").text());
                                $("#reg_type").html($("#reg_type_id option:selected").text());
                                $("#division").html($("#company_office_division_id option:selected").text());
                                $("#district").html($("#company_office_district_id option:selected").text());
                                $("#thana").html($("#company_office_thana_id option:selected").text());
                                $(".last_update").addClass('hidden');
                                $(".email").addClass('hidden');
                                $(".phone").addClass('hidden');
                                $(".user_name").addClass('hidden');
                                // cl = "alert alert-danger"
                                // message = "Company Not Found";
                                $("#user_type_section").hide();
                                $("#bscic_user_section").hide()
                                $("#company_user_section").hide();
                                $("#save_company").show();
                                $("#save_company_yes").prop('required',true);
                                $(".user_type").prop('required',false);
                            }

                            $("#status_message").text(message);
                            $("#status_message").addClass(cl);
                        }else if(response.responseCode == 2) {
                            // swal({
                            //     type: 'warning',
                            //     text: 'কোম্পানিটি ইতমধ্যে আসোসিয়েটেড অবস্থায় আছে',
                            // });

                            toastr.error("কোম্পানিটি ইতমধ্যে আসোসিয়েটেড অবস্থায় আছে")
                        }else if(response.responseCode == 3) {
                            // swal({
                            //     type: 'warning',
                            //     text: 'ইতিমধ্যেই এই কোম্পানিটি এসোসিয়েশন এর জন্য আবেদন করা হয়েছে',
                            // });
                            toastr.error("ইতিমধ্যেই এই কোম্পানিটি এসোসিয়েশন এর জন্য আবেদন করা হয়েছে")
                        }
                    },
                    error: function (jqXHR, exception) {
                        alert("something was wrong")
                        console.log(jqXHR);
                    }
                });

                // $.when(
                //    getCompanyData(),
                //    console.log(2345)
                //  ).then(function() {
                //    console.log('22')
                //    if (isValid) nextStepWizard.removeAttr('disabled').trigger('click');
                //  });

            }else{
                if (isValid) nextStepWizard.removeAttr('disabled').trigger('click');
            }


        });

        $('div.setup-panel div a.btn-success').trigger('click');
    });


    // function getCompanyData() {
    //
    //
    //
    // }

    $(".user_type").on('change',function(){
        var userType = $(this).val();
        if(userType == "bscic"){
            $("#bscic_user_section").show();
            $("#company_user_section").hide();
            $("#bscic_user").prop('required',true);
            $("#company_user").prop('required',false);
        }else if(userType == "company"){
            $("#company_user_section").show();
            $("#bscic_user_section").hide()
            $("#bscic_user").prop('required',false);
            $("#company_user").prop('required',true);
        }else{
            $("#bscic_user_section").hide()
            $("#company_user_section").hide();
            $("#bscic_user").prop('required',false);
            $("#company_user").prop('required',false);
        }
    })

    $('#company_name_english').on('keyup',function(){
        var name = $('#company_name_english').val();
        var lastword = name.split(" ").pop();
        var companyTypeId = $('#company_type_id').val();

        if (companyTypeId == 3 || companyTypeId == 4){
            if (lastword == 'Ltd' || lastword == 'ltd' || lastword == 'Ltd.' || lastword == 'ltd.' || lastword == 'limited' || lastword == 'Limited' || lastword == 'limited.' || lastword == 'Limited.'){
                $('.nextBtn ').attr('disabled', false);
                $('#name_validation').text('');
            }else{
                $('.nextBtn ').attr('disabled', true);
                $('#name_validation').text('প্রতিষ্ঠানের নামের শেষে "Ltd/Limited" থাকা বাঞ্চনীয়');
            }
        }

        if (companyTypeId == 1 || companyTypeId == 2 || companyTypeId == 5){
            if (lastword == 'Ltd' || lastword == 'ltd' || lastword == 'Ltd.' || lastword == 'ltd.' || lastword == 'limited' || lastword == 'Limited' || lastword == 'limited.' || lastword == 'Limited.'){
                $('#name_validation').text('প্রতিষ্ঠানের নামের শেষে "Ltd/Limited" থাকা যাবেনা');
                $('.nextBtn ').attr('disabled', true);
            }else{
                $('#name_validation').text('');
                $('.nextBtn ').attr('disabled', false);
            }

        }

    })

    $('#company_type_id').on('change',function(){
        var companyTypeId = $('#company_type_id').val();
        var name = $('#company_name_english').val();
        var lastword = name.split(" ").pop();

        if (companyTypeId == 3 || companyTypeId == 4){
            $('#name_validation').text('প্রতিষ্ঠানের নামের শেষে "Ltd" থাকা বাঞ্চনীয়');

            if ((lastword == 'Ltd' || lastword == 'ltd' || lastword == 'Ltd.' || lastword == 'ltd.' || lastword == 'limited' || lastword == 'Limited' || lastword == 'limited.' || lastword == 'Limited.') && name != ''){
                $('.nextBtn ').attr('disabled', false);
                $('#name_validation').text('');
            }else{
                $('#name_validation').text('প্রতিষ্ঠানের নামের শেষে "Ltd/Limited" থাকা বাঞ্চনীয়');
                $('.nextBtn ').attr('disabled', true);
            }

        }

        if (companyTypeId == 1 || companyTypeId == 2 || companyTypeId == 5){
            if (lastword == 'Ltd' || lastword == 'ltd' || lastword == 'Ltd.' || lastword == 'ltd.' || lastword == 'limited.' || lastword == 'Limited.' || lastword == 'limited' || lastword == 'Limited' || name == ''){
                $('#name_validation').text('প্রতিষ্ঠানের নামের শেষে "Ltd/Limited" থাকা যাবেনা');
                $('.nextBtn ').attr('disabled', true);
            }else{
                $('#name_validation').text('');
                $('.nextBtn ').attr('disabled', false);
            }

        }

        if (companyTypeId ==  6 || companyTypeId == 7){
            $('.nextBtn ').attr('disabled', false);
            $('#name_validation').text('');
        }
    })

    // if (ltd_validation === false){
    //     $('.nextBtn ').attr('disabled', true);
    // }else{
    //     $('.nextBtn ').attr('disabled', false);
    // }
</script>
