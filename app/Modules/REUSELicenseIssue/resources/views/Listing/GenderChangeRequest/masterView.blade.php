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
{{--            <div class="card">--}}
{{--                <div class="card-header">--}}
{{--                    হজযাত্রীর তথ্য--}}
{{--                </div>--}}
{{--                <div class="card-body">--}}
{{--                    <fieldset>--}}
{{--                        <div class=" row">--}}
{{--                            <label for="name" class="col-lg-4 text-left">Name:</label>--}}
{{--                            <div class="col-lg-8" >--}}
{{--                                <input type="hidden" class="form-control" name="full_name_english" value="{{$json_data->full_name_english}}"  id="name" readonly>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class=" row">--}}
{{--                            <label for="name" class="col-lg-4 text-left">Tracking No:</label>--}}
{{--                            <div class="col-lg-8" >--}}
{{--                                <input type="hidden" class="form-control" placeholder="Enter Tracking no" value="{{$json_data->tracking_no}}" name="tracking_no" id="tracking_no" readonly>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class=" row">--}}
{{--                            <label for="name" class="col-lg-4 text-left">Gender:</label>--}}
{{--                            <div class="col-lg-4" >--}}
{{--                                <input type="hidden" class="form-control"  id="gender" value="{{$json_data->gender}}" name="gender" readonly>--}}
{{--                            </div>--}}
{{--                            <div class="col-lg-4" id="gender_change" >--}}
{{--                                <select class="form-control"  name="gender_change">--}}
{{--                                    <option value="male" @if($json_data->gender_change == 'male') selected @endif>Male</option>--}}
{{--                                    <option value="female" @if($json_data->gender_change == 'female') selected @endif>Female</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}

{{--                        </div>--}}
{{--                        <div class=" row">--}}
{{--                            <label for="name" class="col-lg-4 text-left">Mobile:</label>--}}
{{--                            <div class="col-lg-4" >--}}
{{--                                <input type="tel" class="form-control"  id="mobile" name="mobile" value="{{$json_data->mobile}}" readonly>--}}
{{--                            </div>--}}
{{--                            <div class="col-lg-4" >--}}
{{--                                <input type="tel" class="form-control"  id="mobile_change" name="mobile_change" readonly value="{{$json_data->mobile_change}}">--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                    </fieldset>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="card">
                <div class="card-header text-bold">
                    হজযাত্রীর তথ্য
                </div>
                <div class="card-body">
                    <fieldset>
{{--                        @dd($json_data)--}}
                        <div class=" row">
                            <label for="name" class="col-lg-4 text-left">Tracking No:</label>
                            <div class="col-lg-8" id="tracking_no_div">
                                <p>{{$json_data->tracking_no}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter Tracking no" value="{{$json_data->tracking_no}}" name="tracking_no" id="tracking_no" readonly>
                            </div>
                        </div>
{{--                        <div class=" row">--}}
{{--                            <label for="name" class="col-lg-4 text-left">Serial No:</label>--}}
{{--                            <div class="col-lg-8" id="serial_no_div" >--}}
{{--                                <input type="hidden" class="form-control" placeholder="Enter Serial no" value="{{$json_data->serial_no}}" name="serial_no" id="serial_no" readonly>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class=" row">
                            <label for="name" class="col-lg-4 text-left">Name in English:</label>
                            <div class="col-lg-8">
                                <p id="name_div">{{$json_data->full_name_english}}</p>
                                <input type="hidden" class="form-control" name="full_name_english" value="{{$json_data->full_name_english}}"  id="name" readonly>
                            </div>
                        </div>
                        <div class=" row">
                            <label for="name" class="col-lg-4 text-left">Name in Bangla:</label>
                            <div class="col-lg-8">
                                <p id="bangla_name_div">{{$json_data->name_in_bangla}}</p>
                                <input type="hidden" class="form-control" name="full_name_bangla" value="{{$json_data->name_in_bangla}}"  id="bangla_name" readonly>
                            </div>
                        </div>
                        <div class=" row">
                            <label for="name" class="col-lg-4 text-left">Father Name:</label>
                            <div class="col-lg-8" >
                                <p id="father_name_div">{{$json_data->father_name}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter Father name" value="{{$json_data->father_name}}" name="father_name" id="father_name" readonly>
                            </div>
                        </div>
                        <div class=" row">
                            <label for="name" class="col-lg-4 text-left">Mother Name:</label>
                            <div class="col-lg-8" >
                                <p id="mother_name_div">{{$json_data->mother_name_in_bangla}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter Mother name" value="{{$json_data->mother_name_in_bangla}}" name="mother_name" id="mother_name" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-lg-4 text-left">DoB:</label>
                            <div class="col-lg-8">
                                <p id="dob_div">{{$json_data->dob}}</p>
                                <input type="hidden" class="form-control" value="{{$json_data->dob}}" name="dob" id="dob" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-lg-4 text-left">Gender:</label>
                            <div class="col-lg-8">
                                <p id="gender_div">{{$json_data->gender}}</p>
                                <input type="hidden" class="form-control" value="{{ $json_data->gender }}" name="gender" id="gender" readonly>
                            </div>
                        </div>
                        @if(!empty($json_data->ksa_mobile))
                            <div class="row">
                                <label for="name" class="col-lg-4 text-left">KSA Mobile:</label>
                                <div class="col-lg-8">
                                    <p id="ksa_mobile_div">{{ $json_data->ksa_mobile }}</p>
                                    <input type="hidden" class="form-control" value="{{ $json_data->ksa_mobile }}" name="ksa_mobile" id="ksa_mobile" readonly>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <label for="name" class="col-lg-4 text-left">Mobile:</label>
                            <div class="col-lg-8">
                                <p id="mobile_div">{{ $json_data->mobile }}</p>
                                <input type="hidden" class="form-control" value="{{ $json_data->mobile }}" name="mobile" id="mobile" readonly>
                            </div>
                        </div>
                        <div class=" row" >
                            <label for="name" class="col-lg-4 text-left">Pilgrim Type:</label>
                            <div class="col-lg-8">
                                <p id="is_govt_div">{{$json_data->is_govt}}</p>
                                <input type="hidden" class="form-control" value="{{$json_data->is_govt}}" name="is_govt" id="is_govt" readonly>
                                {{--                                <input type="hidden" class="form-control"  name="pilgrim_type_id" id="pilgrim_type_id" readonly>--}}
                            </div>
                        </div>
                        {{--
                        <div class=" row">
                            <label for="name" class="col-lg-4 text-left">Passport Present Address:</label>
                            <div class="col-lg-8">
                                <p id="passport_present_address_div">{{$json_data->present_address}}</p>
                                <input type="hidden" class="form-control" placeholder="Enter" name="passport_present_address" value="{{$json_data->present_address}}" id="passport_present_address" readonly>
                            </div>
                        </div>
                        --}}
                        <div class=" row">
                            <label for="name" class="col-lg-4 text-left">Present Address:</label>
                            <div class="col-lg-8">
                                <p id="present_address_div">
                                    {{$json_data->village_ward}},
                                    {{$json_data->police_station}},
                                    {{$json_data->district}},
                                    {{$json_data->post_code}}
                                </p>
                                <input type="hidden" class="form-control" placeholder="Enter" name="present_address" value="{{$json_data->present_address}}" id="present_address" readonly>
                            </div>
                        </div>
                        <hr style="margin-top: 0 !important; margin-bottom: 0 !important">
                        <div class="text-bold py-1">পরিবর্তনকৃত হজযাত্রীর তথ্য</div>
                        <hr style="margin-top: 0 !important; margin-bottom: 10px !important">
                        @if(isset($json_data->gender_change) && $json_data->gender_change !=null)
                            <!--
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left">Gender:</label>
                                <div class="col-lg-1" id="check_gender_div" style="display: none">
                                    <input type="checkbox" name="gender_checked"  id="gender_checked"/>
                                </div>
                                <div class="col-lg-4" id="gender_div">
                                    {{--
                                    <p>{{$json_data->gender}}</p>
                                    <input type="hidden" class="form-control"  id="gender" value="{{$json_data->gender_change}}" name="gender" readonly>
                                    --}}
                                </div>
                                <div class="col-lg-3" id="gender_change" style="display: none">

                                </div>

                            </div>
                            -->
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left">Gender:</label>
                                <div class="col-lg-1" id="check_gender_div" style="display: none">
                                    <input type="checkbox" name="gender_checked"  id="gender_checked"/>
                                </div>
                                <div class="col-lg-4" id="gender_div">
                                    <p>{{$json_data->gender_change}}</p>
                                    <input type="hidden" class="form-control"  id="gender" value="{{$json_data->gender_change}}" name="gender" readonly>
                                </div>
                                <div class="col-lg-3" id="gender_change" style="display: none">

                                </div>

                            </div>
                        @endif
                        @if(isset($json_data->mobile_change) && $json_data->mobile_change !=null)
                        <!--
                        <div class="row">
                            <label for="name" class="col-lg-4 text-left">Mobile:</label>
                            <div class="col-lg-4" >
                                {{--
                                <p>{{$json_data->mobile}}</p>
                                <input type="hidden" class="form-control" name="mobile_checked" value="{{$json_data->mobile}}" readonly  id="mobile_checked"/>
                                --}}
                            </div>
                        </div>
                        -->
                        <div class="row">
                            <label for="name" class="col-lg-4 text-left">Mobile:</label>
                            <div class="col-lg-4" >
                                <p>{{$json_data->mobile_change}}</p>
                                <input type="hidden" class="form-control" name="mobile_checked" value="{{$json_data->mobile_change}}" readonly  id="mobile_checked"/>
                            </div>
                        </div>
                        @endif
                        @if(isset($json_data->ksa_mobile_change) && $json_data->ksa_mobile_change !=null)
                            <!--
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="ksa_mobile_label" >KSA Mobile:</label>

                                <div class="col-lg-4" id="ksa_mobile_div">
                                    {{--
                                    <p>{{$json_data->ksa_mobile}}</p>
                                    <input type="hidden" class="form-control" id="ksa_mobile" value="{{$json_data->ksa_mobile_change}}"  name="ksa_mobile" readonly>
                                    --}}
                                </div>

                            </div>
                            -->
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="ksa_mobile_label" >KSA Mobile:</label>

                                <div class="col-lg-4" id="ksa_mobile_div">
                                    <p>{{$json_data->ksa_mobile_change}}</p>
                                    <input type="hidden" class="form-control" id="ksa_mobile" value="{{$json_data->ksa_mobile_change}}"  name="ksa_mobile" readonly>
                                </div>

                            </div>
                        @endif
                        {{-- Name in English start --}}
                        @if(isset($json_data->name_in_english_change) && $json_data->name_in_english_change !=null)
                            <!--
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="name_in_english_label" >Name in English:</label>

                                <div class="col-lg-4" id="name_in_english_div">
                                    {{--
                                    <p>{{$json_data->full_name_english}}</p>
                                    <input type="hidden" class="form-control" id="name_in_english" value="{{$json_data->name_in_english_change}}"  name="name_in_english" readonly>
                                    --}}
                                </div>

                            </div>
                            -->
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="name_in_english_label" >Name in English:</label>

                                <div class="col-lg-4" id="name_in_english_div">
                                    <p>{{$json_data->name_in_english_change}}</p>
                                    <input type="hidden" class="form-control" id="name_in_english" value="{{$json_data->name_in_english_change}}"  name="name_in_english" readonly>
                                </div>

                            </div>
                        @endif
                        {{-- Name in English end --}}
                        {{-- Name in Bangla start --}}
                        @if(isset($json_data->name_in_bangla_change) && $json_data->name_in_bangla_change !=null)
                            <!--
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="name_in_bangla_label" >Name in Bangla:</label>

                                <div class="col-lg-4" id="name_in_bangla_div">
                                    {{--
                                    <p>{{$json_data->name_in_bangla}}</p>
                                    <input type="hidden" class="form-control" id="name_in_bangla" value="{{$json_data->name_in_bangla_change}}"  name="name_in_bangla" readonly>
                                    --}}
                                </div>

                            </div>
                            -->
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="name_in_bangla_label" >Name in Bangla:</label>

                                <div class="col-lg-4" id="name_in_bangla_div">
                                    <p>{{$json_data->name_in_bangla_change}}</p>
                                    <input type="hidden" class="form-control" id="name_in_bangla" value="{{$json_data->name_in_bangla_change}}"  name="name_in_bangla" readonly>
                                </div>

                            </div>
                        @endif
                        {{-- Name in Bangla end --}}
                        {{-- Father Name in Bangla start --}}
                        @if(isset($json_data->father_name_in_bangla_change) && $json_data->father_name_in_bangla_change != null)
                            <!--
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="father_name_in_bangla_label" >Father Name in Bangla:</label>

                                <div class="col-lg-4" id="father_name_in_bangla_div">
                                    {{--
                                    <p>{{$json_data->father_name}}</p>
                                    <input type="hidden" class="form-control" id="father_name_in_bangla" value="{{$json_data->father_name_in_bangla_change}}"  name="name_in_bangla" readonly>
                                    --}}
                                </div>

                            </div>
                            -->
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="father_name_in_bangla_label" >Father Name in Bangla:</label>

                                <div class="col-lg-4" id="father_name_in_bangla_div">
                                    <p>{{$json_data->father_name_in_bangla_change}}</p>
                                    <input type="hidden" class="form-control" id="father_name_in_bangla" value="{{$json_data->father_name_in_bangla_change}}"  name="father_name_in_bangla" readonly>
                                </div>

                            </div>
                        @endif
                        {{-- Father Name in Bangla end --}}
                        {{-- Mother Name in Bangla start --}}
                        @if(isset($json_data->mother_name_in_bangla_change) && $json_data->mother_name_in_bangla_change !=null)
                            <!--
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="mother_name_in_bangla_label" >Mother Name in Bangla:</label>

                                <div class="col-lg-4" id="mother_name_in_bangla_div">
                                    {{--
                                    <p>{{$json_data->mother_name_in_bangla}}</p>
                                    <input type="hidden" class="form-control" id="mother_name_in_bangla" value="{{$json_data->mother_name_in_bangla_change}}"  name="mother_name_in_bangla" readonly>
                                    --}}
                                </div>

                            </div>
                            -->
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="mother_name_in_bangla_label" >Mother Name in Bangla:</label>

                                <div class="col-lg-4" id="mother_name_in_bangla_div">
                                    <p>{{$json_data->mother_name_in_bangla_change}}</p>
                                    <input type="hidden" class="form-control" id="mother_name_in_bangla" value="{{$json_data->mother_name_in_bangla_change}}"  name="mother_name_in_bangla" readonly>
                                </div>

                            </div>
                        @endif
                        {{-- Mother Name in Bangla end --}}
                        {{-- Present Address start --}}
                        @if(
                            isset($json_data->district_change) && $json_data->district_change !=null ||
                            isset($json_data->district_id_change) && $json_data->district_id_change !=null ||
                            isset($json_data->police_station_change) && $json_data->police_station_change !=null ||
                            isset($json_data->thana_id_change) && $json_data->thana_id_change !=null ||
                            isset($json_data->post_code_change) && $json_data->post_code_change !=null ||
                            isset($json_data->village_ward_change) && $json_data->village_ward_change !=null
                            )
                            <!--
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="mother_name_in_bangla_label" >Present Address:</label>
                                {{--
                                <div class="col-lg-4" id="mother_name_in_bangla_div">
                                    <p>
                                        {{$json_data->village_ward}},
                                        {{$json_data->police_station}},
                                        {{$json_data->district}},
                                        {{$json_data->post_code}}
                                    </p>
                                </div>
                                --}}
                            </div>
                            -->
                            <div class=" row">
                                <label for="name" class="col-lg-4 text-left" id="mother_name_in_bangla_label" >Present Address:</label>

                                <div class="col-lg-4" id="mother_name_in_bangla_div">
                                    <p>
                                        {{$json_data->village_ward_change ?: $json_data->village_ward}},
                                        {{$json_data->police_station_change ?: $json_data->police_station}},
                                        {{$json_data->district_change ?: $json_data->district}},
                                        {{$json_data->post_code_change ?: $json_data->post_code}}
                                    </p>
                                </div>

                            </div>
                        @endif
                        {{-- Present Address end --}}

                    </fieldset>
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
    $(document).ready(function() {
        $('#gender_change').prop('disabled', true);
    });
</script>

