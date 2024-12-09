
@extends('layouts.admin')

@section('header-resources')
    @include('partials.datatable-css')
@endsection
@section('content')
    @include('partials.messages')
    <div class="dash-content-main">
        {{-- @include("Flight::dashboard", ['from' => 'reservation']) --}}

        <div class="border-card-block">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <span class="title-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M15.7997 2.20999C15.3897 1.79999 14.6797 2.07999 14.6797 2.64999V6.13999C14.6797 7.59999 15.9197 8.80999 17.4297 8.80999C18.3797 8.81999 19.6997 8.81999 20.8297 8.81999C21.3997 8.81999 21.6997 8.14999 21.2997 7.74999C19.8597 6.29999 17.2797 3.68999 15.7997 2.20999Z" fill="white"/>
                            <path d="M20.5 10.19H17.61C15.24 10.19 13.31 8.26 13.31 5.89V3C13.31 2.45 12.86 2 12.31 2H8.07C4.99 2 2.5 4 2.5 7.57V16.43C2.5 20 4.99 22 8.07 22H15.93C19.01 22 21.5 20 21.5 16.43V11.19C21.5 10.64 21.05 10.19 20.5 10.19ZM13 15.75H8.81L9.53 16.47C9.82 16.76 9.82 17.24 9.53 17.53C9.38 17.68 9.19 17.75 9 17.75C8.81 17.75 8.62 17.68 8.47 17.53L6.47 15.53C6.4 15.46 6.36 15.39 6.32 15.31C6.31 15.29 6.3 15.26 6.29 15.24C6.27 15.18 6.26 15.12 6.25 15.06C6.25 15.03 6.25 15.01 6.25 14.98C6.25 14.9 6.27 14.82 6.3 14.74C6.3 14.73 6.3 14.73 6.31 14.72C6.34 14.64 6.4 14.56 6.46 14.5C6.47 14.49 6.47 14.48 6.48 14.48L8.48 12.48C8.77 12.19 9.25 12.19 9.54 12.48C9.83 12.77 9.83 13.25 9.54 13.54L8.82 14.26H13C13.41 14.26 13.75 14.6 13.75 15.01C13.75 15.42 13.41 15.75 13 15.75Z" fill="white"/>
                        </svg>
                    </span>
                    <h3>List of Reservation</h3>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="ehajj-list-table">
                    <div class="list-table-head flex-space-btw">
                        <div class="list-tabmenu" role="tablist">
                            <div class="nav nav-tabs" role="presentation">
                                <button class="tab-btn nav-link active" data-toggle="tab" data-target="#tabReservationPending" type="button" role="tab">
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
                                    <span>Pending Reservation</span>
                                </button>
                                <button class="tab-btn nav-link" data-toggle="tab" data-target="#tabReservationDone" type="button" role="tab">
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
                                    <span>Reservation Done</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane show active fade" id="tabReservationPending">
                            <div class="lists-tab-content">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="list">
                                        <thead>
                                            <tr>
                                                <th>SL No.</th>
                                                <th>Tracking Number</th>
                                                <th>HL No</th>
                                                <th>Agency Name</th>
                                                <th>Pay Order <br>Received Date</th>
                                                <th>Total Pilgrim</th>
                                                <th>Reserved<br>Pilgrim</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tabReservationDone">
                            <div class="lists-tab-content">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered dt-responsive" id="done_list" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>SL No.</th>
                                                <th>Tracking<br>Number</th>
                                                <th>HL No</th>
                                                <th>Agency Name</th>
                                                <th>Pay Order <br>Received Date</th>
                                                <th>Total<br>Pilgrim</th>
                                                <th>Reserved<br>Pilgrim</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<!--content section-->

@section('footer-script')
    @include('partials.datatable-js')
<script>
    $(function () {
        $('#list').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url('reservation/get-reservation-list') }}',
                method: 'get',
                data: function (d) {
                    d._token = $('input[name="_token"]').val();
                }
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                    name: 'serial_number',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tracking_no',
                    name: 'tracking_no'
                },
                {
                    data: 'HL',
                    name: 'HL'
                },
                {
                    data: 'agency_name',
                    name: 'agency_name'
                },

                {
                    data: 'payorder_date',
                    name: 'payorder_date'
                },
                {
                    data: 'no_of_pilgrim',
                    name: 'no_of_pilgrim'
                },
                {
                    data: 'reserved_pilgrim',
                    name: 'reserved_pilgrim'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            "rowCallback": function(row, data, index) {
                var api = this.api();
                var startIndex = api.page.info().start;
                var counter = startIndex + index + 1;
                $('td:eq(0)', row).html(counter);
            },
            "aaSorting": []
        });
        $('#done_list').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url('reservation/get-reservation-done-list') }}',
                method: 'get',
                data: function (d) {
                    d._token = $('input[name="_token"]').val();
                }
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                    name: 'serial_number',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tracking_no',
                    name: 'tracking_no'
                },
                {
                    data: 'HL',
                    name: 'HL'
                },
                {
                    data: 'agency_name',
                    name: 'agency_name'
                },

                {
                    data: 'payorder_date',
                    name: 'payorder_date'
                },
                {
                    data: 'no_of_pilgrim',
                    name: 'no_of_pilgrim'
                },
                {
                    data: 'reserved_pilgrim',
                    name: 'reserved_pilgrim'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            "rowCallback": function(row, data, index) {
                var api = this.api();
                var startIndex = api.page.info().start;
                var counter = startIndex + index + 1;
                $('td:eq(0)', row).html(counter);
            },
            "aaSorting": []
        });
    });

</script>
@endsection
