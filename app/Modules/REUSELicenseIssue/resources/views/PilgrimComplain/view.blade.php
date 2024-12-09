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

<div class="dash-content-inner">
    <div class="src-hajj-tracking-number">
        <div class="dash-list-table py-4 toggle_data_talbe" style="display:block;">

            <div class="card">
                <div class="card-header">
                    হজযাত্রীর তথ্য
                </div>
                <div class="card-body">
                    @if($data != null)

                        <fieldset>
                            @if($data->is_govt !=null)
                                <div class=" row">
                                    <label for="name" class="col-lg-4 text-left">ম্যানেজমেন্ট :</label>
                                    <div class="col-lg-8">
                                        <p style="font-size: 16px" id="name_div"><b>:</b> {{$data->is_govt}}</p>
                                    </div>
                                </div>
                            @endif
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left">বর্তমান অবস্থান :</label>
                                <div class="col-lg-8">
                                    <p id="father_name_div" style="font-size: 16px"><b>:</b> {{$data->country}}</p>
                                </div>
                            </div>

                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left">প্রাক-নিবন্ধিত হজ যাত্রীর ট্র্যাকিং নং
                                    :</label>
                                <div class="col-lg-8" id="tracking_no_div">
                                    <p style="font-size: 16px"><b>:</b> {{$data->tracking_no}}</p>
                                    <input type="hidden" class="form-control" placeholder="Enter Tracking no"
                                           value="{{$data->tracking_no}}" name="tracking_no" id="tracking_no" readonly>
                                </div>
                            </div>
                            @if($data->pid != null)
                                <div class=" row">
                                    <label for="name" class="col-lg-4 text-left">পিলগ্রিম আইডি নাম্বার :</label>
                                    <div class="col-lg-8">
                                        <p id="name_div" style="font-size: 16px"><b>:</b> {{$data->pid}}</p>
                                    </div>
                                </div>
                            @endif
                            @if($data->agency_license_no != null)
                                <div class=" row">
                                    <label for="name" class="col-lg-4 text-left">হজ এজেন্সি এর লাইসেন্স নাম্বার
                                        :</label>
                                    <div class="col-lg-8">
                                        <p id="name_div" style="font-size: 16px"><b>:</b> {{$data->agency_license_no}}
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($data->agency_name != null)
                                <div class=" row">
                                    <label for="name" class="col-lg-4 text-left">হজ এজেন্সির নাম :</label>
                                    <div class="col-lg-8">
                                        <p id="name_div" style="font-size: 16px"><b>:</b> {{$data->agency_name}}</p>
                                    </div>
                                </div>
                            @endif
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left">অভিযোগকারী হজ যাত্রী নিজে অথবা
                                    পক্ষে </label>
                                <div class="col-lg-8">
                                    <p id="name_div" style="font-size: 16px"><b>:</b> {{$data->is_self?"নিজে":"পক্ষে"}}
                                    </p>
                                </div>
                            </div>
                            <div class=" row">
                            <label for="name" class="col-lg-4 text-left"> {{$data->is_self?" ": " অভিযোগকারী হজ যাত্রীর পক্ষে ব্যক্তির তথ্য :"}}</label>
                            </div>
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left">নাম :</label>
                                <div class="col-lg-8">
                                    <p id="name_div" style="font-size: 16px"><b>:</b> {{$data->pilgrim_name}}</p>
                                </div>
                            </div>
                            @if($data->nid != null)
                                <div class=" row">
                                    <label for="name" class="col-lg-4 text-left">এন আই ডি নম্বর :</label>
                                    <div class="col-lg-8">
                                        <p id="name_div" style="font-size: 16px"><b>:</b> {{$data->nid}}</p>
                                    </div>
                                </div>
                            @endif
                            @if($data->mobile != null)
                                <div class=" row">
                                    <label for="name" class="col-lg-4 text-left">মোবাইল :</label>
                                    <div class="col-lg-8">
                                        <p id="name_div" style="font-size: 16px"><b>:</b> {{$data->mobile}}</p>
                                    </div>
                                </div>
                            @endif
                            @if($data->email != null)
                                <div class=" row">
                                    <label for="name" class="col-lg-4 text-left">ই-মেইল :</label>
                                    <div class="col-lg-8">
                                        <p id="name_div" style="font-size: 16px"><b>:</b> {{$data->email}}</p>
                                    </div>
                                </div>
                            @endif
                            @if($data->comment != null)
                                <div class=" row">
                                    <label for="name" class="col-lg-4 text-left">মন্তব্য :</label>
                                    <div class="col-lg-8">
                                        <p id="name_div" style="font-size: 16px"><b>:</b> {{$data->comment}}</p>
                                    </div>
                                </div>
                            @endif
                            @php
                                $reason = json_decode($data->complain_reason);
                                $allreasons = \App\Modules\Web\Models\ComplainReason::whereIn('id',$reason)->get();
                            @endphp
                            <div class="form-card">

                                <label class="fieldlabels">অভিযোগের কারণ : </label>

                                <div class="col-lg-8" style="float: right">
                                    @if(isset($allreasons) && $allreasons != null)
                                        @foreach($allreasons as $reason)
                                            <div class="checkbox myCheckbox">
                                                <label>
                                                    <input type="checkbox" name="not_registered" checked id="reason">
                                                    {{$reason->title}}
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>


                            </div>
                        </fieldset>
                        @if($data->complain_attachment != null)
                            <div class="form-card">
                                <div class="col-lg-12" style="background: #B2BEB5">
                                <p style="font-size: 25px">সংযুক্তি সমূহ</p>
                                </div>
                                <div class="col-lg-12">
                                    <embed src="data:application/pdf;base64,{{ $data->complain_attachment }}"
                                           type="application/pdf" width="100%" height="600px"/>
                                </div>
                            </div>
                        @endif

                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{--@if(in_array(Auth::user()->user_type,["4x404"]))--}}
{{--    <div class="col-sm-12 text-right">--}}
{{--        <a class="btn btn-info btn-sm" href="{{ url($prefix.'/process/list/'. Encryption::encodeId($process_type_id)) }}">--}}
{{--            বন্ধ করুন--}}
{{--            </a>--}}
{{--            @if($data->processlist->status_id == 1)--}}
{{--                <button class="btn btn-primary btn-sm" name="actionBtn" value="cancel">বাতিল করুন</button>--}}
{{--            @else--}}
{{--            @endif--}}

{{--        --}}{{-- <button class="btn btn-danger btn-sm">বাতিল</button> --}}
{{--    </div>--}}
{{--@endif--}}

{!! Form::close() !!}

<script>
    $(document).ready(function () {
        $('#gender_change').prop('disabled', true);
    });
</script>

