<div class="dash-feature-cards-sec">
    <div class="row">
        {{-- Total Flight --}}
        @if (in_array($from, ['flightDashboard', 'payOrderReceived', 'reservation']))
            <div class="col-xl-3 col-lg-4 col-md-6 dash-feature-col">
                <a href="#" class="feature-cards-item ftr-cards-style-1">
                    <span class="ftr-cards-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                            <path d="M20.0501 11.13L15.3801 9.12L14.3401 8.68C14.1801 8.6 14.0401 8.39 14.0401 8.21V5.15C14.0401 4.19 13.3301 3.05 12.4701 2.61C12.1701 2.46 11.8101 2.46 11.5101 2.61C10.6601 3.05 9.95006 4.2 9.95006 5.16V8.22C9.95006 8.4 9.81006 8.61 9.65006 8.69L3.95006 11.14C3.32006 11.4 2.81006 12.19 2.81006 12.87V14.19C2.81006 15.04 3.45006 15.46 4.24006 15.12L9.25006 12.96C9.64006 12.79 9.96006 13 9.96006 13.43V14.54V16.34C9.96006 16.57 9.83006 16.9 9.67006 17.06L7.35006 19.39C7.11006 19.63 7.00006 20.1 7.11006 20.44L7.56006 21.8C7.74006 22.39 8.41006 22.67 8.96006 22.39L11.3401 20.39C11.7001 20.08 12.2901 20.08 12.6501 20.39L15.0301 22.39C15.5801 22.66 16.2501 22.39 16.4501 21.8L16.9001 20.44C17.0101 20.11 16.9001 19.63 16.6601 19.39L14.3401 17.06C14.1701 16.9 14.0401 16.57 14.0401 16.34V13.43C14.0401 13 14.3501 12.8 14.7501 12.96L19.7601 15.12C20.5501 15.46 21.1901 15.04 21.1901 14.19V12.87C21.1901 12.19 20.6801 11.4 20.0501 11.13Z" fill="#59A0DD"/>
                        </svg>
                    </span>
                    <div class="ftr-cards-desc">
                        <p>Total Flight</p>
                        {{-- <h3>{{ isset($airlinesInfo['totalFlights']) ? $airlinesInfo['totalFlights'] : '0' }}</h3> --}}
                        <h3 id="totalFlight">0</h3>
                    </div>
                </a>
            </div>
        @endif
        
        {{-- Total Seat --}}
        @if (in_array($from, ['flightDashboard', 'payOrderReceived', 'reservation']))
            <div class="col-xl-3 col-lg-4 col-md-6 dash-feature-col">
                <a href="#" class="feature-cards-item ftr-cards-style-2">
                    <span class="ftr-cards-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
                            <path d="M5.13818 9.65V7.5C5.13818 6.4 6.03818 5.5 7.13818 5.5H17.1382C18.2382 5.5 19.1382 6.4 19.1382 7.5V9.66C17.9782 10.07 17.1382 11.17 17.1382 12.47V14.5H7.13818V12.46C7.13818 11.17 6.29818 10.06 5.13818 9.65ZM20.1382 10.5C19.0382 10.5 18.1382 11.4 18.1382 12.5V15.5H6.13818V12.5C6.13818 11.9696 5.92747 11.4609 5.5524 11.0858C5.17732 10.7107 4.66862 10.5 4.13818 10.5C3.60775 10.5 3.09904 10.7107 2.72397 11.0858C2.3489 11.4609 2.13818 11.9696 2.13818 12.5V17.5C2.13818 18.6 3.03818 19.5 4.13818 19.5V21.5H6.13818V19.5H18.1382V21.5H20.1382V19.5C21.2382 19.5 22.1382 18.6 22.1382 17.5V12.5C22.1382 11.4 21.2382 10.5 20.1382 10.5Z" fill="#59A0DD"/>
                        </svg>
                    </span>
                    <div class="ftr-cards-desc">
                        <p>Total Seat</p>
                        {{-- <h3>{{ isset($airlinesInfo['totalSeat']) ? $airlinesInfo['totalSeat'] : '0' }}</h3> --}}
                        <h3 id="totalSeat">0</h3>
                    </div>
                </a>
            </div>
        @endif

        {{-- Reservations --}}
        @if (in_array($from, ['flightDashboard', 'payOrderReceived', 'reservation']))
            <div class="col-xl-3 col-lg-4 col-md-6 dash-feature-col">
                <a href="#" class="feature-cards-item ftr-cards-style-3">
                    <span class="ftr-cards-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                            <g clip-path="url(#clip0_2026_2498)">
                                <path d="M23.3781 6.32748L23.142 4.98845C22.9513 3.90956 21.7967 3.21121 20.5622 3.42873L4.50195 6.26083H22.6943C22.9291 6.26083 23.1576 6.28448 23.3781 6.32748Z" fill="#85C16D"/>
                                <path d="M22.6942 7.93347H1.85586C0.984129 7.93347 0.274902 8.50637 0.274902 9.21028V19.3342C0.274902 20.0381 0.984129 20.611 1.85586 20.611H22.6942C23.5657 20.611 24.2749 20.0381 24.2749 19.3342V9.21028C24.2748 8.50611 23.5657 7.93347 22.6942 7.93347ZM7.70774 19.3108H6.03509V17.8712H7.70774V19.3108ZM7.70774 16.4315H6.03509V14.992H7.70774V16.4315ZM7.70774 13.5524H6.03509V12.1127H7.70774V13.5524ZM7.70774 10.6731H6.03509V9.23343H7.70774V10.6731ZM19.9975 12.4062L18.7104 13.6103L19.8701 17.484L19.2205 18.1335C19.1492 18.2046 19.0337 18.2046 18.9624 18.1335L17.2337 14.9917L15.6438 16.5599L15.897 17.7957L15.4924 18.2006C15.4387 18.2543 15.3514 18.2543 15.2982 18.2008L13.008 15.9106C12.9542 15.8569 12.9542 15.7699 13.008 15.7164L13.4125 15.3113L14.6257 15.5588L16.1949 13.9628L13.0751 12.2455C13.0038 12.1747 13.0038 12.0592 13.0746 11.9879L13.7248 11.3383L17.5728 12.4902L18.7855 11.1939C19.1398 10.8396 19.6984 10.824 20.0331 11.1588C20.3672 11.4933 20.3513 12.052 19.9975 12.4062Z" fill="#85C16D"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_2026_2498">
                                    <rect width="24" height="24" fill="white" transform="translate(0.275879)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <div class="ftr-cards-desc">
                        <p>Reservations</p>
                        {{-- <h3>{{ isset($airlinesInfo['totalTicketReservation']) ? $airlinesInfo['totalTicketReservation'] : '0' }}</h3> --}}
                        <h3 id="reservations">0</h3>
                    </div>
                </a>
            </div>
        @endif

        {{-- Reservations Done (Private) --}}
        {{-- @if (in_array($from, ['flightDashboard']))
            <div class="col-xl-3 col-lg-4 col-md-6 dash-feature-col">
                <a href="#" class="feature-cards-item ftr-cards-style-3">
                    <span class="ftr-cards-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                            <g clip-path="url(#clip0_2026_2498)">
                                <path d="M23.3781 6.32748L23.142 4.98845C22.9513 3.90956 21.7967 3.21121 20.5622 3.42873L4.50195 6.26083H22.6943C22.9291 6.26083 23.1576 6.28448 23.3781 6.32748Z" fill="#85C16D"/>
                                <path d="M22.6942 7.93347H1.85586C0.984129 7.93347 0.274902 8.50637 0.274902 9.21028V19.3342C0.274902 20.0381 0.984129 20.611 1.85586 20.611H22.6942C23.5657 20.611 24.2749 20.0381 24.2749 19.3342V9.21028C24.2748 8.50611 23.5657 7.93347 22.6942 7.93347ZM7.70774 19.3108H6.03509V17.8712H7.70774V19.3108ZM7.70774 16.4315H6.03509V14.992H7.70774V16.4315ZM7.70774 13.5524H6.03509V12.1127H7.70774V13.5524ZM7.70774 10.6731H6.03509V9.23343H7.70774V10.6731ZM19.9975 12.4062L18.7104 13.6103L19.8701 17.484L19.2205 18.1335C19.1492 18.2046 19.0337 18.2046 18.9624 18.1335L17.2337 14.9917L15.6438 16.5599L15.897 17.7957L15.4924 18.2006C15.4387 18.2543 15.3514 18.2543 15.2982 18.2008L13.008 15.9106C12.9542 15.8569 12.9542 15.7699 13.008 15.7164L13.4125 15.3113L14.6257 15.5588L16.1949 13.9628L13.0751 12.2455C13.0038 12.1747 13.0038 12.0592 13.0746 11.9879L13.7248 11.3383L17.5728 12.4902L18.7855 11.1939C19.1398 10.8396 19.6984 10.824 20.0331 11.1588C20.3672 11.4933 20.3513 12.052 19.9975 12.4062Z" fill="#85C16D"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_2026_2498">
                                    <rect width="24" height="24" fill="white" transform="translate(0.275879)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <div class="ftr-cards-desc">
                        <p>Reservations Done <small>(Private)</small></p>
                        <h3>{{ isset($airlinesInfo['reservationsDone']) ? $airlinesInfo['reservationsDone'] : '0' }}</h3>
                    </div>
                </a>
            </div>
        @endif --}}

        {{-- To do --}}
        {{-- Reservations (Due) --}}
        {{-- @if (in_array($from, ['flightDashboard']))
            <div class="col-xl-3 col-lg-4 col-md-6 dash-feature-col">
                <a href="#" class="feature-cards-item ftr-cards-style-4">
                    <span class="ftr-cards-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                            <g clip-path="url(#clip0_2026_4357)">
                                <path d="M23.5163 6.32748L23.2802 4.98845C23.0895 3.90956 21.9349 3.21121 20.7004 3.42873L4.64014 6.26083H22.8325C23.0672 6.26083 23.2958 6.28448 23.5163 6.32748Z" fill="#33DADA"/>
                                <path d="M22.8324 7.93347H1.99405C1.12231 7.93347 0.413086 8.50637 0.413086 9.21028V19.3342C0.413086 20.0381 1.12231 20.611 1.99405 20.611H22.8324C23.7039 20.611 24.4131 20.0381 24.4131 19.3342V9.21028C24.413 8.50611 23.7039 7.93347 22.8324 7.93347ZM7.84592 19.3108H6.17327V17.8712H7.84592V19.3108ZM7.84592 16.4315H6.17327V14.992H7.84592V16.4315ZM7.84592 13.5524H6.17327V12.1127H7.84592V13.5524ZM7.84592 10.6731H6.17327V9.23343H7.84592V10.6731ZM20.1357 12.4062L18.8485 13.6103L20.0082 17.484L19.3587 18.1335C19.2874 18.2046 19.1719 18.2046 19.1006 18.1335L17.3719 14.9917L15.782 16.5599L16.0352 17.7957L15.6306 18.2006C15.5768 18.2543 15.4896 18.2543 15.4363 18.2008L13.1462 15.9106C13.0924 15.8569 13.0924 15.7699 13.1462 15.7164L13.5507 15.3113L14.7639 15.5588L16.3331 13.9628L13.2133 12.2455C13.142 12.1747 13.142 12.0592 13.2128 11.9879L13.863 11.3383L17.711 12.4902L18.9237 11.1939C19.278 10.8396 19.8366 10.824 20.1713 11.1588C20.5054 11.4933 20.4895 12.052 20.1357 12.4062Z" fill="#33DADA"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_2026_4357">
                                    <rect width="24" height="24" fill="white" transform="translate(0.414062)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <div class="ftr-cards-desc">
                        <p>Reservations <small>(Due)</small></p>
                        <h3>24</h3>
                    </div>
                </a>
            </div>
        @endif --}}

        {{-- Available Seat --}}
        @if (in_array($from, ['flightDashboard', 'payOrderReceived', 'reservation']))
            <div class="col-xl-3 col-lg-4 col-md-6 dash-feature-col">
                <a href="#" class="feature-cards-item ftr-cards-style-5">
                    <span class="ftr-cards-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
                            <path d="M5.41406 9.65V7.5C5.41406 6.4 6.31406 5.5 7.41406 5.5H17.4141C18.5141 5.5 19.4141 6.4 19.4141 7.5V9.66C18.2541 10.07 17.4141 11.17 17.4141 12.47V14.5H7.41406V12.46C7.41406 11.17 6.57406 10.06 5.41406 9.65ZM20.4141 10.5C19.3141 10.5 18.4141 11.4 18.4141 12.5V15.5H6.41406V12.5C6.41406 11.9696 6.20335 11.4609 5.82828 11.0858C5.4532 10.7107 4.9445 10.5 4.41406 10.5C3.88363 10.5 3.37492 10.7107 2.99985 11.0858C2.62478 11.4609 2.41406 11.9696 2.41406 12.5V17.5C2.41406 18.6 3.31406 19.5 4.41406 19.5V21.5H6.41406V19.5H18.4141V21.5H20.4141V19.5C21.5141 19.5 22.4141 18.6 22.4141 17.5V12.5C22.4141 11.4 21.5141 10.5 20.4141 10.5Z" fill="#EA5D62"/>
                        </svg>
                    </span>
                    <div class="ftr-cards-desc">
                        <p>Available Seat</p>
                        {{-- <h3>{{ isset($airlinesInfo['availableSeat']) ? $airlinesInfo['availableSeat'] : '0' }}</h3> --}}
                        <h3 id="availableSeat">0</h3>
                    </div>
                </a>
            </div>
        @endif

        {{-- PNL Due --}}
        {{-- <div class="col-xl-3 col-lg-4 col-md-6 dash-feature-col">
            <a href="#" class="feature-cards-item ftr-cards-style-6">
                <span class="ftr-cards-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                        <path d="M20.0501 11.13L15.3801 9.12L14.3401 8.68C14.1801 8.6 14.0401 8.39 14.0401 8.21V5.15C14.0401 4.19 13.3301 3.05 12.4701 2.61C12.1701 2.46 11.8101 2.46 11.5101 2.61C10.6601 3.05 9.95006 4.2 9.95006 5.16V8.22C9.95006 8.4 9.81006 8.61 9.65006 8.69L3.95006 11.14C3.32006 11.4 2.81006 12.19 2.81006 12.87V14.19C2.81006 15.04 3.45006 15.46 4.24006 15.12L9.25006 12.96C9.64006 12.79 9.96006 13 9.96006 13.43V14.54V16.34C9.96006 16.57 9.83006 16.9 9.67006 17.06L7.35006 19.39C7.11006 19.63 7.00006 20.1 7.11006 20.44L7.56006 21.8C7.74006 22.39 8.41006 22.67 8.96006 22.39L11.3401 20.39C11.7001 20.08 12.2901 20.08 12.6501 20.39L15.0301 22.39C15.5801 22.66 16.2501 22.39 16.4501 21.8L16.9001 20.44C17.0101 20.11 16.9001 19.63 16.6601 19.39L14.3401 17.06C14.1701 16.9 14.0401 16.57 14.0401 16.34V13.43C14.0401 13 14.3501 12.8 14.7501 12.96L19.7601 15.12C20.5501 15.46 21.1901 15.04 21.1901 14.19V12.87C21.1901 12.19 20.6801 11.4 20.0501 11.13Z" fill="#FFC107"/>
                    </svg>
                </span>
                <div class="ftr-cards-desc">
                    <p>PNL Due</p>
                    <h3>24</h3>
                </div>
            </a>
        </div> --}}

        {{-- Route to Makkah --}}
        @if (in_array($from, ['flightDashboard']))
            <div class="col-xl-3 col-lg-4 col-md-6 dash-feature-col">
                <a href="#" class="feature-cards-item ftr-cards-style-7">
                    <span class="ftr-cards-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
                            <path d="M20.8259 10.88L16.1559 8.87L15.1159 8.43C14.9559 8.35 14.8159 8.14 14.8159 7.96V4.9C14.8159 3.94 14.1059 2.8 13.2459 2.36C12.9459 2.21 12.5859 2.21 12.2859 2.36C11.4359 2.8 10.7259 3.95 10.7259 4.91V7.97C10.7259 8.15 10.5859 8.36 10.4259 8.44L4.72594 10.89C4.09594 11.15 3.58594 11.94 3.58594 12.62V13.94C3.58594 14.79 4.22594 15.21 5.01594 14.87L10.0259 12.71C10.4159 12.54 10.7359 12.75 10.7359 13.18V14.29V16.09C10.7359 16.32 10.6059 16.65 10.4459 16.81L8.12594 19.14C7.88594 19.38 7.77594 19.85 7.88594 20.19L8.33594 21.55C8.51594 22.14 9.18594 22.42 9.73594 22.14L12.1159 20.14C12.4759 19.83 13.0659 19.83 13.4259 20.14L15.8059 22.14C16.3559 22.41 17.0259 22.14 17.2259 21.55L17.6759 20.19C17.7859 19.86 17.6759 19.38 17.4359 19.14L15.1159 16.81C14.9459 16.65 14.8159 16.32 14.8159 16.09V13.18C14.8159 12.75 15.1259 12.55 15.5259 12.71L20.5359 14.87C21.3259 15.21 21.9659 14.79 21.9659 13.94V12.62C21.9659 11.94 21.4559 11.15 20.8259 10.88Z" fill="#59A0DD"/>
                        </svg>
                    </span>
                    <div class="ftr-cards-desc">
                        <p>Route to Makkah</p>
                        {{-- <h3>{{ isset($airlinesInfo['routeToMakkah']) ? $airlinesInfo['routeToMakkah'] : '0' }}</h3> --}}
                        <h3 id="routeToMakkah">0</h3>
                    </div>
                </a>
            </div>
        @endif

        {{-- Reservation percent --}}
        @if (in_array($from, ['flightDashboard', 'payOrderReceived', 'reservation']))
            <div class="col-xl-3 col-lg-4 col-md-6 dash-feature-col">
                <a href="#" class="feature-cards-item ftr-cards-reserved">
                    <span class="ftr-cards-icon" id=reservationParcent>
                        <div class="cards-circle" style="background: conic-gradient(#FF9F43 0%, #eee 0);">
                            <span class="text-percentage">0%</span>
                        </div>
                    </span>
                    <div class="ftr-cards-desc">
                        <p>Reservation </p>
                    </div>
                </a>
            </div>
        @endif
    </div>
</div>
<input type="hidden" name="_token" value="{{ csrf_token() }}">

<script>
    $(document).ready(function() {
        function getDashboardData() {
            const totalFlight = $('#totalFlight');
            const totalSeat = $('#totalSeat');
            const reservations = $('#reservations');
            const availableSeat = $('#availableSeat');
            const routeToMakkah = $('#routeToMakkah');
            const reservationParcent = $('#reservationParcent');

            const spinner = `
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden"></span>
                </div>
            `;

            totalFlight.html(spinner);
            totalSeat.html(spinner);
            reservations.html(spinner);
            availableSeat.html(spinner);
            routeToMakkah.html(spinner);
            reservationParcent.html(`
                <div class="cards-circle" style="background: conic-gradient(#FF9F43 0%, #eee 0);">
                    <span class="text-percentage">${spinner}</span>
                </div>
            `);

            $.ajax({
                url: '{{ url("/flight/get-dashboard-data") }}',
                method: 'GET',
                success: function(response) {
                    // Code to handle successful response
                    if(response.response_code == 1) {
                        totalFlight.html(response.data.totalFlights);
                        totalSeat.html(response.data.totalSeat);
                        reservations.html(response.data.totalTicketReservation);
                        availableSeat.html(response.data.availableSeat);
                        routeToMakkah.html(response.data.routeToMakkah);
                        reservationParcent.html(`
                            <div class="cards-circle" style="background: conic-gradient(#FF9F43 ${response.data.reservationPercent}%, #eee 0);">
                                <span class="text-percentage">${response.data.reservationPercent}%</span>
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    // Code to handle errors
                    console.error(status, error);
                }
            });
        }
        getDashboardData();

    })
</script>