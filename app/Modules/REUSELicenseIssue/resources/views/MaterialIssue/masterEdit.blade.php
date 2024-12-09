<?php
$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);

$prefix = '';
if ($type[0] == 17) {
    //$prefix = 'client';
    $prefix = 'client';
}
$totalArrEle = count($materials[0]);

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
            Material Receive (Main stock)
        </div>
        @php
            $process_typeid = \App\Libraries\Encryption::encodeId($process_type_id);
            $app_id = \App\Libraries\Encryption::encodeId($app_id);

        @endphp
        <input type="hidden" id="id" value="{!! $process_typeid !!}"/>
        <input type="hidden" name="app_id" value="{{ $app_id }}"/>
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <label class="col-md-3 required-star">Source</label>
                    <div class="col-md-5">
                        {!! Form::select('source', $sources, $source??'', ['class'=>'form-control required', 'placeholder'=>'Select one', 'id'=>'source']) !!}
                    </div>
                </div>
            </div>
            <div class="form-group hidden" id="supplierDiv">
                <div class="row">
                    <label class="col-md-3 required-star">Supplier Name</label>
                    <div class="col-md-5">
                        {!! Form::select('supplier', $suppliers, $supplier??'', ['class'=>'form-control required', 'placeholder'=>'Select one', 'id'=>'supplier']) !!}
                    </div>
                </div>
            </div>
            <div class="form-group hidden" id="clinicDiv">
                <div class="row">
                    <label class="col-md-3 required-star">Clinic Name</label>
                    <div class="col-md-5">
                        {!! Form::select('clinic', $clinics, $clinic??'', ['class'=>'form-control required', 'placeholder'=>'Select one', 'id'=>'clinic']) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label class="col-md-3">File upload</label>
                    <div class="col-md-3">
                        <input type="file" name="medical_products" class="form-control-file"
                               accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        <span><a class="text-xs pull-left"
                                 href="{{ asset('/sample/MaterialsReceive.xlsx') }}">Sample File</a></span>
                    </div>
                </div>
            </div>

            @if ( in_array($user_type,['17x173']) )
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <a class="btn btn-info btn-sm" style="margin-left:10px"
                               href="{{ url($prefix.'/process/list/'. Encryption::encodeId(4)) }}">
                                বন্ধ করুন
                            </a>
                        </div>
                        <div class="col-sm-6 text-right" style="padding-right:35px">
                            <button class="btn btn-primary btn-sm" name="actionBtn" value="draft">সংরক্ষণ এবং খসড়া
                            </button>
                            <button class="btn btn-success btn-sm" name="actionBtn" value="submit">সাবমিট</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-primary">
            Uploaded Materials
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <caption>List of materials</caption>
                            <thead>
                            <tr>
                                @foreach($materials[0] as $keys => $title)
                                    <th>
                                        @if($keys < $totalArrEle-1)
                                            {{$title}}
                                        @else
                                            {{$title}}
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($materials as $key => $item)
                                @if($key > 0)
                                    <tr>
                                        @foreach($item as $k => $val)
                                            <td>
                                                @if($k < $totalArrEle-1)
                                                    {{$val}}
                                                @else
                                                    {{\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('d-M-Y')}}
                                                @endif
                                            </td>

                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{!! Form::close() !!}


<script>

    $('#source').on('change', function () {
        if ($(this).val() == 1) {
            $('#supplierDiv').removeClass('hidden');
            $('#clinicDiv').addClass('hidden');
            $('#clinic').val('');
        } else if ($(this).val() == 3) {
            $('#clinicDiv').removeClass('hidden');
            $('#supplierDiv').addClass('hidden');
            $('#supplier').val('');
        } else {
            $('#supplierDiv').addClass('hidden');
            $('#supplier').val('');
            $('#clinicDiv').addClass('hidden');
            $('#clinic').val('');
        }
    })

    $(document).ready(function () {
        $("#application_form").validate({
            errorPlacement: function () {
                return true;
            },
        });

        $('#source').trigger('change');

    })

</script>
