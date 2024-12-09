@extends('layouts.admin')


@section('content')
    <div class="form-group">
        <div class="row">
                <label for="" class="col-md-2">Select inventory type:</label>
                <div class="col-md-4">
                    {!! Form::select('inventory_type', $inventoryType, null, ['class'=>'form-control', 'placeholder'=>'Total Inventory', 'id'=>'inventory_type']) !!}
                </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div id="total_inventory">

            </div>
        </div>
    </div>

@endsection

@section('footer-script')
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.devbridge-autocomplete/1.2.24/jquery.autocomplete.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

    @include('partials.datatable-js')
    <script src="{{asset('assets/plugins/jquery.table2excel.js')}}"></script>

    <script>
        $(function () {
            $.ajax({
                type: "get",
                {{--url: "{{ url('medicine-store/get-total-inventory') }}",--}}
                url: "<?php echo env('APP_URL').'/medicine-store/get-total-inventory'?>",
                success: function (response) {
                    if(response.responseCode == 1){
                        $("#total_inventory").html(response.html)
                    }

                }
            });
        });

        function table2excel(tableName){
            $("#"+tableName).table2excel({
                exclude:".noExl",
                name:"Worksheet Name",
                filename:tableName,//do not include extension
                fileext:".xls" // file extension
            });

        }
    </script>
@endsection
