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
        <h3>বেসরকারি হজযাত্রী লিষ্টিং ( {{$session_id['caption']}} )</h3>
    </div>
    <div class="dash-section-content pt-3">
        <div class="dash-sec-head">
            <div class="container">
                <div class="card">
                    <div class="card-body" id="search_list" style="display: block">
                        <div class="row mt-2 mb-2">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="search-title">
                                        <h3>সার্চ লিস্ট</h3>
                                    </div>
                                    <div class="ehajj-search-list-form">
                                        @php $process_typeid =  \App\Libraries\Encryption::encodeId($process_type_id)
                                        @endphp
                                        <input type="hidden" id="id" value="{!!$process_typeid!!}" />

                                        <div class="search-list-group input-group-text form-control ">

                                            <div class="col mt-1" style="padding-left:0px;text-align:left">
                                                {!! Form::radio('process_type', 'serial_no', false, ['id'=>'ehajj_src_serial_no'])!!}
                                                <label for="ehajj_src_serial_no">Serial No.</label>
                                            </div>
                                            <div class="col mt-1" style="padding-left:0px;text-align:left">
                                                {!! Form::radio('process_type', 'tracking_no', false, ['id'=>'ehajj_src_tracking_no']) !!}
                                                <label for="ehajj_src_tracking_no">Tracking No.</label>
                                            </div>
                                            <div class="col mt-1" style="padding-left:0px;text-align:left">
                                                {!! Form::radio('process_type', 'voucher_no', false, ['id'=>'ehajj_src_voucher_no']) !!}
                                                <label for="ehajj_src_voucher_no">Voucher No.</label>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="search-title">
                                        <h3>লিষ্টিং</h3>
                                    </div>
                                    <div class="ehajj-search-list-form">
                                        <select class="form-control" name="listing_id" id="listing_id">
                                            <option selected disabled value=""> তালিকা নির্বাচন করুন </option>
                                            {{--@foreach($pilgirm_dropdown_list['data'] as $key => $item)
                                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                                            @endforeach--}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 mb-2">
                                <form class="form-group">
                                    <div class="ehajj-search-input">
                                        <input class="form-control mr-sm-2 search_field" type="search"
                                               name="request_data" id="request_data"  placeholder="ট্র্যাকিং নম্বর/ সিরিয়াল নাম্বার/ রেজিঃ ভাউচার নাম্বার প্রদান করুন" autocomplete="off">
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
                    <div  class="btn btn-accent mb-2">
                        <span>সর্বমোট হজযাত্রী</span> <span class="badge badge-light" id="total_pilgrim">0</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table dash-table">
                            <thead class="table-info">
                            <tr>
                                <th scope="col">ক্রমিক নং</th>
                                <th scope="col">নাম</th>
                                <th scope="col">ট্র্যাকিং নম্বর</th>
                                <th scope="col">সিরিয়াল নং</th>
                                <th scope="col">মোবাইল নং</th>
                            </tr>
                            </thead>
                            <tbody class="load_pilgrim">

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row toggle_data_talbe" style="display:none;">
                    <div class="row">
                        <div class="col-sm-12 text-left" style="padding-left:20px;">
                            <p>বি: দ্র: কোনো ট্র্যাকিং নাম্বার বাদ পরলে উক্ত নাম্বারটি উপরে আপলোড করুন।</p>
                        </div>
                    </div>
                    @if (in_array($user_type, explode(',', $process_info['active_menu_for'])) )
                        <div class="row">
                            <div class="col-sm-6">
                                <a class="btn btn-info btn-sm" style="margin-left:10px" href="{{ url($prefix.'/process/list/'. Encryption::encodeId(1)) }}">
                                    বন্ধ করুন
                                </a>
                            </div>
                            <div class="col-sm-6 text-right" style="padding-right:35px">
                                <button class="btn btn-danger btn-sm" id="beforPosition"> পূর্বাবস্থায় </button>
                                <button class="btn btn-primary btn-sm" name="actionBtn" id="draft" value="draft">সংরক্ষণ এবং খসড়া</button>
                                <button class="btn btn-success btn-sm" name="actionBtn" id="save" value="submit">সাবমিট</button>
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
        console.log(base_url);
        let process_type_id = $('#id').val();
        let pilgrim_type = 'Private';
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
                        console.log(response.data[i]['name']);
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
        let base_url = window.location.origin + '/process/action/getpilgrim/' + $('#id').val();
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


        if(process_type !=undefined && listing_id  && request_data){

            $.ajax({
                cache:false,
                url: base_url,
                type: "POST",
                dataType: 'json',
                data: {'request_data': request_data,'process_type':process_type},
                success: function(response) {

                    if (response.data.data == undefined) {
                        $('.load_pilgrim').html("<tr class='mt-3'><td class='pt-4 text-red' colspan='6'>Data not found!!</td></tr>");
                    }
                    else {
                        $("#search_list").css("display", "block");
                        $(".toggle_data_talbe").css("display", "block");
                        $(".search_field").css("display", "block");
                        let dataLen = response.data.data.length;
                        $('#total_pilgrim').html(dataLen);
                        $('.load_pilgrim').html('');

                        for(var i =0 ; i<dataLen; i++){
                            if($('input[name="process_type"]:checked').val() == 'serial_no'){
                                received_arr.push(response.data.data[i]['serial_no']);
                            }
                            else if($('input[name="process_type"]:checked').val() == 'tracking_no'){
                                received_arr.push(response.data.data[i]['tracking_no']);

                            }
                            else if($('input[name="process_type"]:checked').val() == 'voucher_no'){
                                received_arr.push(response.data.data[i]['voucher_no']);
                            }

                            if(response.data.data[i]['pilgrim_listing_id']!=0){

                                $("#draft").css("display", "none");
                                $("#save").css("display", "none");
                                is_proceed=0;
                                listed_arr.push(response.data.data[i]['serial_no']);
                            }

                            $('.load_pilgrim').append(

                                '<tr class="table-row-space"><td colspan="6">&nbsp;</td></tr><tr id="row' + i + '"><td scope="row">'+  parseInt((i+1),10) + '</td>'+
                                '<td>'+
                                '<input type="hidden" name="pilgrim_id[]"  value="'+response.data.data[i]['id']+'" />'+
                                '<input type="hidden" class="form-control" id="full_name_english'+i+'" name="full_name_english[]" value="'+response.data.data[i]['full_name_english']+'" readonly/>'+response.data.data[i]['full_name_english']+'</td>'+
                                '<td>'+
                                '<input type="hidden" class="form-control" id="tracking_no'+i+'" name="tracking_no[]" value="'+response.data.data[i]
                                    ['tracking_no']+'" readonly/>'+response.data.data[i]
                                    ['tracking_no']+'</td>'+
                                '<td>'+
                                '<input type="hidden" class="form-control" id="serial_no'+i+'" name="serial_no[]" value="'+response.data.data[i]['serial_no']+'" readonly/>'+response.data.data[i]['serial_no']+'</td>'+
                                '<td>'+
                                '<input type="hidden" class="form-control" id="mobile'+i+'" name="mobile[]" value="'+response.data.data[i]['mobile']+'" readonly/>'+response.data.data[i]['mobile']+'</td>'+
                                '</tr>');
                        }

                        if( is_proceed==0){

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



                        if($('input[name="process_type"]:checked').val()!=''){
                            var diff = $(sent_arr).not(received_arr).get();
                            if(jQuery.isEmptyObject(diff)==false){

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

                            if(jQuery.isEmptyObject(received_arr)==true){
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


    // remove pilgrim
    // $(document).on('click', '.btn_remove', function() {
    //     let total_pilgrim =  $('#total_pilgrim').html();
    //     let request_data =  $('#request_data').val();
    //     // alert(request_data);
    //         Swal.fire({
    //         title: 'Are you sure?',
    //         text: "You want to Delete it",
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Yes, delete it!'
    //         }).then((result) => {

    //     });

    // });
</script>
