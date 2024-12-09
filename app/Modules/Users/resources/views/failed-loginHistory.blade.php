<?php
$accessMode = ACL::getAccsessRight('user');
if (!ACL::isAllowed($accessMode, 'V')) {
    abort('400', 'You have no access right!. Contact with system admin for more information.');
}
?>

@extends('layouts.admin')

@section('header-resources')
    @include('partials.datatable-css')
@endsection

@section('content')


    @include('partials.messages')

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-info">
                <div class="card-header">
                    <div class="pull-left">
                        <h5 class="card-title"><strong>{{ trans('messages.Failed') }} ({{ $user[0]->user_full_name }}, {{ $decodedUserEmail }}
                                , {{ $user[0]->user_mobile }})</strong></h5>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <!-- /.panel-heading -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered dt-responsive " cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th>Remote Address</th>
                                <th>Failed Login Time</th>
                                <th>Action</th>
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


    <div class="container">
        <!-- Modal -->
        {!! Form::open(array('url' => 'users/failed-login-data-resolved/','method' => 'post',
                  'id'=> 'formId')) !!}
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Resolve Failed login</h4>
                    </div>
                    <input type="hidden" id="failed_login_id" name="failed_login_id">
                    <div class="modal-body">
                        <b><span class="required-star">Remarks:</span></b> <input type="text" name="remarks"
                                                                                  class="form-control required">
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-default" value="Submit">
                        {{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

{{--    <input type="hidden" name="email" id="email" value="{{$email}}">--}}
@endsection <!--content section-->


@section('footer-script')
    @include('partials.datatable-js')
    <script src="{{ asset("assets/scripts/jquery.validate.min.js") }}"></script>

    <script>
        $(function () {
            var $email = $('#email').val();
            $('#list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{url('users/get-failed-login-data/')}}',
                    method: 'post',
                    data: function (d) {
                        d.email = $email;
                        d._token = $('input[name="_token"]').val();
                    }
                },
                columns: [
                    {data: 'remote_address', name: 'remote_address'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "aaSorting": []
            });
        });

    </script>
    <script>
        var _token = $('input[name="_token"]').val();

        $(document).ready(function () {
            $("#formId").validate({
                errorPlacement: function () {
                    return false;
                }
            });
        });

        function myFunction(id) {
            document.getElementById("failed_login_id").value = id;
        }
    </script>


@endsection <!--- footer-script--->

