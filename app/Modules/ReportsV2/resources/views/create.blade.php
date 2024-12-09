@extends('layouts.admin')

@section('page_heading','<i class="fa fa-book fa-fw"></i> '.trans('messages.report_form'))
@section('content')
    <link rel="stylesheet" href="{{ asset("assets/plugins/select2.min.css") }}">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<?php
$accessMode = ACL::getAccsessRight('reportv2');
if (!ACL::isAllowed($accessMode, 'V'))
    die('no access right!');
?>

    <div class="col-lg-12">

        {!! Session::has('success') ? '<div class="alert alert-success alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("success") .'</div>' : '' !!}
        {!! Session::has('error') ? '<div class="alert alert-danger alert-dismissible"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'. Session::get("error") .'</div>' : '' !!}

        <div class="card card-magenta border border-magenta">
            <div class="card-header">
                <h5 class="card-title">  {!!trans('messages.report_form')!!}</h5>
            </div>
            <!-- /.panel-heading -->
            <div class="card-body">
                {!! Form::open(['url' => '/reportv2/store', 'method' => 'patch', 'class' => 'form report_form', 'role' => 'form', 'id' => 'report_form']) !!}
                <div class="row">
                    <div class="col-sm-8">
                        <!-- text input -->
                        <div class="form-group row {{$errors->has('report_title') ? 'has-error' : ''}}">
                            {!! Form::label('report_title','Report Title', ['class'=>'required-star']) !!}
                            {!! Form::text('report_title','',['class'=>'form-control bnEng required report_title','placeholder'=>'Enter report name...']) !!}
                            {!! $errors->first('report_title','<span class="help-block">:message</span>') !!}
                        </div>

                        <div class="form-group row {{$errors->has('report_category') ? 'has-error' : ''}}">
                            {!! Form::label('report_category','Report Category', ['class'=>'']) !!}
                            {!! Form::text('report_category','',['class'=>'form-control bnEng report_category','placeholder'=>'Enter report category...', 'id'=>'tags']) !!}
                            <input type="hidden" id="tags_value" name="tags_value">
                            {!! $errors->first('report_category','<span class="help-block">:message</span>') !!}
                        </div>

                        <!-- radio -->
                        <div class="form-group row">
                            <div class="radio">
                                <label>
                                    {!! Form::radio('status', '1', true) !!}
                                    Published
                                </label>
                                <label>
                                    {!! Form::radio('status', '0') !!}
                                    Unpublished
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <!-- select -->
                        <div class="form-group {{$errors->has('selection_type') ? 'has-error' : ''}}">
                            {!! Form::label('selection_type','Permission based on :', ['class'=>'required-star']) !!}
                            {!! Form::select('selection_type',[''=>'Select One','1'=>'User Specific','2'=>'User Type Specific'], '',['class' => 'form-control required limitedNumbSelect2 user_id']) !!}
                            {!! $errors->first('selection_type','<span class="help-block">:message</span>') !!}
                        </div>
                        {{--<div class="form-group {{$errors->has('user_id') ? 'has-error' : ''}}">
                            {!! Form::label('user_id','Who can view?') !!}
                            {!! Form::select('user_id[]', $usersList, '',['class' => 'form-control required limitedNumbSelect2 user_id','multiple']) !!}
                            {!! $errors->first('user_id','<span class="help-block">:message</span>') !!}
                        </div>--}}
                        <fieldset style="display: none" ; id="permission_data">
                            <fieldset class="scheduler-border" style="padding: 0px;display: none;"
                                      id="type_specific_data">
                                <legend class="scheduler-border" style="color: gray;margin-bottom:3px;">Permission to
                                    user-type
                                </legend>
                                <div class="form-group {{$errors->has('user_id') ? 'has-error' : ''}}">
                                    {!! Form::label('user_id','Please select user type(s)') !!}
                                    {!! Form::select('user_id[]', $usersList, '',['class' => 'form-control required user_id','multiple']) !!}
                                    {!! $errors->first('user_id','<span class="help-block">:message</span>') !!}
                                </div>
                            </fieldset>


                            <fieldset class="scheduler-border" style="padding: 0px;display: none;"
                                      id="user_specific_data">
                                <legend class="scheduler-border" style="color: gray;margin-bottom:3px;">Permission to
                                    specific users
                                </legend>
                                <div class="form-group {{$errors->has('user_type') ? 'has-error' : ''}}">
                                    {!! Form::label('user_type','Please select user type(s)') !!}
                                    {!! Form::select('user_type[]', $usersList, '',['class' => 'form-control  required user_id','multiple','id'=>'user_types','size' => 10]) !!}
                                    {!! $errors->first('user_type','<span class="help-block">:message</span>') !!}
                                </div>
                                {!! Form::label('users','Please select user(s)') !!}
                                <select id="mySelect2" name="users[]" class="city form-control limitedNumbSelect2"
                                        required="true" data-placeholder="Select users" style="width: 100%;"
                                        multiple="multiple">

                                </select>
                            </fieldset>

                        </fieldset>
                    </div>
                </div>

                {!! Form::hidden('redirect_to_new',0,['class'=>'form-control redirect_to_new']) !!}

                <div class="col-sm-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tab_1" aria-expanded="true"><i
                                        class="fa fa-code"></i> SQL</a></li>
                            <li class="results_tab nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab_2" aria-expanded="false"><i
                                        class="fa fa-table"></i> Results</a></li>
                            <li class="db_tables nav-item"><a class="nav-link" data-toggle="tab" href="#tab_3" aria-expanded="false"><i
                                        class="fa fa-university"></i> Database objects</a></li>
                            <li class="help nav-item"><a class="nav-link" data-toggle="tab" href="#tab_4" aria-expanded="false"><i
                                        class="fa fa-question-circle"></i> Help</a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="tab_1" class="tab-pane active">
                                <!-- textarea -->
                                <div class="form-group {{$errors->has('report_para1') ? 'has-error' : ''}}">
                                    {!! Form::textarea('report_para1', '', ['class'=>'sql autoCompleteSuggest required form-control well fa-code-fork']) !!}
                                    {!! $errors->first('report_para1','<span class="help-block">:message</span>') !!}
                                </div>
                            </div><!-- /.tab-pane -->

                            <div id="tab_2" class="tab-pane">
                                <div class="results">
                                    <br/>
                                    <br/>
                                    Please click on Verify button to run the SQL.
                                    <br/>
                                    <br/>
                                    <br/>
                                </div>
                            </div><!-- /.tab-pane -->
                            <div id="tab_3" class="tab-pane">
                                <div class="db_fields">
                                    <br/>
                                    Please click on Show Tables button to run the SQL.
                                    <br/>
                                    you can run SELECT statement to generate a report.
                                    <br/>
                                    SysAdmin can execute SHOW, DESC, EXPLAIN statement!
                                    <br/>
                                    <br/>
                                    <br/>
                                </div>
                            </div><!-- /.tab-pane -->
                            <div id="tab_4" class="tab-pane">
                                <div class="help">
                                    @include('ReportsV2::help')
                                </div>
                            </div><!-- /.tab-pane -->
                        </div><!-- /.tab-content -->
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        @if(ACL::getAccsessRight('reportv2','A'))
                            {!! Form::button('<i class="fa fa-save"></i> Save', array('type' => 'button', 'value'=> 'save', 'class' => 'btn btn-success save')) !!}
                            {!! Form::button('<i class="fa fa-credit-card"></i> Save & Run', array('type' => 'button', 'value'=> 'save_new', 'class' => 'btn btn-warning save')) !!}
                        @endif
                        <a href="/reportv2">{!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'class' => 'btn btn-danger')) !!}</a>

                        <span class="pull-right">
                    @if(ACL::getAccsessRight('reportv2','E'))
                                <button class="btn btn-sm btn-primary" id="verifyBtn" type="button">
                        <i class="fa fa-check"></i>
                        Verify
                    </button>
                                <button class="btn btn-sm btn-primary" id="showTables" type="button">
                        <i class="fa fa-list"></i> Show Tables
                    </button>
                            @endif
                </span>
                    </div>
                </div>


                {!! Form::close() !!}
            </div><!-- /.box-body -->
        </div>
    </div>

