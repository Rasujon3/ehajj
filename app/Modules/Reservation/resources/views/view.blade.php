
@extends('layouts.admin')

@section('header-resources')
    @include('partials.datatable-css')
@endsection
@section('content')
    @include('partials.messages')
<?php
$flight_count=1;
?>
    <div class="dash-content-main">
        <div class="dash-section-title">
            <h3>{{ isset($airlinesInfo->name) ? $airlinesInfo->name : "" }}</h3>
        </div>
        <div class="border-card-block">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <span class="title-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 24 18" fill="none">
                            <path d="M23.1018 3.3276L22.8657 1.98857C22.675 0.909681 21.5203 0.211331 20.2859 0.428854L4.22559 3.26095H22.418C22.6527 3.26095 22.8812 3.2846 23.1018 3.3276Z" fill="white"/>
                            <path d="M22.4183 4.93359H1.57999C0.70825 4.93359 -0.000976562 5.50649 -0.000976562 6.2104V16.3343C-0.000976562 17.0382 0.70825 17.6111 1.57999 17.6111H22.4183C23.2898 17.6111 23.999 17.0382 23.999 16.3343V6.2104C23.9989 5.50624 23.2898 4.93359 22.4183 4.93359ZM7.43186 16.3109H5.75921V14.8714H7.43186V16.3109ZM7.43186 13.4317H5.75921V11.9921H7.43186V13.4317ZM7.43186 10.5525H5.75921V9.11282H7.43186V10.5525ZM7.43186 7.67325H5.75921V6.23355H7.43186V7.67325ZM19.7217 9.40635L18.4345 10.6104L19.5942 14.4841L18.9446 15.1337C18.8733 15.2047 18.7579 15.2047 18.6865 15.1337L16.9579 11.9918L15.3679 13.56L15.6211 14.7959L15.2165 15.2007C15.1628 15.2544 15.0755 15.2544 15.0223 15.2009L12.7321 12.9107C12.6783 12.857 12.6783 12.77 12.7321 12.7165L13.1367 12.3114L14.3499 12.5589L15.9191 10.9629L12.7992 9.24561C12.7279 9.17479 12.7279 9.05932 12.7987 8.988L13.4489 8.33846L17.2969 9.49032L18.5096 8.19404C18.864 7.83968 19.4226 7.82412 19.7572 8.15888C20.0913 8.49338 20.0754 9.05211 19.7217 9.40635Z" fill="white"/>
                        </svg>
                    </span>
                    <h3>Voucher No : {{ $voucherInfo->tracking_no }}</h3>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="flight-info-lists">
                            <ul>
                                <li>
                                    <span class="flight-info-list-title">Ref. No</span>
                                    <span class="flight-info-list-desc">{!! $voucherInfo->ref_no !!}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Haj License No</span>
                                    <span class="flight-info-list-desc">{!! $voucherInfo->hl_no !!}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Bank Name</span>
                                    <span class="flight-info-list-desc">{!! $voucherInfo->bank_name !!}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Depositor Mobile No</span>
                                    <span class="flight-info-list-desc">{!! $voucherInfo->depositor_mobile_no !!}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Pay order Date</span>
                                    <span class="flight-info-list-desc">{!! date('j-F-Y', strtotime($voucherInfo->payorder_date)) !!}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="flight-info-lists">
                            <ul>
                                <li>
                                    <span class="flight-info-list-title">Agency</span>
                                    <span class="flight-info-list-desc">{!! $voucherInfo->ticketing_agent_name !!}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">No of pilgrim</span>
                                    <span class="flight-info-list-desc">{!! $voucherInfo->no_of_pilgrim !!}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Depositor Name</span>
                                    <span class="flight-info-list-desc">{!! $voucherInfo->depositor_name !!}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Ticketing Agent</span>
                                    <span class="flight-info-list-desc">{!! ucfirst($voucherInfo->ticketing_agent_name) !!}</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Pay order Number</span>
                                    <span class="flight-info-list-desc">{!! $voucherInfo->pay_order !!}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flight-reservation-content mt-3">
                    <div class="flight-tab-sec">
                        <div class="flight-tab-menu" role="tablist">
                            <div class="nav nav-tabs" role="presentation">
                                <button class="tab-btn nav-link active" data-toggle="tab" data-target="#tabReservation" type="button" role="tab">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                            <path d="M7 0.5V6.5" stroke="#082567" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M5 4.5L7 6.5L9 4.5" stroke="#082567" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M9.5 2H13C13.2761 2 13.5 2.22386 13.5 2.5V10.5C13.5 10.7761 13.2761 11 13 11H1C0.723858 11 0.5 10.7761 0.5 10.5V2.5C0.5 2.22386 0.723858 2 1 2H4.5" stroke="#082567" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6 11L5 13.5" stroke="#082567" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8 11L9 13.5" stroke="#082567" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M4 13.5H10" stroke="#082567" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <span>Reservation</span>
                                </button>
                                <button class="tab-btn nav-link" data-toggle="tab" data-target="#tabPilgrim" type="button" role="tab">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M12.9216 11.0321L12.3539 11.5017L13.0489 11.7462C15.5841 12.6381 17.084 14.5907 17.084 16.6676C17.084 16.7781 17.0401 16.8841 16.962 16.9622C16.8839 17.0404 16.7779 17.0843 16.6674 17.0843C16.5568 17.0843 16.4509 17.0404 16.3727 16.9622C16.2946 16.8841 16.2507 16.7781 16.2507 16.6676C16.2507 14.4748 14.0236 12.084 10.0004 12.084C5.97714 12.084 3.75006 14.4748 3.75006 16.6676C3.75006 16.7781 3.70616 16.8841 3.62802 16.9622C3.54987 17.0404 3.44389 17.0843 3.33337 17.0843C3.22286 17.0843 3.11687 17.0404 3.03873 16.9622C2.96059 16.8841 2.91669 16.7781 2.91669 16.6676C2.91669 14.5908 4.41825 12.6381 6.95111 11.7462L7.6458 11.5015L7.07831 11.0321C6.35143 10.4308 5.82779 9.61981 5.57885 8.70987C5.32992 7.79994 5.36781 6.83536 5.68736 5.94776C6.00691 5.06016 6.59255 4.29278 7.36437 3.75034C8.13619 3.20789 9.05658 2.91681 9.99995 2.91681C10.9433 2.91681 11.8637 3.20789 12.6355 3.75034C13.4073 4.29278 13.993 5.06016 14.3125 5.94776C14.6321 6.83536 14.67 7.79994 14.421 8.70987C14.1721 9.61981 13.6485 10.4308 12.9216 11.0321ZM7.34859 4.84868C6.64529 5.55198 6.25018 6.50585 6.25018 7.50046C6.25018 8.49507 6.64529 9.44895 7.34859 10.1522C8.05188 10.8555 9.00575 11.2506 10.0004 11.2506C10.995 11.2506 11.9488 10.8555 12.6521 10.1522C13.3554 9.44895 13.7505 8.49507 13.7505 7.50046C13.7505 6.50585 13.3554 5.55198 12.6521 4.84868C11.9488 4.14539 10.995 3.75028 10.0004 3.75028C9.00575 3.75028 8.05188 4.14539 7.34859 4.84868Z" fill="black" stroke="#112219" stroke-width="0.833374"/>
                                        </svg>
                                    </span>
                                    <span>Pilgrim</span>
                                </button>
                            </div>
                            @if($voucherInfo->status_id  != 4)
                            <div class="title-btn-group">
                                    <button class="list-btn-green addFlightRow">
                                    <span class="list-btn-icon flex-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.49961 9.9001C7.92966 9.9001 9.89961 7.93015 9.89961 5.5001C9.89961 3.07005 7.92966 1.1001 5.49961 1.1001C3.06956 1.1001 1.09961 3.07005 1.09961 5.5001C1.09961 7.93015 3.06956 9.9001 5.49961 9.9001ZM6.04961 3.8501C6.04961 3.54634 5.80337 3.3001 5.49961 3.3001C5.19585 3.3001 4.94961 3.54634 4.94961 3.8501V4.9501H3.84961C3.54585 4.9501 3.29961 5.19634 3.29961 5.5001C3.29961 5.80385 3.54585 6.0501 3.84961 6.0501H4.94961V7.1501C4.94961 7.45386 5.19585 7.7001 5.49961 7.7001C5.80337 7.7001 6.04961 7.45386 6.04961 7.1501V6.0501H7.14961C7.45337 6.0501 7.69961 5.80385 7.69961 5.5001C7.69961 5.19634 7.45337 4.9501 7.14961 4.9501H6.04961V3.8501Z" fill="#EAF6EC"/>
                                        </svg>
                                    </span>
                                        <span class="list-btn-text">Add New Flight</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        <div class="tab-content">
                            <div class="tab-pane show active fade" id="tabReservation">
                                <div class="flight-tab-content">
                                    <div id="flightRow">
                                        @if(count($pilgrimTicketRequisitionDetails) > 0)
                                        <?php
                                            $sl = 1;
                                            $total_added_pilgrim = 0;
                                        ?>
                                            @foreach($pilgrimTicketRequisitionDetails as  $pilgrimTicketRequisition)
                                            <div class="flight-details-box flightElement{{$sl}} flight_slot flight_slot_ext" flight_slot_id="{{\App\Libraries\Encryption::encode($pilgrimTicketRequisition->slotInfo->id)}}">
                                                <div class="flight-box-content">
                                                    <div class="flight-opt-form flightElement" id="flightElement{{$sl}}">
                                                        <div class="flight-box-header">
                                                            <div class="bd-card-title">
                                                                <h3>Flight {{ $sl }}</h3>
                                                            </div>
                                                            <input type="hidden" class="ext_flight_slot_id" value="{{\App\Libraries\Encryption::encodeId($pilgrimTicketRequisition->slotInfo->id)}}">
                                                            @if($voucherInfo->status_id  != 4)
                                                                <div class="title-btn-group">
                                                                    <a href="#ehajjAllVoucherModal" class="title-list-btn btn-navyblue addAllPilgrim" data-toggle="modal">
                                                                        <span class="list-btn-icon flex-center">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.49961 10.4001C7.92966 10.4001 9.89961 8.43015 9.89961 6.0001C9.89961 3.57005 7.92966 1.6001 5.49961 1.6001C3.06956 1.6001 1.09961 3.57005 1.09961 6.0001C1.09961 8.43015 3.06956 10.4001 5.49961 10.4001ZM6.04961 4.3501C6.04961 4.04634 5.80337 3.8001 5.49961 3.8001C5.19585 3.8001 4.94961 4.04634 4.94961 4.3501V5.4501H3.84961C3.54585 5.4501 3.29961 5.69634 3.29961 6.0001C3.29961 6.30385 3.54585 6.5501 3.84961 6.5501H4.94961V7.6501C4.94961 7.95386 5.19585 8.2001 5.49961 8.2001C5.80337 8.2001 6.04961 7.95386 6.04961 7.6501V6.5501H7.14961C7.45337 6.5501 7.69961 6.30385 7.69961 6.0001C7.69961 5.69634 7.45337 5.4501 7.14961 5.4501H6.04961V4.3501Z" fill="#EAF6EC"/>
                                                                            </svg>
                                                                        </span>
                                                                        <span class="list-btn-text">Add All From Voucher</span>
                                                                    </a>
                                                                    <button type="button" class="title-list-btn btn-skyblue addPilgrim">
                                                                        <span class="list-btn-icon flex-center">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.49961 10.4001C7.92966 10.4001 9.89961 8.43015 9.89961 6.0001C9.89961 3.57005 7.92966 1.6001 5.49961 1.6001C3.06956 1.6001 1.09961 3.57005 1.09961 6.0001C1.09961 8.43015 3.06956 10.4001 5.49961 10.4001ZM6.04961 4.3501C6.04961 4.04634 5.80337 3.8001 5.49961 3.8001C5.19585 3.8001 4.94961 4.04634 4.94961 4.3501V5.4501H3.84961C3.54585 5.4501 3.29961 5.69634 3.29961 6.0001C3.29961 6.30385 3.54585 6.5501 3.84961 6.5501H4.94961V7.6501C4.94961 7.95386 5.19585 8.2001 5.49961 8.2001C5.80337 8.2001 6.04961 7.95386 6.04961 7.6501V6.5501H7.14961C7.45337 6.5501 7.69961 6.30385 7.69961 6.0001C7.69961 5.69634 7.45337 5.4501 7.14961 5.4501H6.04961V4.3501Z" fill="#EAF6EC"/>
                                                                            </svg>
                                                                        </span>
                                                                        <span class="list-btn-text">Add Individual</span>
                                                                    </button>
                                                                    <button href="#" class="title-list-btn btn-red removeAllPilgrim">
                                                                        <span class="list-btn-icon flex-center">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.49961 10.4001C7.92966 10.4001 9.89961 8.43015 9.89961 6.0001C9.89961 3.57005 7.92966 1.6001 5.49961 1.6001C3.06956 1.6001 1.09961 3.57005 1.09961 6.0001C1.09961 8.43015 3.06956 10.4001 5.49961 10.4001ZM3.84961 5.4501C3.54585 5.4501 3.29961 5.69634 3.29961 6.0001C3.29961 6.30385 3.54585 6.5501 3.84961 6.5501H7.14961C7.45337 6.5501 7.69961 6.30385 7.69961 6.0001C7.69961 5.69634 7.45337 5.4501 7.14961 5.4501H3.84961Z" fill="#EAF6EC"/>
                                                                            </svg>
                                                                        </span>
                                                                        <span class="list-btn-text">Remove all Pilgrim</span>
                                                                    </button>
                                                                    <button class="title-list-btn btn-outline-red">
                                                                        <span class="list-btn-icon flex-center">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.49961 10.4001C7.92966 10.4001 9.89961 8.43015 9.89961 6.0001C9.89961 3.57005 7.92966 1.6001 5.49961 1.6001C3.06956 1.6001 1.09961 3.57005 1.09961 6.0001C1.09961 8.43015 3.06956 10.4001 5.49961 10.4001ZM3.84961 5.4501C3.54585 5.4501 3.29961 5.69634 3.29961 6.0001C3.29961 6.30385 3.54585 6.5501 3.84961 6.5501H7.14961C7.45337 6.5501 7.69961 6.30385 7.69961 6.0001C7.69961 5.69634 7.45337 5.4501 7.14961 5.4501H3.84961Z" fill="#DC3545"/>
                                                                            </svg>
                                                                        </span>
                                                                        <span class="list-btn-text removeFlightRow">Remove This Flight</span>
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <br>
                                                        <div class="row">
                                                            <div class="col-lg-3 col-md-6">
                                                                <div class="form-group">
                                                                    <label>Flight Date</label>
                                                                    <div class="input-has-icon">
                                                                        <input class="form-control flightDate required engOnly valid datepicker" name="flight_date" value="{{date('d-M-Y', strtotime($pilgrimTicketRequisition->slotInfo->flight_date))}}" autocomplete="off" type="text" placeholder="Enter Month">
                                                                        <span class="form-input-icon">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                                                <g clip-path="url(#clip0_2139_2246)">
                                                                                    <path d="M14.75 2H14V1.25C14 1.05109 13.921 0.860322 13.7803 0.71967C13.6397 0.579018 13.4489 0.5 13.25 0.5C13.0511 0.5 12.8603 0.579018 12.7197 0.71967C12.579 0.860322 12.5 1.05109 12.5 1.25V2H6.5V1.25C6.5 1.05109 6.42098 0.860322 6.28033 0.71967C6.13968 0.579018 5.94891 0.5 5.75 0.5C5.55109 0.5 5.36032 0.579018 5.21967 0.71967C5.07902 0.860322 5 1.05109 5 1.25V2H4.25C3.2558 2.00119 2.30267 2.39666 1.59966 3.09966C0.896661 3.80267 0.501191 4.7558 0.5 5.75L0.5 14.75C0.501191 15.7442 0.896661 16.6973 1.59966 17.4003C2.30267 18.1033 3.2558 18.4988 4.25 18.5H14.75C15.7442 18.4988 16.6973 18.1033 17.4003 17.4003C18.1033 16.6973 18.4988 15.7442 18.5 14.75V5.75C18.4988 4.7558 18.1033 3.80267 17.4003 3.09966C16.6973 2.39666 15.7442 2.00119 14.75 2ZM2 5.75C2 5.15326 2.23705 4.58097 2.65901 4.15901C3.08097 3.73705 3.65326 3.5 4.25 3.5H14.75C15.3467 3.5 15.919 3.73705 16.341 4.15901C16.7629 4.58097 17 5.15326 17 5.75V6.5H2V5.75ZM14.75 17H4.25C3.65326 17 3.08097 16.7629 2.65901 16.341C2.23705 15.919 2 15.3467 2 14.75V8H17V14.75C17 15.3467 16.7629 15.919 16.341 16.341C15.919 16.7629 15.3467 17 14.75 17Z" fill="#474D49"></path>
                                                                                    <path d="M9.5 12.875C10.1213 12.875 10.625 12.3713 10.625 11.75C10.625 11.1287 10.1213 10.625 9.5 10.625C8.87868 10.625 8.375 11.1287 8.375 11.75C8.375 12.3713 8.87868 12.875 9.5 12.875Z" fill="#474D49"></path>
                                                                                    <path d="M5.75 12.875C6.37132 12.875 6.875 12.3713 6.875 11.75C6.875 11.1287 6.37132 10.625 5.75 10.625C5.12868 10.625 4.625 11.1287 4.625 11.75C4.625 12.3713 5.12868 12.875 5.75 12.875Z" fill="#374957"></path>
                                                                                    <path d="M13.25 12.875C13.8713 12.875 14.375 12.3713 14.375 11.75C14.375 11.1287 13.8713 10.625 13.25 10.625C12.6287 10.625 12.125 11.1287 12.125 11.75C12.125 12.3713 12.6287 12.875 13.25 12.875Z" fill="#374957"></path>
                                                                                </g>
                                                                                <defs>
                                                                                    <clipPath id="clip0_2139_2246">
                                                                                        <rect width="18" height="18" fill="white" transform="translate(0.5 0.5)"></rect>
                                                                                    </clipPath>
                                                                                </defs>
                                                                            </svg>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 col-md-6">
                                                                <div class="form-group">
                                                                    <label>Flight Code</label>
                                                                    <select class="form-control required engOnly flightCode valid" name="flight_code">
                                                                        <option value="0">Select</option>
                                                                        @foreach(\App\Libraries\CommonFunction::getFlightCodeByDate($pilgrimTicketRequisition->slotInfo->flight_date) as $flightCode)
                                                                            <option value="{{$flightCode->id}}"
                                                                                {{($pilgrimTicketRequisition->slotInfo->flight_id==$flightCode->id)?'selected':''}}>{{$flightCode->flight_code}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 col-md-6">
                                                                <div class="form-group">
                                                                    <label>PNR Number</label>
                                                                    <input class="form-control pnrNumber" type="text" name="pnr_number" value="{{$pilgrimTicketRequisition->slotInfo->pnr_number}}" placeholder="PNR Number">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 col-md-6">
                                                                <div class="form-group">
                                                                    <label>No Of Pilgrim</label>
                                                                    <input class="form-control noOfPilgrim" name="no_of_pilgrim" type="text" value="{{$pilgrimTicketRequisition->slotInfo->no_of_pilgrim}}" placeholder="No Of Pilgrim">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ehajj-list-table">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th width="50">SL No.</th>
                                                                        <th>Voucher Number</th>
                                                                        <th>Pilgrim Name</th>
                                                                        <th>Gender</th>
                                                                        <th>Passport Number</th>
                                                                        <th>Phone Number</th>
                                                                        @if($voucherInfo->status_id  != 4)
                                                                        <th width="70">Action</th>
                                                                        @endif
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if(count($pilgrimTicketRequisition->pilgrims)==0)
                                                                        <tr style="    background-color: white">
                                                                            <td colspan="4" class="text-center text-danger">No pilgrim
                                                                                added
                                                                            </td>
                                                                        </tr>
                                                                    @else
                                                                    @php($total_added_pilgrim = $total_added_pilgrim + count($pilgrimTicketRequisition->pilgrims))
                                                                    @foreach($pilgrimTicketRequisition->pilgrims as $key=>$pilgrim)
                                                                    <tr>
                                                                        <td>{{ $key+1 }}</td>
                                                                        <td>{{ $pilgrim->voucher_no }}</td>
                                                                        <td>{{ $pilgrim->full_name_english }}</td>
                                                                        <td>{{ $pilgrim->gender }}</td>
                                                                        <td>{{ $pilgrim->passport_no }}</td>
                                                                        <td>{{ $pilgrim->mobile }}</td>
                                                                        @if($voucherInfo->status_id  != 4)
                                                                        <td>
                                                                            <button type="button" class="icon-minus removePilgrimRow" pilgrim_id="<?php echo \App\Libraries\Encryption::encodeId($pilgrim->id) ?>">
																				<svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
																					<path fill-rule="evenodd" clip-rule="evenodd" d="M13.5 24.25C19.4371 24.25 24.25 19.4371 24.25 13.5C24.25 7.56294 19.4371 2.75 13.5 2.75C7.56294 2.75 2.75 7.56294 2.75 13.5C2.75 19.4371 7.56294 24.25 13.5 24.25ZM9.46875 12.1562C8.72662 12.1562 8.125 12.7579 8.125 13.5C8.125 14.2421 8.72662 14.8438 9.46875 14.8438H17.5312C18.2734 14.8438 18.875 14.2421 18.875 13.5C18.875 12.7579 18.2734 12.1562 17.5312 12.1562H9.46875Z" fill="#DC3545"/>
																				</svg>
																			</button>
                                                                        </td>
                                                                        @endif
                                                                    </tr>
                                                                    @endforeach
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $sl++?>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="no-flight-text flex-center">
{{--                                        <span>flight not added</span>--}}
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tabPilgrim" style="margin-top:20px;">
                                <div class="flight-tab-content">
                                    <div class="ehajj-list-table">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="pilgrim_table">
                                                <thead>
                                                    <tr>
                                                        <th width="50">SL No.</th>
                                                        <th class="text-left">Pilgrim Name</th>
                                                        <th>PID</th>
                                                        <th>Tracking Number</th>
                                                        <th>Passport No</th>
                                                        <th>Date of Birth</th>
                                                        <th>Gender</th>
                                                        <th>Flight Date</th>
                                                        <th>Flight Code</th>
                                                        <th>PNR Number</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pilgrims as $key=>$pilgrim)
                                                        <tr>
                                                            <td>{{ $key+1 }}</td>
                                                            <td class="text-left">{{ $pilgrim->full_name_english }}</td>
                                                            <td>{{ $pilgrim->pid }}</td>
                                                            <td>{{ $pilgrim->tracking_no }}</td>
                                                            <td>{{ $pilgrim->passport_no }}</td>
                                                            <td>{{ $pilgrim->birth_date }}</td>
                                                            <td>{{ $pilgrim->gender }}</td>
                                                            <td>{{ !empty($pilgrim->air_flight_date) ? $pilgrim->air_flight_date : 'N/A' }}</td>
                                                            <td>{{ !empty($pilgrim->air_flight_code) ? $pilgrim->air_flight_code : 'N/A' }}</td>
                                                            <td>{{ !empty($pilgrim->pnr_number) ? $pilgrim->pnr_number : 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($voucherInfo->status_id  == 3)
                     <div class="notify-text">
                        <div class="form-check">
                            <input class="form-check-input i_agree_checkbox" type="checkbox" id="notifyCheck">
                            <label class="form-check-label" for="notifyCheck">উপরোক্ত হজযাত্রীদের টিকেটের পে-অর্ডার গ্রহণ করা হয়েছে; টিকেট রিজার্ভেশন দেওয়া হল।</label>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-md-6 col-md-offset-3">
                    <div id="reservation_success"></div>
                </div>
            </div>

            <div class="bd-card-footer">
                <div class="flex-space-btw info-btn-group">
                    <a href="/reservation" class="btn btn-default"><span>Close</span></a>
                    <div>
                        @if($voucherInfo->status_id  != 4)
                            <button class="btn btn-outline-blue btn-squire draftReservation" id="draft_reservation" reservation_master_id="{{ \App\Libraries\Encryption::encodeId($voucherInfo->id) }}"><span>Save as Draft</span></button>
                            <button class="btn btn-outline-blue btn-squire completeReservation" disabled  id="complete_reservation" reservation_master_id="{{ \App\Libraries\Encryption::encodeId($voucherInfo->id) }}"><span>Complete Reservation</span></button>
                        @else
                            <a class="btn btn-outline-blue btn-danger btn-squire cancelReservation" id="cancel_reservation" reservation_master_id="{{ \App\Libraries\Encryption::encodeId($voucherInfo->id) }}"><span>Cancel Reservation</span></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    </div>

@endsection

<div class="modal-header" style="border-bottom: 0px">
    <!-- Modal -->
    <div class="modal fade" id="addPilgrimModal" tabindex="-1" role="dialog" aria-labelledby="addPilgrimModalLabel"
            aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="addPilgrimModalBodyReservation"></div>
        </div>
    </div>
</div>
<!--content section-->

@section('footer-script')

<script src="{{ asset("assets/scripts/jquery.validate.js") }}"></script>
<script src="{{ asset("assets/scripts/moment.js") }}"></script>
<script src="{{ asset("assets/scripts/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset("assets/plugins/datepicker-oss/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset("assets/plugins/intlTelInput/js/intlTelInput.js") }}"  type="text/javascript"></script>
<script src="{{ asset("assets/plugins/intlTelInput/js/utils.js") }}"  type="text/javascript"></script>

@include('partials.datatable-js')
<script>
    var total_no_of_pilgrim = parseInt('{{ $voucherInfo->no_of_pilgrim }}');
    $(document).ready(function() {
        $('#pilgrim_table').DataTable();
    });

    $(document).ready(function () {
        function capacityCheck() {
            let capacityOverloaded = false;
            let capacity_overloaded_flight_codes = [];

            $(".added_new_slot").each(function (i, elem) {
                let noOfPilgrims = $(elem).find('.noOfPilgrim').val();
                let flightRow = $(elem);
                let selectedCapacity = flightRow.find('.flightCode').find('option:selected').attr('id');
                let selectedFlight = flightRow.find('.flightCode').find('option:selected').text();
                if (Number(noOfPilgrims) > Number(selectedCapacity)) {
                    capacityOverloaded = true;
                    capacity_overloaded_flight_codes.push(selectedFlight);
                }
            });

            $(".flight_slot_ext").each(function (i, elem) {
                let noOfPilgrims = $(elem).find('.noOfPilgrim').val();
                let flightRow = $(elem);
                let selectedCapacity = flightRow.find('.flightCode').find('option:selected').attr('id');
                let selectedFlight = flightRow.find('.flightCode').find('option:selected').text();
                if (Number(noOfPilgrims) > Number(selectedCapacity)) {
                    capacityOverloaded = true;
                    capacity_overloaded_flight_codes.push(selectedFlight);
                }
            });
            return {capacityOverloaded, capacity_overloaded_flight_codes};
        }
        $(document).on('click', ".addFlightRow", function (event) {
            event.stopImmediatePropagation();
            var nthFlightRow = '<div class="flight-details-box flight_slot added_new_slot" flight_slot_id="{{  App\Libraries\Encryption::encode('NEW') }}">\n'+
                                    '<div class="flight-box-content">\n'+
                                        '<div class="flight-opt-form flightElement" id="flightElement1">\n'+
                                            '<div class="flight-box-header">\n'+
                                                '<div class="bd-card-title">\n'+
                                                    '<h3>Flight</h3>\n'+
                                                '</div>\n'+
                                                '\n'+
                                                '<div class="title-btn-group">\n'+
                                                    '<a href="#ehajjAllVoucherModal" class="title-list-btn btn-navyblue addAllPilgrim" data-toggle="modal">\n'+
                                                        '<span class="list-btn-icon flex-center">\n'+
                                                            '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">\n'+
                                                            '<path fill-rule="evenodd" clip-rule="evenodd" d="M5.49961 10.4001C7.92966 10.4001 9.89961 8.43015 9.89961 6.0001C9.89961 3.57005 7.92966 1.6001 5.49961 1.6001C3.06956 1.6001 1.09961 3.57005 1.09961 6.0001C1.09961 8.43015 3.06956 10.4001 5.49961 10.4001ZM6.04961 4.3501C6.04961 4.04634 5.80337 3.8001 5.49961 3.8001C5.19585 3.8001 4.94961 4.04634 4.94961 4.3501V5.4501H3.84961C3.54585 5.4501 3.29961 5.69634 3.29961 6.0001C3.29961 6.30385 3.54585 6.5501 3.84961 6.5501H4.94961V7.6501C4.94961 7.95386 5.19585 8.2001 5.49961 8.2001C5.80337 8.2001 6.04961 7.95386 6.04961 7.6501V6.5501H7.14961C7.45337 6.5501 7.69961 6.30385 7.69961 6.0001C7.69961 5.69634 7.45337 5.4501 7.14961 5.4501H6.04961V4.3501Z" fill="#EAF6EC"/>\n'+
                                                            '</svg>\n'+
                                                        '</span>\n'+
                                                        '<span class="list-btn-text">Add All From Voucher</span>\n'+
                                                    '</a>\n'+
                                                    '\n'+
                                                    '<button type="button" class="title-list-btn btn-skyblue addPilgrim">\n'+
                                                        '<span class="list-btn-icon flex-center">\n'+
                                                            '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">\n'+
                                                            '<path fill-rule="evenodd" clip-rule="evenodd" d="M5.49961 10.4001C7.92966 10.4001 9.89961 8.43015 9.89961 6.0001C9.89961 3.57005 7.92966 1.6001 5.49961 1.6001C3.06956 1.6001 1.09961 3.57005 1.09961 6.0001C1.09961 8.43015 3.06956 10.4001 5.49961 10.4001ZM6.04961 4.3501C6.04961 4.04634 5.80337 3.8001 5.49961 3.8001C5.19585 3.8001 4.94961 4.04634 4.94961 4.3501V5.4501H3.84961C3.54585 5.4501 3.29961 5.69634 3.29961 6.0001C3.29961 6.30385 3.54585 6.5501 3.84961 6.5501H4.94961V7.6501C4.94961 7.95386 5.19585 8.2001 5.49961 8.2001C5.80337 8.2001 6.04961 7.95386 6.04961 7.6501V6.5501H7.14961C7.45337 6.5501 7.69961 6.30385 7.69961 6.0001C7.69961 5.69634 7.45337 5.4501 7.14961 5.4501H6.04961V4.3501Z" fill="#EAF6EC"/>\n'+
                                                            '</svg>\n'+
                                                        '</span>\n'+
                                                        '<span class="list-btn-text">Add Individual</span>\n'+
                                                    '</button>\n'+
                                                    '\n'+
                                                    '<a href="#" class="title-list-btn btn-outline-red">\n'+
                                                        '<span class="list-btn-icon flex-center">\n'+
                                                            '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">\n'+
                                                            '<path fill-rule="evenodd" clip-rule="evenodd" d="M5.49961 10.4001C7.92966 10.4001 9.89961 8.43015 9.89961 6.0001C9.89961 3.57005 7.92966 1.6001 5.49961 1.6001C3.06956 1.6001 1.09961 3.57005 1.09961 6.0001C1.09961 8.43015 3.06956 10.4001 5.49961 10.4001ZM3.84961 5.4501C3.54585 5.4501 3.29961 5.69634 3.29961 6.0001C3.29961 6.30385 3.54585 6.5501 3.84961 6.5501H7.14961C7.45337 6.5501 7.69961 6.30385 7.69961 6.0001C7.69961 5.69634 7.45337 5.4501 7.14961 5.4501H3.84961Z" fill="#DC3545"/>\n'+
                                                            '</svg>\n'+
                                                        '</span>\n'+
                                                        '<span class="list-btn-text removeFlightRow">Remove This Flight</span>\n'+
                                                    '</a>\n'+
                                                '</div>\n'+
                                            '</div>\n'+
                                            '<br>\n'+
                                            '<div class="row">\n'+
                                                '<div class="col-lg-3 col-md-6">\n'+
                                                    '<div class="form-group">\n'+
                                                        '<label>Flight Date</label>\n'+
                                                        '<div class="input-has-icon">\n'+
                                                            '<input class="datepicker form-control flightDate required engOnly valid" name="flight_date" value="" autocomplete="off" type="text" placeholder="Enter Month">\n'+
                                                            '<span class="form-input-icon">\n'+
                                                                '<svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">\n'+
                                                                    '<g clip-path="url(#clip0_2139_2246)">\n'+
                                                                        '<path d="M14.75 2H14V1.25C14 1.05109 13.921 0.860322 13.7803 0.71967C13.6397 0.579018 13.4489 0.5 13.25 0.5C13.0511 0.5 12.8603 0.579018 12.7197 0.71967C12.579 0.860322 12.5 1.05109 12.5 1.25V2H6.5V1.25C6.5 1.05109 6.42098 0.860322 6.28033 0.71967C6.13968 0.579018 5.94891 0.5 5.75 0.5C5.55109 0.5 5.36032 0.579018 5.21967 0.71967C5.07902 0.860322 5 1.05109 5 1.25V2H4.25C3.2558 2.00119 2.30267 2.39666 1.59966 3.09966C0.896661 3.80267 0.501191 4.7558 0.5 5.75L0.5 14.75C0.501191 15.7442 0.896661 16.6973 1.59966 17.4003C2.30267 18.1033 3.2558 18.4988 4.25 18.5H14.75C15.7442 18.4988 16.6973 18.1033 17.4003 17.4003C18.1033 16.6973 18.4988 15.7442 18.5 14.75V5.75C18.4988 4.7558 18.1033 3.80267 17.4003 3.09966C16.6973 2.39666 15.7442 2.00119 14.75 2ZM2 5.75C2 5.15326 2.23705 4.58097 2.65901 4.15901C3.08097 3.73705 3.65326 3.5 4.25 3.5H14.75C15.3467 3.5 15.919 3.73705 16.341 4.15901C16.7629 4.58097 17 5.15326 17 5.75V6.5H2V5.75ZM14.75 17H4.25C3.65326 17 3.08097 16.7629 2.65901 16.341C2.23705 15.919 2 15.3467 2 14.75V8H17V14.75C17 15.3467 16.7629 15.919 16.341 16.341C15.919 16.7629 15.3467 17 14.75 17Z" fill="#474D49"></path>\n'+
                                                                        '<path d="M9.5 12.875C10.1213 12.875 10.625 12.3713 10.625 11.75C10.625 11.1287 10.1213 10.625 9.5 10.625C8.87868 10.625 8.375 11.1287 8.375 11.75C8.375 12.3713 8.87868 12.875 9.5 12.875Z" fill="#474D49"></path>\n'+
                                                                        '<path d="M5.75 12.875C6.37132 12.875 6.875 12.3713 6.875 11.75C6.875 11.1287 6.37132 10.625 5.75 10.625C5.12868 10.625 4.625 11.1287 4.625 11.75C4.625 12.3713 5.12868 12.875 5.75 12.875Z" fill="#374957"></path>\n'+
                                                                        '<path d="M13.25 12.875C13.8713 12.875 14.375 12.3713 14.375 11.75C14.375 11.1287 13.8713 10.625 13.25 10.625C12.6287 10.625 12.125 11.1287 12.125 11.75C12.125 12.3713 12.6287 12.875 13.25 12.875Z" fill="#374957"></path>\n'+
                                                                    '</g>\n'+
                                                                    '<defs>\n'+
                                                                        '<clipPath id="clip0_2139_2246">\n'+
                                                                            '<rect width="18" height="18" fill="white" transform="translate(0.5 0.5)"></rect>\n'+
                                                                        '</clipPath>\n'+
                                                                    '</defs>\n'+
                                                                '</svg>\n'+
                                                            '</span>\n'+
                                                        '</div>\n'+
                                                    '</div>\n'+
                                                '</div>\n'+
                                                '<div class="col-lg-3 col-md-6">\n'+
                                                    '<div class="form-group">\n'+
                                                        '<label>Flight Code</label>\n'+
                                                        '<select class="form-control required engOnly flightCode valid" name="flight_code">\n'+
                                                            '<option value="0">Select</option>\n'+
                                                        '</select>\n'+
                                                    '</div>\n'+
                                                '</div>\n'+
                                                '<div class="col-lg-3 col-md-6">\n'+
                                                    '<div class="form-group">\n'+
                                                        '<label>PNR Number</label>\n'+
                                                        '<input class="form-control pnrNumber" name="pnr_number" type="text" placeholder="PNR Number">\n'+
                                                    '</div>\n'+
                                                '</div>\n'+
                                                '<div class="col-lg-3 col-md-6">\n'+
                                                    '<div class="form-group">\n'+
                                                        '<label>No Of Pilgrim</label>\n'+
                                                        '<input class="form-control noOfPilgrim" name="no_of_pilgrim" type="text" placeholder="No Of Pilgrim">\n'+
                                                    '</div>\n'+
                                                '</div>\n'+
                                            '</div>\n'+
                                        '\n'+
                                        '</div>\n'+
                                        '<div class="ehajj-list-table">\n'+
                                            '<div class="table-responsive">\n'+
                                                '<table class="table table-striped">\n'+
                                                    '<thead>\n'+
                                                        ' <tr>\n'+
                                                            '<th width="50">SL No.</th>\n'+
                                                            '<th>Voucher Number</th>\n'+
                                                            '<th>Pilgrim Name</th>\n'+
                                                            '<th>Gender</th>\n'+
                                                            '<th>Passport Number</th>\n'+
                                                            '<th>Phone Number</th>\n'+
                                                            '<th width="70">Action</th>\n'+
                                                            '</tr>\n'+
                                                        '</thead>\n'+
                                                        '<tbody>\n'+
                                                        '</tbody>\n'+
                                                    '</table>\n'+
                                                '</div>\n'+
                                            '</div>\n'+
                                    '</div>\n'+
                                '</div>\n';

            $('#flightRow').append(nthFlightRow);
        });

        $(document).on('click', ".removeFlightRow", function (event) {
            event.stopImmediatePropagation();
            var thisElement = $(this);
            var flightDateField = $(this).closest('.flightElement').find('input.flightDate').val();
            var flight_slot_id = $(this).closest('.flight_slot').attr('flight_slot_id');
            if (!flightDateField) {
                ($(thisElement).closest('.flightElement')).parents().eq(1).remove();
                return false;
            }
            if (confirm('Are you sure to remove?') === false)
                return false;

            $.ajax({
                type: "POST",
                url: '{{url("reservation/remove-pilgrims-from-flight")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    flight_date: flightDateField,
                    process_ref: '{!! \App\Libraries\Encryption::encodeId($voucherInfo->id) !!}',
                    flight_slot_id: flight_slot_id
                },
                cache: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.responseCode === 1) {
                        // Remove corresponding row
                        var rowCount = $('.flightElement').length;
                        ($(thisElement).closest('.flightElement')).parent().parent().remove();
                    }
                    event.stopImmediatePropagation();
                }
            });
        });

        $(document).on('click', ".addPilgrim", function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            var flightDateField = $(this).parents().eq(3).find('.flightElement').find('input.flightDate').val();
            var flightCodeField = $(this).parents().eq(3).find('.flightElement').find('select.flightCode').val();
            var pnrNumber = $(this).parents().eq(3).find('.flightElement').find('input.pnrNumber').val();
            var noOfPilgrim = $(this).parents().eq(3).find('.flightElement').find('input.noOfPilgrim').val();
            var flight_slot_id = $(this).closest('.flight_slot').attr('flight_slot_id');
            let capacityChecked = capacityCheck();

            if (capacityChecked.capacityOverloaded) {
                alert(`No of Pilgrim can not be greater than for ${capacityChecked.capacity_overloaded_flight_codes} flight capacity.`);
                return false;
            }

            if (flightDateField && !capacityChecked.capacityOverloaded) {

                var modelUrl = '{!! url("reservation/show-pilgrim-list-modal") !!}';
                $(this).parent().find('small.text-danger').remove();
                $("#addPilgrimModal").modal();
                $('#addPilgrimModalBodyReservation').html('<div style="text-align:center;"><br/><h3 class="text-primary">Loading Form...</h3></div>');

                $.get(modelUrl, {
                    flightDate: flightDateField,
                    flightCode: flightCodeField,
                    pnrNumber: pnrNumber,
                    noOfPilgrim: noOfPilgrim,
                    process_ref: '{!! \App\Libraries\Encryption::encodeId($voucherInfo->id) !!}',
                    flight_slot_id: flight_slot_id


                }).done(function (html) {
                    $('#addPilgrimModalBodyReservation').html(html);
                });
            } else {
                alert("Fill Flight Date First");
                return;
                $(this).parent().find('small.text-danger').remove();
                $(this).parent().append('<small class="text-danger"><p>Fill Flight Date First</p></small>');
            }
            });

        $(document).on('focus', ".datepicker", function () {
            $(this).datetimepicker({
                format: 'DD-MMM-YYYY',
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-clock-o",
                    clear: "fa fa-trash-o"
                }
            });
        });

        $(this).on("dp.change", '.datepicker', function (e) {
            if ($(this).val() != '' ) {
                $(this).removeClass('error');
            }else{
                return false;
            }
            var div = $(this);
            $.ajax({
                type: "GET",
                url: '{{url("reservation/get-flight-code")}}/',
                data: {date: Date.parse(e.date) / 1000},
                success: function (response) {

                    var option = '';
                    if (response.responseCode === 1) {

                        if (response.data.length > 0) {
                            option += '<option value="">Select Code</option>';
                        }
                        $.each(response.data, function (id, value) {
                            option += '<option id="' + value.capacity + '" value="' + value.id + '">' + value.flight_code + '</option>';
                        });

                        div.closest('.flightElement').find('.flightCode').html(option);
                    }
                }
            });
            return false;
        });

        $(document).on('click', ".removePilgrimRow", function (event) {

            event.stopImmediatePropagation();
            var thisElement = $(this);
            var pilgrim_id = $(this).attr('pilgrim_id');
            if (confirm('Are you sure to remove?') === false) {
                return false;
            }

            $.ajax({
                type: "POST",
                url: '{{url("reservation/remove-pilgrims-from-flight-row")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    pilgrim_id: pilgrim_id,
                },
                success: function (response) {
                    if (response.responseCode === 1) {
                        // $(thisElement.closest("tr")).remove();
                        location.reload();
                    }
                }
            });
        });

        $(document).on('click', ".addAllPilgrim", function (event) {

            event.stopImmediatePropagation();
            if (confirm('Are you sure to add all Pilgrim?') === false) {
                return false;
            }

            var flightDateField = $(this).parents().eq(3).find('.flightElement').find('input.flightDate').val();
            var flightCodeField = $(this).parents().eq(3).find('.flightElement').find('select.flightCode').val();
            var pnrNumber = $(this).parents().eq(3).find('.flightElement').find('input.pnrNumber').val();
            var noOfPilgrim = parseInt($(this).parents().eq(3).find('.flightElement').find('input.noOfPilgrim').val());
            var flight_slot_id = $(this).closest('.flight_slot').attr('flight_slot_id');
            var _token = $('input[name="_token"]').val();
            if (flightCodeField == null) {
                alert('Flight code not found.');
                return false;
            }
            
            let capacityChecked = capacityCheck();
            if (capacityChecked.capacityOverloaded) {
                alert(`No of Pilgrim can not be greater than for ${capacityChecked.capacity_overloaded_flight_codes} flight capacity.`);
                return false;
            }

            if (flightDateField && flightCodeField) {
                $('#requisition-content-box').html('<div class="row text-center">Loading...</div>');
                $.ajax({
                    url: '{{url('/reservation/add-all-pilgrim-to-ticket-reservation')}}',
                    type: 'POST',
                    data: {
                        _token: _token,
                        ticket_requisition_id: '{!! \App\Libraries\Encryption::encodeId($voucherInfo->id) !!}',
                        flight_date: flightDateField,
                        flight_code: flightCodeField,
                        pnr_number: pnrNumber,
                        no_of_pilgrim: noOfPilgrim,
                        flight_slot_id: flight_slot_id
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.responseCode === 1) {
                            $('#messageShow').html('<h3 class="text-center text-success">Successfully Added  Pilgrim');
                            window.location.reload();
                        } else if (response.responseCode === -1) {
                            alert(response.msg);
                        }
                        $('#voucher_no_search').trigger('click');

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    },
                    beforeSend: function (xhr) {
                        console.log('before send');
                    },
                    complete: function () {
                    }
                });
            } else {
                alert("Fill Flight Date First");
                return;
                $(this).parent().find('small.text-danger').remove();
                $(this).parent().append('<small class="text-danger"><p>Fill Flight Date First</p></small>');
            }
        });

        $(document).on('click', ".removeAllPilgrim", function (event) {

            event.stopImmediatePropagation();
            if (confirm('Are you sure to remove all Pilgrim?') === false) {
                return false;
            }
            let flightDateField = $(this).parents().eq(3).find('.flightElement').find('input.flightDate').val();
            let flightCodeField = $(this).parents().eq(3).find('.flightElement').find('select.flightCode').val();
            let pnrNumber = $(this).parents().eq(3).find('.flightElement').find('input.pnrNumber').val();
            let flight_slot_id = $(this).closest('.flight_slot').attr('flight_slot_id');
            let _token = $('input[name="_token"]').val();

            if (flightDateField) {
                $('#requisition-content-box').html('<div class="row text-center">Loading...</div>');
                $.ajax({
                    url: '{{url('/reservation/remove-all-pilgrim-to-ticket-reservation')}}',
                    type: 'POST',
                    data: {
                        _token: _token,
                        ticket_requisition_id: '{!! \App\Libraries\Encryption::encodeId($voucherInfo->id) !!}',
                        flight_date: flightDateField,
                        flight_code: flightCodeField,
                        pnr_number: pnrNumber,
                        flight_slot_id: flight_slot_id
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.responseCode === 1) {
                            $('#messageShow').html('<h3 class="text-center text-success">Successfully Removed  Pilgrim');
                            window.location.reload();
                        }else if (response.responseCode === -1) {
                            alert(response.msg);
                        }
                        $('#voucher_no_search').trigger('click');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    },
                    beforeSend: function (xhr) {
                        console.log('before send');
                    },
                    complete: function () {
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on('click', '#complete_reservation', function (event) {


            event.stopImmediatePropagation();
            var total_pilgirm = 0;
            btn = $(this);
            btn_content = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin"></i> &nbsp;' + btn_content);
            btn.prop('disabled', true);

            //Ready flight slots
            var fligh_new_slots = [];
            var fligh_ext_slots = [];
            // let capacityOverloaded = false;
            // let capacity_overloaded_flight_codes = [];
            //
            // $(".added_new_slot").each(function (i, elem) {
            //     let noOfPilgrims = $(elem).find('.noOfPilgrim').val();
            //     let flightRow = $(elem);
            //     let selectedCapacity = flightRow.find('.flightCode').find('option:selected').attr('id');
            //     let selectedFlight = flightRow.find('.flightCode').find('option:selected').text();
            //     if (Number(noOfPilgrims) > Number(selectedCapacity)) {
            //         capacityOverloaded = true;
            //         capacity_overloaded_flight_codes.push(selectedFlight);
            //     }
            // });
            //
            // $(".flight_slot_ext").each(function (i, elem) {
            //     let noOfPilgrims = $(elem).find('.noOfPilgrim').val();
            //     let flightRow = $(elem);
            //     let selectedCapacity = flightRow.find('.flightCode').find('option:selected').attr('id');
            //     let selectedFlight = flightRow.find('.flightCode').find('option:selected').text();
            //     if (Number(noOfPilgrims) > Number(selectedCapacity)) {
            //         capacityOverloaded = true;
            //         capacity_overloaded_flight_codes.push(selectedFlight);
            //     }
            // });

            $(".added_new_slot").each(function (i, elem) {
                fligh_new_slots.push({
                    flightDate: $(this).find('.flightDate').val(),
                    flichtCode: $(this).find('.flightCode').val(),
                    pnrNumber: $(this).find('.pnrNumber').val(),
                    noOfPilgrim: $(this).find('.noOfPilgrim').val()
                });
            });

            $(".flight_slot_ext").each(function (i, elem) {
                fligh_ext_slots.push({
                    flightDate: $(this).find('.flightDate').val(),
                    flichtCode: $(this).find('.flightCode').val(),
                    pnrNumber: $(this).find('.pnrNumber').val(),
                    noOfPilgrim: $(this).find('.noOfPilgrim').val(),
                    flight_slot_id: $(this).find('.ext_flight_slot_id').val(),
                });
            });

            var has_empty_date = false;
            $('.flightDate').each(function (i, element) {
                if ($(element).val() == '') {
                    $(element).addClass('error');
                    has_empty_date = true;
                }
            });
            if (has_empty_date) {
                alert("Please enter Flight slot date, it's mandetory");
                btn.html(btn_content);
                btn.prop('disabled', false);
                return false;
            }

            var has_empty_no_of_pilgrim = false;

            $(".flight_slot").each(function (i, elem) {
                noOfPilgrim = $(elem).find('.noOfPilgrim').val();
                if ((noOfPilgrim == 0) || noOfPilgrim == '') {
                    has_empty_no_of_pilgrim = true;
                }
            });

            if (has_empty_no_of_pilgrim) {
                alert('No of Pilgrim can not be 0 or blank');
                btn.html(btn_content);
                btn.prop('disabled', false);
                return false;
            }


            $(".noOfPilgrim").each(function () {
                var noOfPilgrim = $.trim($(this).val());
                if (noOfPilgrim != '') {
                    total_pilgirm = parseInt(noOfPilgrim) + total_pilgirm;
                }
            });

            if (total_no_of_pilgrim < total_pilgirm) {
                alert('Added pilgrim should not be above of number of pilgrim');
                btn.html(btn_content);
                btn.prop('disabled', false);
                return false;
            }

            if (fligh_new_slots.length === 0 && fligh_ext_slots.length === 0) {
                alert('No flight added yet');
                btn.html(btn_content);
                btn.prop('disabled', false);
                return false;
            }
            let pilgrimCount = "@if(!empty($pilgrimTicketRequisition->pilgrims)){{count($pilgrimTicketRequisition->pilgrims)}} @else 0 @endif";
            let total_added_pilgrim = "@if(empty($total_added_pilgrim)) 0 @else {{$total_added_pilgrim}} @endif";
            // if (pilgrimCount == 0) {
            //     alert('No pilgrim added yet');
            //     btn.html(btn_content);
            //     btn.prop('disabled', false);
            //     return false;
            // }

            // if (total_no_of_pilgrim != total_added_pilgrim) {
            //     alert('Added pilgrim should equal to voucher number of pilgrim');
            //     btn.html(btn_content);
            //     btn.prop('disabled', false);
            //     return false;
            // }

            if (total_no_of_pilgrim != total_pilgirm) {
                alert('No of Pilgrim should be equal to total number of pilgrim');
                btn.html(btn_content);
                btn.prop('disabled', false);
                return false;
            }

            let has_empty_flight = false;
            $('.flightCode').each(function (i, element) {
                if ($(element).val() == 0) {
                    $(element).addClass('error');
                    has_empty_flight = true;
                }
            });
            if (has_empty_flight) {
                alert("No Flight found.");
                btn.html(btn_content);
                btn.prop('disabled', false);
                return false;
            }
            let capacityChecked = capacityCheck();
            if (capacityChecked.capacityOverloaded) {
                alert(`No of Pilgrim can not be greater than for ${capacityChecked.capacity_overloaded_flight_codes} flight capacity.`);
                btn.html(btn_content);
                btn.prop('disabled', false);
                return false;
            }
            
            $.ajax({
                url: '{{url("reservation/complete-ticket-reservation")}}',
                type: 'post',
                data: {
                    _token: $('input[name="_token"]').val(),
                    ticket_req_master_id: '{!! \App\Libraries\Encryption::encodeId($voucherInfo->id) !!}',
                    fligh_new_slots: fligh_new_slots,
                    fligh_ext_slots: fligh_ext_slots
                },
                dataType: 'json',
                success: function (response) {
                    btn.html(btn_content);
                    if (response.responseCode == 1) {
                        btn.prop('disabled', false);
                        $('#reservation_success').html('<h3 class="text-center">' + response.msg + '</h3>');
                        setTimeout(function() {
                            window.location.href = "{{ url('/reservation') }}";
                        }, 3000);


                    } else if (response.responseCode == 0) {
                        btn.prop('disabled', false);
                        $('#reservation_success').html('<h3 class="text-center" style="color:red">' + response.msg + '</h3>');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);

                },
                beforeSend: function (xhr) {
                    console.log('before send');
                },
                complete: function () {
                }
            });
        });

        $(document).on('click', '#draft_reservation', function (event) {
            event.stopImmediatePropagation();
            var total_pilgirm = 0;
            btn = $(this);
            btn_content = btn.html();
            btn.html('<i class="fa fa-spinner fa-spin"></i> &nbsp;' + btn_content);
            //btn.prop('disabled', true);
            //Ready flight slots
            var fligh_new_slots = [];
            var fligh_ext_slots = [];
            $(".added_new_slot").each(function (i, elem) {
                fligh_new_slots.push({
                    flightDate: $(this).find('.flightDate').val(),
                    flichtCode: $(this).find('.flightCode').val(),
                    pnrNumber: $(this).find('.pnrNumber').val(),
                    noOfPilgrim: $(this).find('.noOfPilgrim').val()
                });
            });
            $(".flight_slot_ext").each(function (i, elem) {
                fligh_ext_slots.push({
                    flightDate: $(this).find('.flightDate').val(),
                    flichtCode: $(this).find('.flightCode').val(),
                    pnrNumber: $(this).find('.pnrNumber').val(),
                    noOfPilgrim: $(this).find('.noOfPilgrim').val(),
                    flight_slot_id: $(this).find('.ext_flight_slot_id').val(),
                });
            });
            var has_empty_date = false;
            $('.flightDate').each(function (i, element) {
                if ($(element).val() == '') {
                    $(element).addClass('error');
                    has_empty_date = true;
                }
            });
            if (has_empty_date) {
                alert("Please enter Flight slot date, it's mandetory");
                btn.html(btn_content);
                btn.prop('disabled', false);
                return false;
            }
            var has_empty_no_of_pilgrim = false;
            $(".flight_slot").each(function (i, elem) {
                noOfPilgrim = $(elem).find('.noOfPilgrim').val();
                if ((noOfPilgrim == 0) || noOfPilgrim == '') {
                    has_empty_no_of_pilgrim = true;
                }
            });
            if (has_empty_no_of_pilgrim) {
                alert('No of Pilgrim can not be 0 or blank');
                btn.html(btn_content);
                btn.prop('disabled', false);
                return false;
            }
            $(".noOfPilgrim").each(function () {
                var noOfPilgrim = $.trim($(this).val());
                if (noOfPilgrim != '') {
                    total_pilgirm = parseInt(noOfPilgrim) + total_pilgirm;
                }
            });
            if (total_no_of_pilgrim < total_pilgirm) {
                alert('Added pilgrim should not be above of number of pilgrim');
                btn.html(btn_content);
                btn.prop('disabled', false);
                return false;
            }
            if (fligh_new_slots.length === 0 && fligh_ext_slots.length === 0) {
                alert('No flight added yet');
                btn.html(btn_content);
                btn.prop('disabled', false);
                return false;
            }
            $.ajax({
                url: '{{url("reservation/draft-ticket-reservation")}}',
                type: 'post',
                data: {
                    _token: $('input[name="_token"]').val(),
                    ticket_req_master_id: '{!! \App\Libraries\Encryption::encodeId($voucherInfo->id) !!}',
                    fligh_new_slots: fligh_new_slots,
                    fligh_ext_slots: fligh_ext_slots
                },
                dataType: 'json',
                success: function (response) {
                    btn.html(btn_content);
                    if (response.responseCode == 1) {
                        btn.prop('disabled', false);
                        $('#reservation_success').html('<h3 class="text-center">' + response.msg + '</h3>');
                    } else if (response.responseCode == 0) {
                        btn.prop('disabled', false);
                        $('#reservation_success').html('<h3 class="text-center" style="color:red">' + response.msg + '</h3>');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                beforeSend: function (xhr) {
                },
                complete: function () {
                }
            });
        });

        $(".i_agree_checkbox").on('click', verifyAgree);
        function verifyAgree() {
            $('.completeReservation').prop('disabled', true);
            $(".i_agree_checkbox").each(function (i, input) {
                if ($(input).is(':checked')) {
                    $('.completeReservation').prop('disabled', false);
                }else{
                    $('.completeReservation').prop('disabled', true);
                }
            });
        }

        $('#cancel_reservation').on('click', function () {

            if (confirm('Are you sure to delete')) {
                var tracking_no = "";
               /* var tracking_no = $('input[name="tracking_no"]').val();
                if (tracking_no.trim() === '') {
                    $('input[name="tracking_no"]').addClass('error');
                    return false;
                } else {
                    $('input[name="tracking_no"]').removeClass('error');
                } */

                token = '{!! csrf_token() !!}';
                button = $(this);
                btn_content = button.html();
                button.html('Loading...');
                button.prop('disabled', true);
                $.ajax({
                    url: '{{ url("/reservation/ticket-reservation-cancel") }}',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    data: {
                        tracking_no: tracking_no,
                        ticket_req_master_id: '{!! \App\Libraries\Encryption::encodeId($voucherInfo->id) !!}',
                    },
                    cache: false,
                    success: function (response) {
                        window.location.href = '/reservation'
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
                return false;
            }
            return false;
        });
    })





</script>
@endsection
<!--- footer-script--->
