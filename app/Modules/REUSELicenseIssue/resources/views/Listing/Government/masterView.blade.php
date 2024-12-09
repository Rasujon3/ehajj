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
<div class="dash-list-table my-4 toggle_data_talbe" >
    {!! Form::hidden('app_id', \App\Libraries\Encryption::encodeId($data->id), ['class' => 'form-control input-md required', 'id' => 'app_id']) !!}
    <div class="table-responsive">
        <table class="table dash-table">
            <thead>
            <tr>
                <th scope="col">ক্রমিক নং</th>
                <th scope="col">নাম</th>
                <th scope="col">ট্র্যাকিং নম্বর</th>
                <th scope="col">সিরিয়াল নং</th>
                <th scope="col">মোবাইল নং</th>
            </tr>
            </thead>
            <tbody class="load_pilgrim">
                @php $json_data = json_decode($data->json_object); @endphp
                    @foreach($json_data as $key => $pldata)
                        <tr>
                            <td>{{$key + 1}}</td>
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
@if(in_array(Auth::user()->user_type,["4x404"]))
    <div class="col-sm-12 text-right">
        <a class="btn btn-info btn-sm" href="{{ url($prefix.'/process/list/'. Encryption::encodeId($process_type_id)) }}">
            বন্ধ করুন
            </a>
            @if($data->processlist->status_id == 1)
{{--                <button class="btn btn-primary btn-sm" name="actionBtn" value="cancel">বাতিল করুন</button>--}}
            @else
            @endif

        {{-- <button class="btn btn-danger btn-sm">বাতিল</button> --}}
    </div>
@endif

{!! Form::close() !!}


