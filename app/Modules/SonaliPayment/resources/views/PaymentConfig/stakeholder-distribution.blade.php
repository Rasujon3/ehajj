{!! Form::open(array('url' => url('/spg/stakeholder-distribution'),'method' => 'post', 'class' => 'form-horizontal smart-form','id'=>'stakeholderDistribution',
        'enctype' =>'multipart/form-data', 'files' => 'true', 'role' => 'form')) !!}

<div class="modal-header">
    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-th-large"></i> New Payment Distribution</h4>

    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
    </button>
</div>

<div class="modal-body">
    <div class="errorMsg alert alert-danger alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>
    <div class="successMsg alert alert-success alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    </div>

    <input name="pay_config_id" type="hidden" value="{{ \App\Libraries\Encryption::encodeId($paymentConfig->id) }}">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group row {{$errors->has('stakeholder_ac_name') ? 'has-error' : ''}}">
                {!! Form::label('stakeholder_name','Account Name : ',['class'=>'col-md-4 text-right required-star']) !!}
                <div class="col-md-8">
                    {!! Form::text('stakeholder_ac_name', null, ['class'=>'form-control required input-sm','placeholder'=>'Stakeholder Name']) !!}
                    {!! $errors->first('stakeholder_ac_name','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="form-group row {{$errors->has('stakeholder_ac_no') ? 'has-error' : ''}}">
                {!! Form::label('stakeholder_ac_no','Account No : ',['class'=>'col-md-4 text-right required-star']) !!}
                <div class="col-md-8">
                    {!! Form::text('stakeholder_ac_no', null, ['class'=>'form-control required input-sm','placeholder'=>'Stakeholder Account No']) !!}
                    {!! $errors->first('stakeholder_ac_no','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="form-group row {{$errors->has('amount') ? 'has-error' : ''}}">
                {!! Form::label('purpose','Purpose: ',['class'=>'col-md-4 text-right  required-star']) !!}
                <div class="col-md-8">
                    {!! Form::text('purpose', null, ['class'=>'form-control required input-sm','placeholder'=>'Purpose']) !!}
                    {!! $errors->first('purpose','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

      <div class="row">
        <div class="col-lg-12">
            <div class="form-group row {{$errors->has('purpose_sbl') ? 'has-error' : ''}}">
                {!! Form::label('purpose_sbl','Purpose (SBL): ',['class'=>'col-md-4 text-right']) !!}
                <div class="col-md-8 {{$errors->has('purpose_sbl') ? 'has-error' : ''}}">
                    <label>{!! Form::radio('purpose_sbl', 'TRN', true, ['class'=>'required', 'id' => 'yes']) !!} TRN</label>
                    <label>{!! Form::radio('purpose_sbl', 'CHL', false, ['class'=>' required', 'id' => 'no']) !!} CHL</label>
                    {!! $errors->first('purpose_sbl','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

     <div class="row">
        <div class="col-md-12">
            <div class="form-group row {{$errors->has('status') ? 'has-error' : ''}}">
                {!! Form::label('fix_status','Fix Status: ',['class'=>'col-md-4 text-right required-star']) !!}
                <div class="col-md-8 {{$errors->has('fix_status') ? 'has-error' : ''}}">
                    <label>{!! Form::radio('fix_status', 1, true, ['class'=>'required', 'id'=>'fixed']) !!}
                        Fixed</label>
                    <label>{!! Form::radio('fix_status', 0, false, ['class'=>' required', 'id'=>'un_fixed']) !!}
                        Unfixed</label>
                    {!! $errors->first('fix_status','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12" id="payAmountField">
            <div class="form-group row {{$errors->has('amount') ? 'has-error' : ''}}">
                {!! Form::label('pay_amount','Amount: ',['class'=>'col-md-4 text-right required-star']) !!}
                <div class="col-md-8">
                    {!! Form::text('pay_amount', null, ['class'=>'form-control required input-sm', 'placeholder'=>'Amount']) !!}
                    {!! $errors->first('pay_amount','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

     <div class="row">
        <div class="col-lg-12">
            <div class="form-group row {{$errors->has('distribution_type') ? 'has-error' : ''}}">
                {!! Form::label('distribution_type','Distribution type: ',['class'=>'col-md-4 text-right required-star']) !!}
                <div class="col-md-8">
                    {!! Form::select('distribution_type', $distribution_types, '', ['class'=>'form-control input-sm required', 'placeholder'=>'Select distribution type']) !!}
                    {!! $errors->first('distribution_type','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group row {{$errors->has('status') ? 'has-error' : ''}}">
                {!! Form::label('status','Status: ',['class'=>'col-md-4 text-right required-star']) !!}
                <div class="col-md-8 {{$errors->has('status') ? 'has-error' : ''}}">
                    <label>{!! Form::radio('status', 1, true, ['class'=>'required']) !!} Active</label>
                    <label>{!! Form::radio('status', 0, false, ['class'=>' required']) !!} Inactive</label>
                    {!! $errors->first('status','<span class="help-block">:message</span>') !!}
                </div>
            </div>
        </div>
    </div>



{{--    <div class="row">--}}
{{--        <div class="col-lg-12" id="distTypeField" hidden>--}}
{{--            <div class="form-group col-md-12 {{$errors->has('distribution_type') ? 'has-error' : ''}}">--}}
{{--                {!! Form::label('distribution_type','Distribution type: ',['class'=>'col-md-4 text-right required-star']) !!}--}}
{{--                <div class="col-md-8">--}}
{{--                    {!! Form::select('distribution_type', $distribution_types, '', ['class'=>'form-control input-sm', 'placeholder'=>'Select distribution type']) !!}--}}
{{--                    {!! $errors->first('distribution_type','<span class="help-block">:message</span>') !!}--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>

<div class="modal-footer">
    <div class="float-left">
        {!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'class' => 'btn btn-danger', 'data-dismiss' => 'modal')) !!}
    </div>
    <div class="float-right">
        @if(\App\Modules\SonaliPayment\Library\SonaliPaymentACL::getAccessRight('SonaliPayment','A'))
            <button type="submit" class="btn btn-primary" id="action_btn" name="actionBtn" value="draft">
                <i class="fa fa-chevron-circle-right"></i> Save
            </button>
        @endif
    </div>
    <div class="clearfix"></div>
</div>
{!! Form::close() !!}
{{--<script src="{{ mix("assets/scripts/jquery.validate.min.js") }}"></script>--}}
<script src="{{ asset("assets/scripts/jquery.validate.min.js") }}"></script>

<script>
    $(document).ready(function () {
        $("#stakeholderDistribution").validate({
            errorPlacement: function () {
                return true;
            },
            submitHandler: formSubmit
        });

        var form = $("#stakeholderDistribution"); //Get Form ID
        var url = form.attr("action"); //Get Form action
        var type = form.attr("method"); //get form's data send method
        var info_err = $('.errorMsg'); //get error message div
        var info_suc = $('.successMsg'); //get success message div

        //============Ajax Setup===========//
        function formSubmit() {
            $.ajax({
                type: type,
                url: url,
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function (msg) {
                    console.log("before send");
                    $("#action_btn").html('<i class="fa fa-cog fa-spin"></i> Loading...');
                    $("#action_btn").prop('disabled', true); // disable button
                },
                success: function (data) {
                    //==========validation error===========//
                    if (data.success == false) {
                        info_err.hide().empty();
                        $.each(data.error, function (index, error) {
                            info_err.removeClass('hidden').append('<li>' + error + '</li>');
                        });
                        info_err.slideDown('slow');
                        info_err.delay(2000).slideUp(1000, function () {
                            $("#action_btn").html('Submit');
                            $("#action_btn").prop('disabled', false);
                        });
                    }
                    //==========if data is saved=============//
                    if (data.success == true) {
                        info_suc.hide().empty();
                        info_suc.removeClass('hidden').html(data.status);
                        info_suc.slideDown('slow');
                        info_suc.delay(2000).slideUp(800, function () {
                            window.location.href = data.link;
                        });
                        form.trigger("reset");

                    }
                    //=========if data already submitted===========//
                    if (data.error == true) {
                        info_err.hide().empty();
                        info_err.removeClass('hidden').html(data.status);
                        info_err.slideDown('slow');
                        info_err.delay(1000).slideUp(800, function () {
                            $("#action_btn").html('Submit');
                            $("#action_btn").prop('disabled', false);
                        });
                    }
                },
                error: function (data) {
                    var errors = data.responseJSON;
                    $("#action_btn").prop('disabled', false);
                    console.log(errors);
                    alert('Sorry, an unknown Error has been occured! Please try again later.');
                }
            });
            return false;
        }
    });
</script>
<script>
    $("#un_fixed").click(function () {
        if ($(this).is(':checked')) {
            $("#payAmountField").hide();
            $("#pay_amount").removeClass("required");

            // $("#distTypeField").show();
            // $("#distribution_type").addClass("required");
        }
    });

    $("#fixed").click(function () {
        if ($(this).is(':checked')) {
            $("#payAmountField").show();
            $("#pay_amount").addClass("required");

            // $("#distTypeField").hide();
            // $("#distribution_type").removeClass("required");
        }
    });
</script>
