<style>
    .passport-img{
        height: 150px !important;
        width: 150px !important;
        border-radius: 10px !important;
    }
</style>
<div class="modal fade" id="stickerVisaMemberEntryModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
     role="dialog" aria-labelledby="stickerVisaMemberEntryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="font-size: 13px">
            <div class="modal-header" style="background-color: #0F6849 !important">
                <h5 class="modal-title" id="gridSystemModalLabel" style="color: white !important">পাসপোর্ট এন্ট্রি করুন</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true" style="font-size: 22px; color: white">&times;</span></button>
            </div>
            <div class="modal-body">
                {!! Form::open([
                         'url' => url('go-passport/store-sticker-pilgrims/'.($encode_ref_id ).'/'.($encode_process_type_id)),
                         'method' => 'post',
                         'class' => 'form-horizontal',
                         'id' => 'go_passport_member_add',
                         'enctype' => 'multipart/form-data',
                         'files' => 'true'
               ])!!}
                <div class="mt-4" id="stickerVisaMemberEntryDiv">
                    <div class="row memberInfo d-none">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('name', 'নাম', ['class' => 'col-md-4 ']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('pass_name','',['class' => 'form-control required','placeholder' => 'Enter', 'id' => 'pass_name']) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                {!! Form::label('father_name', 'পিতার নাম ', ['class' => 'col-md-4 ']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('father_name','',['class' => 'form-control','placeholder' => 'Enter', 'id' => 'father_name']) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                {!! Form::label('gender', 'লিঙ্গ', ['class' => 'col-md-4 ']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('gender','',['class' => 'form-control','placeholder' => 'Enter', 'id' => 'gender']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="col-md-4">পাসপোর্টের ছবি </label>
                            <img class="passport-img align-center col-md-8" alt="">
                            <span>পাসপোর্টের থেকে প্রাপ্ত ছবি</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-md-4 ">
                                    <label><input name="passport_type" type="radio" value="E-PASSPORT" checked>
                                        E-Passport</label>
                                    <label><input name="passport_type" type="radio" value="MRP"> MRP</label>
                                </div>
                                <div class="col-md-8">
                                    {!! Form::text('passport_no','',['class' => 'form-control','placeholder' => 'Enter Passport Number', 'id' => 'passport_no']) !!}
                                    <span id="passport_no_error" class="text-danger" ></span>
                                </div>
                                <input type="hidden" name="selected_passport_type" value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('passport_dob', 'পাসপোর্টের জন্মতারিখ', ['class' => 'col-md-4 datetimepicker']) !!}
                                <div class="col-md-8">
                                    <div class="input-group" id="datepicker1"
                                         data-target-input="nearest">
                                        {!! Form::text('passport_dob','',['class' => 'form-control datepicker','placeholder'=>'MM/DD/YYYY HH:MM', 'data-rule-maxlength'=>'20', 'value'=>"12:00 AM", 'id' => 'passport_dob']) !!}
                                        <div class="input-group-append" data-target="#datepicker1"
                                             data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    <span id="passport_dob_error" class="text-danger" ></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="loading"><span class="msg1 text-info"></span></div>
                    <div class="row memberInfo d-none">
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('go_serial_no', 'জিও ক্রমিক নং', ['class' => 'col-md-4 ']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('go_serial_no','',['class' => 'form-control required','placeholder' => 'Enter', 'id' => 'go_serial_no']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {!! Form::label('mobile_no', 'মোবাইল নং', ['class' => 'col-md-4 ']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('mobile_no','',['class' => 'form-control required','placeholder' => 'Enter', 'id' => 'mobile_no']) !!}
                                    <span id="mobile_no_error" class="text-danger" ></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($goMemberCheck->fee_applicable == 'yes')
                        <div class="row memberInfo d-none">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {!! Form::label('amount', 'টাকার পরিমান', ['class' => 'col-md-4 ']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('amount','',['class' => 'form-control required','placeholder' => 'Enter', 'id' => 'amount']) !!}
                                        <span id="amount_error" class="text-danger" ></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    {!! Form::label('referance no', 'রেফারেন্স নাম্বার', ['class' => 'col-md-4 ']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('referance_no','',['class' => 'form-control required','placeholder' => 'Enter', 'id' => 'referance_no']) !!}
                                        <span id="referance_no_error" class="text-danger" ></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {!! Form::label('taka received date', 'টাকা গ্রহনের তারিখ', ['class' => 'col-md-4 ']) !!}
                                    <div class="col-md-8">
                                        <div class="input-group" id="datepicker2"
                                             data-target-input="nearest">
                                            {!! Form::text('taka_received_date','',['class' => 'form-control datepicker required','placeholder'=>'MM/DD/YYYY HH:MM', 'value'=>"12:00 AM",'data-rule-maxlength'=>'20', 'id' => 'taka_received_date']) !!}
                                            <div class="input-group-append" data-target="#datepicker2"
                                                 data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <hr>
                <span class="passportError"></span>
                <div class="text-right" id="check">
                    <div class=" d-flex justify-content-between">
                    <button type="button" id="close_passport_member_add_modal" class="btn btn-danger"> <i class="fa fa-times"></i>&nbsp;Close</button>
                    <button type="button" id="verifyPassport" class="btn btn-primary"> <i class="fa fa-check"></i>&nbsp;Verify</button>
                    </div>
                    <button type="button" id="updateToList" class="btn btn-primary d-none"> <i class="fa fa-save"></i>&nbsp;Save</button>
                </div>
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                {!! Form::hidden('requestData','',['id' => 'requestData'])!!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

