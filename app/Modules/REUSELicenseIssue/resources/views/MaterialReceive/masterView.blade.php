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

<div class="dash-content-main">
    <div class="card">
        <div class="card-header bg-primary">
            Material Receive (Main stock)
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <label class="col-md-3">Source</label>
                    <div class="col-md-5">
                        {!! Form::select('source', $sources, $source??'', ['class'=>'form-control required', 'placeholder'=>'Select one', 'id'=>'source', 'disabled'=>true]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group hidden" id="supplierDiv">
                <div class="row">
                    <label class="col-md-3">Supplier Name</label>
                    <div class="col-md-5">
                        {!! Form::select('supplier', $suppliers, $supplier??'', ['class'=>'form-control required', 'placeholder'=>'Select one', 'id'=>'supplier', 'disabled'=>true]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group" id="clinicDiv">
                <div class="row">
                    <label class="col-md-3">Pharmacy Name</label>
                    <div class="col-md-5">
                        {!! Form::select('clinic', $clinics, $clinic??'', ['class'=>'form-control required', 'placeholder'=>'Select one', 'id'=>'clinic', 'disabled'=>true]) !!}
                    </div>
                </div>
            </div>
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
{{--                                @foreach($materials[0] as $keys => $title)--}}
{{--                                    <th>--}}
{{--                                        {{$title}}--}}
{{--                                    </th>--}}
{{--                                @endforeach--}}
                                    <th>S/N</th>
                                    <th>Type</th>
                                    <th>Generic Name</th>
                                    <th>Trade Name</th>
                                    <th>SKU</th>
                                    <th>Existing</th>
                                    <th>New</th>
                                    <th>Batch Name</th>
                                    <th>Expiry Date</th>
                                    <th>Last Updated</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($materials as $key => $item)
                                @if($key > 0)
                                    <tr>
                                        @foreach($item as $k => $val)
                                            <td>
                                                @if($k != 8)
                                                    {{$val}}
                                                @else
                                                    {{$val!=null?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('d-M-Y'):'N/A'}}
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

        $('#source').trigger('change');

    })

</script>
