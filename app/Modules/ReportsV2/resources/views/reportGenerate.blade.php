<?php $accessMode = ACL::getAccsessRight('reportv2');
if (!ACL::isAllowed($accessMode, 'V')) die('no access right!');
?>

@extends('layouts.admin')

@section('header-resources')
    @include('partials.datatable-css')

{{--    <link rel="stylesheet" href="{{ asset("assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css") }}" />--}}
    <link rel="stylesheet" href="{{ asset("assets/plugins/datepicker-oss/css/bootstrap-datetimepicker.min.css") }}" />
@endsection

@section('app_title',$report_data->report_title)

@section('content')
    @include('partials.messages')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-info">
                <div class="card-header">
                    <div class="float-left">
                        <h5 class="card-title"><strong><?php echo $report_data->report_title.''; ?></strong></h5>
                    </div>
                    <div class="float-right">
                        @if(ACL::getAccsessRight('reportv2','E'))
                            {!! link_to('reportv2/edit/'. $report_id,'Edit',['class' => 'btn btn-info']) !!}
                            @endif

                        @if($fav_report_info)
                            @if($fav_report_info->status == 1)
                                <btn type="button" onclick="favFunction('remove_fav', '{{$report_id}}')" class="btn btn-primary">
                                    <b>Remove From Favourite</b>
                                    &nbsp;<i class="fa fa-remove"></i>
                                </btn>
                            @elseif($fav_report_info->status == 0)
                                <btn type="button" onclick="favFunction('add_fav', '{{$report_id}}')" class="btn btn-info" >
                                    <b>Add to Favourite</b>
                                    &nbsp;<i class="fa fa-check-square-o"></i>
                                </btn>
                            @endif
                        @else
                            <btn type="button" onclick="favFunction('add_fav', '{{$report_id}}')" class="btn btn-info" >
                                <b>Add to Favourite</b>
                                &nbsp;<i class="fa fa-check-square-o"></i>
                            </btn>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                </div>
                <!-- /.panel-heading -->
                <div class="card-body">
                    @include('ReportsV2::input-form')
                </div><!-- /.box-body -->
            </div>
            <div id="report_list_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <?php
                            $report = new \App\Modules\ReportsV2\Models\ReportHelperModel();
                            $report->report_gen($report_id, $recordSet, $report_data->report_title, '');
                            //\App\Libraries\CommonFunction::report_gen($report_id, $recordSet, $report_data->report_title, '');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer-script')
    <script src="{{ asset("assets/scripts/moment.min.js") }}"></script>
    <script src="{{ asset("assets/plugins/datepicker-oss/js/bootstrap-datetimepicker.js") }}"></script>
{{--    <script src="{{ asset("assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js") }}"></script>--}}
    @include('partials.datatable-js')

    <script src="{{ asset("assets/plugins/datatable/dataTables.buttons.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/datatable/buttons.flash.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/datatable/pdfmake.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/datatable/vfs_fonts.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/datatable/buttons.html5.min.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/datatable/buttons.print.min.js") }}" type="text/javascript"></script>
    <script>

        $(function () {
            $(".datepicker").datetimepicker({
                viewMode: 'years',
                format: 'YYYY-MM-DD'
            });

        });

        function favFunction(status,id){
                    $.ajax({
                        url: "{{ url('reportv2/add-remove-favourite-ajax') }}",
                        type: 'POST',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {report_id: id, status: status},
                        success: function (response) {
                            location.reload();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                        },
                    });
        }


        $(document).ready(function () {
            // window.location.reload()
            $('.report_data_list').DataTable({
                iDisplayLength: 20,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print',{
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL' }
                ]
            });
        });
    </script>
@endsection
