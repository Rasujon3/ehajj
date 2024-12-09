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
        <h3> হজ পালন বাতিল ( {{$session_id['caption']}} )</h3>
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
                                            <div class="col mt-1" style="padding-left:0px;text-align:left">
                                                {!! Form::radio('process_type', 'selected', 'tracking_no', false, ['id'=>'ehajj_src_tracking_no']) !!}
                                                <label for="ehajj_src_tracking_no">Tracking No.</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

                <div id="no-record-found" style="display:none;">
                    কোনো তথ্য খুঁজে পাওয়া যায়নি
                </div>

                <div class="dash-list-table py-4 toggle_data_talbe" style="display:none;" >

                    <div class="card">
                        <div class="card-header">
                            হজযাত্রীর তথ্য
                        </div>
                        <div class="card-body">
                            <fieldset>

                                <div class="row">
                                    <label for="name" class="col-lg-2 text-left">Agency Name</label>
                                    <div class="col-lg-8">
                                        <p id="agency_div"></p>
                                        <input type="hidden" class="form-control" placeholder="Enter Agency no" name="agency" id="agency" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="name" class="col-lg-2 text-left">License No:</label>
                                    <div class="col-lg-8">
                                        <p id="license_div"></p>
                                        <input type="hidden" class="form-control" placeholder="Enter license no" name="license_no" id="license" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="name" class="col-lg-2 text-left"> Applicant Name:</label>
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
                                    <label for="name" class="col-lg-2 text-left">Mother Name:</label>
                                    <div class="col-lg-8" >
                                        <p id="mother_name_div"></p>
                                        <input type="hidden" class="form-control" placeholder="Enter Father name" name="mother_name" id="mother_name" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="name" class="col-lg-2 text-left">Tracking No:</label>
                                    <div class="col-lg-8">
                                        <p id="tracking_no_div"></p>
                                        <input type="hidden" class="form-control" placeholder="Enter" name="tracking_no" id="tracking_no" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="name" class="col-lg-2 text-left">NID:</label>
                                    <div class="col-lg-8">
                                        <p id="nid_div"></p>
                                        <input type="hidden" class="form-control"  name="nid" id="nid" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="name" class="col-lg-3 text-left">Reason :</label>
                                    <div class="col-lg-4">
                                        <select class="form-control" name="reason_for_not_perform_hajj" required id="reason_for_not_perform_hajj">
                                            <option value="">Select One</option>
                                            <option value="Death">Death</option>
                                            <option value="Serious ill">Serious Illness</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="form-group row">
                                    <label for="name" class="col-lg-3 text-left">Remarks :</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" required id="remarks" name="remarks"/>
                                    </div>

                                </div>
                            </fieldset>
                        </div>
                    </div>


                </div>

                <div class="row toggle_data_talbe" style="display:none;">
                    <div class="row">
                        <div class="col-sm-12 text-left" style="padding-left:20px;"></div>
                    </div>
                    @if ( in_array($user_type,['4x404','2x202','3x301','3x302']) )
                        <div class="row">
                            <div class="col-sm-6">
                                <a class="btn btn-info btn-sm" style="margin-left:10px" href="{{ url($prefix.'/process/list/'. Encryption::encodeId($process_type_id)) }}">
                                    বন্ধ করুন
                                </a>
                            </div>
                            <div class="col-sm-6 text-right" style="padding-right:35px">
                                <button class="btn btn-danger btn-sm" id="beforPosition"> পূর্বাবস্থায় </button>
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
        console.log(base_url);
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
            let base_url = window.location.origin + '/process/action/getpilgrim-for-hajj-canceled/' + $('#id').val();
            let request_data = $('#request_data').val();
            let process_type = $('input[name="process_type"]:checked').val();
            var listing_id = $('select#listing_id option:selected').val();

            $("#no-record-found").css("display", "none");

            var ptype = process_type ? '' : 'Select process type ' ;
            var listingid = listing_id ? '' : ' Select Listing';
            var reqdata = request_data ? '' : ' Write Tracking/Serial/Voucher Number';
            let message  = ptype + reqdata + listingid ;
            if(request_data){
                $.ajax({
                    cache:false,
                    url: base_url,
                    type: "POST",
                    dataType: 'json',
                    data: {'request_data': request_data,'process_type':process_type},
                    success: function(response) {

                        if(response.data.status == 400){
                            $("#no-record-found").css("display", "block");
                            return false;
                        }
                        $('#name').val(response.data.data['full_name_english']);
                        $('#tracking_no').val(response.data.data['tracking_no']);
                        $('#agency').val(response.data.data['agency_name']);
                        $('#license').val(response.data.data['agency_license']);
                        $('#nid').val(response.data.data['national_id']);
                        $('#father_name').val(response.data.data['father_name_english']);
                        $('#mother_name').val(response.data.data['mother_name_english']);
                        if (response.data.data == undefined) {
                            $('.load_pilgrim').html("<tr class='mt-3'><td class='pt-4 text-red' colspan='6'>Data not found!!</td></tr>");
                        }
                        else {
                            $('#name_div').html(response.data.data['full_name_english']);
                            $('#tracking_no_div').html(response.data.data['tracking_no']);
                            $('#agency_div').html(response.data.data['agency_name']);
                            $('#license_div').html(response.data.data['agency_license']);
                            $('#nid_div').html(response.data.data['national_id']);
                            $('#father_name_div').html(response.data.data['father_name_english']);
                            $('#mother_name_div').html(response.data.data['mother_name_english']);
                            $("#search_list").css("display", "block");
                            $(".toggle_data_talbe").css("display", "block");
                            $(".search_field").css("display", "block");
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
    function checkInputData(){
        // event.preventDefault();

        if($('#reason_for_not_perform_hajj') == null){

                event.preventDefault();
                alert("Can not Submit Same KSA Mobile No !")
                return false;

        }




    }
</script>
