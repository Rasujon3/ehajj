<?php $accessMode = \App\Modules\SonaliPayment\Library\SonaliPaymentACL::getAccessRight('SonaliPayment');
if (!\App\Modules\SonaliPayment\Library\SonaliPaymentACL::isAllowed($accessMode, 'V')) die('no access right!');
?>

@extends('layouts.admin')


@section('header-resources')
    @include('partials.datatable-css')
@endsection


@section('content')
    @include('partials.messages')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-magenta border border-magenta">
                <div class="card-header">

                    <div class="pull-left">
                        <h5><strong><i class="fa fa-list"></i> <strong>List IPN</strong></strong></h5>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <!-- /.panel-heading -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th>Request Ip</th>
                                <th>Trans Id</th>
                                <th>Pay Mode</th>
                                <th>Trans Time</th>
                                <th>Ref. Trans No</th>
                                <th>Trans Status</th>
                                <th>Trans Amount</th>
                                <th>Pay Amount</th>
                                <th>Request Status</th>
                                <th width="9%">Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                </div><!-- /.panel-body -->
            </div><!-- /.panel -->
        </div><!-- /.col-lg-12 -->
    </div>

@endsection

@section('footer-script')
    @include('partials.datatable-js')

    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <script>
        $(function () {
            $('#list').DataTable({
                processing: true,
                serverSide: true,
                iDisplayLength: 10,
                ajax: {
                    url: '{{url("ipn/get-list")}}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'request_ip', name: 'request_ip'},
                    {data: 'transaction_id', name: 'transaction_id'},
                    {data: 'pay_mode_code', name: 'pay_mode_code'},
                    {data: 'trans_time', name: 'trans_time'},
                    {data: 'ref_tran_no', name: 'ref_tran_no'},
                    {data: 'trans_status', name: 'trans_status'},
                    {data: 'trans_amount', name: 'trans_amount'},
                    {data: 'pay_amount', name: 'pay_amount'},
                    {data: 'is_authorized_request', name: 'is_authorized_request'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": []
            });
            t.on('order.dt search.dt', function () {
                t.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
        });

    </script>
@endsection
