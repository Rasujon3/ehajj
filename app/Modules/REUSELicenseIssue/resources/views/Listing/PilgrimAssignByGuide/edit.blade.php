<?php
$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);

$prefix = '';
if ($type[0] == 5) {
    //$prefix = 'client';
    $prefix = 'client';
}
$pilgrim_assign_limit = \App\Modules\Settings\Models\Configuration::where('caption','Guide_Pilgrim_Assign_limit')->first(['value']);
?>
<style >
    .dash-list-table {
        overflow-x: hidden;
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
        <h3>গাইডের অধীনে হজযাত্রী এড ( {{$session_id['caption']}} )</h3>
    </div>
    <div class="dash-section-content pt-3">
        <div class="dash-sec-head">
            <div class="container">
                <div class="card">
                    <div class="card-body" id="search_list" style="display: block">
                        <div class="row mt-4 mb-2">
                            @php
                                $process_typeid =  \App\Libraries\Encryption::encodeId($process_type_id)
                            @endphp
                            @php
                                $process_typeid =  \App\Libraries\Encryption::encodeId($process_type_id);
                                $app_id =  \App\Libraries\Encryption::encodeId($data->id);
                            @endphp
                            <input type="hidden" name="app_id" value="{{$app_id}}" />
                            <input type="hidden" id="id" value="{!!$process_typeid!!}" />
                            <input type="hidden" id="pilgrim_assign_limit" value="{{$pilgrim_assign_limit->value}}" />
                            <input type="hidden" id="check_array" value="{{$pilgrim_array}}" />
                            <input type="hidden" id="remaining" value=""/>
                            <input type="hidden" id="total_capacity" value=""/>
                            <input type="hidden" id="guide_id" value="{{\Illuminate\Support\Facades\Auth::user()->hmis_guide_id}}"/>
                            <input type="hidden" id="is_crm_guide" value="{{\Illuminate\Support\Facades\Auth::user()->is_crm_guide}}"/>

                        </div>

                        <div class="row">
                            <div class="col-sm-11 mb-2">
                                <form class="form-group">
                                    <input class="form-control mr-sm-2 search_field" type="search"
                                           name="request_data" id="request_data" value="{{$tracking_no}}" placeholder="হজযাত্রীর  ট্র্যাকিং নম্বর প্রদান করুন" autocomplete="off">
                                </form>
                            </div>
                            <div class="col-sm-1">
                                <button type="button" style="float:right;" class="btn btn-secondary search_button" onclick='searchPilgrim()'>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
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
                <div class="dash-list-table my-4 toggle_data_talbe" style="display:none;" >
                    <span  class="btn btn-primary btn-sm mb-2">
                        একই ইউনিট এ সর্বমোট হজযাত্রী <span class="badge badge-light" id="total_pilgrim">0 </span>
                    </span>
                    <span  class="btn btn-secondary btn-sm mb-2" style="float: right;">
                        মোট কোটা <span class="badge badge-light" id="guide_capacity">0 </span>
                    </span>

                    <span  class="btn btn-info btn-sm mb-2">
                        নতুন হজযাত্রী এড করতে পারবেন  <span class="badge badge-light" id="seat_available">0 </span>
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
                                    <th scope="col">গাইড</th>
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
                    @if ( in_array($user_type,['4x404','21x101']) )
                        <div class="row">
                            <div class="col-sm-6">
                                <a class="btn btn-info btn-sm" style="margin-left:10px" href="{{ url($prefix.'/process/list/'. Encryption::encodeId(1)) }}">
                                    বন্ধ করুন
                                </a>
                            </div>
                            <div class="col-sm-6 text-right" style="padding-right:35px">
                                <button class="btn btn-danger btn-sm" id="beforPosition"> পূর্বাবস্থায় </button>
                                <button class="btn btn-primary btn-sm" name="actionBtn" onclick="checkTotalPilgrim()" value="draft">সংরক্ষণ এবং খসড়া</button>
                                <button class="btn btn-success btn-sm" name="actionBtn" onclick="checkTotalPilgrim()" value="submit">সাবমিট</button>
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


    /*$('input[type=radio][name=process_type]').change(function() {
        if (this.value == 'serial_no') {
            $("#request_data").attr("placeholder", "সিরিয়াল নাম্বার প্রদান করুন");
        }
        else if (this.value == 'tracking_no') {
            $("#request_data").attr("placeholder", "ট্র্যাকিং নম্বর প্রদান করুন");
        }
        else if (this.value == 'voucher_no') {
            $("#request_data").attr("placeholder", " রেজিঃ ভাউচার নাম্বার প্রদান করুন");
        }
    });*/


    function searchPilgrim()

    {
        let base_url = window.location.origin + '/process/action/get-hmis-pilgrims-for-guide/' + $('#id').val();
        let request_data = $('#request_data').val();
        let is_crm_guide = $('#is_crm_guide').val();

        if(request_data !=undefined){

            $.ajax({
                cache:false,
                url: base_url,
                type: "POST",
                dataType: 'json',
                data: {
                    'request_data': request_data,
                    'is_crm_guide': is_crm_guide
                },
                success: function(response) {
                    var selected_pilgrim_array = response.data.data.available_pid;

                    if (response.data.data.hmisPilgrinmData == undefined || response.data.data.hmisPilgrinmData.length == 0 ) {
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
                    }
                    else {
                        $("#search_list").css("display", "block");
                        $(".toggle_data_talbe").css("display", "block");
                        $(".search_field").css("display", "block");
                        let dataLen = response.data.data.hmisPilgrinmData.length;
                        $('#total_pilgrim').html(dataLen);
                        $('#flight_capacity').html($('#total_capacity').val());
                        $('#seat_available').html($('#remaining').val());

                        $('.load_pilgrim').html('');

                        for(var i =0 ; i<dataLen; i++){
                            // console.log(response.data.data.hmisPilgrinmData[i]['flight_code']);
                            if(response.data.data.hmisPilgrinmData[i]['pilgrim_listing_id']!=0){

                                $("#draft").css("display", "none");
                                $("#save").css("display", "none");

                            }

                            if(response.data.data.hmisPilgrinmData[i]['guide_id'] != 0 || response.data.data.hmisPilgrinmData[i]['will_not_perform'] != 0  ){
                                var status_msg = "";
                                if(response.data.data.hmisPilgrinmData[i]['will_not_perform'] == 0){
                                    status_msg = "Will Not Perform Hajj";
                                }else{
                                    status_msg =  "Already Assigned";
                                }
                                $('.load_pilgrim').append(

                                    '<tr id="row' + i + '"><td scope="row">'+  '<input type="hidden" name="' + i + '" class="checkbox">' + '</td>'+
                                    '<td>'+ '<input type="hidden" class="form-control" id="pid'+i+'" name="pid[]" value="'+response.data.data.hmisPilgrinmData[i]['pid']+'" readonly/>'+response.data.data.hmisPilgrinmData[i]['pid']+'</td>'+

                                    '<td>'+
                                    '<input type="hidden" name="pilgrim_id[]"  value="'+response.data.data.hmisPilgrinmData[i]['id']+'" />'+
                                    '<input type="hidden" class="form-control" id="full_name_english'+i+'" name="full_name_english[]" value="'+response.data.data.hmisPilgrinmData[i]['full_name_english']+'" readonly/>'+response.data.data.hmisPilgrinmData[i]['full_name_english']+'</td>'+
                                    '<td>'+
                                    '<input type="hidden" class="form-control" id="tracking_no'+i+'" name="tracking_no[]" value="'+response.data.data.hmisPilgrinmData[i]
                                        ['tracking_no']+'" readonly/>'+response.data.data.hmisPilgrinmData[i]
                                        ['tracking_no']+'</td>'+
                                    '<td>'+
                                    '<input type="hidden" class="form-control" id="mobile'+i+'" name="flight_code[]" value="'+response.data.data.hmisPilgrinmData[i]['flight_code']+'" readonly/>'+

                                    '<input type="hidden" class="form-control" id="mobile'+i+'" name="mobile[]" value="'+response.data.data.hmisPilgrinmData[i]['mobile']+'" readonly/>'+status_msg+' </td>'+

                                    '</tr>'
                                );
                            }else{
                                let check_array = $('#check_array').val();
                                var check_status = '';
                                if(check_array.includes(response.data.data.hmisPilgrinmData[i]['pid']) ){
                                    check_status = 'checked';
                                }
                                $('.load_pilgrim').append(

                                    '<tr id="row' + i + '"><td scope="row">'+  '<input type="checkbox" '+check_status+' name="check_data['+response.data.data.hmisPilgrinmData[i]['pid']+']" value="'+response.data.data.hmisPilgrinmData[i]['pid']+'" class="checkbox">' + '</td>'+
                                    '<td>'+  '<input type="hidden" class="form-control" id="pid'+i+'" name="pid[]" value="'+response.data.data.hmisPilgrinmData[i]['pid']+'" readonly/>'+response.data.data.hmisPilgrinmData[i]['pid'] + '</td>'+
                                    '<td>'+
                                    '<input type="hidden" name="pilgrim_id2['+response.data.data.hmisPilgrinmData[i]['pid']+']"  value="'+response.data.data.hmisPilgrinmData[i]['id']+'" />'+
                                    '<input type="hidden" name="pilgrim_id[]"  value="'+response.data.data.hmisPilgrinmData[i]['id']+'" />'+
                                    '<input type="hidden" class="form-control" id="full_name_english'+i+'" name="full_name_english2['+response.data.data.hmisPilgrinmData[i]['pid']+']" value="'+response.data.data.hmisPilgrinmData[i]['full_name_english']+'" readonly/>'+response.data.data.hmisPilgrinmData[i]['full_name_english']+'</td>'+
                                    '<input type="hidden" class="form-control" id="full_name_english'+i+'" name="full_name_english[]" value="'+response.data.data.hmisPilgrinmData[i]['full_name_english']+'" readonly/>'+response.data.data.hmisPilgrinmData[i]['full_name_english']+'</td>'+
                                    '<td>'+
                                    '<input type="hidden" class="form-control" id="tracking_no'+i+'" name="tracking_no2['+response.data.data.hmisPilgrinmData[i]['pid']+']" value="'+response.data.data.hmisPilgrinmData[i] ['tracking_no']+'" readonly/>'+
                                    '<input type="hidden" class="form-control" id="tracking_no'+i+'" name="tracking_no[]" value="'+response.data.data.hmisPilgrinmData[i] ['tracking_no']+'" readonly/>'+response.data.data.hmisPilgrinmData[i] ['tracking_no']+
                                    '</td>'+
                                    // '<td>'+
                                    '<input type="hidden" class="form-control" id="pid'+i+'" name="pid2['+response.data.data.hmisPilgrinmData[i]['pid']+']" value="'+response.data.data.hmisPilgrinmData[i]['pid']+'" readonly/>'+
                                    // '</td>'+
                                    // '<input type="hidden" class="form-control" id="serial_no'+i+'" name="serial_no[]" value="'+response.data.data.hmisPilgrinmData[i]['serial_no']+'" readonly/>'+response.data.data.hmisPilgrinmData[i]['serial_no']+'</td>'+
                                    '<td>'+"Not Assigned"+'</td>'+
                                    '<input type="hidden" class="form-control" id="mobile'+i+'" name="flight_code[]" value="'+response.data.data.hmisPilgrinmData[i]['flight_code']+'" readonly/>'+
                                    '<input type="hidden" class="form-control" id="mobile'+i+'" name="flight_code2['+response.data.data.hmisPilgrinmData[i]['pid']+']" value="'+response.data.data.hmisPilgrinmData[i]['flight_code']+'" readonly/>'+
                                    '<input type="hidden" class="form-control" id="mobile'+i+'" name="mobile2['+response.data.data.hmisPilgrinmData[i]['pid']+']" value="'+response.data.data.hmisPilgrinmData[i]['mobile']+'" readonly/>'+
                                    '<input type="hidden" class="form-control" id="mobile'+i+'" name="mobile[]" value="'+response.data.data.hmisPilgrinmData[i]['mobile']+'" readonly/>'+
                                    +'</td>'+
                                    '</tr>'
                                );
                            }
                        }
                    }
                },
                error: function(data) {
                    // console.log((data.responseJSON.errors));
                }
            })
        }
        else{
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

    $('#beforPosition').on('click',function(e){
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
            // Get the checked status of the header checkbox
            var checkedStatus = this.checked;
            // Set the checked property of all checkboxes in the table body
            $('#myTable tbody input[type="checkbox"]').each(function() {
                this.checked = checkedStatus;
            });
        });
    });
</script>
<script>
    function checkTotalPilgrim(){
        var assign_limit = $('#pilgrim_assign_limit').val();
        var i =0;
        var checkedValues = $('.checkbox:checked').map(function() {
            // console.log(this.value);
            i++;

        }).get();
        prev_remaining = $('#remaining').val();
        remaining = prev_remaining-i;
        if(i==0){
            event.preventDefault();
            alert("Can Not Submit this form !")
            return false;
        }
        if(i>assign_limit){
            event.preventDefault();
            alert("You Can Add Max 44.")
            return false;
        }
        if(remaining<0){
            event.preventDefault();
            alert("Maximum Value Exceeded.")
            return false;
        }
    }
</script>



<script>
    // jQuery code that handles the onchange event and triggers an AJAX call
    $(document).ready(function() {
        let base_url = window.location.origin + '/process/action/getGuideDetails/' + $('#id').val();

        let guide_id = $('#guide_id').val();


        $.ajax({
            cache:false,
            url: base_url,
            type: "POST",
            dataType: 'json',
            data: {
                'guide_id': guide_id,

            },
            success: function(response) {
                var assign_limit = $('#pilgrim_assign_limit').val();
                var remaining = assign_limit - response.data.data.total_assigned_pilgrims
                $('#remaining').val(remaining);
                $('#flight_get_code').val(response.data.data.flight_code);
                $('#flight_capacity').html($('#total_capacity').val());
                $('#seat_available').html($('#remaining').val());
                $('#guide_capacity').html(assign_limit);
            },
            error: function(xhr, status, error) {
                // handle error
            }
        });

    });

</script>
<script>
    $("document").ready(function() {
        $(".search_button").trigger('click');
    });
</script>
