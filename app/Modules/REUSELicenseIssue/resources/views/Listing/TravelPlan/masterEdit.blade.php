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
</style>
{!! Form::open([
    'url' => url('process/action/store/' . \App\Libraries\Encryption::encodeId($process_type_id)),
    'method' => 'post',
    'class' => 'form-horizontal',
    'id' => 'application_form',
    'enctype' => 'multipart/form-data',
    'files' => 'true',
]) !!}

@csrf


<div class="dash-content-main">
    <div class="dash-sec-head mt-3">
        <h3>ট্রাভেল প্ল্যান লিষ্টিং ( {{ $session_id['caption'] }} )</h3>
    </div>
    <div class="dash-section-content pt-3">
        <div class="dash-sec-head">
            <div class="container">
                <div class="card">
                    <div class="card-body" id="search_list" style="display: block">
                        <div class="row mt-4 mb-2">
                            @php
                                $process_typeid = \App\Libraries\Encryption::encodeId($process_type_id);
                                $app_id = \App\Libraries\Encryption::encodeId($data->id);
                                $get_data = $data->json_object;
                                
                            @endphp
                            <input type="hidden" id="id" value="{!! $process_typeid !!}" />
                            <input type="hidden" name="app_id" value="{{ $app_id }}" />
                            <input type="hidden" id="get_data" value="{{ $get_data }}" />
                            <input type="hidden" id="selected_pid" value="{{ $pilgrim_array }}" />

                            {{--                            <div class="col-sm-6"> --}}
                            {{--                                <div class="form-group"> --}}
                            {{--                                    <label for="radio" class="form-label label_modify">সার্চ লিস্ট</label> --}}
                            {{--                                    <div class="row" style="margin:0px;"> --}}
                            {{--                                        @php $process_typeid =  \App\Libraries\Encryption::encodeId($process_type_id) --}}
                            {{--                                        @endphp --}}
                            {{--                                        <input type="hidden" id="id" value="{!!$process_typeid!!}" /> --}}
                            {{--                                        <div class="input-group-text form-control" style="padding:0px 15px;"> --}}
                            {{--                                            <div class="col mt-1" style="padding-left:0px;"> --}}
                            {{--                                                <label class="mb-0" style="float:left">{!! Form::radio('process_type', 'serial_no')!!} Serial No. --}}
                            {{--                                                </label> --}}
                            {{--                                            </div> --}}
                            {{--                                            <div class="col mt-1" style="padding-left:0px;"> --}}
                            {{--                                                <label class="mb-0">{!! Form::radio('process_type', 'tracking_no') !!} Tracking No. --}}
                            {{--                                                </label> --}}
                            {{--                                            </div> --}}
                            {{--                                            <div class="col mt-1" style="padding-left:0px;"> --}}
                            {{--                                                <label class="mb-0" style="float:right;">{!! Form::radio('process_type', 'voucher_no') !!} Voucher No. --}}
                            {{--                                                </label> --}}
                            {{--                                            </div> --}}
                            {{--                                        </div> --}}
                            {{--                                    </div> --}}
                            {{--                                </div> --}}
                            {{--                            </div> --}}

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">
                                        হজ ফ্লাইট সিলেক্ট করুন</label>
                                    <select class="form-control" name="flight_id" id="listing_id">
                                        <option selected disabled value=""> তালিকা নির্বাচন করুন </option>

                                        @foreach ($flight_drop_down_list['data'] as $key => $item)
                                            <option value="{{ $item['id'] }}"
                                                @if ($data->listing_id == $item['id']) selected @endif>{{ $item['Flight'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="total_capacity" value=""/>
                                    <input type="hidden" id="remaining" value=""/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @php
                                $searchitem = '';
                                $json_data = json_decode($data->processlist->json_object);
                                $numItems = count($json_data);
                                $i = 0;
                                $delimeter = ',';
                            @endphp
                            @foreach ($json_data as $key => $value)
                                @if (++$i === $numItems)
                                    @php $delimeter = ''; @endphp
                                @else
                                    @php $delimeter = ','; @endphp
                                @endif
                                @php $searchitem.= $value.$delimeter;  @endphp
                            @endforeach
                            <div class="col-sm-11 mb-2">
                                <form class="form-group">
                                    <input class="form-control mr-sm-2 search_field" type="search" name="request_data"
                                        id="request_data" value="{{ $searchitem }}"
                                        placeholder="ট্র্যাকিং নম্বর/ সিরিয়াল নাম্বার/ রেজিঃ ভাউচার নাম্বার প্রদান করুন"
                                        autocomplete="off">

                                </form>
                            </div>
                            <div class="col-sm-1">
                                <button type="button" style="float:right;" class="btn btn-secondary"
                                    onclick='searchPilgrim()' id="search_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z">
                                        </path>
                                    </svg>
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dash-content-inner">
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
                                        <th scope="col"> <input type="checkbox" class="checkbox" id="selectAll"></th>
                                        <th scope="col">পিআইডি</th>
                                        <th scope="col">নাম</th>
                                        <th scope="col">ট্র্যাকিং নম্বর</th>
                                        {{-- <th scope="col">পিআইডি</th> --}}
                                        <th scope="col">ফ্লাইট কোড</th>
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
                            {{-- <p>বি: দ্র: কোনো ট্র্যাকিং নাম্বার বাদ পরলে উক্ত নাম্বারটি উপরে আপলোড করুন।</p> --}}
                        </div>
                    </div>
                    @if (in_array($user_type, ['4x404', '21x101']))
                        <div class="row">
                            <div class="col-sm-6">
                                <a class="btn btn-info btn-sm" style="margin-left:10px"
                                    href="{{ url($prefix . '/process/list/' . Encryption::encodeId(1)) }}">
                                    বন্ধ করুন
                                </a>
                            </div>
                            <div class="col-sm-6 text-right" style="padding-right:35px">
                                <button class="btn btn-danger btn-sm" id="beforPosition"> পূর্বাবস্থায় </button>
                                <button class="btn btn-primary btn-sm" name="actionBtn" value="draft">সংরক্ষণ এবং
                                    খসড়া</button>
                                <button class="btn btn-success btn-sm" name="actionBtn" value="submit">সাবমিট</button>
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
    function searchPilgrim()

    {
        let base_url = window.location.origin + '/process/action/getHmisPilgrimListing/' + $('#id').val();
        let request_data = $('#request_data').val();
        let selected_pilgrim_array = $('#selected_pid').val();
        let pilgrim_array = $.parseJSON(selected_pilgrim_array);
        let myArr = Object.values(pilgrim_array);

        if (request_data != undefined) {

            $.ajax({
                cache: false,
                url: base_url,
                type: "POST",
                dataType: 'json',
                data: {
                    'request_data': request_data
                },
                success: function(response) {
                    
                    if (response.data.data == undefined) {
                        $('.load_pilgrim').html(
                            "<tr class='mt-3'><td class='pt-4 text-red' colspan='6'>Data not found!!</td></tr>"
                        );
                    } else {
                        $("#search_list").css("display", "block");
                        $(".toggle_data_talbe").css("display", "block");
                        $(".search_field").css("display", "block");
                        let dataLen = response.data.data.length;
                        $('#total_pilgrim').html(dataLen);
                        // alert(dataLen);
                        $('.load_pilgrim').html('');

                        for (var i = 0; i < dataLen; i++) {

                            if (response.data.data[i]['pilgrim_listing_id'] != 0) {

                                $("#draft").css("display", "none");
                                $("#save").css("display", "none");
                                // is_proceed=0;
                                // listed_arr.push(response.data.data[i]['serial_no']);
                            }
                            if (selected_pilgrim_array.includes(response.data.data[i]['pid'])) {
                                checked = "checked";
                                // console.log()
                            } else {
                                checked = '';
                            }
                            // console.log(selected_pilgrim_array);
                            // console.log(checked,selected_pilgrim_array,response.data.data[i]['pid']);
                            if (response.data.data[i]['flight_code'] != null) {
                                $('.load_pilgrim').append(

                                    '<tr id="row' + i + '"><td scope="row">' +
                                    '<input type="hidden" name="' + i + '"' + (selected_pilgrim_array
                                        .includes(response.data.data[i]['pid']) ? ' checked' : '') +
                                    ' class="checkbox">' +
                                    // '<input type="hidden" name="' + i + '" "'+checked+'"  class="checkbox">' +
                                    '<td>' + '<input type="hidden" class="form-control" id="pid' + i +
                                    '" name="pid[]" value="' + response.data.data[i]['pid'] +
                                    '" readonly/>' + response.data.data[i]['pid'] + '</td>' +

                                    '<td>' +
                                    '<input type="hidden" name="pilgrim_id[]"  value="' + response.data
                                    .data[i]['id'] + '" />' +
                                    '<input type="hidden" class="form-control" id="full_name_english' +
                                    i + '" name="full_name_english[]" value="' + response.data.data[i][
                                        'full_name_english'
                                    ] + '" readonly/>' + response.data.data[i]['full_name_english'] +
                                    '</td>' +
                                    '<td>' +
                                    '<input type="hidden" class="form-control" id="tracking_no' + i +
                                    '" name="tracking_no[]" value="' + response.data.data[i]
                                    ['tracking_no'] + '" readonly/>' + response.data.data[i]
                                    ['tracking_no'] + '</td>' +
                                    '<td>' +
                                    '<input type="hidden" class="form-control" id="mobile' + i +
                                    '" name="flight_code[]" value="' + response.data.data[i][
                                        'flight_code'
                                    ] + '" readonly/>' +
                                    '<input type="hidden" class="form-control" id="mobile' + i +
                                    '" name="mobile[]" value="' + response.data.data[i]['mobile'] +
                                    '" readonly/>' +
                                    response.data.data[i]['flight_code'] + '</td>' +

                                    '</tr>'
                                );
                            } else {
                                $('.load_pilgrim').append(

                                    '<tr id="row' + i + '"><td scope="row">' +
                                    '<input type="checkbox" name="check_data[' + response.data.data[i][
                                        'pid'
                                    ] + ']"' + (selected_pilgrim_array.includes(response.data.data[i][
                                        'pid'
                                    ]) ? ' checked' : '') + ' class="checkbox">' +
                                    // '<input type="checkbox" "'+checked+'" name="check_data[' + response.data.data[i][
                                    //     'pid'
                                    // ] + ']" class="checkbox">' +
                                    '<td>' + '<input type="hidden" class="form-control" id="pid' + i +
                                    '" name="pid[]" value="' + response.data.data[i]['pid'] +
                                    '" readonly/>' + response.data.data[i]['pid'] + '</td>' +
                                    '<td>' +
                                    '<input type="hidden" name="pilgrim_id2[' + response.data.data[i][
                                        'pid'
                                    ] + ']"  value="' + response.data.data[i]['id'] + '" />' +
                                    '<input type="hidden" name="pilgrim_id[]"  value="' + response.data
                                    .data[i]['id'] + '" />' +
                                    '<input type="hidden" class="form-control" id="full_name_english' +
                                    i + '" name="full_name_english2[' + response.data.data[i]['pid'] +
                                    ']" value="' + response.data.data[i]['full_name_english'] +
                                    '" readonly/>' + response.data.data[i]['full_name_english'] +
                                    '</td>' +
                                    '<input type="hidden" class="form-control" id="full_name_english' +
                                    i + '" name="full_name_english[]" value="' + response.data.data[i][
                                        'full_name_english'
                                    ] + '" readonly/>' + response.data.data[i]['full_name_english'] +
                                    '</td>' +
                                    '<td>' +
                                    '<input type="hidden" class="form-control" id="tracking_no' + i +
                                    '" name="tracking_no2[' + response.data.data[i]['pid'] +
                                    ']" value="' + response.data.data[i]['tracking_no'] +
                                    '" readonly/>' +
                                    '<input type="hidden" class="form-control" id="tracking_no' + i +
                                    '" name="tracking_no[]" value="' + response.data.data[i][
                                        'tracking_no'
                                    ] + '" readonly/>' + response.data.data[i]['tracking_no'] +
                                    '</td>' +
                                    // '<td>'+
                                    '<input type="hidden" class="form-control" id="pid' + i +
                                    '" name="pid2[' + response.data.data[i]['pid'] + ']" value="' +
                                    response.data.data[i]['pid'] + '" readonly/>' +
                                    // '</td>'+
                                    // '<input type="hidden" class="form-control" id="serial_no'+i+'" name="serial_no[]" value="'+response.data.data[i]['serial_no']+'" readonly/>'+response.data.data[i]['serial_no']+'</td>'+
                                    '<td>' + "Not Assigned" + '</td>' +
                                    '<input type="hidden" class="form-control" id="mobile' + i +
                                    '" name="flight_code[]" value="' + response.data.data[i][
                                        'flight_code'
                                    ] + '" readonly/>' +
                                    '<input type="hidden" class="form-control" id="mobile' + i +
                                    '" name="flight_code2[' + response.data.data[i]['pid'] +
                                    ']" value="' + response.data.data[i]['flight_code'] +
                                    '" readonly/>' +
                                    '<input type="hidden" class="form-control" id="mobile' + i +
                                    '" name="mobile2[' + response.data.data[i]['pid'] + ']" value="' +
                                    response.data.data[i]['mobile'] + '" readonly/>' +
                                    '<input type="hidden" class="form-control" id="mobile' + i +
                                    '" name="mobile[]" value="' + response.data.data[i]['mobile'] +
                                    '" readonly/>' +
                                    +'</td>' +
                                    '</tr>'
                                );
                            }
                        }


                        if (is_proceed == 0) {

                            Swal.fire({
                                // text: 'These applications are already listed:' + listed_arr.toString(),
                                text: 'These applications are already listed',
                                icon: 'warning',
                                // showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'OK'
                            })
                            // alert('These applications are already listed:' + listed_arr.toString() );
                        }

                        if ($('input[name="process_type"]:checked').val() != '') {
                            var diff = $(sent_arr).not(received_arr).get();

                            if (jQuery.isEmptyObject(diff) == false) {

                                Swal.fire({
                                    // text: 'Data not found for serial:' + diff.toString(),
                                    text: 'Data not found',
                                    icon: 'warning',
                                    // showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'OK'
                                })


                            }

                            if (jQuery.isEmptyObject(received_arr) == true) {
                                $("#search_list").css("display", "block");
                                $("#request_data").css("display", "block");
                                $(".search_field ").css("display", "block");
                                $(".toggle_data_talbe").css("display", "none");
                            }
                        }


                    }
                },
                error: function(data) {
                    // console.log((data.responseJSON.errors));
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

    $('#beforPosition').on('click', function(e) {
        e.preventDefault();
        $("#search_list").css("display", "block");
        $(".toggle_data_talbe").css("display", "none");
        $(".search_field").css("display", "block");
    })
</script>
<script>
    $(document).ready(function() {
        // Attach a click event to the header checkbox
        $('#selectAll').click(function() {
            
            var checkedStatus = this.checked;
            
            $('#myTable tbody input[type="checkbox"]').each(function() {
                this.checked = checkedStatus;
            });
        });
    });
</script>

<script>
    var data = $('#get_data').val();
    data = $.parseJSON(data);
    let dataLen = data.length;
    if (dataLen > 0) {
        $('#search_btn').click();
    }
</script>

<script>
    $(document).ready(function() {
        let base_url = window.location.origin + '/process/action/getFlightDetails/' + $('#id').val();
        let flight_id = $('#listing_id').val();
        
        $('#listing_id').on('change', function() {
            var flight_id = $(this).val();
            console.log(flight_id);
            $.ajax({
                cache:false,
                url: base_url,
                type: "POST",
                dataType: 'json',
                data: {'flight_id': flight_id},
                success: function(response) {                    
                    // console.log(response.data.data.flight_capacity);
                    $('#total_capacity').val(response.data.data.flight_capacity);                    
                    var remaining = response.data.data.flight_capacity - response.data.data.TotalPilgrims
                    $('#remaining').val(remaining);
                    $('#flight_capacity').html($('#total_capacity').val());
                    $('#seat_available').html($('#remaining').val());
                },
                error: function(xhr, status, error) {
                    // handle error
                }
            });
        });
    });

    $(document).ready(function() {
        let base_url = window.location.origin + '/process/action/getFlightDetails/' + $('#id').val();
        let flight_id = $('#listing_id').val();
        
            $.ajax({
                cache:false,
                url: base_url,
                type: "POST",
                dataType: 'json',
                data: {'flight_id': flight_id},
                success: function(response) {                    
                    $('#total_capacity').val(response.data.data.flight_capacity);                    
                    var remaining = response.data.data.flight_capacity - response.data.data.TotalPilgrims
                    $('#remaining').val(remaining);
                    $('#flight_capacity').html($('#total_capacity').val());
                    $('#seat_available').html($('#remaining').val());
                },
                error: function(xhr, status, error) {
                    // handle error
                }
            });
       
    });
</script>

