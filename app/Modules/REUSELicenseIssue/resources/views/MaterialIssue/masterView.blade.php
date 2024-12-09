<?php
$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);

$prefix = '';
if ($type[0] == 17) {
    //$prefix = 'client';
    $prefix = 'client';
}

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
                    <label class="col-md-3">Issue For</label>
                    <div class="col-md-5">
                        {!! Form::select('clinic', $clinics, $clinic??'', ['class'=>'form-control', 'placeholder'=>'Select one', 'id'=>'clinic', 'disabled'=>true]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label class="col-md-3">Requisition By</label>
                    <div class="col-md-5">
                        {{$requisition_by}}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label class="col-md-3">Scan Copy</label>
                    <div class="col-md-3">
                        <a class="btn btn-default btn-sm" href="{{asset($scan_copy)}}" target="_blank"><i class="fa fa-eye"></i> View scan copy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-primary">
            Issued Materials
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
                                    <th>Trade Name</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Requisition</th>
{{--                                    <th>Requisition By</th>--}}
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
