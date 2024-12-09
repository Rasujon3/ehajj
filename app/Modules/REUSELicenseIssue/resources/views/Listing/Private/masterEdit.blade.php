
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
            <div class="dropdown-listing dropdown">

            </div>
            <div class="container">
                <div class="card">
                    <div class="card-body" id="search_list" style="display: block">
                        <div class="row mt-4">
                            <div class="col-sm-6" style="padding:0px 15px;">
                                <div class="form-group">
                                    <label for="radio" class="form-label label_modify">সার্চ লিস্ট</label>
                                    <div class="row" style="margin:0px;">
                                        @php
                                        $process_typeid =  \App\Libraries\Encryption::encodeId($process_type_id);
                                        $app_id =  \App\Libraries\Encryption::encodeId($data->id);
                                        @endphp
                                        <input type="hidden" name="app_id" value="{{$app_id}}" />

                                        <input type="hidden" id="id" value="{!!$process_typeid!!}" />
                                        <div class="input-group-text form-control" style="padding:0px 15px;">
                                            <div class="col mt-1" style="padding-left:0px;">
                                                <label class="mb-0" style="float:left">{!! Form::radio('process_type', 'serial_no',($data->process_type === 'serial_no')? 'checked':'')!!} Serial No.
                                                </label>
                                            </div>
                                            <div class="col-sm-4 mt-1" style="padding-left:0px;">
                                                <label class="mb-0">{!! Form::radio('process_type', 'tracking_no',($data->process_type === 'tracking_no')? 'checked':'') !!} Tracking No.
                                                </label>
                                            </div>
                                            <div class="col-sm-4 mt-1" style="padding-left:0px;">
                                                <label class="mb-0" style="float:right;">{!! Form::radio('process_type', 'voucher_no',($data->process_type === 'voucher_no')? 'checked':'') !!} Voucher No.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6" style="padding:0px 15px;">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">
                                    লিষ্টিং</label>
                                    <select class="form-control" name="listing_id" id="listing_id">
{{--                                        <option selected disabled value=""> তালিকা নির্বাচন করুন </option>--}}
{{--                                        @foreach($pilgirm_dropdown_list['data'] as $key => $item)--}}
{{--                                            <option value="{{$item['id']}}" <?php if($item['id'] == $data->listing_id) { echo "selected";}  ?> >{{$item['name']}}</option>--}}
{{--                                        @endforeach--}}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @php
                            $searchitem = '';
                                $json_data = json_decode( $data->processlist->json_object);
                                $numItems = count($json_data);
                                $i = 0;
                                $delimeter = ',';
                            @endphp
                            @foreach($json_data as $key=>$value)
                                @if(++$i === $numItems)
                                    @php $delimeter = ''; @endphp
                                @else @php $delimeter = ','; @endphp
                                @endif
                                @php $searchitem.= $value.$delimeter;  @endphp

                            @endforeach
                            <div class="col-sm-11 mb-2">
                                <form class="form-group">
                                <input class="form-control mr-sm-2 search_field" type="search"
                                name="request_data" id="request_data"  value="{{$searchitem}}" autocomplete="off">

                                </form>
                            </div>
                            <div class="col-sm-1">
                                <button type="button" style="float:right;" class="btn btn-secondary" onclick='searchPilgrim()'>
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
                <div class="dash-list-table my-4 toggle_data_talbe">
                    <div class="dash-list-table my-4 toggle_data_talbe">
                        <span  class="btn btn-primary btn-sm mb-2">
                            সর্বমোট হজযাত্রী <span class="badge badge-light" id="total_pilgrim"> {{count(json_decode($data->json_object))}}</span>
                        </span>
                        <div class="card card-magenta border border-magenta">
                            <div class="table-responsive">
                                <table class="table table-bordered">
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

                                        @php $json_data = json_decode($data->json_object); $i=0; @endphp

                                        @foreach($json_data as $key => $pldata)
                                            <tr>
                                                <td>{{$key + 1 }}</td>
                                                <td>{{$pldata->full_name_english}}</td>
                                                <td>{{$pldata->tracking_no}}</td>
                                                <td>{{$pldata->serial_no}}</td>
                                                <td>{{$pldata->mobile}}</td>
                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


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
                            <div class="col-sm-6 text-right">
                                <button class="btn btn-danger btn-sm" id="beforPosition"> পূর্বাবস্থায় </button>
                                    <button class="btn btn-primary btn-sm" name="actionBtn" value="draft" >সংরক্ষণ এবং খসড়া</button>
                                    <button class="btn btn-success btn-sm" name="actionBtn" value="submit" >সাবমিট</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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
                        let option = '<option selected disabled value=""> তালিকা নির্বাচন করুন </option>';
                        let selectedOption = response.data[i]['name'];
                        $('#listing_id').append(
                            option += '<option value="' + selectedOption + '" selected>' + response.data[i]['name'] + '</option>'
                        );
                    }
                }
            },
            error: function(data) {
                // console.log((data.responseJSON.errors));
            }
        })
    }

    function searchPilgrim()

    {
        let base_url = window.location.origin + '/process/action/getpilgrim/' + $('#id').val();
        let request_data = $('#request_data').val();
        let process_type = $('input[name="process_type"]:checked').val();
        // let pilgrim_type = $('input[name="pilgrim_type"]:checked').val();
        var listing_id = $('select#listing_id option:selected').val();

        let pilgrim_type = $('#pilgrimtype').val();
        if(pilgrim_type == 1){
            var pilgirmtype = "Government";
        }else {
            var pilgirmtype = "Private"
        }

        var ptype = process_type ? '' : 'Select process type ' ;
        var listingid = listing_id ? '' : ' Select Listing';
        var reqdata = request_data ? '' : ' Write Tracking/Serial/Voucher Number';
        let message  = ptype + reqdata + listingid ;


        if(process_type !=undefined && listing_id  && request_data){

            $.ajax({
                cache:false,
                url: base_url,
                type: "POST",
                dataType: 'json',
                data: {'request_data': request_data,'process_type':process_type},
                success: function(response) {
                    console.log(response);
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
                        for(var i = 0 ; i<dataLen; i++){
                        $('.load_pilgrim').append('' +

                        '<tr id="row' + i + '"><td scope="row">'+  parseInt((i+1),10) + '</td>'+
                            '<td>'+
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
                    }
                },
                error: function(data) {
                    // console.log((data.responseJSON.errors));
                }
            })
        }
        else{
            Swal.fire({
            // title: message,
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
    })
     // remove pilgrim
    $(document).on('click', '.btn_remove', function() {
        let total_pilgrim =  $('#total_pilgrim').html();
        let request_data =  $('#request_data').val();
        // alert(request_data);
            Swal.fire({
            title: 'Are you sure?',
            text: "You want to Delete it",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {

        });

    });
</script>
