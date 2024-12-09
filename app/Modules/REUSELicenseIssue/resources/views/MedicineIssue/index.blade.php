@extends('layouts.admin')

@section('content')
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <link rel="stylesheet" href="{{ asset("assets/plugins/select2/css/select2.min.css") }}">
    @include('partials.messages')
    <div class="card">
        <div class="card-header bg-primary">
            <h5 style="float: left">
                Pilgrim Delivery
            </h5>
            <button style="float: right ;color: black" class="btn btn-warning"  onclick="scanPrescription()">Scan Prescription</button>
        </div>
        {!! Form::open(array('url' => 'medicine-issue/store','method' => 'post', 'id'=> 'formId')) !!}
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <label for="pid" class="col-md-1 required-star">PID:</label>
                    <div class="col-md-3">
                        {!! Form::text('pid', '', ['class'=>'form-control required', 'placeholder'=>'Enter PID number', 'id'=>'pid']) !!}
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary" onclick="searchPilgrim()">Search</button>
                    </div>
                    <label for="date" class="col-md-1">Date:</label>
                    <div class="col-md-3">
                        {!! Form::text('date', date('d M Y', strtotime(\Carbon\Carbon::now())), ['class'=>'form-control', 'id'=>'date', 'readonly'=>true]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="pilgrim_name" class="col-md-1">Pilgrim Name:</label>
                    <div class="col-md-3">
                        {!! Form::text('pilgrim_name', '', ['class'=>'form-control', 'id'=>'pilgrim_name', 'readonly'=>true]) !!}
                    </div>
                    <div class="col-md-2"></div>
                    <label for="date" class="col-md-1">Pilgrim mobile:</label>
                    <div class="col-md-3">
                        {!! Form::text('pilgrim_mobile', '', ['class'=>'form-control', 'id'=>'pilgrim_mobile', 'readonly'=>true]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="pilgrim_name" class="col-md-1">Pharmacy:</label>
                    <div class="col-md-3">
                        {!! Form::text('pharmacy', $pharmacy->name, ['class'=>'form-control', 'id'=>'pilgrim_name', 'readonly'=>true]) !!}
                        <input type="hidden" name="pharmacy_id" value="{{$pharmacy->id}}">
                    </div>
                </div>
            </div>
            <br><br>
            <div class="form-group hidden" id="issueDiv">
                <div class="row table-responsive">
                    <table id="issueTable" class="table table-bordered">
                        <thead style="background-color: #F7F7F7">
                        <tr>
                            <th class="text-center required-star">Medicine</th>
                            <th class="text-center required-star">Quantity</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr id="issueRow" data-number="1">

                            <td>
                                {!! Form::select('medicine[0]', $medicineArr, '', ['class' => 'form-control select2_field required', 'placeholder' => 'Select medicine']) !!}
                            </td>
                            <td>
                                {!! Form::number('quantity[0]', '', ['class' => 'form-control required', 'placeholder' => 'Enter qty']) !!}
                            </td>

                            <td class="text-center">
                                <a class="btn btn-sm btn-primary addTableRows" data-toggle="tooltip"
                                   onclick="addTableRows('issueTable', 'issueRow');"><i
                                        class="fa fa-plus"></i></a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                <div style="float: right">
                    <button class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @include('REUSELicenseIssue::MedicineIssue.modal.scan-prescription-modal')

@endsection

@section('footer-script')
    <script type="text/javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.2.24/jquery.autocomplete.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="{{ asset("assets/plugins/select2/js/select2.full.min.js") }}"></script>

    <script>
        $(document).ready(function (){
            $("#formId").validate({
                errorPlacement: function () {
                    return false;
                }
            });
            $('.select2_field').select2();

            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });

            $('#formId').on('submit', function (e) {
                const pilgrim_name = $('#pilgrim_name').val();
                if(pilgrim_name) {
                    return true;
                } else {
                    searchPilgrim();
                    return false;
                }

            });

        })
        function searchPilgrim() {
            let pid = $('#pid').val();
            if (pid == '') {
                alert("Please enter PID");
            } else {
                $.ajax({
                    type: "get",
                    {{--url: "{{ url('medicine-issue/search-pilgrim') }}",--}}
                    data: {pid: pid},
                    url: "<?php echo env('APP_URL').'/medicine-issue/search-pilgrim'?>",
                    success: function (response) {
                        if (response.responseCode == 1) {
                            $('#pilgrim_name').val(response.data.data.pilgrim.full_name_english)
                            $('#pilgrim_mobile').val(response.data.data.pilgrim.mobile)
                            $('#issueDiv').removeClass('hidden')
                        } else {
                            $('#pilgrim_name').val('')
                            $('#pilgrim_mobile').val('')
                            $('#issueDiv').addClass('hidden')
                            toastr.warning(response.msg);
                        }

                    }
                });
            }
        }

        // Add table Row script
        function addTableRows(tableID, template_row_id) {
            $('.select2_field').select2('destroy');
            let i;
            // Copy the template row (first row) of table and reset the ID and Styling
            const new_row = document.getElementById(template_row_id).cloneNode(true);
            new_row.id = "";
            new_row.style.display = "";

            // Get the total row number, and last row number of table
            let current_total_row = $('#' + tableID).find('tbody tr').length;
            let final_total_row = current_total_row + 1;

            // Generate an ID of the new Row, set the row id and append the new row into table
            let last_row_number = $('#' + tableID).find('tbody tr').last().attr('data-number');
            if (last_row_number != '' && typeof last_row_number !== "undefined") {
                last_row_number = parseInt(last_row_number) + 1;
            } else {
                last_row_number = Math.floor(Math.random() * 101);
            }

            const new_row_id = 'rowCount' + tableID + last_row_number;
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

            all_select_box.val('').attr('readonly', false).css('pointer-events', 'all'); // value reset
            // $("#" + tableID).find('#' + new_row_id).find('.branch_id').empty();
            // all_select_box.prop('selectedIndex', 0).select2();

            // $("#" + tableID).find('#' + new_row_id).find('.select2_field').select2({
            //     placeholder: "Select one"
            // });

            // } else {
            //     $('.branch_id').select2({
            //         placeholder: "শাখার নাম"
            //     });
            // }


            for (i = 0; i < all_select_box.length; i++) {

                var name_of_select_box = all_select_box[i].name;
                var id_of_select_box = all_select_box[i].id;

                var lastChars = name_of_select_box.substr(name_of_select_box.length - 6);
                var spicalCase;
                if (lastChars == '[0][0]') {
                    spicalCase = '[0]'
                    var updated_name_of_select_box = name_of_select_box.replace('[0][0]', '' + spicalCase + '[' + final_total_row + ']');
                } else {
                    var updated_name_of_select_box = name_of_select_box.replace('[0]', '[' + final_total_row + ']');
                }

                // const updated_name_of_select_box = name_of_select_box.replace('[0]', '[' + final_total_row + ']'); //increment all array element name
                all_select_box[i].name = updated_name_of_select_box;
                all_select_box[i].id = id_of_select_box.replace('0', final_total_row);


                if (all_select_box[i].classList.contains('select2_field')) {
                    // $('#' + new_row_id).find('.select2_field').select2();
                }
            }

            $('.select2_field').select2();
            // Get all input box elements from the new row, reset the value, and change the name of input box
            const all_input_box = $("#" + tableID).find('#' + new_row_id).find('input:not(:radio)');
            all_input_box.val(''); // value reset
            // console.log(final_total_row)
            for (i = 0; i < all_input_box.length; i++) {
                const name_of_input_box = all_input_box[i].name;
                var lastChars = name_of_input_box.substr(name_of_input_box.length - 6);
                var spicalCase;
                if (lastChars == '[0][0]') {
                    spicalCase = '[0]'
                    var updated_name_of_input_box = name_of_input_box.replace('[0][0]', '' + spicalCase + '[' + final_total_row + ']');
                } else {
                    var updated_name_of_input_box = name_of_input_box.replace('[0]', '[' + final_total_row + ']');
                }
                all_input_box[i].name = updated_name_of_input_box;
            }


            var radio_box = $("#" + tableID).find('#' + new_row_id).find(':radio');
            // all_input_box.val(''); // value reset
            // console.log(final_total_row)
            for (i = 0; i < radio_box.length; i++) {
                var name_of_radio_box = radio_box[i].name;
                var lastChars = name_of_radio_box.substr(name_of_radio_box.length - 6);
                var spicalCase;
                if (lastChars == '[0][0]') {
                    spicalCase = '[0]'
                    var updated_name_of_input_box = name_of_radio_box.replace('[0][0]', '' + spicalCase + '[' + final_total_row + ']');
                } else {
                    var updated_name_of_input_box = name_of_radio_box.replace('[0]', '[' + final_total_row + ']');
                }
                radio_box[i].name = updated_name_of_input_box;
            }


            // Get all textarea box elements from the new row, reset the value, and change the name of textarea box
            const all_textarea_box = $("#" + tableID).find('#' + new_row_id).find('textarea');
            all_textarea_box.val(''); // value reset
            for (i = 0; i < all_textarea_box.length; i++) {
                const name_of_textarea = all_textarea_box[i].name;
                var lastChars = name_of_textarea.substr(name_of_textarea.length - 6);
                var lastChars = name_of_textarea.substr(name_of_textarea.length - 6);
                var spicalCase;
                if (lastChars == '[0][0]') {
                    spicalCase = '[0]'
                    var updated_name_of_textarea = name_of_textarea.replace('[0][0]', '' + spicalCase + '[' + final_total_row + ']');
                } else {
                    var updated_name_of_textarea = name_of_textarea.replace('[0]', '[' + final_total_row + ']');
                }

                // const updated_name_of_textarea = name_of_textarea.replace('[0]', ''+spicalCase+'[' + final_total_row + ']');
                all_textarea_box[i].name = updated_name_of_textarea;
                $('#' + new_row_id).find('.readonlyClass').prop('readonly', true);
            }


        } // end of addTableRow() function
        function removeTableRow(tableID, removeNum) {
            $('#' + tableID).find('#' + removeNum).remove();
        }

        var arr = [];
        $(document).on('change', '.select2_field', function (){
            arr.push($(this).val())
            var elementToCount = $(this).val();
            console.log(elementToCount)
            var count = $.grep(arr, function(elem){
                return elem === elementToCount;
            }).length;
            if (count > 1){
                $(this).val('')
                alert("You are selecting same medicine twice!")
            }
        })
    </script>
@endsection