@endsection

@section('footer-script')
    <script src="{{ asset("assets/plugins/select2.min.js") }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset("assets/scripts/jquery.validate.min.js") }}"></script>
<script language="javascript">
    $(document).ready(
            function () {
                $('.save').click(function () {
                    switch ($(this).val()) {
                        case 'save_as_new' :
                            $('.report_title').val($('.report_title').val() + "-{{ Carbon\Carbon::now() }}");
                            $('.report_form').attr('action', "{!! URL::to('/reportv2/store') !!}");
                            break;
                        case 'save_new':
                            $('.redirect_to_new').val(1);
                            break;
                        default:
                    }
                    $('.report_form').submit();
                });
                $(".limitedNumbSelect2").select2({
                    //maximumSelectionLength: 1
                });
                $('#user_types').on('change',function () {
                    var types = $('#user_types').val();
                    var _token = $('input[name="_token"]').val();
                    var userSelect = $('#mySelect2');
                    $.ajax({
                        url: '/reportv2/getuserbytype',
                        type: 'GET',
                        data: {types: types},
                        success: function (data) {
                            console.log(data);
                            // var option = new Option(data.user_full_name, data.id, true, true);
                            // userSelect.append(option).trigger('change');
                            var option = '';
                            $.each(data, function (id, value) {
                                option += '<option value="' + value.id + '">' + value.user_full_name+'('+value.user_email +')</option>';
                            });
                            // userSelect.trigger({
                            //     type: 'select2:select',
                            //     params: {
                            //         data: data
                            //     }
                            // });
                            $("#mySelect2").html(option);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('.results').html(jqXHR.responseText);
                            $('.results_tab a').trigger('click');
                        }
                    });
                });

                $('#selection_type').on('change',function () {

                    var types = $('#selection_type').val();
                        ;
                    if (types==2){
                        $('#user_type').find('option:selected').remove().end();
                        $('#mySelect2').find('option:selected').remove().end();
                        $('#permission_data').hide();
                        $('#user_specific_data').hide();
                        $('#type_specific_data').show();
                        $('#permission_data').show();

                    }else if(types==1){
                        $('#user_id').find('option:selected').remove().end();
                        $('#permission_data').hide();
                        $('#type_specific_data').hide();
                        $('#user_specific_data').show();
                        $('#permission_data').show();
                    }else{
                        $('#permission_data').hide();
                    }

                });
                $('#selection_type').change();

                $('#verifyBtn').click(function () {
                    var sql = $('.sql').val();
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: '/reportv2/verify',
                        type: 'POST',
                        data: {sql: sql, _token: _token},
                        dataType: 'text',
                        success: function (data) {
                            $('.results').html(data);
                            $('.results_tab a').trigger('click');
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('.results').html(jqXHR.responseText);
                            $('.results_tab a').trigger('click');
                        }
                    });
                });

                $('#showTables').click(function () {
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: '/reportv2/tables',
                        type: 'GET',
                        data: {_token: _token},
                        dataType: 'text',
                        success: function (data) {
                            $('.db_fields').html(data);
                            $('.db_tables a').trigger('click');
                        }
                    });
                });
            });

    $(document).ready(
            function () {
                $("#report_form").validate({
                    errorPlacement: function () {
                        return false;
                    }
                });
            });
</script>
    <script>
        $( function() {

            $("#tags").autocomplete({
                minLength: 3,
                source: function(request, response) {
                    $.ajax({
                        url: "{{url('reportv2/get-report-category')}}",
                        data: {
                            term : request.term
                        },
                        dataType: "json",
                        success: function(data){
                            var resp = $.map(data,function(obj){
                                return {
                                    label: obj.category_name,
                                    value: obj.id
                                };
                            });

                            response(resp);
                        }
                    });
                },
                select: function (event, ui) {
                    $("#tags").val(ui.item.label); // display the selected text
                    $("#tags_value").val(ui.item.value); // save selected id to hidden input
                    return false;
                }

            });
        } );
        $("#tags").on('keyup', function (){
            $("#tags_value").val('');
        })

    </script>
@endsection
