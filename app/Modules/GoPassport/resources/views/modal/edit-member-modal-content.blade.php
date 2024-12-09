<style>
    .passport-img2{
        height: 150px !important;
        width: 150px !important;
        border-radius: 10px !important;
    }
</style>
{!! Form::open([
 'url' => url('go-passport/passport-member-entry/update/'.($id ).'/'.($encode_ref_id ).'/'.($encode_process_type_id)),
 'method' => 'post',
 'class' => 'form-horizontal',
 'id' => 'go_passport_member_entry_update',
 'enctype' => 'multipart/form-data',
 'files' => 'true'
])!!}
<div class="mt-4" id="stickerVisaMemberEditDiv">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group row">
                {!! Form::label('go_serial_no', 'জিও ক্রমিক নং', ['class' => 'col-md-4 ']) !!}
                <div class="col-md-8">
                    {!! Form::text('go_serial_no', $membersInfo->go_serial_no,['class' => 'form-control','placeholder' => 'Enter', 'id' => '']) !!}
                </div>

                {!! Form::label('name', 'নাম', ['class' => 'col-md-4 ']) !!}
                <div class="col-md-8" style="margin-top: 10px">
                    {!! Form::text('name',$membersInfo->name,['class' => 'form-control','placeholder' => 'Enter','readonly'=>true, 'id' => 'name']) !!}
                </div>

                {!! Form::label('mobile_no', 'মোবাইল নং', ['class' => 'col-md-4 ']) !!}
                <div class="col-md-8" style="margin-top: 10px">
                    {!! Form::text('mobile_no',$membersInfo->mobile_no,['class' => 'form-control','placeholder' => 'Enter', 'id' => '']) !!}
                    <span id="mobile_no_error" class="text-danger" ></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                <label class="col-md-4">পাসপোর্টের ছবি </label>
                <img class="passport-img2 col-md-8" alt="" src="{{$picture}}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group row">
                <div class="col-md-4">
                    <label><input name="passport_type" type="radio" disabled value="E-PASSPORT" {{$membersInfo->passport_type == 'E-PASSPORT' ? 'checked': '' }}>
                        E-Passport</label>
                    <label><input name="passport_type" type="radio" disabled value="MRP" {{$membersInfo->passport_type == 'MRP' ? 'checked': '' }}> MRP</label>
                </div>
                <div class="col-md-8">
                    {!! Form::text('passport_no',$membersInfo->passport_no,['class' => 'form-control','placeholder' => 'Enter','readonly'=>true, 'id' => '']) !!}
                    <span id="passport_no_error" class="text-danger" ></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group row">
                {!! Form::label('passport_dob', 'পাসপোর্টের জন্মতারিখ', ['class' => 'col-md-4 ']) !!}
                <div class="col-md-8">
                    <div class="input-group date datetimepicker5" id="datepicker5"
                         data-target-input="nearest">
                        {!! Form::text('passport_dob',$membersInfo->passport_dob,['class' => 'form-control','placeholder' => 'Select', 'readonly'=>true, 'id' => '']) !!}
                        <div class="input-group-append" data-target="#datepicker5"
                             data-toggle="datetimepicker">
                            <div class="input-group-text readonly"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($membersVisaMetadata->fee_applicable == "yes")
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    {!! Form::label('amount', 'টাকার পরিমান', ['class' => 'col-md-4 ']) !!}
                    <div class="col-md-8">
                        {!! Form::text('amount',$membersInfo->amount,['class' => 'form-control','placeholder' => 'Enter', 'id' => '']) !!}
                        <span id="amount_error" class="text-danger" ></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group row">
                    {!! Form::label('taka received date', 'টাকা গ্রহনের তারিখ', ['class' => 'col-md-4 ']) !!}
                    <div class="col-md-8">
                        <div class="input-group" id="datepicker6" data-target-input="nearest">
                            {!! Form::text('taka_received_date',$taka_received_date,['class' => 'form-control datepicker','placeholder'=>'MM/DD/YYYY HH:MM','value'=>"12:00 AM", 'data-rule-maxlength'=>'20', 'id' => '']) !!}
                            <div class="input-group-append" data-target="#datepicker6"
                                 data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    {!! Form::label('referance no', 'রেফারেন্স নাম্বার', ['class' => 'col-md-4 ']) !!}
                    <div class="col-md-8">
                        {!! Form::text('referance_no',$membersInfo->referance_no,['class' => 'form-control','placeholder' => 'Enter', 'id' => '']) !!}
                        <span id="referance_no_error" class="text-danger" ></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6"> </div>
        </div>
    @endif
</div>
<hr>
<div class="text-right">
    <button type="submit" id="" class="btn btn-primary">Update</button>
</div>
<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
{!! Form::close() !!}
