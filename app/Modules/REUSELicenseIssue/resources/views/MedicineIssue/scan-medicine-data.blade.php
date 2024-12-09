@extends('layouts.admin')

@section('content')
    <style>
        .select2 {
            width: 100% !important;
        }

        html.magnifying > body {
            overflow-x: hidden !important;
        }
        .magnify,
        .magnify > .magnify-lens,
        .magnify-mobile,
        .lens-mobile {
            min-width: 0;
            min-height: 0;
            animation: none;
            border: none;
            float: none;
            margin: 0;
            opacity: 1;
            outline: none;
            overflow: visible;
            padding: 0;
            text-indent: 0;
            transform: none;
            transition: none;
        }
        .magnify {
            position: relative;
            width: auto;
            height: auto;
            box-shadow: none;
            display: inline-block;
            z-index: inherit;
        }
        .magnify > .magnify-lens {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 100%;
            box-shadow: 0 0 0 7px rgba(255, 255, 255, 0.85), 0 0 7px 7px rgba(0, 0, 0, 0.25), inset 0 0 40px 2px rgba(0, 0, 0, 0.25);
            cursor: none;
            display: none;
            z-index: 99;
        }
        .magnify > .magnify-lens.loading {
            background: #333 !important;
            opacity: 0.8;
        }
        .magnify > .magnify-lens.loading:after {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            color: #fff;
            content: 'Loading...';
            font: italic normal 16px/1 Calibri, sans-serif;
            letter-spacing: 1px;
            margin-top: -8px;
            text-align: center;
            text-shadow: 0 0 2px rgba(51, 51, 51, 0.8);
            text-transform: none;
        }

        .imgZoom-wrap{
            position: relative;
            padding: 10px;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        #issueTable .select2-container .select2-selection--single,
        #issueTable .select2-container--default .select2-selection--single .select2-selection__arrow{
            height: 38px;
        }

        .loader {
            display: inline-block;
            margin-left: 5px;
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3498db;
            width: 30px;
            height: 30px;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <link rel="stylesheet" href="{{ asset("assets/plugins/select2/css/select2.min.css") }}">
    @include('partials.messages')
    <div class="card">
        <div class="card-header bg-primary">
            <h5 style="float: left">
                Medicine Delivery
            </h5>
            {{-- <button style="float: right ;color: black" type="button" class="btn btn-warning" id="showImageBtn">Show Prescription</button> --}}
        </div>
        <div class="row align-items-center">
            <div class="col-md-6">
                {!! Form::open(array('url' => 'medicine-issue/scan-medicine-store','method' => 'post', 'id'=> 'formId')) !!}
                <div class="card-body">
                    <div class="form-group">
                        <div class="row mb-2">
                            @if($draftPid)
                                <label for="draftPid" class="col-md-3 required-star">PID:</label>
                                <div class="col-9 col-md-9">
                                    {!! Form::text('draftPid', $draftPid ? $draftPid : '', ['class'=>'form-control ', 'required','placeholder'=>'Enter PID', 'id'=>'draftPid']) !!}
                                    {!! Form::hidden('passport_no', '', ['class'=>'form-control none',  'id'=>'passport_no']) !!}
                                    <div class="d-flex align-items-center pt-2">
                                        <button type="button" id="searchBtn" class="btn btn-primary" onclick="searchPilgrimByPID()">Search</button>
                                    </div>
                                </div>
                            @else
                                <label for="passport_no" class="col-md-3 required-star">Passport No:</label>
                                <div class="col-9 col-md-9">
                                    {!! Form::text('passport_no', $passportNo ? $passportNo:'', ['class'=>'form-control ', 'required','placeholder'=>'Enter Passport number', 'id'=>'passport_no']) !!}
                                    <div class="d-block pt-2">
                                        {{-- @if(!$passportNo) --}}
                                            <button type="button" id="searchBtn" class="btn btn-primary" onclick="searchPilgrim()">Search</button>
                                        {{-- @endif --}}
                                    </div>
                                </div>
                            @endif
                            {!! Form::hidden('pid', '', ['class'=>'form-control none',  'id'=>'hidden_pid']) !!}
                            {!! Form::hidden('hidden_draft_id', $encodeId, ['class'=>'form-control none',  'id'=>'hidden_draft_id']) !!}

                        </div>
                        <div class="row mb-2">
                            <label for="pilgrim_name" class="col-md-3">Pilgrim Name:</label>
                            <div class="col-md-9">
                                {!! Form::text('pilgrim_name', '', ['class'=>'form-control', 'id'=>'pilgrim_name', 'readonly'=>true]) !!}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="date" class="col-md-3">Pilgrim mobile:</label>
                            <div class="col-md-9">
                                {!! Form::text('pilgrim_mobile', '', ['class'=>'form-control', 'id'=>'pilgrim_mobile', 'readonly'=>true]) !!}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="date" class="col-md-3">Date:</label>
                            <div class="col-md-9">
                                {!! Form::text('date', date('d M Y', strtotime(\Carbon\Carbon::now())), ['class'=>'form-control', 'id'=>'date', 'readonly'=>true]) !!}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <label for="pilgrim_name" class="col-md-3">Pharmacy:</label>
                            <div class="col-md-9">
                                {!! Form::text('pharmacy', $pharmacy->name, ['class'=>'form-control', 'id'=>'pilgrim_name', 'readonly'=>true]) !!}
                                <input type="hidden" name="pharmacy_id" value="{{$pharmacy->id}}">
                            </div>
                        </div>
                    </div>

                    <br><br>
                    <div class="form-group" id="issueDiv">
                        <div class="row table-responsive">
                            <table id="issueTable" class="table table-bordered">
                                <thead style="background-color: #F7F7F7">
                                <tr>
                                    @if($returnData['medicines'] && count($returnData['medicines']) > 0)
                                        <th class="text-center required-star">Scan Medicine</th>
                                    @endif
                                    <th class="text-center required-star">Medicine</th>
                                    <th class="text-center required-star">Quantity</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($returnData['medicines'] && count($returnData['medicines']) > 0)
                                    @foreach($returnData['medicines'] as $key => $value)
                                        <tr id="issueRow" data-number="1">
                                            <td>
                                                <input type="text" value="{{$value['med_type']. ' '. $value['trade_name'].' '.$value['sku']}}" readonly class="form-control">
                                                {!! Form::text('scan_med_nm[]', $value['trade_name'], ['class' => 'form-control required d-none']) !!}

                                            </td>
                                            <td>
                                                {!! Form::select('medicine[]', $medicineArr, !empty($medicineTradeArr[$value['trade_name']]) ? $medicineTradeArr[$value['trade_name']] : 0, ['class' => 'form-control select2_field required', 'placeholder' => 'Select medicine']) !!}
                                            </td>
                                            <td style="width: 25px;">
                                                {!! Form::number('quantity[]', $value['quantity'], ['class' => 'form-control required', 'placeholder' => 'Enter qty']) !!}
                                            </td>

                                            <td class="text-center" style="width: 20px;">
                                                @if($key === 0)
                                                    <a class="btn btn-sm btn-primary addTableRows" data-toggle="tooltip"
                                                       onclick="addTableRows('issueTable', 'issueRow');"><i
                                                            class="fa fa-plus"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr id="issueRow" data-number="1">
                                        <td>
                                            {!! Form::select('medicine[]', $medicineArr, 0, ['class' => 'form-control select2_field required', 'placeholder' => 'Select medicine']) !!}
                                        </td>
                                        <td style="width: 25px;">
                                            {!! Form::number('quantity[]', 0, ['class' => 'form-control required', 'placeholder' => 'Enter qty']) !!}
                                        </td>

                                        <td class="text-center" style="width: 20px;">
                                            <a class="btn btn-sm btn-primary addTableRows" data-toggle="tooltip" onclick="addTableRows('issueTable', 'issueRow');">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <div>
                            <button id="save_btn" style="float: right" class="btn btn-primary" disabled><i class="fa fa-save"></i> Save</button>
                            <button type="button" id="reject_btn" style="float: left" class="btn btn-danger"><i class="fa fa-ban"></i> Reject</button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="col-md-6">
                <div class="imgZoom-wrap">
                    <div class="magnify">
                        <div class="magnify-lens magnify-bigImg" style="background: url('{{$imageData}}') no-repeat;"></div>
                        <img class="photo-zoom" src="{{$imageData}}" alt="Image" data-magnify-src="{{$imageData}}">
                    </div>
                </div>
                <div style="width: 100%; text-align: right; padding-right: 10px;">
                    <button id="openImageBtn" class="btn btn-secondary">Open Image</button>
                </div>
            </div>
        </div>

    </div>
    {{--  Show prescription modal  --}}
    {{-- <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="margin-top: 0; top: 0;">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="w-100 text-center">
                        <h5 class="modal-title text-center" id="imageModalLabel">Prescription Image</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img src="{{$imageData}}" alt="prescription-img" id="prescriptionImage" />
                </div>
            </div>
        </div>
    </div> --}}

@endsection

@section('footer-script')
    <script type="text/javascript"
            src="//cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.2.24/jquery.autocomplete.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="{{ asset("assets/plugins/select2/js/select2.full.min.js") }}"></script>
    <script src="{{ asset('assets/custom/js/jquery.magnify.js') }}"></script>

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
                const pilgrim_id = $('#passport_no').val();
                if(pilgrim_id) {
                    return true;
                } else {
                    alert("Please provide passport no");
                    return false;
                }

            });
            <?php echo $draftPid ? 'searchPilgrimByPID()' : 'searchPilgrim()' ?>

            $('#openImageBtn').on('click', function() {
                var imageData = "{{$imageData}}";
                var newTab = window.open();
                newTab.document.write('<img src="' + imageData + '" alt="Image" style="width: 100%;"/>');
                newTab.document.title = "Image Preview";
            });

            $('#reject_btn').on('click', function() {
                let draftId = $('#hidden_draft_id').val();
                $('#reject_btn').prop('disabled', true);
                $.ajax({
                    type: "get",
                    url: "<?php echo env('APP_URL').'/medicine-issue/draft-reject/'?>"+draftId,
                    success: function (response) {
                        if (response.responseCode == 1) {
                            window.location.href = "<?php echo env('APP_URL'); ?>" + response.url;
                        } else {
                            alert(response.msg);
                            $('#reject_btn').prop('disabled', false);
                        }
                    }
                });
            });
        })

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
        function searchPilgrim() {
            let passport_no = $('#passport_no').val();
            if (passport_no == '') {
                alert("Please enter Passport Number");
                $('#save_btn').prop('disabled', true);
                return false;
            } else {
                $.ajax({
                    type: "get",
                    data: {passport_no: passport_no},
                    url: "<?php echo env('APP_URL').'/medicine-issue/search-pilgrim-by-passport'?>",
                    success: function (response) {
                        if (response.responseCode == 1) {
                            $('#pilgrim_name').val(response.data.data.pilgrim.full_name_english)
                            $('#pilgrim_mobile').val(response.data.data.pilgrim.mobile)
                            $('#hidden_pid').val(response.data.data.pilgrim.pid)
                            $('#issueDiv').removeClass('hidden')
                            // $('#passport_no').prop('readonly', true);
                            $('#save_btn').prop('disabled', false);
                        } else {
                            $('#pilgrim_name').val('')
                            $('#pilgrim_mobile').val('')
                            $('#hidden_pid').val('')
                            // $('#issueDiv').addClass('hidden')
                            $('#save_btn').prop('disabled', true);
                            toastr.warning(response.msg);
                        }

                    }
                });
            }
        }

        function searchPilgrimByPID() {
            let pid = $('#draftPid').val();
            if (pid == '') {
                alert("Please enter PID or Passport Number");
                $('#save_btn').prop('disabled', true);
                return false;
            } else {
                $('#searchBtn').after('<div class="loader"></div>');
                $.ajax({
                    type: "get",
                    data: {pid},
                    url: "<?php echo env('APP_URL').'/medicine-issue/search-pilgrim-by-pid-or-passport'?>",
                    success: function (response) {
                        if (response.responseCode == 1) {
                            $('#pilgrim_name').val(response.data.data.pilgrim.full_name_english)
                            $('#pilgrim_mobile').val(response.data.data.pilgrim.mobile)
                            $('#hidden_pid').val(response.data.data.pilgrim.pid)
                            $('#passport_no').val(response.data.data.pilgrim.passport_no)
                            $('#issueDiv').removeClass('hidden')
                            $('#draftPid').prop('readonly', false);
                            $('#save_btn').prop('disabled', false);
                        } else {
                            $('#pilgrim_name').val('')
                            $('#pilgrim_mobile').val('')
                            $('#hidden_pid').val('')
                            $('#draftPid').prop('readonly', false);
                            $('#save_btn').prop('disabled', true);
                            toastr.warning(response.msg);
                        }
                        $('#searchBtn').next().hide();
                    }
                });
            }
        }
        var arr = [];
        $(document).on('change', '.select2_field', function (){
            arr.push($(this).val())
            var elementToCount = $(this).val();
            var count = $.grep(arr, function(elem){
                return elem === elementToCount;
            }).length;
            if (count > 1){
                $(this).val('')
                alert("You are selecting same medicine twice!")
            }
        })
        $('#showImageBtn').click(function() {
            $('#imageModal').modal('show');
        });

        $(document).ready(function() {
            $('.photo-zoom').magnify({
                magnifiedWidth: 1600,
                magnifiedHeight: 1600,
            });
        });
    </script>
@endsection
