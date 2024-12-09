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

<div class="dash-list-table my-4 toggle_data_talbe">

    <div class="card">
        <div class="card-header">
            অত্র হজ গাইডের আবেদন বিবরণী
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-4">
                    <div class="clearfix no-margin row">
                        <label class="col-md-4 col-xs-4">নাম : </label>
                        <div class="col-md-7 col-xs-7">
                            <span> {{$guide_information->user_first_name.$guide_information->user_middle_name.$guide_information->user_last_name}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                      <div class="clearfix no-margin row">
                        <label class="col-md-4 col-xs-4">ট্র্যাকিং নং :</label>
                          @php
                              $email = $guide_information->user_email;
                              $tracking_no = explode('@',$email);
                          @endphp
                        <div class="col-md-7 col-xs-7">
                           {{$tracking_no[0]}}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                      <div class="clearfix no-margin row">
                        <label class="col-md-4 col-xs-4">মোবাইল নং :</label>
                        <div class="col-md-7 col-xs-7">
                           {{$guide_information->user_mobile}}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                      <div class="clearfix no-margin row">
                        <label class="col-md-4 col-xs-4">অনুমোদিত:</label>
                        <div class="col-md-7 col-xs-7">
                          {{$total_approved_request_for_guide}} জন
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                      <div class="clearfix no-margin row">
                        <label class="col-md-3 col-xs-3">সাবমিটেড :</label>
                        <div class="col-md-7 col-xs-7">
                         {{$total_submitted_request_by_guide}} জন
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <span class="btn btn-primary btn-sm mb-2">
                        ফ্লাইট প্রস্তাবকৃত হজ যাত্রীদের তালিকা
      </span>
    @if($guide_information->flight_id == 0)
        <p style="color: red">বিঃদ্রঃ এটি একটি সম্ভাব্য ফ্লাইট তারিখ অনুযায়ী আবেদন, যা পরিচালক মহোদয়ের সিন্ধান্ত অনুযায়ী ফ্লাইটের তারিখ, ফ্লাইট ও বাড়ি নির্ধারণ করে দিবেন এবং সেটাই চূড়ান্ত ফ্লাইট অনুযায়ী নির্ধারণ করা হবে। Application Print থেকে আবেদনটি প্রিন্ট করে হজ অফিসে জমা দিবেন।</p>
    @endif
    {!! Form::hidden('app_id', \App\Libraries\Encryption::encodeId($data->id), ['class' => 'form-control input-md required', 'id' => 'app_id']) !!}
    <div class="table-responsive">

        <table class="table dash-table">
            <thead>
            <tr>
                <th scope="col">পিআইডি</th>
                <th scope="col">নাম</th>
                <th scope="col">ট্র্যাকিং নম্বর</th>
                <th scope="col">মোবাইল নং</th>
                @if(!empty($remove_permission) && $remove_permission ==1)
                <th scope="col">অ্যাকশান</th>
                @endif
            </tr>
            </thead>
            <tbody class="load_pilgrim">
            @php $json_data = json_decode($data->json_object); @endphp
            @foreach($json_data as $key => $pldata)
                @if(isset($pldata->is_checked) && $pldata->is_checked == 1)
                    {{-- @if(in_array($pldata->pid, $flight_request_pilgrims_array)) --}}
                    <tr>
                        {{--                    <td>{{$key + 1}}</td>--}}
                        <td>{{$pldata->pid}}</td>
                        <td>{{$pldata->full_name_english}}</td>
                        <td>{{$pldata->tracking_no}}</td>
                        <td>{{$pldata->mobile}}</td>
                        @if(!empty($remove_permission) && $remove_permission ==1)
                        <td>
                            <a style="color: #DC3545" class="remove_pilgrim" href="{{ route('delete-pilgrim', [
                                'pid' => \App\Libraries\Encryption::encodeId($pldata->pid),
                                'id' => \App\Libraries\Encryption::encodeId($data->id),
                                'process_type_id' => \App\Libraries\Encryption::encodeId($process_type_id),
                            ]) }}" onclick="return confirmDelete();">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                        @endif

                        {{--                    <td>{{$pldata->flight_id}}</td>--}}
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@if(in_array(Auth::user()->user_type,["4x404"]))
    <div class="col-sm-12 text-right">
        <a class="btn btn-info btn-sm"
           href="{{ url($prefix.'/process/list/'. Encryption::encodeId($process_type_id)) }}">
            বন্ধ করুন
        </a>
        @if($data->processlist->status_id == 1)
            {{--                <button class="btn btn-primary btn-sm" name="actionBtn" value="cancel">বাতিল করুন</button>--}}
        @endif

        {{-- <button class="btn btn-danger btn-sm">বাতিল</button> --}}
    </div>
@endif

{!! Form::close() !!}

<script>
    function confirmDelete() {
        return confirm("আপনি কি এই হজযাত্রীকে গাইড তালিকা হতে রিমুভ করতে চাচ্ছেন ?");
    }
</script>


