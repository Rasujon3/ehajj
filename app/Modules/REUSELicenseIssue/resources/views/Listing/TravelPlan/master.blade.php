<?php
$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);

$prefix = '';
if ($type[0] == 5) {
    //$prefix = 'client';
    $prefix = 'client';
}

?>
<style>
    .dash-list-table {
        overflow-x: hidden;
    }

    .bootstrap-datetimepicker-widget.dropdown-menu {
        width: 17rem;
    }
</style>
{!! Form::open([
                'url' => url('process/action/store/'.\App\Libraries\Encryption::encodeId($process_type_id)),
                'method' => 'post',
                'class' => 'form-horizontal',
                'id' => 'application_form',
                'enctype' => 'multipart/form-data',
                'files' => 'true'
            ])
        !!}

@csrf


<div class="dash-content-main">
    <div class="dash-sec-head mt-3">
        <h3>ট্রাভেল প্ল্যান লিষ্টিং ( {{$session_id['caption']}} )</h3>
    </div>
    <div class="dash-section-content pt-3">
        <div class="dash-sec-head">
            <div class="container">
                <div class="card">
                    <div class="card-body" id="search_list" style="display: block">
                        <div class="row mt-4 mb-2">
                            @php
                                $process_typeid =  \App\Libraries\Encryption::encodeId($process_type_id);
                                $pilgrim_assign_limit = \App\Modules\Settings\Models\Configuration::where('caption','Guide_Pilgrim_Assign_limit')->first(['value']);

                            @endphp
                            <input type="hidden" id="id" value="{!!$process_typeid!!}"/>
                            <input type="hidden" id="pilgrim_assign_limit" value="{{$pilgrim_assign_limit->value}}"/>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    @if(!empty($flight_drop_down_list['data']))
                                        <label for="exampleFormControlSelect1">হজ ফ্লাইট সিলেক্ট করুন</label>
                                        <select class="form-control" name="flight_id" id="listing_id">
                                            <option selected disabled value=""> তালিকা নির্বাচন করুন</option>
                                            @foreach($flight_drop_down_list['data'] as $key => $item)
                                                <option value="{{$item['id']}}">{{$item['Flight']}}</option>
                                            @endforeach
                                            <input type="hidden" name="flight_get_code" id="flight_get_code"/>
                                        </select>
                                    @elseif(empty($flight_drop_down_list['data']))
                                        <label for="exampleFormControlSelect1">সিলেক্ট সম্ভাব্য হজ ফ্লাইট তারিখ</label>
                                        <div class="input-group date datetimepicker4" id="datepicker1" data-target-input="nearest">
                                            <input type="text" class="form-control input-disabled" name="possible_flight_date" >
                                            <div class="input-group-append" data-target="#datepicker1" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="flight_drop_down_list_null" value="true"/>
                                        <span class="text-danger date_error"></span>
                                    @endif
                                    <input type="hidden" id="total_capacity" value=""/>
                                    <input type="hidden" id="remaining" value=""/>
                                    <input type="hidden" id="hmis_guide_id" value="{{\Illuminate\Support\Facades\Auth::user()->hmis_guide_id}}"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-11 mb-2">
                                <form class="form-group">
                                    <input class="form-control mr-sm-2 search_field" type="search"
                                           name="request_data" id="request_data"
                                           placeholder="হজযাত্রীর ট্র্যাকিং নম্বর প্রদান করুন"
                                           autocomplete="off">

                                </form>
                            </div>
                            <div class="col-sm-1">
                                <button type="button" style="float:right;" class="btn btn-secondary"
                                        onclick='searchPilgrim()'>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
                                    </svg>
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dash-content-inner">
            <div class="col-sm-12 text-left" style="padding-left:20px;">
            <p style="color: red;"> বি: দ্র: একজন গাইড তাঁর অধীনে ফ্লাইটভিত্তিক মোফা গ্ৰুপ প্রস্তাব করতে
                পারবেন। তিনি তাঁর গ্ৰুপে সর্বোচ্চ ৪৭ জন ও সর্বনিম্ন ৪৪ জন হজযাত্রীকে তাঁর সঙ্গে সংযুক্ত
                করে আবেদন করবেন। আবেদনটি পরিচালক, হজ অফিস ঢাকা কর্তৃক অনুমোদিত হলে পরবর্তী ধাপের জন্য
                প্রেরণ করা হবে। তবে, যদি গাইডের প্রস্তাবনা থেকে কোনো হজযাত্রী আলাদা গমন করার জন্য ধর্ম
                বিষয়ক মন্ত্রণালয়ে আবেদন করে থাকেন, তাহলে উক্ত হজযাত্রীকে তিনি গ্ৰুপে গ্রহণ করতে পারবেন
                না। ধর্ম বিষয়ক মন্ত্রণালয় এই প্রস্তাবনা পূর্ণাঙ্গ / আংশিকভাবে গ্রহণ করতে পারে অথবা
                সম্পূর্ণ গ্ৰুপকে নতুন করে সংযুক্ত করতে পারবে। এই সকল ক্ষেত্রে, ধর্ম বিষয়ক মন্ত্রণালয়
                অথবা পরিচালক হজ অফিস ঢাকার সিদ্ধান্ত চূড়ান্ত।</p>
            </div>
            <div class="src-hajj-tracking-number">
                <div class="dash-list-table my-4 toggle_data_talbe" style="display:none;">
                    <span class="btn btn-primary btn-sm mb-2">
                        সর্বমোট হজযাত্রী <span class="badge badge-light" id="total_pilgrim">0</span>
                    </span>
                    <span class="btn btn-secondary btn-sm mb-2" style="float: right;">
                        ফ্লাইট ক্যাপাসিটি <span class="badge badge-light" id="flight_capacity">0</span>
                    </span>

                    <span class="btn btn-info btn-sm mb-2">
                        অবশিষ্ট আসন সংখ্যা <span class="badge badge-light" id="seat_available">0</span>
                    </span>
                    <div class="card card-magenta border border-magenta">

                        <div class="table-responsive">
                            <table class="table table-bordered" id="myTable">
                                <thead class="table-info">
                                <tr>
                                    <th scope="col"><input type="checkbox" class="checkbox" id="selectAll"></th>
                                    <th scope="col">পিআইডি</th>
                                    <th scope="col">নাম</th>
                                    <th scope="col">ট্র্যাকিং নম্বর</th>
                                    {{-- <th scope="col">পিআইডি</th> --}}
                                    <th scope="col">ফ্লাইট</th>
                                    <th scope="col">গাইড</th>
                                    <th scope="col">স্ট্যাটাস</th>
                                </tr>
                                </thead>
                                <tbody class="load_pilgrim">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row toggle_data_talbe" style="display:none;">
                    <div class="row">
                        <div class="col-sm-12 text-left" style="padding-left:20px;">
                            <p style="color: red;"> বি: দ্র: * আপনার সঙ্গে প্রস্তাবিত হজযাত্রীর তালিকা সাবমিট করার
                                পূর্বে অবশ্যই ভালো প্রতিজনের চেক করে নিশ্চিত করে নিবেন। তালিকায় প্রদত্ত কোনো হজযাত্রীর
                                সঙ্গে পরিচালক হজ অফিস ঢাকার নির্দেশনার গরমিল থাকলে, তালিকাটি অনুমোদিত নাও হতে পারে।
                                সেক্ষেত্রে পুনরায় হজযাত্রীর তালিকা নতুন করে এন্ট্রি করতে হবে।
                                তাই, ধর্ম বিষয়ক মন্ত্রণালয়ের স্মারক নং ১৬.০০.০০০.০২২.১১.০০১.২২.৭৭৫ তারিখ <span id="possible_flight_date_set"></span> এর
                                আলোকে হজযাত্রীর তালিকা ও ফ্লাইট নিশ্চিত করেই সিস্টেমে এন্ট্রি করবেন। </p>
                            <label style="display: inline-block; vertical-align: middle;"><input type="checkbox" id="btn_enable" style="vertical-align: middle;"> উপরোক্ত বিষয়সমূহ নিশ্চিত করে আমি সিস্টেমে হজযাত্রীর তালিকা সাবমিট করছি। </label>
                        </div>

                    </div>
                    @if ( in_array($user_type,['4x404','21x101']) )
                        <div class="row">
                            <div class="col-sm-6">
                                <a class="btn btn-info btn-sm" style="margin-left:10px"
                                   href="{{ url($prefix.'/process/list/'. Encryption::encodeId(1)) }}">
                                    বন্ধ করুন
                                </a>
                            </div>
                            <div class="col-sm-6 text-right" style="padding-right:35px">
                                <button class="btn btn-danger btn-sm" id="beforPosition"> পূর্বাবস্থায়</button>
                                {{--                                <button class="btn btn-primary btn-sm" name="actionBtn" onclick="checkTotalPilgrim()" value="draft">সংরক্ষণ এবং খসড়া</button>--}}
                                <button class="btn btn-success btn-sm" name="actionBtn" onclick="checkTotalPilgrim()"
                                        value="submit" id="submit_btn" disabled>সাবমিট
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

