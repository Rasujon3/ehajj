<div class="modal fade" id="stickerGOEdit" data-backdrop="static" data-keyboard="false" tabindex="-1"
     role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="font-size: 16px">
            <div class="modal-header" style="background-color: #0F6849 !important">
                <h5 class="modal-title" id="gridSystemModalLabel" style="color: white !important">জিও পাসপোর্ট সংশোধন করুন</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true" style="font-size: 25px;color: white !important">&times;</span></button>
            </div>

            <div class="modal-body">
                {!! Form::open([
                          'url' => url('go-passport/update/'.($encode_ref_id ).'/'.($encode_process_type_id)),
                          'method' => 'post',
                          'class' => 'form-horizontal',
                          'id' => 'go_passport_form_update',
                          'enctype' => 'multipart/form-data',
                          'files' => 'true'
                ])!!}
                <div class="card">

                    <div class="card-body">
                        <div class="row mt-4 mb-2" id="stickerVisaInfo">

                                <div class="col-lg-3">
                                    <div class="form-group row">
                                        <label for="team_type" class="col-sm-4">দলের ধরণ</label>
                                        @if(count($stickerPilgrimsData) == 0)
                                        {!! Form::select('team_type',["" => 'Select'] + $team_type, $stickerVisaData->team_type, ['class' => 'form-control col-sm-8 customClass required', 'id' => 'team_type', 'onchange' => 'getSubTeamByTeamId("team_type",this.value,"sub_team_type",0)']) !!}
                                        @else
                                            <input type="text" class="form-control col-sm-8" readonly value="{{ $team_type[$stickerVisaData->team_type] }}">
                                        @endif
                                    </div>
                                </div>
                                @if ($team_sub_type != 'no')
                                    <div class="col-lg-3"  id="subTypeCheck">
                                        <div class="form-group row">
                                            <label for="sub_team_type" class="col-sm-4">দলের প্রকার</label>
                                            @if(count($stickerPilgrimsData) == 0)
                                                <input type="text" id="sub_team_type_hidden" class="form-control col-sm-8" readonly value="{{ (isset($team_sub_type[0]) ? $team_sub_type[0]: 'N/A')}}">
                                            {!! Form::select('sub_team_type',[], null, ['class' => 'form-control col-sm-8 customClass d-none', 'id' => 'sub_team_type',]) !!}
                                            @else
                                                <input type="text" class="form-control col-sm-8" readonly value="{{ (isset($team_sub_type[0]) ? $team_sub_type[0]: 'N/A')}}">
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="col-lg-3" id="subTypeCheck" style="display: none;">
                                        <div class="form-group row">
                                            <label for="sub_team_type" class="col-sm-4">দলের প্রকার</label>
                                            {!! Form::select('sub_team_type',[], null, ['class' => 'form-control col-sm-8 customClass d-none', 'id' => 'sub_team_type',]) !!}
                                        </div>
                                    </div>
                                @endif
                                <div class="col-lg-3">
                                    <div class="form-group row">
                                        <label for="go_number" class="col-sm-4">জিও নাম্বার</label>
                                        @if(count($stickerPilgrimsData) == 0)
                                        {!! Form::text('go_number',$stickerVisaData->go_number,['class'=>'form-control col-sm-8 customClass required','placeholder' =>'','id'=>'go_number']) !!}
                                        @else
                                            <input type="text" class="form-control col-sm-8" readonly id="" value="{{ (isset($stickerVisaData->go_number) ? $stickerVisaData->go_number: 'N/A')}}">
                                        @endif
                                    </div>
                                </div>
                            <div class="col-lg-3">
                                <div class="form-group row">
                                    <label for="go_member" class="col-sm-6">জিওতে সদস্য সংখ্যা </label>
                                    {!! Form::number('go_member',$stickerVisaData->go_member,['class'=>'form-control col-sm-6 required','placeholder' =>'','id'=>'go_member']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 mb-2">
                            <div class="col-sm-6 col-lg-4">
                                <div class="form-group row">
                                    <div class="input-group" id="datepicker1" data-target-input="nearest">
                                        <label for="go_date" class="col-sm-4">জিও তারিখ </label>
                                        {!! Form::text('go_date', (!empty($goMemberCheck->go_date) && ($goMemberCheck->go_date != null)) ? (\Carbon\Carbon::createFromFormat('Y-m-d', $goMemberCheck->go_date)->format('d-M-Y')): null,['class' => 'form-control datepicker','placeholder'=>'MM/DD/YYYY', 'data-rule-maxlength'=>'20', 'value'=>"12:00 AM", 'id' => '']) !!}
                                        <div class="input-group-append" data-target="#datepicker1" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4 is_fee_applicable">
                                <label for="is_fee_applicable" class="col-sm-6">ফি প্রযোজ্য</label>
                                <div class="form-check form-check-inline ">
                                    {!! Form::radio('fee_applicable', 'yes', ((!empty($stickerVisaData->fee_applicable) && $stickerVisaData->fee_applicable == 'yes') ? 'checked' : false) , ['class' => 'form-check-input']) !!}
                                    {!! Form::label('fee_yes', 'Yes', ['class' => 'form-check-label']) !!}
                                </div>
                                <div class="form-check form-check-inline ">
                                    {!! Form::radio('fee_applicable', 'no', ((empty($stickerVisaData->fee_applicable) || $stickerVisaData->fee_applicable == 'no') ? 'checked' : false ), ['class' => 'form-check-input']) !!}
                                    {!! Form::label('fee_no', 'No', ['class' => 'form-check-label']) !!}
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4" id="payableAmountSection">
                                <div class="form-group row">
                                    <label for="payable_amount" class="col-sm-4">পরিশোধযোগ্য এমাউন্ট</label>
                                    {!! Form::number('payable_amount',(!empty($stickerVisaData->payable_amount) ? $stickerVisaData->payable_amount: ''),['class'=>'form-control col-sm-8','placeholder' =>'Enter','id'=>'payable_amount']) !!}
                                </div>
                            </div>

                        </div>

                        <div class="row mt-1">
                            <div class="col-sm-12">
                                <button type="button" id="goUpdate" style=" background-color: #0F6849  !important; margin-right: 5px !important;" class="btn btn-primary float-right">
                                    <i class="fa fa-save" style="color: white "> </i>&nbsp;Update</button>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
