<?php
$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);

$prefix = '';
if ($type[0] == 5) {
    //$prefix = 'client';
    $prefix = 'client';
}

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
        <h3> হজযাত্রীর তথ্য পরিবর্তন  ( {{$session_id['caption']}} )</h3>
    </div>
    <div class="dash-section-content pt-3">
        <div class="dash-sec-head">
            <div class="container">
                <div class="card">
                    <div class="card-body" id="search_list" style="display: block">
                        <div class="row mt-2 mb-2">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="search-title">
                                        <h3>সার্চ লিস্ট</h3>
                                    </div>
                                    <div class="ehajj-search-list-form">
                                        @php $process_typeid =  \App\Libraries\Encryption::encodeId($process_type_id)
                                        @endphp
                                        <input type="hidden" id="id" value="{!!$process_typeid!!}" />

                                        <div class="search-list-group input-group-text form-control " >

{{--                                            <div class="col mt-1" style="padding-left:0px;text-align:left">--}}
{{--                                                {!! Form::radio('process_type', 'serial_no', false, ['id'=>'ehajj_src_serial_no'])!!}--}}
{{--                                                <label for="ehajj_src_serial_no">Serial No.</label>--}}
{{--                                            </div>--}}
                                            <div class="col mt-1" style="padding-left:0px;text-align:left">
                                                {!! Form::radio('process_type', 'selected', 'tracking_no', false, ['id'=>'ehajj_src_tracking_no']) !!}

                                                <label for="ehajj_src_tracking_no">Tracking No.</label>
                                            </div>
{{--                                            <div class="col mt-1" style="padding-left:0px;text-align:left">--}}
{{--                                                {!! Form::radio('process_type', 'voucher_no', false, ['id'=>'ehajj_src_voucher_no']) !!}--}}
{{--                                                <label for="ehajj_src_voucher_no">Voucher No.</label>--}}
{{--                                            </div>--}}


                                        </div>
                                    </div>
                                </div>
                            </div>
{{--                            <div class="col-sm-6">--}}
{{--                                <div class="form-group">--}}
{{--                                    <div class="search-title">--}}
{{--                                        <h3>লিষ্টিং</h3>--}}
{{--                                    </div>--}}
{{--                                    <div class="ehajj-search-list-form">--}}
{{--                                        <select class="form-control" name="listing_id" id="listing_id">--}}
{{--                                            <option selected disabled value=""> তালিকা নির্বাচন করুন </option>--}}
{{--                                            --}}{{--@foreach($pilgirm_dropdown_list['data'] as $key => $item)--}}
{{--                                                <option value="{{$item['id']}}">{{$item['name']}}</option>--}}
{{--                                            @endforeach--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                        </div>

                        <div class="row">
                            <div class="col-sm-12 mb-2">
                                <form class="form-group">
                                    <div class="ehajj-search-input">
                                        <input class="form-control mr-sm-2 search_field" type="search"
                                               name="request_data" id="request_data"  placeholder="ট্র্যাকিং নম্বর প্রদান করুন" autocomplete="off">
                                        <button type="button" style="float:right;background:#6c757d;border-color:#6c757d" class="btn search_field btn-ehajj-search btn-secondary" onclick='searchPilgrim()'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dash-content-inner">
            <div class="src-hajj-tracking-number">
                <div class="dash-list-table py-4 toggle_data_talbe" style="display:none;" >

                    <div class="card">
                        <div class="card-header text-bold">
                            হজযাত্রীর তথ্য
                        </div>
                        <div class="card-body">
                            <fieldset>

                                <div class="row">
                                    <label for="name" class="col-lg-2 text-left">Tracking No:</label>
                                    <div class="col-lg-8">
                                        <p id="tracking_no_div"></p>
                                        <input type="hidden" class="form-control" placeholder="Enter Tracking no" name="tracking_no" id="tracking_no" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="name" class="col-lg-2 text-left">Serial No:</label>
                                    <div class="col-lg-8">
                                        <p id="serial_no_div"></p>
                                        <input type="hidden" class="form-control" placeholder="Enter Serial no" name="serial_no" id="serial_no" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="name" class="col-lg-2 text-left">Name:</label>
                                    <div class="col-lg-8">
                                        <p id="name_div"></p>
                                        <input type="hidden" class="form-control" name="full_name_english"  id="name" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="name" class="col-lg-2 text-left">Father Name:</label>
                                    <div class="col-lg-8" >
                                        <p id="father_name_div"></p>
                                        <input type="hidden" class="form-control" placeholder="Enter Father name" name="father_name" id="father_name" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="name" class="col-lg-2 text-left">Present Address:</label>
                                    <div class="col-lg-8">
                                        <p id="present_address_div"></p>
                                        <input type="hidden" class="form-control" placeholder="Enter" name="present_address" id="present_address" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="name" class="col-lg-2 text-left">DoB:</label>
                                    <div class="col-lg-8">
                                        <p id="dob_div"></p>
                                        <input type="hidden" class="form-control"  name="dob" id="dob" readonly>
                                    </div>
                                </div>
                                <div class="row" >
                                    <label for="name" class="col-lg-2 text-left">Pilgrim Type:</label>
                                    <div class="col-lg-8">
                                        <p id="is_govt_div"></p>
                                        <input type="hidden" class="form-control"  name="is_govt" id="is_govt" readonly>
                                        <input type="hidden" class="form-control"  name="pilgrim_type_id" id="pilgrim_type_id" readonly>
                                    </div>
                                </div>
                                <div class="row" >
                                <span class="text-bold">পরিবর্তনযোগ্য তথ্য</span><br><br>
                                </div>

                                <div class="form-group row">
                                    <label for="name" class="col-lg-2 text-left">Gender:</label>
                                    <div class="col-lg-1" id="check_gender_div" style="display: none">
                                        <input type="checkbox" name="gender_checked"  id="gender_checked"/>
                                    </div>
                                    <div class="col-lg-4" >
                                        <p id="gender_div"></p>
                                        <input type="hidden" class="form-control"  id="gender" name="gender" readonly>
                                    </div>
                                    <div class="col-lg-4" id="gender_change" style="display: none">

                                    </div>

                                </div>
                                <div class="form-group row">
                                    <label for="name" class="col-lg-2 text-left">Mobile:</label>
                                    <div class="col-lg-1" >
                                        <input type="checkbox" name="mobile_checked"  id="mobile_checked"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <p id="mobile_div"></p>
                                        <input type="hidden" class="form-control" id="mobile" name="mobile" readonly>
                                    </div>
                                    <div class="col-lg-4" id="mobile_change_div" style="display: none">
                                        <input type="text" class="form-control"  id="mobile_change" name="mobile_change">
                                        <span id="phone-error"></span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="name" class="col-lg-2 text-left" id="ksa_mobile_label" style="display: none">KSA Mobile:</label>
                                    <div class="col-lg-1" >
                                        <input type="checkbox" name="ksa_mobile_checked"  id="ksa_mobile_checked" style="display: none"/>
                                    </div>
                                    <div class="col-lg-4" id="ksa_mobile_div" style="display: none">
                                        <input type="hidden" class="form-control" id="ksa_mobile"  name="ksa_mobile" readonly>
                                    </div>
                                    <div class="col-lg-4" id="ksa_mobile_change_div" style="display: none">
                                        <input type="text" class="form-control"  id="ksa_mobile_change" name="ksa_mobile_change">
                                        <span id="phone-error"></span>
                                    </div>
                                </div>
                                <!-- Name in English Start -->
                                <div class="form-group row">
                                    <label for="name" class="col-lg-2 text-left">Name in English:</label>
                                    <div class="col-lg-1" >
                                        <input type="checkbox" name="name_in_english_checked"  id="name_in_english_checked"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <p id="name_in_english_div"></p>
                                        <input type="hidden" class="form-control" id="name_in_english" name="name_in_english" readonly>
                                    </div>
                                    <div class="col-lg-4" id="name_in_english_change_div" style="display: none">
                                        <input type="text" class="form-control"  id="name_in_english_change" name="name_in_english_change" placeholder="Name in English">
                                        <span id="name_in_english_error" class="text-danger"></span>
                                    </div>
                                </div>
                                <!-- Name in English End -->
                                <!-- Name in Bangla Start -->
                                <div class="form-group row">
                                    <label for="name" class="col-lg-2 text-left">Name in Bangla:</label>
                                    <div class="col-lg-1" >
                                        <input type="checkbox" name="name_in_bangla_checked"  id="name_in_bangla_checked"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <p id="name_in_bangla_div"></p>
                                        <input type="hidden" class="form-control" id="name_in_bangla" name="name_in_bangla" readonly>
                                    </div>
                                    <div class="col-lg-4" id="name_in_bangla_change_div" style="display: none">
                                        <input type="text" class="form-control"  id="name_in_bangla_change" name="name_in_bangla_change" placeholder="Name in Bangla">
                                        <span id="name_in_bangla_error" class="text-danger"></span>
                                    </div>
                                </div>
                                <!-- Name in Bangla End -->
                                <!-- Father Name in Bangla Start -->
                                <div class="form-group row">
                                    <label for="name" class="col-lg-2 text-left">Father Name in Bangla:</label>
                                    <div class="col-lg-1" >
                                        <input type="checkbox" name="father_name_in_bangla_checked"  id="father_name_in_bangla_checked"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <p id="father_name_in_bangla_div"></p>
                                        <input type="hidden" class="form-control" id="father_name_in_bangla" name="father_name_in_bangla" readonly>
                                    </div>
                                    <div class="col-lg-4" id="father_name_in_bangla_change_div" style="display: none">
                                        <input type="text" class="form-control"  id="father_name_in_bangla_change" name="father_name_in_bangla_change" placeholder="Father Name in Bangla">
                                        <span id="father_name_in_bangla_error" class="text-danger"></span>
                                    </div>
                                </div>
                                <!-- Father Name in Bangla End -->
                                <!-- Mother Name in Bangla Start -->
                                <div class="form-group row">
                                    <label for="name" class="col-lg-2 text-left">Mother Name in Bangla:</label>
                                    <div class="col-lg-1" >
                                        <input type="checkbox" name="mother_name_in_bangla_checked"  id="mother_name_in_bangla_checked"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <p id="mother_name_in_bangla_div"></p>
                                        <input type="hidden" class="form-control" id="mother_name_in_bangla" name="mother_name_in_bangla" readonly>
                                    </div>
                                    <div class="col-lg-4" id="mother_name_in_bangla_change_div" style="display: none">
                                        <input type="text" class="form-control"  id="mother_name_in_bangla_change" name="mother_name_in_bangla_change" placeholder="Mother Name in Bangla">
                                        <span id="mother_name_in_bangla_error" class="text-danger"></span>
                                    </div>
                                </div>
                                <!-- Mother Name in Bangla End -->
                                <!-- Present Address Start -->
                                <input type="hidden" class="form-control" name="village_ward" id="village_ward">
                                <input type="hidden" class="form-control" name="district" id="district">
                                <input type="hidden" class="form-control" name="district_id" id="district_id">
                                <input type="hidden" class="form-control" name="police_station" id="police_station">
                                <input type="hidden" class="form-control" name="thana_id" id="thana_id">
                                <input type="hidden" class="form-control" name="post_code" id="post_code">
                                <div class="form-group row">
                                    <label for="name" class="col-lg-2 text-left">Present Address:</label>
                                    <div class="col-lg-1" >
                                        <input type="checkbox" name="present_address_checked"  id="present_address_checked"/>
                                    </div>
                                    <div class="col-lg-4">
                                        <p id="present_addresss_div"></p>
                                    </div>
                                    <div class="col-lg-4" id="present_address_change_div" style="display: none">
                                        <div class="form-group has-feedback d-flex">
                                            <label  class="col-lg-4 text-left">Post Code: </label>
                                            <div class="col-lg-8">
                                                {!! Form::text('post_code_change', '' , ['placeholder' => "Post Code",
                                                    'class' => 'form-control input-md','id'=>'post_code_change']) !!}
                                            </div>
                                        </div>

                                        <div class="form-group has-feedback d-flex">
                                            <label  class="col-lg-4 text-left">District: </label>
                                            <div class="col-lg-8">
                                                {!! Form::select('district_id_change', [], '', [ 'class'=>'form-control',
                                                    'placeholder' => 'Select One', 'id'=>"district_id_change" ]) !!}
                                            </div>
                                            <input type="hidden" class="form-control" id="district_change" name="district_change" readonly>
                                        </div>

                                        <div class="form-group has-feedback d-flex">
                                            <label  class="col-lg-4 text-left">Thana: </label>
                                            <div class="col-lg-8">
                                                {!! Form::select('thana_id_change', [], '', ['class'=>'form-control',
                                                    'placeholder' => 'Select One', 'id'=>"thana_id_change"]) !!}
                                            </div>
                                            <input type="hidden" class="form-control" id="police_station_change" name="police_station_change" readonly>
                                        </div>

                                        <div class="form-group has-feedback d-flex">
                                            <label  class="col-lg-4 text-left">Village Ward: </label>
                                            <div class="col-lg-8">
                                                {!! Form::text('village_ward_change', '' , ['placeholder' => "Village Ward",
                                                    'class' => 'form-control input-md','id'=>'village_ward_change']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Present Address End -->

                            </fieldset>
                        </div>
                    </div>


                </div>

                <div class="row toggle_data_talbe" style="display:none;">
                    <div class="row">
                        <div class="col-sm-12 text-left" style="padding-left:20px;">
                            <p>বি: দ্র: পরিবর্তনযোগ্য তথ্যগুলি থেকে যেসকল তথ্য পরিবর্তন করতে চান সে সকল তথ্যের উপরে টিক চিহ্ন দিন এবং পরিবর্তন করুন । </p>
                        </div>
                    </div>
                    @if ( in_array($user_type, $active_menu_for) )
                        <div class="row">
                            <div class="col-sm-6">
                                <a class="btn btn-info btn-sm" style="margin-left:10px" href="{{ url($prefix.'/process/list/'. Encryption::encodeId(1)) }}">
                                    বন্ধ করুন
                                </a>
                            </div>
                            <div class="col-sm-6 text-right" style="padding-right:35px">
                                <button class="btn btn-danger btn-sm" id="beforPosition"> পূর্বাবস্থায় </button>
{{--                                <button class="btn btn-primary btn-sm" name="actionBtn" id="draft" value="draft">সংরক্ষণ এবং খসড়া</button>--}}
                                <button class="btn btn-success btn-sm" name="actionBtn" id="save" onclick="checkInputData()" value="submit">সাবমিট</button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

{!! Form::close() !!}


<script>
    $(document).ready(function () {
        fetchDropdownlist( '/getDropDownData');

    });
    function fetchDropdownlist(base_url){
        let process_type_id = $('#id').val();
        let pilgrim_type = 'Government';
        $.ajax({
            url: base_url,
            type: 'ajax',
            method: 'POST',
            data:
                {
                    _token: $('input[name="_token"]').val(),
                    'process_type_id': process_type_id,
                    'pilgrim_type':pilgrim_type
                },
            success: function(response) {
                if (typeof response.data != "undefined"){
                    for (let i =0 ; i < response.data.length; i++){
                        $('#listing_id').append(
                            $("<option></option>").val(response.data[i]['id']).html(response.data[i]['name'])
                        );
                    }
                }
            },
            error: function(data) {
                // console.log((data.responseJSON.errors));
            }
        })
    }


$('input[type=radio][name=process_type]').change(function() {
    if (this.value == 'serial_no') {
        $("#request_data").attr("placeholder", "সিরিয়াল নাম্বার প্রদান করুন");
    }
    else if (this.value == 'tracking_no') {
        $("#request_data").attr("placeholder", "ট্র্যাকিং নম্বর প্রদান করুন");
    }
    else if (this.value == 'voucher_no') {
        $("#request_data").attr("placeholder", " রেজিঃ ভাউচার নাম্বার প্রদান করুন");
    }
});


    function searchPilgrim()
        {
            let base_url = window.location.origin + '/process/action/getpilgrim-for-gender-change/' + $('#id').val();
            let request_data = $('#request_data').val();
            let process_type = $('input[name="process_type"]:checked').val();
            // let pilgrim_type = $('input[name="pilgrim_type"]:checked').val();
            var listing_id = $('select#listing_id option:selected').val();

            var ptype = process_type ? '' : 'Select process type ' ;
            var listingid = listing_id ? '' : ' Select Listing';
            var reqdata = request_data ? '' : ' Write Tracking/Serial/Voucher Number';
            let message  = ptype + reqdata + listingid ;
            var is_proceed = 1;
            var listed_arr = [];
            var sent_arr = request_data.split(',');

            if($('input[name="process_type"]:checked').val() == 'serial_no'){
                sent_arr = sent_arr.map(Number);
            }
            var received_arr = [];



            if(request_data){

                // document.getElementById("gender_change").reset();
                $('#check_gender_div').css('display','none')
                $("#gender_change").css('display','none')
                $.ajax({
                    cache:false,
                    url: base_url,
                    type: "POST",
                    dataType: 'json',
                    data: {'request_data': request_data,'process_type':process_type},
                    success: function(response) {
                        $('#name').val(response.data.data[0]['full_name_english']);
                        $('#tracking_no').val(response.data.data[0]['tracking_no']);
                        $('#gender').val(response.data.data[0]['gender']);
                        $('#mobile').val(response.data.data[0]['mobile']);
                        $('#ksa_mobile').val(response.data.data[0]['ksa_mobile_no']);
                        $('#serial_no').val(response.data.data[0]['serial_no']);
                        $('#father_name').val(response.data.data[0]['father_name']);
                        $('#dob').val(response.data.data[0]['birth_date']);
                        $('#is_govt').val(response.data.data[0]['is_govt']);
                        $('#pilgrim_type_id').val(response.data.data[0]['pilgrim_type_id']);
                        $('#present_address').val(response.data.data[0]['pass_village'] + response.data.data[0]['pass_thana'] + response.data.data[0]['pass_district']);
                        if (response.data.data == undefined) {
                        $('.load_pilgrim').html("<tr class='mt-3'><td class='pt-4 text-red' colspan='6'>Data not found!!</td></tr>");
                        }
                        else {
                            $('#name_div').html(response.data.data[0]['full_name_english']);
                            $('#tracking_no_div').html(response.data.data[0]['tracking_no']);
                            $('#gender_div').html(response.data.data[0]['gender']);
                            $('#mobile_div').html(response.data.data[0]['mobile']);
                            $('#serial_no_div').html(response.data.data[0]['serial_no']);
                            $('#father_name_div').html(response.data.data[0]['father_name']);
                            $('#present_address_div').html(response.data.data[0]['village_ward'] + ', ' + response.data.data[0]['police_station'] + ', ' + response.data.data[0]['district'] + ', ' + response.data.data[0]['post_code']);
                            $('#dob_div').html(response.data.data[0]['birth_date']);
                            $('#ksa_mobile_div').html(response.data.data[0]['ksa_mobile_no']);
                            // full_name_english start
                            $('#name_in_english_div').html(response.data.data[0]['full_name_english']);
                            $('#name_in_english').val(response.data.data[0]['full_name_english']);
                            // full_name_english end
                            // full_name_bangla start
                            $('#name_in_bangla_div').html(response.data.data[0]['full_name_bangla']);
                            $('#name_in_bangla').val(response.data.data[0]['full_name_bangla']);
                            // full_name_bangla end
                            // Father Name in Bangla start
                            $('#father_name_in_bangla_div').html(response.data.data[0]['father_name']);
                            $('#father_name_in_bangla').val(response.data.data[0]['father_name']);
                            // Father Name in Bangla end
                            // Mother Name in Bangla start
                            $('#mother_name_in_bangla_div').html(response.data.data[0]['mother_name']);
                            $('#mother_name_in_bangla').val(response.data.data[0]['mother_name']);
                            // Mother Name in Bangla end
                            // Present Address start
                            $('#village_ward').val(response.data.data[0]['village_ward']);
                            $('#district').val(response.data.data[0]['district']);
                            $('#district_id').val(response.data.data[0]['district_id']);
                            $('#police_station').val(response.data.data[0]['police_station']);
                            $('#thana_id').val(response.data.data[0]['thana_id']);
                            $('#post_code').val(response.data.data[0]['post_code']);

                            $('#present_addresss_div').html(response.data.data[0]['village_ward'] + ', ' + response.data.data[0]['police_station'] + ', ' + response.data.data[0]['district'] + ', ' + response.data.data[0]['post_code']);
                            // change start
                            $('#village_ward_change').val('');
                            $('#district_change').val('');
                            $('#district_id_change').val('');
                            $('#police_station_change').val('');
                            $('#thana_id_change').val('');
                            $('#post_code_change').val('');
                            // change end
                            // Present Address end

                            if(response.data.data[0]['pilgrim_type_id'] == 6){
                                $('#is_govt_div').html(response.data.data[0]['is_govt'] + '(Guide)');
                            }else{
                                $('#is_govt_div').html(response.data.data[0]['is_govt']);
                            }
                            $('#gender_change').empty();
                            if(response.data.data[0]['payment_status']==12 && response.data.data[0]['reg_payment_status'] ==0 && response.data.data[0]['is_archived']==0 ){
                                if(response.data.data[0]['pid'] == undefined){
                                    $("#gender_change").val("off");
                                    $('#check_gender_div').css('display','block');
                                    var genderVal = response.data.data[0]['gender'];
                                    var selectVal = 'female';
                                    if (genderVal === 'male') {
                                        selectVal = 'male';
                                    }
                                    $('#gender_change').append(
                                        '<select class="form-control" id="gender_change_input" name="gender_change"> <option value="male"'+(selectVal==='male'?' selected':'')+' >Male</option> <option value="female"'+(selectVal==='female'?' selected':'')+'>Female</option> </select>'
                                    )

                                }else{
                                    $('#check_gender_div').css('display','none');
                                    $("#gender_change").css('display','none');
                                    $('#gender_change').empty();
                                }
                            }
                            if(response.data.data[0]['pid'] != undefined){

                                $('#ksa_mobile_label').css('display','block')
                                $('#ksa_mobile_checked').css('display','block')
                                $('#ksa_mobile_checked').css('display','block')

                                if(response.data.data[0]['ksa_mobile_no']!=null){
                                    // alert(response.data.data[0]['ksa_mobile_no']);
                                    $('#ksa_mobile_div').css('display','block')
                                }
                            }

                            // $('#lisence_number_div').html()

                                $("#search_list").css("display", "block");
                                $(".toggle_data_talbe").css("display", "block");
                                $(".search_field").css("display", "block");
                                let dataLen = response.data.data.length;
                                $('#total_pilgrim').html(dataLen);
                                $('.load_pilgrim').html('');

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
        $(".toggle_data_talbe").css("display", "none");
        $(".search_field").css("display", "block");
        $("#search_list").css("display", "block");
        $("#draft").css("display", "inline-block");
        $("#save").css("display", "inline-block");
    })
</script>

<script>
    $(document).ready(function() {
        // Attach a click event to the header checkbox
        $('#gender_checked').click(function() {
            // Get the checked status of the header checkbox
            if(this.checked){
                $("#gender_change").css('display','block')
            }else{
                $("#gender_change").css('display','none')
                $('#gender_change_input').val('');
            }
        });
        $('#mobile_checked').click(function() {
            // Get the checked status of the header checkbox
            if(this.checked){
                $("#mobile_change_div").css('display','block')
            }else{
                $("#mobile_change_div").css('display','none')
                $('#mobile_change').val('');
            }
        });
        $('#ksa_mobile_checked').click(function() {
            // Get the checked status of the header checkbox
            if(this.checked){
                $("#ksa_mobile_change_div").css('display','block')
            }else{
                $("#ksa_mobile_change_div").css('display','none')
                $('#ksa_mobile_change').val('');
            }
        });
        // full_name_english start
        $('#name_in_english_checked').click(function() {
            if(this.checked) {
                $("#name_in_english_change_div").css('display','block');
            } else {
                $("#name_in_english_change_div").css('display','none');
                $('#name_in_english_change').val('');
            }
        });
        // full_name_english end
        // full_name_bangla start
        $('#name_in_bangla_checked').click(function() {
            if(this.checked) {
                $("#name_in_bangla_change_div").css('display','block');
            } else {
                $("#name_in_bangla_change_div").css('display','none');
                $('#name_in_bangla_change').val('');
            }
        });
        // full_name_bangla end
        // father_name_bangla start
        $('#father_name_in_bangla_checked').click(function() {
            if(this.checked) {
                $("#father_name_in_bangla_change_div").css('display','block');
            } else {
                $("#father_name_in_bangla_change_div").css('display','none');
                $('#father_name_in_bangla_change').val('');
            }
        });
        // father_name_bangla end
        // mother_name_bangla start
        $('#mother_name_in_bangla_checked').click(function() {
            if(this.checked) {
                $("#mother_name_in_bangla_change_div").css('display','block');
            } else {
                $("#mother_name_in_bangla_change_div").css('display','none');
                $('#mother_name_in_bangla_change').val('');
            }
        });
        // mother_name_bangla end
        // Present Address start
        $('#present_address_checked').click(function() {
            if(this.checked) {
                $("#present_address_change_div").css('display','block');
                getDistricts();
            } else {
                $("#present_address_change_div").css('display','none');
                $('#village_ward_change').val('');
                $('#district_change').val('');
                $('#district_id_change').val('');
                $('#police_station_change').val('');
                $('#thana_id_change').val('');
                $('#post_code_change').val('');
            }
        });
        // Present Address end

    });

</script>
<script>
    $(document).ready(function() {
        $('#mobile_change').on('input', function() {
            var phone_number = $(this).val().replace(/\D/g, ''); // Remove all non-numeric characters
            phone_number = phone_number.replace(/^(\+)?(88)?/, ''); // Remove leading "+88"
            if (phone_number.length == 11 && phone_number.substring(0, 2) == '01' && phone_number.substring(2, 3).match(/[3-9]/)) {
                // Valid Bangladesh mobile number
                $('#phone-error').text('');
            } else {
                // Invalid Bangladesh mobile number
                $('#phone-error').text('Please enter a valid Bangladesh mobile number');
            }
        });
        const validateBanglaText = (input) => {
            const banglaPattern = /[\u0980-\u09FF\s\:\-\.\u0964]+/g;
            const filteredInput = input.match(banglaPattern) ? input.match(banglaPattern).join('') : '';
            return input === filteredInput;
        }

        const validateEnglishText = (input) => {
            const englishPattern = /^[\u0000-\u007F\s.,!?"'()-]+$/;
            const filteredInput = input.match(englishPattern) ? input.match(englishPattern).join('') : '';
            return input === filteredInput;
        }
        // full_name_english start
        $('#name_in_english_change').on('input', function() {
            // check english word or not
            const validEnglishWords = validateEnglishText($(this).val().trim());
            if (validEnglishWords) {
                // Valid english words
                $('#name_in_english_error').text('');
            } else {
                // Invalid english words
                $('#name_in_english_error').text('Please enter a valid English words.');
            }
        });
        // full_name_english end
        // full_name_bangla start
        $('#name_in_bangla_change').on('input', function() {
            // check bangla word or not
            const validBanglaWords = validateBanglaText($(this).val().trim());
            if (validBanglaWords) {
                // Valid bangla words
                $('#name_in_bangla_error').text('');
            } else {
                // Invalid bangla words
                $('#name_in_bangla_error').text('Please enter a valid Bangla words.');
            }
        });
        // full_name_bangla end
        // father_name_bangla start
        $('#father_name_in_bangla_change').on('input', function() {
            // check english word or not
            const validBanglaWords = validateBanglaText($(this).val().trim());
            if (validBanglaWords) {
                // Valid bangla words
                $('#father_name_in_bangla_error').text('');
            } else {
                // Invalid bangla words
                $('#father_name_in_bangla_error').text('Please enter a valid Bangla words.');
            }
        });
        // father_name_bangla end
        // mother_name_bangla start
        $('#mother_name_in_bangla_change').on('input', function() {
            // check bangla word or not
            const validBanglaWords = validateBanglaText($(this).val().trim());
            if (validBanglaWords) {
                // Valid bangla words
                $('#mother_name_in_bangla_error').text('');
            } else {
                // Invalid bangla words
                $('#mother_name_in_bangla_error').text('Please enter a valid Bangla words.');
            }
        });
        // mother_name_bangla end
    });
</script>

<script>
    function checkInputData(){
        function minOneChecked() {
            if (
                $('#ksa_mobile_checked').prop("checked") != true &&
                $('#mobile_checked').prop("checked") != true &&
                $('#gender_checked').prop("checked") != true &&
                $('#name_in_english_checked').prop("checked") != true &&
                $('#name_in_bangla_checked').prop("checked") != true &&
                $('#father_name_in_bangla_checked').prop("checked") != true &&
                $('#mother_name_in_bangla_checked').prop("checked") != true &&
                $('#present_address_checked').prop("checked") != true
            ) {
                return true;
            } else {
                return false;
            }
        }
        if(minOneChecked()) {
                event.preventDefault();
                alert("Sorry ! Can not Submit !")
                return false;
        }
        if($('#gender_checked').prop("checked") == true){
            if($('#gender').val() == $('#gender_change_input').val()){
                event.preventDefault();
                alert("Can not Submit Same Gender Value !")
                return false;
            } else if ($('#gender_change_input').val() === "") {
                event.preventDefault();
                alert("Please Enter English Name !")
                return false;
            }
        }
        if($('#mobile_checked').prop("checked") == true){
            if($('#mobile').val() == $('#mobile_change').val()){
                event.preventDefault();
                alert("Can not Submit Same Mobile No !")
                return false;
            } else if ($('#mobile_change').val() === "") {
                event.preventDefault();
                alert("Please Enter English Name !")
                return false;
            }
        }

        if($('#ksa_mobile_checked').prop("checked") == true){
            if($('#ksa_mobile').val() == $('#ksa_mobile_change').val()){
                event.preventDefault();
                alert("Can not Submit Same KSA Mobile No !")
                return false;
            } else if ($('#ksa_mobile_change').val() === "") {
                event.preventDefault();
                alert("Please Enter English Name !")
                return false;
            }
        }
        // full_name_english start
        if($('#name_in_english_checked').prop("checked") == true) {
            if ($('#name_in_english').val() == $('#name_in_english_change').val()) {
                event.preventDefault();
                alert("Can not Submit Same English Name !")
                return false;
            } else if ($('#name_in_english_change').val() === "") {
                event.preventDefault();
                alert("Please Enter English Name !")
                return false;
            } else if ($('#name_in_english_error').text() !== '') {
                    event.preventDefault();
                    alert($('#name_in_english_error').text());
                    return false;
                }
        }
        // full_name_english end
        // full_name_bangla start
        if($('#name_in_bangla_checked').prop("checked") == true) {
            if ($('#name_in_bangla').val() == $('#name_in_bangla_change').val()) {
                event.preventDefault();
                alert("Can not Submit Same Bangla Name !")
                return false;
            } else if ($('#name_in_bangla_change').val() === "") {
                event.preventDefault();
                alert("Please Enter Bangla Name !")
                return false;
            } else if ($('#name_in_bangla_error').text() !== '') {
                    event.preventDefault();
                    alert($('#name_in_bangla_error').text());
                    return false;
                }
        }
        // full_name_bangla end
        // father_name_bangla start
        if($('#father_name_in_bangla_checked').prop("checked") == true) {
            if ($('#father_name_in_bangla').val() == $('#father_name_in_bangla_change').val()) {
                event.preventDefault();
                alert("Can not Submit Same Bangla Name !")
                return false;
            } else if ($('#father_name_in_bangla_change').val() === "") {
                event.preventDefault();
                alert("Please Enter Bangla Name !")
                return false;
            } else if ($('#father_name_in_bangla_error').text() !== '') {
                    event.preventDefault();
                    alert($('#father_name_in_bangla_error').text());
                    return false;
                }
        }
        // father_name_bangla end
        // mother_name_bangla start
        if($('#mother_name_in_bangla_checked').prop("checked") == true) {
            if ($('#mother_name_in_bangla').val() == $('#mother_name_in_bangla_change').val()) {
                event.preventDefault();
                alert("Can not Submit Same Bangla Name !")
                return false;
            } else if ($('#mother_name_in_bangla_change').val() === "") {
                event.preventDefault();
                alert("Please Enter Bangla Name !")
                return false;
            } else if ($('#mother_name_in_bangla_error').text() !== '') {
                    event.preventDefault();
                    alert($('#mother_name_in_bangla_error').text());
                    return false;
                }
        }
        // mother_name_bangla end
        // present_address start
        function allValueEmpty() {
            if (
                $('#village_ward_change').val() === '' &&
                $('#district_change').val() === '' &&
                $('#district_id_change').val() === '' &&
                $('#police_station_change').val() === '' &&
                $('#thana_id_change').val() === '' &&
                $('#post_code_change').val() === ''
            ) {
                return true;
            } else {
                return false;
            }
        }
        if($('#present_address_checked').prop("checked") == true) {
            if (allValueEmpty()) {
                event.preventDefault();
                alert("Please enter present address !")
                return false;
            }
        }
        // present_address end




    }

</script>
<script>
    function getDistricts() {
        const self = $('#district_id_change');
        self.after('<span class="loading_data">Loading...</span>');
        $.ajax({
            type: "GET",
            url: "/get-all-district-list",
            success: function (response) {
                var option = '<option value="">Select One</option>';
                if (response.responseCode == 1) {
                    $.each(response.data, function (id, value) {
                        option += '<option value="' + value.area_id + '">' + value.area_nm + '</option>';
                    });
                }
                $("#district_id_change").html(option);
                $(self).next().hide();
            }
        });
    }
    function getThanaByDistrictId() {
        const districtId = $('#district_id_change').val();
        const self = $('#police_station_change');
        self.after('<span class="loading_data">Loading...</span>');
        $.ajax({
            type: "GET",
            url: "/get-thana-by-district-id",
            data: {
                districtId: districtId
            },
            success: function (response) {
                var option = '<option value="">Select One</option>';
                if (response.responseCode == 1) {
                    $.each(response.data, function (id, value) {
                        option += '<option value="' + id + '">' + value + '</option>';
                    });
                }
                $("#thana_id_change").html(option);
                $(self).next().hide();
            }
        });
    }

    $('#district_id_change').on('change', function() {
        getThanaByDistrictId();
        $('#police_station_change').val('');
        $('#police_station_change').val('');
        $('#thana_id_change').val('');
        const selectedText = $(this).find('option:selected').text();
        $('#district_change').val(selectedText);
    });
    $('#thana_id_change').on('change', function() {
        const selectedText = $(this).find('option:selected').text();
        $('#police_station_change').val(selectedText)
    });

</script>