{!! Form::close() !!}

<script>
    $(document).ready(function() {
        $('#btn_enable').change(function() {
            if ($(this).is(':checked')) {
                $('#submit_btn').prop('disabled', false);
            } else {
                $('#submit_btn').prop('disabled', true);
            }
        });
    });

    $(function () {
        var today = new Date();
        var yyyy = today.getFullYear();
        $('.datetimepicker4').datetimepicker({
            format: 'DD-MMM-YYYY',
            minDate: '01/01/' + (yyyy - 110),
            ignoreReadonly: true,
        });
    });

    $('input[name="possible_flight_date"]').on('input', function() {
       let possible_flight_date_set = $('input[name="possible_flight_date"]').val();
       $('#possible_flight_date_set').text(possible_flight_date_set);
          dateFormatCheck();
    });
    function dateFormatCheck() {
        var input = $('input[name="possible_flight_date"]');
        let dateRegex = /^(0[1-9]|[1-2][0-9]|3[0-1])-(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)-\d{4}$/;
        var pattern = new RegExp(dateRegex);
        if (!pattern.test(input.val())) {
            $('.date_error').text('Please provide a valid Date');
        } else {
            $('.date_error').text('');
        }
    }
</script>
<script>
    function searchPilgrim() {
        let base_url = window.location.origin + '/process/action/getHmisPilgrimListing/' + $('#id').val();
        let request_data = $('#request_data').val();


        if (request_data != undefined) {

            $.ajax({
                cache: false,
                url: base_url,
                type: "POST",
                dataType: 'json',
                data: {'request_data': request_data},
                success: function (response) {
                    var selected_pilgrim_array = response.data.data.available_pid;
                    var selected_pilgrim_status_array = response.data.data.available_pid_status;
                    if (response.data.data.hmisPilgrinmData == undefined || response.data.data.hmisPilgrinmData.length == 0) {
                        Swal.fire({
                            // text: 'These applications are already listed:' + listed_arr.toString(),
                            text: 'Data Not Found',
                            icon: 'warning',
                            // showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        })
                        $('.load_pilgrim').html("<tr class='mt-3'><td class='pt-4 text-red' colspan='6'>Data not found!!</td></tr>");
                    } else {
                        $("#search_list").css("display", "block");
                        $(".toggle_data_talbe").css("display", "block");
                        $(".search_field").css("display", "block");
                        let dataLen = response.data.data.hmisPilgrinmData.length;
                        $('#total_pilgrim').html(dataLen);
                        $('#flight_capacity').html($('#total_capacity').val());
                        $('#seat_available').html($('#remaining').val());
                        var hmis_guide_id = ($('#hmis_guide_id').val());

                        $('.load_pilgrim').html('');

                        for (var i = 0; i < dataLen; i++) {
                            let hmis_pid = response.data.data.hmisPilgrinmData[i]['pid'];
                            if (response.data.data.hmisPilgrinmData[i]['pilgrim_listing_id'] != 0) {
                                $("#draft").css("display", "none");
                                $("#save").css("display", "none");
                            }
                            if (response.data.data.hmisPilgrinmData[i]['guide_id'] > 0) {
                                guide_msg = "Already Assigned";
                            } else {
                                guide_msg = "Not Assigned";
                            }
                            if (response.data.data.hmisPilgrinmData[i]['bd_to_ksa_id'] > 0) {
                                flight_msg = "Already Assigned";
                            } else {
                                flight_msg = "Not Assigned";
                            }
                            status_msg = '';
                            if (selected_pilgrim_array.includes(hmis_pid)) {
                                status_msg = (selected_pilgrim_status_array[hmis_pid] == 1) ? "Submitted" : "Approved";
                            }
                            // console.log(guide_msg);
                            // console.log(response.data.data.hmisPilgrinmData[i]['guide_id']);
                            var without_checkbox = '<tr id="row' + i + '"><td scope="row">' + '<input type="hidden" name="' + i + '" class="checkbox">' + '</td>' +
                                '<td>' + '<input type="hidden" class="form-control" id="pid' + i + '" name="pid[]" value="' + response.data.data.hmisPilgrinmData[i]['pid'] + '" readonly/>' + response.data.data.hmisPilgrinmData[i]['pid'] + '</td>' +

                                '<td>' +
                                '<input type="hidden" name="pilgrim_id[]"  value="' + response.data.data.hmisPilgrinmData[i]['id'] + '" />' +
                                '<input type="hidden" class="form-control" id="full_name_english' + i + '" name="full_name_english[]" value="' + response.data.data.hmisPilgrinmData[i]['full_name_english'] + '" readonly/>' + response.data.data.hmisPilgrinmData[i]['full_name_english'] + '</td>' +
                                '<td>' +
                                '<input type="hidden" class="form-control" id="tracking_no' + i + '" name="tracking_no[]" value="' + response.data.data.hmisPilgrinmData[i]
                                    ['tracking_no'] + '" readonly/>' + response.data.data.hmisPilgrinmData[i]
                                    ['tracking_no'] + '</td>' +
                                '<td>' +
                                '<input type="hidden" class="form-control" id="mobile' + i + '" name="flight_code[]" value="' + response.data.data.hmisPilgrinmData[i]['flight_code'] + '" readonly/>' +
                                '<input type="hidden" class="form-control" id="mobile' + i + '" name="mobile[]" value="' + response.data.data.hmisPilgrinmData[i]['mobile'] + '" readonly/> ' + flight_msg + '</td>' +
                                '<td>' + guide_msg + '</td>' +
                                '<td>' + status_msg + '</td>' +
                                '</tr>';
                            var with_checkbox = '<tr id="row' + i + '"><td scope="row">' + '<input type="checkbox" name="check_data[' + response.data.data.hmisPilgrinmData[i]['pid'] + ']" value="' + response.data.data.hmisPilgrinmData[i]['pid'] + '" class="checkbox">' + '</td>' +
                                '<td>' + '<input type="hidden" class="form-control" id="pid' + i + '" name="pid[]" value="' + response.data.data.hmisPilgrinmData[i]['pid'] + '" readonly/>' + response.data.data.hmisPilgrinmData[i]['pid'] + '</td>' +
                                '<td>' +
                                '<input type="hidden" name="pilgrim_id2[' + response.data.data.hmisPilgrinmData[i]['pid'] + ']"  value="' + response.data.data.hmisPilgrinmData[i]['id'] + '" />' +
                                '<input type="hidden" name="pilgrim_id[]"  value="' + response.data.data.hmisPilgrinmData[i]['id'] + '" />' +
                                '<input type="hidden" class="form-control" id="full_name_english' + i + '" name="full_name_english2[' + response.data.data.hmisPilgrinmData[i]['pid'] + ']" value="' + response.data.data.hmisPilgrinmData[i]['full_name_english'] + '" readonly/>' + response.data.data.hmisPilgrinmData[i]['full_name_english'] + '</td>' +
                                '<input type="hidden" class="form-control" id="full_name_english' + i + '" name="full_name_english[]" value="' + response.data.data.hmisPilgrinmData[i]['full_name_english'] + '" readonly/>' + response.data.data.hmisPilgrinmData[i]['full_name_english'] + '</td>' +
                                '<td>' +
                                '<input type="hidden" class="form-control" id="tracking_no' + i + '" name="tracking_no2[' + response.data.data.hmisPilgrinmData[i]['pid'] + ']" value="' + response.data.data.hmisPilgrinmData[i] ['tracking_no'] + '" readonly/>' +
                                '<input type="hidden" class="form-control" id="tracking_no' + i + '" name="tracking_no[]" value="' + response.data.data.hmisPilgrinmData[i] ['tracking_no'] + '" readonly/>' + response.data.data.hmisPilgrinmData[i] ['tracking_no'] +
                                '</td>' +
                                // '<td>'+
                                '<input type="hidden" class="form-control" id="pid' + i + '" name="pid2[' + response.data.data.hmisPilgrinmData[i]['pid'] + ']" value="' + response.data.data.hmisPilgrinmData[i]['pid'] + '" readonly/>' +
                                // '</td>'+
                                // '<input type="hidden" class="form-control" id="serial_no'+i+'" name="serial_no[]" value="'+response.data.data.hmisPilgrinmData[i]['serial_no']+'" readonly/>'+response.data.data.hmisPilgrinmData[i]['serial_no']+'</td>'+
                                '<td>' + flight_msg + '</td>' +
                                '<td>' + guide_msg + '</td>' +
                                '<td>' + status_msg + '</td>' +
                                '<input type="hidden" class="form-control" id="mobile' + i + '" name="flight_code[]" value="' + response.data.data.hmisPilgrinmData[i]['flight_code'] + '" readonly/>' +
                                '<input type="hidden" class="form-control" id="mobile' + i + '" name="flight_code2[' + response.data.data.hmisPilgrinmData[i]['pid'] + ']" value="' + response.data.data.hmisPilgrinmData[i]['flight_code'] + '" readonly/>' +
                                '<input type="hidden" class="form-control" id="mobile' + i + '" name="mobile2[' + response.data.data.hmisPilgrinmData[i]['pid'] + ']" value="' + response.data.data.hmisPilgrinmData[i]['mobile'] + '" readonly/>' +
                                '<input type="hidden" class="form-control" id="mobile' + i + '" name="mobile[]" value="' + response.data.data.hmisPilgrinmData[i]['mobile'] + '" readonly/>' +
                                +'</td>' +
                                '</tr>';

                            if (response.data.data.hmisPilgrinmData[i]['guide_id'] != hmis_guide_id && response.data.data.hmisPilgrinmData[i]['guide_id'] > 0
                                || response.data.data.hmisPilgrinmData[i]['bd_to_ksa_id'] > 0
                                || selected_pilgrim_array.includes(response.data.data.hmisPilgrinmData[i]['pid'])) {

                                $('.load_pilgrim').append(
                                    without_checkbox
                                );
                            } else {
                                $('.load_pilgrim').append(
                                    with_checkbox
                                );
                            }
                        }
                    }
                },
                error: function (data) {
                }
            })
        } else {
            Swal.fire({
                text: message,
                icon: 'warning',
                // showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            })

        }
    }

    $('#beforPosition').on('click', function (e) {
        e.preventDefault();
        $("#search_list").css("display", "block");
        $(".toggle_data_talbe").css("display", "none");
        $(".search_field").css("display", "block");
    })

</script>
<script>
    $(document).ready(function () {
        // Attach a click event to the header checkbox
        $('#selectAll').click(function () {
            // Get the checked status of the header checkbox
            var checkedStatus = this.checked;
            // Set the checked property of all checkboxes in the table body
            $('#myTable tbody input[type="checkbox"]').each(function () {
                this.checked = checkedStatus;
            });
        });
    });
</script>
<script>
    function checkTotalPilgrim() {
        var max_assign_limit = $('#pilgrim_assign_limit').val();
        var flight_drop_down_list_null = $('#flight_drop_down_list_null').val();

        var fruitValues = [];
        var i = 0;
        var checkedValues = $('.checkbox:checked').map(function () {
            i++;
        }).get();
        prev_remaining = $('#remaining').val();
        // var max_assign_limit_exists = max_assign_limit - i;
        already_assigned = <?php echo $remain_assign_limit; ?>;
        var max_assign_limit_exists = already_assigned - i;
        remaining = prev_remaining - i;
        if (i == 0) {
            event.preventDefault();
            alert("Can Not Submit this form !")
            return false;
        }
        if (max_assign_limit_exists < 0) {
            event.preventDefault();
            alert("Your Assign Limit is " + max_assign_limit)
            return false;
        }
        if(flight_drop_down_list_null != "true"){
            if (remaining < 0) {
                event.preventDefault();
                alert("Maximum Value Exceeded.")
                return false;
            }
        }
    }
</script>


<script>
    // jQuery code that handles the onchange event and triggers an AJAX call
    $(document).ready(function () {
        let base_url = window.location.origin + '/process/action/getFlightDetails/' + $('#id').val();
        let flight_id = $('#listing_id').val();

        $('#listing_id').on('change', function () {
            var flight_id = $(this).val();

            $.ajax({
                cache: false,
                url: base_url,
                type: "POST",
                dataType: 'json',
                data: {'flight_id': flight_id},
                success: function (response) {
                    // console.log(response.data.data[0]);
                    $('#total_capacity').val(response.data.data[0].flight_capacity);
                    var remaining = response.data.data[0].flight_capacity - response.data.data[0].TotalPilgrims
                    $('#remaining').val(remaining);
                    $('#flight_get_code').val(response.data.data[0].flight_code);
                    $('#flight_capacity').html($('#total_capacity').val());
                    $('#seat_available').html($('#remaining').val());
                },
                error: function (xhr, status, error) {
                    // handle error
                }
            });
        });
    });

</script>
