@extends('layouts.admin')
@section('header-resources')
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

        .page-item.active .page-link{
            background-color: #0F6849  !important;
        }
        .btn-outline-success{
            color: #0F6849  !important;
            border-color: #0F6849  !important;
        }

        .pagination .page-item.active .page-link,
        .pagination .page-item .page-link:focus,
        .pagination .page-item .page-link:active{
            color: #ffffff !important;
        }
    </style>

    @include('partials.datatable-css')

@endsection
@section('content')
    @include('partials.messages')
    <div class="dash-content-main">
        <div class="dash-section-content">
            <div class="dash-content-inner">
                <div class="card card-magenta border border-magenta">
                    <div class="card-header" style="background-color: #0F6849 !important">
                        <h3 class="card-title pt-2 pb-2" style="color: white !important">হজযাত্রীর তালিকা</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-default float-right btn-sm" style="margin-left: 5px !important;">
                                <a href="" style="color: #0F6849 !important;">
                                    <i class="fa fa-plus-circle" style="color: #0F6849 !important;"></i>&nbsp;&nbsp;Add New Pilgrim
                                </a>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-table-head flex-space-btw">
                            <div class="list-tabmenu" role="tablist">
                                <div class="nav nav-tabs" role="presentation">
                                    <button class="tab-btn nav-link active" data-toggle="tab" data-target="#tabPreRegPilgrim" type="button" role="tab">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none">
                                            <path d="M7 1V7" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5 5L7 7L9 5" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9.5 2.5H13C13.2761 2.5 13.5 2.72386 13.5 3V11C13.5 11.2761 13.2761 11.5 13 11.5H1C0.723858 11.5 0.5 11.2761 0.5 11V3C0.5 2.72386 0.723858 2.5 1 2.5H4.5" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6 11.5L5 14" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8 11.5L9 14" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M4 14H10" stroke="#42526D" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                        <span>প্রাক-নিবন্ধিত হজযাত্রী</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane show active fade" id="tabPreRegPilgrim">
                                <div class="lists-tab-content">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="PreRegPilgrim"  style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>SL NO</th>
                                                    <th>Tracking ID</th>
                                                    <th>Name</th>
                                                    <th>Gender</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

@endsection

@section('footer-script')
    @include('partials.datatable-js')
    <script>
        $(document).ready(function() {
            $('#PreRegPilgrim').DataTable({
                iDisplayLength: 25,
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url:  '{{url("pilgrim/pre-reg/get-list")}}',
                    method:'post',
                    data: function (d) {
                        d._token = $('input[name="_token"]').val();
                    }
                },
                columns: [
                    {data: 'serial_no', name: 'serial_no'},
                    {data: 'tracking_no', name: 'tracking_no'},
                    {data: 'name_age', name: 'name_age'},
                    {data: 'group_name', name: 'group_name'},
                    {data: 'payment_status_name', name: 'payment_status_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                // "aaSorting": [],
                // "initComplete": function() {
                //     $(document.body).find('[data-toggle="tooltip"]').tooltip();
                // }
            });

        });
    </script>
@endsection
