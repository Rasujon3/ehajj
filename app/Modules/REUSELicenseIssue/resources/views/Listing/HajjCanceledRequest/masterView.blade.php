<?php
$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);

$prefix = '';
if ($type[0] == 5) {
    //$prefix = 'client';
    $prefix = 'client';
}

?>

{!! Form::open([
     'url' => url('process/action/store/'.\App\Libraries\Encryption::encodeId($process_type_id)),
    'method' => 'post',
    'class' => 'form-horizontal',
    'id' => 'application_form'
])
!!}
@php
    $json_data = json_decode($data->json_object);

@endphp
<div class="dash-content-inner">
    <div class="src-hajj-tracking-number">
        <div class="dash-list-table py-4 toggle_data_talbe" style="display:block;" >
            <div class="card">
                <div class="card-header">
                    হজযাত্রীর তথ্য
                </div>
                <div class="card-body">
                    <fieldset>

                        <div class="row">
                            <label for="name" class="col-lg-2 text-left">Agency Name</label>
                            <div class="col-lg-8">
                                <p id="agency_div">{{$json_data->agency}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter Agency no" name="agency" id="agency" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-lg-2 text-left">License No:</label>
                            <div class="col-lg-8">
                                <p id="lisence_no_div">{{$json_data->license_no}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter license no" name="license_no" id="license_no" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-lg-2 text-left"> Applicant Name:</label>
                            <div class="col-lg-8">
                                <p id="name_div">{{$json_data->full_name_english}}</p>
                                <input type="hidden" class="form-control" name="full_name_english"  id="name" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-lg-2 text-left">Father Name:</label>
                            <div class="col-lg-8" >
                                <p id="father_name_div">{{$json_data->father_name}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter Father name" name="father_name" id="father_name" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-lg-2 text-left">Mother Name:</label>
                            <div class="col-lg-8" >
                                <p id="father_name_div">{{$json_data->mother_name}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter Father name" name="father_name" id="father_name" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-lg-2 text-left">Tracking No:</label>
                            <div class="col-lg-8">
                                <p id="tracking_no_div">{{$json_data->tracking_no}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter" name="tracking_no" id="tracking_no" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-lg-2 text-left">NID:</label>
                            <div class="col-lg-8">
                                <p id="nid_div">{{$json_data->nid}}</p>
                                <input type="hidden" class="form-control"  name="nid" id="nid" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-lg-3 text-left">Reason :</label>
                            <div class="col-lg-4">
                                <p>{{$json_data->reason_for_not_perform_hajj}}</p>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-lg-3 text-left">Remarks :</label>
                            <div class="col-lg-4">
                                <p>{{$json_data->remarks}}</p>
                            </div>

                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

    </div>
</div>
{!! Form::close() !!}

<script>
    $(document).ready(function() {
        $('#gender_change').prop('disabled', true);
    });
</script>

