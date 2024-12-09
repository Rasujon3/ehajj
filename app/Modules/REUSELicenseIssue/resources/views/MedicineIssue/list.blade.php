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

        #goList {
            text-align: center; /* Center align the entire table */
        }

        #goList th,
        #goList td {
            text-align: center; /* Center align header and cell content */
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
                        <h3 class="card-title pt-2 pb-2" style="color: white !important">ড্রাফট ঔষধ এর তালিকা</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-table-head flex-space-btw">
                            <div class="list-tabmenu" role="tablist">
                                <div class="nav nav-tabs" role="presentation">
                                    <button class="tab-btn nav-link active" data-toggle="tab" data-target="#tabReceivedDraftMedicine" type="button" role="tab">
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
                                        <span>Received Draft Medicine</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane show active fade" id="tabReceivedDraftMedicine">
                                <div class="lists-tab-content">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="draftList"  style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>SL NO</th>
                                                    <th>Medicine NO</th>
                                                    <th>Image Url</th>
                                                    <th>Status</th>
                                                    <th>Modified Date</th>
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
    <script src="{{ asset("assets/scripts/moment.js") }}"></script>
    <script src="{{ asset("assets/scripts/bootstrap-datetimepicker.js") }}"></script>
    <script src="{{ asset("assets/plugins/datepicker-oss/js/bootstrap-datetimepicker.js") }}"></script>
    <script>
        $(document).ready(function() {

            $('#draftList').DataTable({
                processing: true,
                serverSide: true,
                "bDestroy": true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    url: '/medicine-draft/list',
                    method: 'get',
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'image_url',
                        name: 'image_url',
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: 'issue_checker',
                        name: 'issue_checker',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            let table = $('#draftList').DataTable();
            $('#draftList').on('click', '.open-btn', function(e) {
                e.preventDefault();

                let rowId = $(this).data('row-id');
                let row = $(this).closest('.open-btn');
                $(row).css('background-color', '#ffae00');

                window.open($(this).attr('href'), '_blank');
            });
        });
    </script>

@endsection
