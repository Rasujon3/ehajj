{!! Form::open(array('url' => '/settings/store-forcefully-data-update','method' => 'post', 'class' => 'form-horizontal smart-form','id'=>'forcefullyDataUpdateForm',
        'enctype' =>'multipart/form-data', 'files' => 'true', 'role' => 'form')) !!}

<div class="modal-header">
    <h4 class="modal-title" id="forcefullyDataUpdateModalLabel">
        {!!trans('messages.new_forcefully_data_update')!!}
    </h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
</div>

<div class="modal-body">
    <div class="errorMsg alert alert-danger alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>
    <div class="successMsg alert alert-success alert-dismissible hidden">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button></div>

    <div class="row">
        <div class="form-group col-md-12 {{$errors->has('table_name') ? 'has-error' : ''}}">
            {!! Form::label('table_name','Table name: ',['class'=>'col-md-2  required-star']) !!}
            <div class="col-md-10">
                {!! Form::text('table_name', null, ['class' => 'form-control required', 'id' => 'table_name']) !!}
                {!! $errors->first('table_name','<span class="help-block">:message</span>') !!}
            </div>
        </div>

        <div class="form-group col-md-12 {{$errors->has('update_type') ? 'has-error' : ''}}">
            {!! Form::label('update_type','Update type: ',['class'=>'col-md-2   required-star']) !!}
            <div class="col-md-10">
                <label class="radio-inline">{!! Form::radio('update_type', 'field', true, ['class'=>'required', 'id' => 'field', 'onchange' => "TypeWiseData(this.value)"]) !!} Field based</label>
                <label class="radio-inline">{!! Form::radio('update_type', 'user', false, ['class'=>'required', 'id' => 'user', 'onchange' => "TypeWiseData(this.value)"]) !!} User based</label>
                <label class="radio-inline">{!! Form::radio('update_type', 'company', false, ['class'=>'required', 'id' => 'company', 'onchange' => "TypeWiseData(this.value)"]) !!} Company based</label>
                {!! $errors->first('update_type','<span class="help-block">:message</span>') !!}
            </div>
        </div>

        <div style="display: none;" class="form-group col-md-12" id="user_list_div">
            {!! Form::label('select_user', 'User:', ['class' => 'col-md-2 required-star']) !!}
            <div class="col-md-10 {{$errors->has('user_id') ? 'has-error': ''}}">
                <select name="user_id"
                        class="form-control select2"
                        id="user_id"
                        data-placeholder="Select user"
                        style="width: 100%;">
                    <option value=""></option>
                    @foreach($users as $user)
                        <option value="{{ \App\Libraries\Encryption::encodeId($user->id) }}">{{ $user->user_info }}</option>
                    @endforeach
                </select>
                <span class="text-danger" style="font-size: 10px; font-weight: bold">[Enter user name or email address.]</span>
                {!! $errors->first('user_id','<span class="help-block">:message</span>') !!}
            </div>
        </div>

        <div style="display: none;" class="form-group col-md-12" id="company_list_div">
            {!! Form::label('select_company', 'Company:', ['class' => 'col-md-2 required-star']) !!}
            <div class="col-md-10 {{$errors->has('company_id') ? 'has-error': ''}}">
                <select name="company_id"
                        class="form-control select2"
                        id="company_id"
                        data-placeholder="Select company"
                        style="width: 100%;">
                    <option value=""></option>
                    @foreach($companies as $company)
                        <option value="{{ \App\Libraries\Encryption::encodeId($company->id) }}">{{ $company->org_nm }}</option>
                    @endforeach
                </select>
                {!! $errors->first('company_id','<span class="help-block">:message</span>') !!}
            </div>
        </div>

        <div style="display: none;" class="form-group col-md-12 {{$errors->has('row_id') ? 'has-error' : ''}}" id="row_id_div">
            {!! Form::label('row_id','Row ID: ',['class'=>'col-md-2  required-star']) !!}
            <div class="col-md-10">
                {!! Form::text('row_id', null, ['class' => 'form-control', 'id' => 'row_id']) !!}
                {!! $errors->first('row_id','<span class="help-block">:message</span>') !!}
            </div>
        </div>

        <div class="table-responsive col-md-12">
            <table id="forcefullyDataUpdateTable"
                   class="table table-striped table-bordered dt-responsive">
                <thead>
                <tr>
                    <th class="required-star">Label name</th>
                    <th class="required-star">Column name</th>
                    <th class="required-star">Value</th>
                    <th>#</th>
                </tr>
                </thead>
                <tbody>
                <tr id="forcefullyDataUpdateRow">
                    <td>
                        {!! Form::text('label_name[0]', '', ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('label_name','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::text('column_name[0]', '', ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('column_name','<span class="help-block">:message</span>') !!}
                    </td>
                    <td>
                        {!! Form::text('column_value[0]', '', ['class' => 'form-control input-md required']) !!}
                        {!! $errors->first('column_value','<span class="help-block">:message</span>') !!}
                    </td>
                    <td style="text-align: left;">
                        <a class="btn btn-sm btn-primary addTableRows" title="Add more" onclick="forcefullyDataUpdateAddTableRow('forcefullyDataUpdateTable', 'forcefullyDataUpdateRow');">
                            <i class="fa fa-plus"></i></a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer" style="text-align:left;">
    <div class="pull-left">
        {!! Form::button('<i class="fa fa-times"></i> Close', array('type' => 'button', 'class' => 'btn btn-danger', 'data-dismiss' => 'modal')) !!}
    </div>
    <div class="pull-right">
        <button type="submit" class="btn btn-primary" name="actionBtn" value="draft">
            <i class="fa fa-chevron-circle-right"></i> Save
        </button>
    </div>
    <div class="clearfix"></div>
</div>
{!! Form::close() !!}

<link rel="stylesheet" href="{{ asset("assets/plugins/select2.min.css") }}">
<script src="{{ asset("assets/plugins/select2.min.js") }}"></script>
<script>
    $(document).ready(function () {
        //trigger update type
        $('input[name=update_type]:checked').trigger('change');
        //Select2
        $(".select2").select2({
            dropdownParent: $("#forcefullyDataUpdateModal")
        });

        $("#forcefullyDataUpdateForm").validate({
            errorPlacement: function () {
                return true;
            },
            submitHandler: formSubmit
        });


        var form = $("#forcefullyDataUpdateForm"); //Get Form ID
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
                    $("#Duplicated jQuery selector").html('<i class="fa fa-cog fa-spin"></i> Loading...');
                    $("#Duplicated jQuery selector").prop('disabled', true); // disable button
                },
                success: function (data) {
                    //==========validation error===========//
                    if (data.success == false) {
                        info_err.hide().empty();
                        $.each(data.error, function (index, error) {
                            info_err.removeClass('hidden').append('<li>' + error + '</li>');
                        });
                        info_err.slideDown('slow');
                        info_err.delay(2000).slideUp(800, function () {
                            $("#Duplicated jQuery selector").html('Submit');
                            $("#Duplicated jQuery selector").prop('disabled', false);
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
                        info_err.delay(5000).slideUp(800, function () {
                            $("#Duplicated jQuery selector").html('Submit');
                            $("#Duplicated jQuery selector").prop('disabled', false);
                        });
                    }
                },
                error: function (data) {
                    var errors = data.responseJSON;
                    $("#Duplicated jQuery selector").prop('disabled', false);
                    console.log(errors);
                    alert('Sorry, an unknown Error has been occured! Please try again later.');
                }
            });
            return false;
        }
    });

    function TypeWiseData(update_type) {
        if (update_type == 'user') {
            $("#user_list_div").show();
            $("#user_id").addClass('required');

            $("#company_list_div").hide();
            $("#company_id").removeClass('required');

            $("#row_id_div").hide();
            $("#row_id").removeClass('required');
        } else if (update_type == 'company') {
            $("#company_list_div").show();
            $("#company_id").addClass('required');

            $("#user_list_div").hide();
            $("#user_id").removeClass('required');

            $("#row_id_div").hide();
            $("#row_id").removeClass('required');
        } else if (update_type == 'field') {
            $("#row_id_div").show();
            $("#row_id").addClass('required');

            $("#user_list_div").hide();
            $("#user_id").removeClass('required');

            $("#company_list_div").hide();
            $("#company_id").removeClass('required');
        } else {
            $("#row_id_div").hide();
            $("#row_id").removeClass('required');

            $("#user_list_div").hide();
            $("#user_id").removeClass('required');

            $("#company_list_div").hide();
            $("#company_id").removeClass('required');
        }
    }

    // Add table Row script
    function forcefullyDataUpdateAddTableRow(tableID, template_row_id) {

        // Copy the template row (first row) of table and reset the ID and Styling
        var new_row = document.getElementById(template_row_id).cloneNode(true);
        new_row.id = "";
        new_row.style.display = "";

        //Get the total now, and last row number of table
        var current_total_row = $('#' + tableID).find('tbody tr').length;
        var final_total_row = current_total_row + 1;

        // Generate an ID of the new Row, set the row id and append the new row into table
        var last_row_number = $('#' + tableID).find('tbody tr').last().attr('data-number');
        if (last_row_number != '' && typeof last_row_number !== "undefined") {
            last_row_number = parseInt(last_row_number) + 1;
        } else {
            last_row_number = Math.floor(Math.random() * 101);
        }

        var new_row_id = 'rowCount' + tableID + last_row_number;
        new_row.id = new_row_id;
        $("#" + tableID).append(new_row);

        // Convert the add button into remove button of the new row
        $("#" + tableID).find('#' + new_row_id).find('.addTableRows').removeClass('btn-primary').addClass('btn-danger')
            .attr('onclick', 'removeTableRow("' + tableID + '","' + new_row_id + '")');
        // Icon change of the remove button of the new row
        $("#" + tableID).find('#' + new_row_id).find('.addTableRows > .fa').removeClass('fa-plus').addClass('fa-times');
        // data-number attribute update of the new row
        $('#' + tableID).find('tbody tr').last().attr('data-number', last_row_number);

        // Get all select box elements from the new row, reset the selected value, and change the name of select box
        var all_select_box = $("#" + tableID).find('#' + new_row_id).find('select');
        all_select_box.val(''); //reset value
        all_select_box.prop('selectedIndex', 0);
        for (var i = 0; i < all_select_box.length; i++) {
            var name_of_select_box = all_select_box[i].name;
            var updated_name_of_select_box = name_of_select_box.replace('[0]', '[' + final_total_row + ']'); //increment all array element name
            all_select_box[i].name = updated_name_of_select_box;
        }

        // Get all input box elements from the new row, reset the value, and change the name of input box
        var all_input_box = $("#" + tableID).find('#' + new_row_id).find('input');
        all_input_box.val(''); // value reset
        for (var i = 0; i < all_input_box.length; i++) {
            var name_of_input_box = all_input_box[i].name;
            var updated_name_of_input_box = name_of_input_box.replace('[0]', '[' + final_total_row + ']');
            all_input_box[i].name = updated_name_of_input_box;
        }

        // Get all textarea box elements from the new row, reset the value, and change the name of textarea box
        var all_textarea_box = $("#" + tableID).find('#' + new_row_id).find('textarea');
        all_textarea_box.val(''); // value reset
        for (var i = 0; i < all_textarea_box.length; i++) {
            var name_of_textarea = all_textarea_box[i].name;
            var updated_name_of_textarea = name_of_textarea.replace('[0]', '[' + final_total_row + ']');
            all_textarea_box[i].name = updated_name_of_textarea;
            $('#' + new_row_id).find('.readonlyClass').prop('readonly', true);
        }

        // Table footer adding with add more button
        var check_tfoot_element = $('#' + tableID + ' tfoot').length;
        if (final_total_row > 3 && check_tfoot_element === 0) {
            var table_header_columns = $('#' + tableID).find('thead th');
            var table_footer = document.getElementById(tableID).createTFoot();
            var table_footer_row = table_footer.insertRow(0);
            for (var i = 0; i < table_header_columns.length; i++) {
                var table_footer_th = table_footer_row.insertCell(i);
                // if this is the last column, then push add more button
                if (i === (table_header_columns.length - 1)) {
                    table_footer_th.innerHTML = '<a class="btn btn-sm btn-primary addTableRows" title="Add more" onclick="addTableRow(\'' + tableID + '\', \'' + template_row_id + '\')"><i class="fa fa-plus"></i></a>';
                } else {
                    table_footer_th.innerHTML = '<b>' + table_header_columns[i].innerHTML + '</b>';
                }
            }
        }

        $("#" + tableID).find('#' + new_row_id).find('.onlyNumber').on('keydown', function (e) {
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
                || (e.keyCode >= 35 && e.keyCode <= 39)) {
                var $this = $(this);
                setTimeout(function () {
                    $this.val($this.val().replace(/[^0-9.]/g, ''));
                }, 4);
                var thisVal = $(this).val();
                if (thisVal.indexOf(".") != -1 && e.key == '.') {
                    return false;
                }
                $(this).removeClass('error');
                return true;
            } else {
                $(this).addClass('error');
                return false;
            }
        }).on('paste', function (e) {
            var $this = $(this);
            setTimeout(function () {
                $this.val($this.val().replace(/[^.0-9]/g, ''));
            }, 4);
        });
        $("#" + tableID).find('.datepicker').datetimepicker({
            viewMode: 'years',
            format: 'DD-MMM-YYYY',
            extraFormats: ['DD.MM.YY', 'DD.MM.YYYY'],
            maxDate: 'now',
            minDate: '01/01/1905'
        });

        $("#" + tableID).find('.YearPicker').datetimepicker({
            viewMode: 'years',
            format: 'YYYY',
            extraFormats: ['DD.MM.YY', 'DD.MM.YYYY'],
            // maxDate: 'now',
            minDate: '01/01/1905'
        });
    } // end of addTableRow() function
</script>
