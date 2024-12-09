@extends('layouts.admin')
@section('header-resources')
    @include('partials.datatable-css')
@endsection
@section('content')
    @include('partials.messages')
    <div class="dash-content-main">
        {{-- @include("Flight::dashboard", ['from' => 'payOrderReceived']) --}}
        <div class="border-card-block">
            <div class="bd-card-head">
                <div class="bd-card-title">
                    <span class="title-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M15.7997 2.20999C15.3897 1.79999 14.6797 2.07999 14.6797 2.64999V6.13999C14.6797 7.59999 15.9197 8.80999 17.4297 8.80999C18.3797 8.81999 19.6997 8.81999 20.8297 8.81999C21.3997 8.81999 21.6997 8.14999 21.2997 7.74999C19.8597 6.29999 17.2797 3.68999 15.7997 2.20999Z" fill="white"/>
                            <path d="M20.5 10.19H17.61C15.24 10.19 13.31 8.26 13.31 5.89V3C13.31 2.45 12.86 2 12.31 2H8.07C4.99 2 2.5 4 2.5 7.57V16.43C2.5 20 4.99 22 8.07 22H15.93C19.01 22 21.5 20 21.5 16.43V11.19C21.5 10.64 21.05 10.19 20.5 10.19ZM13 15.75H8.81L9.53 16.47C9.82 16.76 9.82 17.24 9.53 17.53C9.38 17.68 9.19 17.75 9 17.75C8.81 17.75 8.62 17.68 8.47 17.53L6.47 15.53C6.4 15.46 6.36 15.39 6.32 15.31C6.31 15.29 6.3 15.26 6.29 15.24C6.27 15.18 6.26 15.12 6.25 15.06C6.25 15.03 6.25 15.01 6.25 14.98C6.25 14.9 6.27 14.82 6.3 14.74C6.3 14.73 6.3 14.73 6.31 14.72C6.34 14.64 6.4 14.56 6.46 14.5C6.47 14.49 6.47 14.48 6.48 14.48L8.48 12.48C8.77 12.19 9.25 12.19 9.54 12.48C9.83 12.77 9.83 13.25 9.54 13.54L8.82 14.26H13C13.41 14.26 13.75 14.6 13.75 15.01C13.75 15.42 13.41 15.75 13 15.75Z" fill="white"/>
                        </svg>
                    </span>
                    <h3>List of Pay Order</h3>
                </div>
                <div class="title-btn-group">
                    <a class="list-btn-white" style="display: none;cursor: pointer"  id="bulkReceiveButton" data-toggle="modal">
                        <span class="list-btn-icon flex-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5001 10.4C7.93015 10.4 9.9001 8.43006 9.9001 6.00001C9.9001 3.56995 7.93015 1.60001 5.5001 1.60001C3.07005 1.60001 1.1001 3.56995 1.1001 6.00001C1.1001 8.43006 3.07005 10.4 5.5001 10.4ZM6.0501 4.35001C6.0501 4.04625 5.80385 3.80001 5.5001 3.80001C5.19634 3.80001 4.9501 4.04625 4.9501 4.35001V5.45001H3.8501C3.54634 5.45001 3.3001 5.69625 3.3001 6.00001C3.3001 6.30376 3.54634 6.55001 3.8501 6.55001H4.9501V7.65001C4.9501 7.95376 5.19634 8.20001 5.5001 8.20001C5.80385 8.20001 6.0501 7.95376 6.0501 7.65001V6.55001H7.1501C7.45386 6.55001 7.7001 6.30376 7.7001 6.00001C7.7001 5.69625 7.45386 5.45001 7.1501 5.45001H6.0501V4.35001Z" fill="#0F6849"/>
                            </svg>
                        </span>
                        <span class="list-btn-text" onclick="bulkConfirmPayOrder('', 'received')" >Bulk Receive</span>
                    </a>
                    <a class="list-btn-white" style="display: none;cursor: pointer"  id="bulkReturnButton" data-toggle="modal">
                        <span class="list-btn-icon flex-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5001 10.4C7.93015 10.4 9.9001 8.43006 9.9001 6.00001C9.9001 3.56995 7.93015 1.60001 5.5001 1.60001C3.07005 1.60001 1.1001 3.56995 1.1001 6.00001C1.1001 8.43006 3.07005 10.4 5.5001 10.4ZM6.0501 4.35001C6.0501 4.04625 5.80385 3.80001 5.5001 3.80001C5.19634 3.80001 4.9501 4.04625 4.9501 4.35001V5.45001H3.8501C3.54634 5.45001 3.3001 5.69625 3.3001 6.00001C3.3001 6.30376 3.54634 6.55001 3.8501 6.55001H4.9501V7.65001C4.9501 7.95376 5.19634 8.20001 5.5001 8.20001C5.80385 8.20001 6.0501 7.95376 6.0501 7.65001V6.55001H7.1501C7.45386 6.55001 7.7001 6.30376 7.7001 6.00001C7.7001 5.69625 7.45386 5.45001 7.1501 5.45001H6.0501V4.35001Z" fill="#0F6849"/>
                            </svg>
                        </span>
                        <span class="list-btn-text" onclick="bulkConfirmPayOrder('', 'return')" >Bulk Return</span>
                    </a>
                    <a class="list-btn-white" href="#ehajjPayorderSearchByHL" data-toggle="modal">
                        <span class="list-btn-icon flex-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5001 10.4C7.93015 10.4 9.9001 8.43006 9.9001 6.00001C9.9001 3.56995 7.93015 1.60001 5.5001 1.60001C3.07005 1.60001 1.1001 3.56995 1.1001 6.00001C1.1001 8.43006 3.07005 10.4 5.5001 10.4ZM6.0501 4.35001C6.0501 4.04625 5.80385 3.80001 5.5001 3.80001C5.19634 3.80001 4.9501 4.04625 4.9501 4.35001V5.45001H3.8501C3.54634 5.45001 3.3001 5.69625 3.3001 6.00001C3.3001 6.30376 3.54634 6.55001 3.8501 6.55001H4.9501V7.65001C4.9501 7.95376 5.19634 8.20001 5.5001 8.20001C5.80385 8.20001 6.0501 7.95376 6.0501 7.65001V6.55001H7.1501C7.45386 6.55001 7.7001 6.30376 7.7001 6.00001C7.7001 5.69625 7.45386 5.45001 7.1501 5.45001H6.0501V4.35001Z" fill="#0F6849"/>
                            </svg>
                        </span>
                        <span class="list-btn-text">Pay Order search by HL</span>
                    </a>
{{--                    voucher search is disable--}}
{{--                    <a class="list-btn-white" href="#ehajjPayorderSearchByVoucher" data-toggle="modal">--}}
{{--                        <span class="list-btn-icon flex-center">--}}
{{--                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">--}}
{{--                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.5001 10.4C7.93015 10.4 9.9001 8.43006 9.9001 6.00001C9.9001 3.56995 7.93015 1.60001 5.5001 1.60001C3.07005 1.60001 1.1001 3.56995 1.1001 6.00001C1.1001 8.43006 3.07005 10.4 5.5001 10.4ZM6.0501 4.35001C6.0501 4.04625 5.80385 3.80001 5.5001 3.80001C5.19634 3.80001 4.9501 4.04625 4.9501 4.35001V5.45001H3.8501C3.54634 5.45001 3.3001 5.69625 3.3001 6.00001C3.3001 6.30376 3.54634 6.55001 3.8501 6.55001H4.9501V7.65001C4.9501 7.95376 5.19634 8.20001 5.5001 8.20001C5.80385 8.20001 6.0501 7.95376 6.0501 7.65001V6.55001H7.1501C7.45386 6.55001 7.7001 6.30376 7.7001 6.00001C7.7001 5.69625 7.45386 5.45001 7.1501 5.45001H6.0501V4.35001Z" fill="#0F6849"/>--}}
{{--                            </svg>--}}
{{--                        </span>--}}
{{--                        <span class="list-btn-text">Pay Order Search by Voucher</span>--}}
{{--                    </a>--}}
                </div>
            </div>
            <div class="bd-card-content">
                <div class="ehajj-list-table">
                    <div class="list-table-head flex-space-btw">
                        <div class="list-tabmenu" role="tablist">
                            <div class="nav nav-tabs" role="presentation">
                                <button class="tab-btn nav-link active" data-toggle="tab" data-target="#tabReceivePayorderNotYet" type="button" role="tab">
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
                                    <span id="payOrder" onclick="PayOrder('payorder')">Pay Order Receive Not Yet</span>
                                </button>
                                <button class="tab-btn nav-link" data-toggle="tab" data-target="#tabReceivePayorder" type="button" role="tab">
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
                                    <span id="receivedOrder" onclick="PayOrder('rpayorder')">Received Pay order</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane show active fade" id="tabReceivePayorderNotYet">
                            <div class="lists-tab-content">
                                <div class="table-responsive">
                                    <table id="pending_list" class="table table-striped table-bordered dt-responsive" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th><input class="table-checkbox" type="checkbox" id="checkPendingAll"></th>
                                                <th>SL</th>
                                                <th>Tracking No</th>
                                                <th>Agency Name</th>
                                                <th>HL</th>
                                                <th>Pay Order No</th>
                                                <th>Created Date</th>
                                                <th>Total Pilgrim</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tabReceivePayorder">
                            <div class="lists-tab-content">
                                <div class="table-responsive">
                                    <table id="received_list" class="table table-striped table-bordered dt-responsive" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th><input class="table-checkbox" type="checkbox" id="checkReceivedAll"></th>
                                                <th>SL</th>
                                                <th>Tracking No</th>
                                                <th>Agency Name</th>
                                                <th>HL</th>
                                                <th>Pay Order No</th>
                                                <th>Created Date</th>
                                                <th>Total Pilgrim</th>
                                                <th>Status</th>
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

    <div class="modal ba-modal fade" id="ehajjPayorderSearchByHL" tabindex="-1" aria-labelledby="ehajjPayorderSearchByHL" aria-hidden="true">
        <div class="modal-dialog">
            {!! Form::open(array('url' => 'pay-order-received/hl-search', 'method' => 'get','id'=>'payOrderHlSearch')) !!}
            <div class="modal-content search-modal-content">
                <div class="modal-header">
                    <h3>Pay Order Search By HL</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="icon-close-modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <g clip-path="url(#clip0_2216_3720)">
                                    <path d="M0.292786 0.292105C0.480314 0.104634 0.734622 -0.000681838 0.999786 -0.000681838C1.26495 -0.000681838 1.51926 0.104634 1.70679 0.292105L7.99979 6.5851L14.2928 0.292105C14.385 0.196594 14.4954 0.120412 14.6174 0.0680032C14.7394 0.0155942 14.8706 -0.011992 15.0034 -0.0131458C15.1362 -0.0142997 15.2678 0.0110021 15.3907 0.0612829C15.5136 0.111564 15.6253 0.185817 15.7192 0.27971C15.8131 0.373602 15.8873 0.485254 15.9376 0.608151C15.9879 0.731047 16.0132 0.862727 16.012 0.995506C16.0109 1.12829 15.9833 1.25951 15.9309 1.38151C15.8785 1.50351 15.8023 1.61386 15.7068 1.7061L9.41379 7.9991L15.7068 14.2921C15.8889 14.4807 15.9897 14.7333 15.9875 14.9955C15.9852 15.2577 15.88 15.5085 15.6946 15.6939C15.5092 15.8793 15.2584 15.9845 14.9962 15.9868C14.734 15.9891 14.4814 15.8883 14.2928 15.7061L7.99979 9.4131L1.70679 15.7061C1.51818 15.8883 1.26558 15.9891 1.00339 15.9868C0.741189 15.9845 0.490376 15.8793 0.304968 15.6939C0.11956 15.5085 0.0143908 15.2577 0.0121124 14.9955C0.00983399 14.7333 0.110628 14.4807 0.292786 14.2921L6.58579 7.9991L0.292786 1.7061C0.105315 1.51858 0 1.26427 0 0.999105C0 0.73394 0.105315 0.479632 0.292786 0.292105Z" fill="black"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_2216_3720">
                                        <rect width="16" height="16" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-src">
                        <div class="form-group">
                            <label for="">HL Number <span class="clr-red">*</span></label>
                            <input type="number" name="hl_no" class="form-control" placeholder="Enter License Number">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-group-center">
                        <button class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
                        <button class="btn btn-primary" type="submit">
                            <span class="btn-svg-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.16667 3.33339C6.28261 3.33339 5.43477 3.6846 4.80964 4.30975C4.18452 4.9349 3.83333 5.78279 3.83333 6.66689C3.83333 7.55098 4.18452 8.39887 4.80964 9.02402C5.43477 9.64918 6.28261 10.0004 7.16667 10.0004C8.05072 10.0004 8.89857 9.64918 9.52369 9.02402C10.1488 8.39887 10.5 7.55098 10.5 6.66689C10.5 5.78279 10.1488 4.9349 9.52369 4.30975C8.89857 3.6846 8.05072 3.33339 7.16667 3.33339ZM2.5 6.66689C2.5 5.92769 2.67558 5.19907 3.01229 4.54102C3.34899 3.88297 3.83719 3.31431 4.43667 2.88189C5.03615 2.44946 5.72978 2.16562 6.46042 2.05375C7.19107 1.94189 7.93784 2.00518 8.63923 2.23844C9.34063 2.47169 9.97659 2.86823 10.4947 3.39539C11.0129 3.92255 11.3984 4.56527 11.6196 5.2706C11.8407 5.97594 11.8912 6.72372 11.7668 7.45237C11.6424 8.18102 11.3466 8.8697 10.904 9.46169L14.3047 12.8619C14.4298 12.9869 14.5001 13.1565 14.5001 13.3333C14.5002 13.5102 14.43 13.6798 14.305 13.8049C14.18 13.93 14.0104 14.0003 13.8336 14.0004C13.6567 14.0004 13.4871 13.9302 13.362 13.8052L9.962 10.4051C9.26834 10.924 8.44395 11.2396 7.58111 11.3165C6.71828 11.3934 5.85105 11.2287 5.07654 10.8407C4.30202 10.4527 3.65079 9.85675 3.19574 9.1196C2.74068 8.38245 2.49978 7.53319 2.5 6.66689Z" fill="white"/>
                                </svg>
                            </span>
                            <span class="btn-text">Search</span>
                        </button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="modal ba-modal fade" id="ehajjPayorderSearchByVoucher" tabindex="-1" aria-labelledby="ehajjPayorderSearchByVoucher" aria-hidden="true">
        <div class="modal-dialog">
            {!! Form::open(array('url' => 'pay-order-received/voucher-search', 'method' => 'get','id'=>'payOrderVoucherSearch')) !!}
            <div class="modal-content search-modal-content">
                <div class="modal-header">
                    <h3>Pay Order Search by Voucher</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="icon-close-modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <g clip-path="url(#clip0_2216_3720)">
                                    <path d="M0.292786 0.292105C0.480314 0.104634 0.734622 -0.000681838 0.999786 -0.000681838C1.26495 -0.000681838 1.51926 0.104634 1.70679 0.292105L7.99979 6.5851L14.2928 0.292105C14.385 0.196594 14.4954 0.120412 14.6174 0.0680032C14.7394 0.0155942 14.8706 -0.011992 15.0034 -0.0131458C15.1362 -0.0142997 15.2678 0.0110021 15.3907 0.0612829C15.5136 0.111564 15.6253 0.185817 15.7192 0.27971C15.8131 0.373602 15.8873 0.485254 15.9376 0.608151C15.9879 0.731047 16.0132 0.862727 16.012 0.995506C16.0109 1.12829 15.9833 1.25951 15.9309 1.38151C15.8785 1.50351 15.8023 1.61386 15.7068 1.7061L9.41379 7.9991L15.7068 14.2921C15.8889 14.4807 15.9897 14.7333 15.9875 14.9955C15.9852 15.2577 15.88 15.5085 15.6946 15.6939C15.5092 15.8793 15.2584 15.9845 14.9962 15.9868C14.734 15.9891 14.4814 15.8883 14.2928 15.7061L7.99979 9.4131L1.70679 15.7061C1.51818 15.8883 1.26558 15.9891 1.00339 15.9868C0.741189 15.9845 0.490376 15.8793 0.304968 15.6939C0.11956 15.5085 0.0143908 15.2577 0.0121124 14.9955C0.00983399 14.7333 0.110628 14.4807 0.292786 14.2921L6.58579 7.9991L0.292786 1.7061C0.105315 1.51858 0 1.26427 0 0.999105C0 0.73394 0.105315 0.479632 0.292786 0.292105Z" fill="black"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_2216_3720">
                                        <rect width="16" height="16" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-src">
                        <div class="form-group">
                            <label>Voucher Number <span class="clr-red">*</span></label>
                            <input type="text" name="voucher_no" class="form-control" placeholder="Enter Voucher Number">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-group-center">
                        <button class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
                        <button class="btn btn-primary" type="submit">
                            <span class="btn-svg-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.16667 3.33339C6.28261 3.33339 5.43477 3.6846 4.80964 4.30975C4.18452 4.9349 3.83333 5.78279 3.83333 6.66689C3.83333 7.55098 4.18452 8.39887 4.80964 9.02402C5.43477 9.64918 6.28261 10.0004 7.16667 10.0004C8.05072 10.0004 8.89857 9.64918 9.52369 9.02402C10.1488 8.39887 10.5 7.55098 10.5 6.66689C10.5 5.78279 10.1488 4.9349 9.52369 4.30975C8.89857 3.6846 8.05072 3.33339 7.16667 3.33339ZM2.5 6.66689C2.5 5.92769 2.67558 5.19907 3.01229 4.54102C3.34899 3.88297 3.83719 3.31431 4.43667 2.88189C5.03615 2.44946 5.72978 2.16562 6.46042 2.05375C7.19107 1.94189 7.93784 2.00518 8.63923 2.23844C9.34063 2.47169 9.97659 2.86823 10.4947 3.39539C11.0129 3.92255 11.3984 4.56527 11.6196 5.2706C11.8407 5.97594 11.8912 6.72372 11.7668 7.45237C11.6424 8.18102 11.3466 8.8697 10.904 9.46169L14.3047 12.8619C14.4298 12.9869 14.5001 13.1565 14.5001 13.3333C14.5002 13.5102 14.43 13.6798 14.305 13.8049C14.18 13.93 14.0104 14.0003 13.8336 14.0004C13.6567 14.0004 13.4871 13.9302 13.362 13.8052L9.962 10.4051C9.26834 10.924 8.44395 11.2396 7.58111 11.3165C6.71828 11.3934 5.85105 11.2287 5.07654 10.8407C4.30202 10.4527 3.65079 9.85675 3.19574 9.1196C2.74068 8.38245 2.49978 7.53319 2.5 6.66689Z" fill="white"/>
                                </svg>
                            </span>
                            <span class="btn-text">Search</span>
                        </button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="modal ba-modal fade" id="ehajjPayorderSearchByReference" tabindex="-1" aria-labelledby="ehajjPayorderSearchByReference" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content search-modal-content">
                <div class="modal-header">
                    <h3>Reference Info</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="icon-close-modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <g clip-path="url(#clip0_2216_3720)">
                                    <path d="M0.292786 0.292105C0.480314 0.104634 0.734622 -0.000681838 0.999786 -0.000681838C1.26495 -0.000681838 1.51926 0.104634 1.70679 0.292105L7.99979 6.5851L14.2928 0.292105C14.385 0.196594 14.4954 0.120412 14.6174 0.0680032C14.7394 0.0155942 14.8706 -0.011992 15.0034 -0.0131458C15.1362 -0.0142997 15.2678 0.0110021 15.3907 0.0612829C15.5136 0.111564 15.6253 0.185817 15.7192 0.27971C15.8131 0.373602 15.8873 0.485254 15.9376 0.608151C15.9879 0.731047 16.0132 0.862727 16.012 0.995506C16.0109 1.12829 15.9833 1.25951 15.9309 1.38151C15.8785 1.50351 15.8023 1.61386 15.7068 1.7061L9.41379 7.9991L15.7068 14.2921C15.8889 14.4807 15.9897 14.7333 15.9875 14.9955C15.9852 15.2577 15.88 15.5085 15.6946 15.6939C15.5092 15.8793 15.2584 15.9845 14.9962 15.9868C14.734 15.9891 14.4814 15.8883 14.2928 15.7061L7.99979 9.4131L1.70679 15.7061C1.51818 15.8883 1.26558 15.9891 1.00339 15.9868C0.741189 15.9845 0.490376 15.8793 0.304968 15.6939C0.11956 15.5085 0.0143908 15.2577 0.0121124 14.9955C0.00983399 14.7333 0.110628 14.4807 0.292786 14.2921L6.58579 7.9991L0.292786 1.7061C0.105315 1.51858 0 1.26427 0 0.999105C0 0.73394 0.105315 0.479632 0.292786 0.292105Z" fill="black"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_2216_3720">
                                        <rect width="16" height="16" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-src">
                        <div class="form-group">
                            <label>Reference Number</label>
                            <input type="text" class="form-control" placeholder="Enter Reference Number">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-group-center">
                        <button class="btn btn-default" data-dismiss="modal" aria-label="Close">Close</button>
                        <button class="btn btn-primary" type="submit">
                            <span class="btn-svg-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.16667 3.33339C6.28261 3.33339 5.43477 3.6846 4.80964 4.30975C4.18452 4.9349 3.83333 5.78279 3.83333 6.66689C3.83333 7.55098 4.18452 8.39887 4.80964 9.02402C5.43477 9.64918 6.28261 10.0004 7.16667 10.0004C8.05072 10.0004 8.89857 9.64918 9.52369 9.02402C10.1488 8.39887 10.5 7.55098 10.5 6.66689C10.5 5.78279 10.1488 4.9349 9.52369 4.30975C8.89857 3.6846 8.05072 3.33339 7.16667 3.33339ZM2.5 6.66689C2.5 5.92769 2.67558 5.19907 3.01229 4.54102C3.34899 3.88297 3.83719 3.31431 4.43667 2.88189C5.03615 2.44946 5.72978 2.16562 6.46042 2.05375C7.19107 1.94189 7.93784 2.00518 8.63923 2.23844C9.34063 2.47169 9.97659 2.86823 10.4947 3.39539C11.0129 3.92255 11.3984 4.56527 11.6196 5.2706C11.8407 5.97594 11.8912 6.72372 11.7668 7.45237C11.6424 8.18102 11.3466 8.8697 10.904 9.46169L14.3047 12.8619C14.4298 12.9869 14.5001 13.1565 14.5001 13.3333C14.5002 13.5102 14.43 13.6798 14.305 13.8049C14.18 13.93 14.0104 14.0003 13.8336 14.0004C13.6567 14.0004 13.4871 13.9302 13.362 13.8052L9.962 10.4051C9.26834 10.924 8.44395 11.2396 7.58111 11.3165C6.71828 11.3934 5.85105 11.2287 5.07654 10.8407C4.30202 10.4527 3.65079 9.85675 3.19574 9.1196C2.74068 8.38245 2.49978 7.53319 2.5 6.66689Z" fill="white"/>
                                </svg>
                            </span>
                            <span class="btn-text">Search</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-script')
    @include('partials.datatable-js')
    <script>
        var ptable = "";
        var rtable = "";
        $(function() {
             ptable = $('#pending_list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('pay-order-received/pending') }}",
                    method: 'get',
                    data: function(d) {
                        d._token = $('input[name="_token"]').val();
                    }
                },
                columns: [
                    {
                        data: 'checkbox_row', name: 'checkbox_row', orderable: false, searchable: false,
                    },
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
                        data: 'AgencyName',
                        name: 'AgencyName'
                    },
                    {
                        data: 'HL',
                        name: 'HL'
                    },
                    {
                        data: 'pay_order',
                        name: 'pay_order'
                    },
                    {
                        data: 'payorder_date',
                        name: 'payorder_date'
                    },
                    {
                        data: 'no_of_pilgrim',
                        name: 'no_of_pilgrim',
                    },
                    {
                        data: 'status_id',
                        name: 'status_id'
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
                    $('td:eq(1)', row).html(counter);
                },
                "aaSorting": []
            });
             rtable = $('#received_list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('pay-order-received/received') }}",
                    method: 'get',
                    data: function(d) {
                        d._token = $('input[name="_token"]').val();
                    }
                },
                columns: [
                    {
                        data: 'checkbox_row', name: 'checkbox_row', orderable: false, searchable: false,
                    },
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
                        data: 'AgencyName',
                        name: 'AgencyName'
                    },
                    {
                        data: 'HL',
                        name: 'HL'
                    },
                    {
                        data: 'pay_order',
                        name: 'pay_order'
                    },
                    {
                        data: 'payorder_date',
                        name: 'payorder_date'
                    },
                    {
                        data: 'no_of_pilgrim',
                        name: 'no_of_pilgrim',
                    },
                    {
                        data: 'status_id',
                        name: 'status_id'
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
                    $('td:eq(1)', row).html(counter);
                },
                "aaSorting": []
            });
        });
        $(document).ready(function() {
            $('#checkPendingAll').on('click', function () {
                $('.row-pending-checkbox').prop('checked', this.checked);
            });
            $('#checkPendingAll').on('change', function () {
                var bulkReceiveButton = $('#bulkReceiveButton');
                if ($(this).prop('checked')) {
                    bulkReceiveButton.show();
                } else {
                    bulkReceiveButton.hide();
                }
            });
            $('#pending_list tbody').on('change', '.row-pending-checkbox', function () {
                if ($('.row-pending-checkbox:checked').length === $('.row-pending-checkbox').length) {
                    $('#checkPendingAll').prop('checked', true);
                } else {
                    $('#checkPendingAll').prop('checked', false);
                }
                if ($('.row-pending-checkbox:checked').length > 0) {
                    $('#bulkReceiveButton').show();
                } else {
                    $('#bulkReceiveButton').hide();
                }
            });
            $('#checkReceivedAll').on('click', function () {
                $('.row-received-checkbox').prop('checked', this.checked);
            });
            $('#checkReceivedAll').on('change', function () {
                if ($(this).prop('checked')) {
                    $('#bulkReturnButton').show();
                } else {
                    $('#bulkReturnButton').hide();
                }
            });
            $('#received_list tbody').on('change', '.row-received-checkbox', function () {
                if ($('.row-received-checkbox:checked').length === $('.row-received-checkbox').length) {
                    $('#checkReceivedAll').prop('checked', true);
                } else {
                    $('#checkReceivedAll').prop('checked', false);
                }
                if ($('.row-received-checkbox:checked').length > 0) {
                    $('#bulkReturnButton').show();
                } else {
                    $('#bulkReturnButton').hide();
                }
            });
        });
        function confirmPayOrder(rowId, type) {
            var confirmMsg = "Do you want to receive this item?";
            if (confirm(confirmMsg)) {
                $.ajax({
                    url: "{{ url('pay-order-received/confirm') }}",
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        id: [rowId],
                        type: type
                    },
                    success: function (response) {
                        if(response.responseCode == 1){
                            ptable.ajax.reload();
                            rtable.ajax.reload();
                            $('#bulkReturnButton').hide();
                            $('#bulkReceiveButton').hide();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }
        }
        $('#bulkProcessButton').on('click', function () {
            var selectedIds = $('.row-checkbox:checked').map(function () {
                return $(this).data('id');
            }).get();
            var process_type_id = $(document).find('#selected_process_type').val();
            var process_type_status = $(document).find('#selected_process_status').val();
            $.ajax({
                url: '{{ url('bulk-process-application') }}',
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    id: selectedIds,
                    process_type_id: process_type_id,
                    status_id: process_type_status
                },
                success: function (response) {
                    $('#bulkApplicationFormContent').html(response);
                    // Trigger the modal to open
                    $('#bulkApplicationFormDiv').modal('show');
                    $("#application_status").trigger('change');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        });

        function bulkConfirmPayOrder(rowId, type) {
            var checkbox = "";
            var confirmMsg = "";
            var bulkReceiveButton = $('#bulkReceiveButton');
            var bulkReturnButton = $('#bulkReturnButton');
            if (type == "received"){
                confirmMsg = "Do you want to receive this item?";
                checkbox = $('.row-pending-checkbox:checked');
            }else{
                confirmMsg = "Do you want to return this item?";
                checkbox = $('.row-received-checkbox:checked');
            }
            if (confirm(confirmMsg)) {
                    if( checkbox.length <= 0){
                        alert('Please select at least one voucher');
                        return false;
                    }
                    rowIds = checkbox.map(function () {
                        return $(this).data('id');
                    }).get();
                    $.ajax({
                        url: "{{ url('pay-order-received/confirm') }}",
                        type: "GET",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            id: rowIds,
                            type: type
                        },
                        success: function (response) {
                            if(response.responseCode == 1){
                                ptable.ajax.reload();
                                rtable.ajax.reload();
                                bulkReturnButton.hide();
                                bulkReceiveButton.hide();

                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                        }
                    });
            }
        }
        function PayOrder(type) {
            var bulkReceiveButton = document.getElementById("bulkReceiveButton");
            var bulkReturnButton = document.getElementById("bulkReturnButton");
            if (type== 'payorder'){
                bulkReturnButton.style.display = "none"
            }else{
                bulkReceiveButton.style.display = "none"
            }

        }
    </script>
@endsection
