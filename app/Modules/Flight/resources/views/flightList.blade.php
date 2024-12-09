<?php
$user_type = Auth::user()->user_type;
$type = explode('x', $user_type);
$check_association_from = checkCompanyAssociationForm();
$bscicUsers = getBscicUser();
$is_eligibility = 0;
if ($user_type == '5x505') {
    $is_eligibility = \App\Libraries\CommonFunction::checkEligibility();
}
?>

@extends('layouts.admin')
@section('header-resources')
    @include('partials.datatable-css')
@endsection

@section('content')
    <style>
        /* #flightListTable_info.dataTables_info {
            margin-top: 5px;
            display: inline-block;
            width: 50%;
        } */

        /* #flightListTable_paginate.dataTables_paginate {
            margin-top: 5px;
            display: inline-block;
            width: 50%;
        } */
        #flightListTable_paginate.dataTables_paginate .pagination {
            justify-content: flex-end;
        }

        .flight-list-btns {
            padding: 15px 15px;
            display: inline-block;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #F0F0F0;
        }
        .flightBtn {
            border: 1px solid #0F6849;
            padding: 8px 15px;
            background-color: #fff;
            color: #000;
            margin-right: 5px;
        }
        .active {
            background-color: #0F6849;
            color: #fff;
        }


    </style>
    <div class="dash-content-main">
        @if (in_array($user_type, ['4x401', '6x606', '6x607']))
            @include("Flight::dashboard", ['from' => 'flightDashboard'])
        @endif
        <div class="border-card-block">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <span class="title-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 26 26" fill="none">
                            <path d="M20.9041 15.0465L17.4667 11.3003L16.6981 10.473C16.5849 10.3345 16.5431 10.0856 16.6169 9.92139L17.8699 7.12973C18.2631 6.25391 18.0822 4.92313 17.4778 4.16954C17.2655 3.90984 16.9371 3.76242 16.6019 3.77642C15.6463 3.82975 14.5276 4.58816 14.1345 5.46397L12.8814 8.25563C12.8077 8.41985 12.594 8.5541 12.4153 8.56157L6.21182 8.46254C5.53059 8.44175 4.74181 8.95363 4.46334 9.574L3.92279 10.7782C3.57471 11.5537 3.9866 12.199 4.84655 12.2123L10.3017 12.2933C10.7272 12.2979 10.9331 12.6206 10.757 13.0129L10.3025 14.0255L9.56536 15.6677C9.47117 15.8775 9.21743 16.1253 9.00594 16.2058L5.93524 17.3814C5.61801 17.5021 5.32519 17.8858 5.28631 18.241L5.13992 19.6661C5.06253 20.278 5.55911 20.8078 6.17554 20.7776L9.16585 19.9276C9.62122 19.7922 10.1595 20.0338 10.361 20.4641L11.7132 23.2633C12.1045 23.7349 12.8263 23.7629 13.2503 23.3066L14.2178 22.2501C14.4533 21.9941 14.5495 21.5111 14.4288 21.1939L13.2664 18.1182C13.1769 17.9026 13.1934 17.5483 13.2876 17.3385L14.4792 14.6836C14.6553 14.2913 15.02 14.2358 15.3194 14.5456L19.0056 18.5678C19.5871 19.2015 20.3429 19.0804 20.691 18.305L21.2316 17.1007C21.51 16.4803 21.3683 15.5508 20.9041 15.0465Z" fill="white"/>
                        </svg>
                    </span>
                    <h3>Flight List for {{ $hajjSession['caption'] }}</h3>
                </div>

                <div class="title-btn-group">
                    <a href="{{URL::to('/flight/create-flight')}}" class="list-btn-white">
                        <span class="list-btn-icon flex-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5001 10.4C7.93015 10.4 9.9001 8.43006 9.9001 6.00001C9.9001 3.56995 7.93015 1.60001 5.5001 1.60001C3.07005 1.60001 1.1001 3.56995 1.1001 6.00001C1.1001 8.43006 3.07005 10.4 5.5001 10.4ZM6.0501 4.35001C6.0501 4.04625 5.80385 3.80001 5.5001 3.80001C5.19634 3.80001 4.9501 4.04625 4.9501 4.35001V5.45001H3.8501C3.54634 5.45001 3.3001 5.69625 3.3001 6.00001C3.3001 6.30376 3.54634 6.55001 3.8501 6.55001H4.9501V7.65001C4.9501 7.95376 5.19634 8.20001 5.5001 8.20001C5.80385 8.20001 6.0501 7.95376 6.0501 7.65001V6.55001H7.1501C7.45386 6.55001 7.7001 6.30376 7.7001 6.00001C7.7001 5.69625 7.45386 5.45001 7.1501 5.45001H6.0501V4.35001Z" fill="#0F6849"/>
                            </svg>
                        </span>
                        <span class="list-btn-text">Create Flight</span>
                    </a>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="ehajj-list-table">
                    
                    <div class="table-responsive">
                        <div class="flight-list-btns">
                            <button class="flightBtn" id="departureFlight">Departure Flight</button>
                            <button class="flightBtn" id="arrivalFlight">Arrival Flight</button>
                        </div>
                        {{-- @if (!empty($flightList)) --}}
                            <table id="flightListTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>SL No.</th>
                                        <th>Departure<br>Time</th>
                                        <th>Flight<br>Code</th>
                                        <th>City</th>
                                        <th>Arrival Time</th>
                                        <th>Status</th>
                                        <th>Arrived<br>Pilgrim</th>
                                        {{-- <th>GACA Reservation No</th> --}}
                                        <th>Departed<br>Pilgrim</th>
                                        {{-- <th>Departed Pilgrim (CAAB)</th> --}}
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table> 
                        {{-- @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection

@section('footer-script')
    {{-- <script src="{{ asset('assets/custom/js/datatables.min.js') }}"></script> --}}
    @include('partials.datatable-js')
    <script>

        $(document).ready(function() {

            function getFlight(type='departure') {
                if ($.fn.DataTable.isDataTable('#flightListTable')) {
                    $('#flightListTable').DataTable().destroy();
                    $('#flightListTable tbody').empty();
                }
                $('#flightListTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ url('/flight/get-flight-list') }}',
                        method: 'get',
                        data: function (d) {
                            d._token = $('input[name="_token"]').val();
                            d.type= type;
                        }
                    },
                    columns: [
                        {
                            data: null,
                            name: 'serial_number',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'departure_time',
                            name: 'departure_time',
                            render: function(data, type, row, meta) {
                                if (type === 'display') {
                                    return data.replace(/\n/g, '<br>');
                                }
                                return data;
                            }
                        },
                        {
                            data: 'flight_code',
                            name: 'flight_code'
                        },

                        {
                            data: 'city',
                            name: 'city'
                        },
                        {
                            data: 'arrival_time',
                            name: 'arrival_time',
                            render: function(data, type, row, meta) {
                                if (type === 'display') {
                                    return data.replace(/\n/g, '<br>');
                                }
                                return data;
                            }
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'arrived_pilgrim',
                            name: 'arrived_pilgrim'
                        },
                        {
                            data: 'deported_pilgrim',
                            name: 'deported_pilgrim'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    "rowCallback": function (row, data, index) {
                        var api = this.api();
                        var startIndex = api.page.info().start;
                        var counter = startIndex + index + 1;
                        $('td:eq(0)', row).html(counter);
                    },
                    "aaSorting": [],
                    "language": {
                        "paginate": {
                            "previous": '<i class="fas fa-chevron-left"></i>',
                            "next": '<i class="fas fa-chevron-right"></i>',
                        }
                    }
                });
            }
            
            $('#departureFlight').on('click', function() {
                $(this).addClass('active');
                $('#arrivalFlight').removeClass('active');
                getFlight('departure');
            })
            $('#arrivalFlight').on('click', function() {
                $(this).addClass('active');
                $('#departureFlight').removeClass('active');
                getFlight('arrival');
            })

            $('#departureFlight').trigger('click');           
            
        });

        
    </script>
@endsection
