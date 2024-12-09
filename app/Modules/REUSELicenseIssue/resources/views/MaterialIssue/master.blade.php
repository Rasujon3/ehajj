<?php
$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);

$prefix = '';
if ($type[0] == 17) {
    $prefix = 'client';
}

?>
<style>
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
    <div class="card">
        <div class="card-header bg-primary">
            Material Issue (Main stock)
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <label class="col-md-3 required-star">Issue For</label>
                    <div class="col-md-5">
                        {!! Form::select('clinic', $clinics, '', ['class'=>'form-control required', 'placeholder'=>'Select one', 'id'=>'clinic']) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label class="col-md-3 required-star">Requisition By</label>
                    <div class="col-md-5">
                        {!! Form::text('requisition_by', '', ['class'=>'form-control required', 'placeholder'=>'Enter name', 'id'=>'requisition_by']) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label class="col-md-3 required-star">File</label>
                    <div class="col-md-3">
                        <input type="file" name="medical_products" class="form-control-file required" id="medical_products"
                               accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        <span><a class="text-xs"
                                 href="{{ asset('/sample/MaterialsIssue.xlsx') }}">Sample File</a></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label class="col-md-3 required-star">Scan Copy</label>
                    <div class="col-md-3">
                        <input type="file" name="scan_copy" class="form-control-file required" id="scan_copy"
                               accept="image/*">
                    </div>
                </div>
            </div>

            @if ( in_array($user_type,['17x173']) )
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <a class="btn btn-info btn-sm" style="margin-left:10px"
                               href="{{ url($prefix.'/process/list/'. Encryption::encodeId(5)) }}">
                                বন্ধ করুন
                            </a>
                        </div>
                        <div class="col-sm-6 text-right" style="padding-right:35px">
{{--                            <button class="btn btn-primary btn-sm" name="actionBtn" value="draft">সংরক্ষণ এবং খসড়া--}}
{{--                            </button>--}}
                            <button class="btn btn-success btn-sm" name="actionBtn" id="submitBtn" value="submit">সাবমিট</button>
                        </div>
                    </div>
                </div>

            @endif
        </div>
    </div>
    <div id="excelPreview">

    </div>
</div>

{!! Form::close() !!}


<script>

    $(document).ready(function () {
        $("#application_form").validate({
            errorPlacement: function () {
                return true;
            },
        });

    })

    $('#medical_products').on('change', function(){
        var file_data = $('#medical_products').prop('files')[0];
        var form_data = new FormData();
        form_data.append('file_upload', file_data);
        form_data.append('type', 'issue');
        $.ajax({
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            url: "<?php echo env('APP_URL').'/preview-excel-data'?>",
            {{--url: "<?php echo url('/preview-excel-data')?>",--}}
            success: function (response) {

                if (response.responseCode == 1) {
                    $('#excelPreview').html(response.html)
                    if (response.negativeInventory > 0){
                        $('#submitBtn').addClass('hidden');
                    }else{
                        $('#submitBtn').removeClass('hidden');
                    }
                }

            }
        });
    })

</script>
