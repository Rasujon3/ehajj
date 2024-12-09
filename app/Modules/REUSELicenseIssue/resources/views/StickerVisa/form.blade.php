<style>
    .card-header {
        padding: 0.75rem 1.25rem !important;
    }
    .input-disabled{
        pointer-events: none;
    }
    .modal.fade .modal-dialog {
        margin-top: 5px;
        margin-left: 19%;
    }

    .modal-content {
        width: 190%;
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
        <h3>স্টিকার ভিসা আবেদন ফরম</h3>
    </div>
    <div class="dash-section-content pt-1">
        <div class="dash-sec-head">
            <div class="container m-0 p-0">
                <div class="card">
                    <div class="card-body">
                        <div class="row mt-4 mb-2" id="stickerVisaInfo">
                            <div class="col-sm-3">
                                <div class="form-group row">
                                    <label for="team_type" class="col-sm-4">দলের ধরণ</label>
                                    {!! Form::select('team_type',["" => 'Select'] + $team_type, null, ['class' => 'form-control col-sm-8', 'id' => 'team_type', 'onchange' => 'getSubTeamByTeamId("team_type",this.value,"sub_team_type",0)' ]) !!}

                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group row">
                                    <label for="sub_team_type" class="col-sm-4">দলের প্রকার</label>
                                    {!! Form::select('sub_team_type',[], null, ['class' => 'form-control col-sm-8', 'id' => 'sub_team_type']) !!}

                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group row">
                                    <label for="go_number" class="col-sm-4">জিও নাম্বার</label>
                                    {!! Form::text('go_number','',['class'=>'form-control col-sm-8','placeholder' =>'','id'=>'go_number']) !!}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group row">
                                    <label for="go_member" class="col-sm-6">জিওতে সদস্য সংখ্যা </label>
                                    {!! Form::number('go_member','',['class'=>'form-control col-sm-6','placeholder' =>'','id'=>'go_member']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4 mb-2 d-none" id="stickerVisaExtraInfo">
                            <div class="col-sm-3">
                                <div class="form-group row">
                                    <label for="go_number" class="col-sm-4">জিওতে বাকি সদস্য সংখ্যা</label>
                                    {!! Form::text('remaining_member','',['class'=>'form-control col-sm-8','placeholder' =>'','id'=>'remaining_member','readonly']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12">
                                <button type="button" class="btn btn-primary float-right" id="addParticipant">Add
                                    Participant
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dash-content-inner d-none" id="stickerVisaList">
            <div class="card">
                <div class="card-header row">
                    <div class="col-sm-3">স্টিকার ভিসা হজযাত্রীদের যুক্ত করুন</div>
                    <div class="col-sm-9">
                        <button type="button" class="btn btn-primary float-right btn-sm"
                                onclick="showModalForAddMember()">সদস্য এন্ট্রি করুন
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table dt-responsive">
                        <thead>
                        <tr>
                            <th>জিও ক্রমিক নং</th>
                            <th>নাম</th>
                            <th>এনআইডি/জন্ম নিবন্ধন নং</th>
                            <th>জন্মতারিখ</th>
                            <th>পাসপোর্ট নং</th>
                            <th>পাসপোর্টের জন্মতারিখ</th>
                            <th>মোবাইল নং</th>
                            <th>লিঙ্গ</th>
                            <th>অ্যাকশন</th>
                        </tr>
                        </thead>
                        <tbody id="stickerVisaMembersList">
                        <tr id="noRecordRow">
                            <td colspan="6" style="text-align: center">No Pilgrims Found</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="float-left" style="padding-left: 1em;">
                <a href="{{ url('process/list')}}" id="closeForm" style="cursor: pointer;" class="btn btn-danger btn-md"
                   value="close" name="close">Close
                </a>
            </div>
            <div class="float-right" style="padding-left: 1em;">
                <button type="submit" id="submitForm" style="cursor: pointer;" class="btn btn-success btn-md"
                        value="submit" name="actionBtn">Save
                </button>
            </div>
            <div class="float-right ml-2">
                <button type="submit" class="btn btn-info btn-md cancel" value="draft" name="actionBtn"
                        id="save_as_draft">Save as Draft
                </button>
            </div>
        </div>

    </div>

    <div class="modal fade" id="stickerVisaMemberEntryModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         role="dialog" aria-labelledby="stickerVisaMemberEntryLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="font-size: 13px">
                <div class="modal-header">
                    <h5 class="modal-title" id="gridSystemModalLabel">সদস্য এন্ট্রি করুন</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true" style="font-size: 22px;">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="mt-4" id="stickerVisaMemberEntryDiv">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {!! Form::label('name', 'নাম', ['class' => 'col-md-4 ']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('','',['class' => 'form-control','placeholder' => '', 'id' => 'name']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {!! Form::label('dob', 'জন্মতারিখ ', ['class' => 'col-md-4 ']) !!}
                                    <div class="col-md-8">
                                        <div class="input-group date datetimepicker4" id="datepicker0"
                                             data-target-input="nearest">
                                            {!! Form::text('','',['class' => 'form-control input-disabled','placeholder' => '', 'id' => 'dob']) !!}
                                            <div class="input-group-append" data-target="#datepicker0"
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
                                    <div class="col-md-4">
                                        <label><input name="identity" type="radio" value="NID" checked> NID</label>
                                        <label><input name="identity" type="radio" value="BRN"> BRN</label>
                                    </div>
                                    <div class="col-md-8">
                                        {!! Form::number('','',['class' => 'form-control','placeholder' => '', 'id' => 'identity_no']) !!}
                                        <span id="identity_no_error" class="text-danger" ></span>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <div class="col-md-4">
                                        <label><input name="type_of_passport" type="radio" value="E-PASSPORT" checked>
                                            E-Passport</label>
                                        <label><input name="type_of_passport" type="radio" value="MRP"> MRP</label>
                                    </div>
                                    <div class="col-md-8">
                                        {!! Form::text('','',['class' => 'form-control','placeholder' => '', 'id' => 'passport_no']) !!}
                                        <span id="passport_no_error" class="text-danger" ></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {!! Form::label('passport_dob', 'পাসপোর্টের জন্মতারিখ', ['class' => 'col-md-4 ']) !!}
                                    <div class="col-md-8">
                                        <div class="input-group date datetimepicker4" id="datepicker1"
                                             data-target-input="nearest">
                                            {!! Form::text('','',['class' => 'form-control input-disabled','placeholder' => '', 'id' => 'passport_dob']) !!}
                                            <div class="input-group-append" data-target="#datepicker1"
                                                 data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {!! Form::label('mobile_no', 'মোবাইল নং', ['class' => 'col-md-4 ']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('','',['class' => 'form-control','placeholder' => '', 'id' => 'mobile_no']) !!}
                                        <span id="mobile_no_error" class="text-danger" ></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {!! Form::label('go_serial_no', 'জিও ক্রমিক নং', ['class' => 'col-md-4 ']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('','',['class' => 'form-control','placeholder' => '', 'id' => 'go_serial_no']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {!! Form::label('gender', 'লিঙ্গ', ['class' => 'col-md-4 ']) !!}
                                    <div class="col-md-8">
                                        <label><input name="_gender" type="radio" value="Male" checked>
                                            Male</label>
                                        <label><input name="_gender" type="radio" value="Female"> Female</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-right">
                        <button type="button" id="addToList" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

{!! Form::close() !!}


<script>
    const addRecipientBtn = document.getElementById("addParticipant");
    const teamTypeElement = document.getElementById('team_type');
    const subTeamTypeElement = document.getElementById('sub_team_type');
    const goNumberElement = document.getElementById('go_number');
    const goMemberElement = document.getElementById('go_member');
    const goRemainingMemberElement = document.getElementById('remaining_member');
    const stickerVisaListElement = document.getElementById('stickerVisaList');
    const stickerVisaExtraInfo = document.getElementById('stickerVisaExtraInfo');
    let _token = $('input[name="_token"]').val();
    addRecipientBtn.addEventListener("click", function () {

        if (validateRequiredFields('#stickerVisaInfo input, #stickerVisaInfo select')) {
            return false;
        }
        // check go number exist in db
        $.ajax({
            type: "POST",
            url: "/get-sticker-go-member",
            data: {_token, go_number: goNumberElement.value},
            dataType: 'json',
            success: function (response) {
                if (response.responseCode == 1) {
                    stickerVisaExtraInfo.classList.remove('d-none');
                    goMemberElement.value = response.stickerVisaGoMemberInfo.go_members;
                    goRemainingMemberElement.value = response.stickerVisaGoMemberInfo.remainingMembers;
                } else {
                    goRemainingMemberElement.value = goMemberElement.value;
                }
            }
        });

        teamTypeElement.classList.add('input_disabled');
        subTeamTypeElement.classList.add('input_disabled');
        goNumberElement.readOnly = true;
        goMemberElement.readOnly = true;
        stickerVisaListElement.classList.remove('d-none');
        addRecipientBtn.classList.add('d-none');

    });

    // pilgrim add
    const addToListElement = document.getElementById('addToList');
    addToListElement.addEventListener("click", function () {
        let hasError = false;
        if (validateRequiredFields('#stickerVisaMemberEntryDiv input, #stickerVisaMemberEntryDiv select')) {
            hasError = true;
        }
        let name = document.getElementById('name').value;
        let dob = document.getElementById('dob').value;
        let identity_type = document.querySelector('input[name="identity"]:checked').value;
        let identity_no = document.getElementById('identity_no').value;
        let passport_type = document.querySelector('input[name="type_of_passport"]:checked').value;
        let passport_no = document.getElementById('passport_no').value;
        let passport_dob = document.getElementById('passport_dob').value;
        let mobile_no = document.getElementById('mobile_no').value;
        let go_serial_no = document.getElementById('go_serial_no').value;
        let gender = document.querySelector('input[name="_gender"]:checked').value;

        const stickerVisaMembersListElement = document.getElementById('stickerVisaMembersList');
        let index = stickerVisaMembersListElement.children.length;

        if (identity_type === 'NID' && ![10, 17].includes(identity_no.toString().length)) {
            hasError = true;
            $('#identity_no_error').text('Please enter valid NID No');
        } else {
            $('#identity_no_error').text('');
        }

        if (passport_type === 'E-PASSPORT' && (!passport_no.match('^[A-Z]{1}[0-9]{8}$') || !passport_no.length == 9)) {
            hasError = true;
            $('#passport_no_error').text('Please enter valid Passport No');
        } else if (passport_type === 'MRP' && (!passport_no.match('^[A-Z]{2}[0-9]{7}$') || !passport_no.length == 9)) {
            hasError = true;
            $('#passport_no_error').text('Please enter valid Passport No');
        } else {
            $('#passport_no_error').text('');
        }


        if (!BdMobileValidation(mobile_no)) {
            hasError = true;
            $('#mobile_no_error').text('Please enter valid Mobile No');
        }else{
            $('#mobile_no_error').text('');
        }

        if(hasError) return false;

        let addMemberHtml = `<tr id="member_${index}">
                                <td><input class="form-control" name="go_serial_no[]" value="${go_serial_no}" readonly></td>
                                <td><input class="form-control" name="name[]" value="${name}"  readonly></td>
                                <td>
                                    <input class="form-control" name="identity_no[]" value="${identity_no}"  readonly>
                                    <input type="hidden" name="identity_type[]" value="${identity_type}" >
                                </td>
                                <td><input class="form-control" name="dob[]" value="${dob}"  readonly></td>
                                <td>
                                    <input class="form-control" name="passport_no[]" value="${passport_no}"  readonly>
                                    <input type="hidden" name="passport_type[]" value="${passport_type}" >
                                </td>
                                <td><input class="form-control" name="passport_dob[]" value="${passport_dob}"  readonly></td>
                                <td><input class="form-control" name="mobile_no[]" value="${mobile_no}"  readonly></td>
                                <td><input class="form-control" name="gender[]" value="${gender}"  readonly></td>
                                <td><button type="button" class="btn btn-danger" onclick="removeStickerVisaMember(${index})" > <i class="fa fa-trash" style="cursor: pointer" data-toggle="tooltip" title="" aria-describedby="tooltip"></i></button></td>
                            </tr>`;

        stickerVisaMembersListElement.insertAdjacentHTML('beforeend', addMemberHtml);
        // modal hide
        resetStickerVisaMemberForm();
        $("#stickerVisaMemberEntryModal").modal('hide');
    });

    function validateRequiredFields(selector) {
        const requiredClientFields = document.querySelectorAll(selector);
        let hasErrors = false;
        for (const elem of requiredClientFields) {
            if (elem.classList.contains('required') && !elem.value) {
                elem.classList.add('error');
                hasErrors = true;
            } else {
                elem.classList.remove('error');
            }
        }
        return hasErrors;
    }

    function resetStickerVisaMemberForm() {
        document.getElementById('name').value = '';
        document.getElementById('dob').value = '';
        document.querySelector('input[name="identity"][value="NID"]').checked = true;
        document.getElementById('identity_no').value = '';
        document.querySelector('input[name="type_of_passport"][value="E-PASSPORT"]').checked = true;
        document.getElementById('passport_no').value = '';
        document.getElementById('passport_dob').value = '';
        document.getElementById('identity_no').value = '';
        document.getElementById('mobile_no').value = '';
        document.getElementById('go_serial_no').value = '';
        document.querySelector('input[name="_gender"][value="Male"]').checked = true;
    }

    function removeStickerVisaMember(index) {
        $("#member_" + index).remove();
    }

    function showModalForAddMember() {
        $("#noRecordRow").remove();
        let total_members = document.getElementById('stickerVisaMembersList').children.length
        let go_member = +document.getElementById('remaining_member').value

        if (total_members >= go_member) {
            alert('You can no longer add member')
            return false;
        }
        $("#stickerVisaMemberEntryModal").modal('show');
    }

    function getSubTeamByTeamId(team_type, team_id, sub_team_type, selected_value = 0) {
        let _token = $('input[name="_token"]').val();
        if (team_id !== '') {
            $("#" + team_type).after('<span class="loading_data">Loading...</span>');
            $.ajax({
                type: "POST",
                url: "/getSubTeamData",
                data: {_token, team_id},
                dataType: 'json',
                success: function (response) {
                    let option = '<option value="">Selected</option>';
                    if (response.status == 200) {
                        if (response.data.length > 0) {
                            document.getElementById(sub_team_type).classList.add('required');
                        } else {
                            document.getElementById(sub_team_type).classList.remove('required');
                        }
                        $.each(response.data, function (index, item) {
                            if (item.id == selected_value) {
                                option += '<option value="' + item.id + '" selected>' + item.name + '</option>';
                            } else {
                                option += '<option value="' + item.id + '">' + item.name + '</option>';
                            }
                        });
                    }
                    $("#" + sub_team_type).html(option);
                    $("#" + team_type).next().hide('slow');
                }
            });
        }
    }

    function BdMobileValidation(phone){
        var reg = /(^(\+88|0088)?(01){1}[3456789]{1}(\d){8})$/;
        if (reg.test(phone)) {
            return true;
        }
        return false;
    }

    $(function () {
        var today = new Date();
        var yyyy = today.getFullYear();
        $('.datetimepicker4').datetimepicker({
            format: 'DD-MMM-YYYY',
            maxDate: 'now',
            minDate: '01/01/' + (yyyy - 110),
            ignoreReadonly: true,
        });
    });

</script>




