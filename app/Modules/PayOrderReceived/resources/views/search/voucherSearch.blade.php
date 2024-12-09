@extends('layouts.admin')
@section('header-resources')
    @include('partials.datatable-css')
@endsection
@section('content')
    @include('partials.messages')
    <div class="dash-content-main">
        <div class="dash-section-title">
            <h3>@if(!empty($pay_order_info->airlines_name)){{$pay_order_info->airlines_name}}@endif</h3>
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
                    <h3>Voucher No : @if(!empty($pay_order_info->tracking_no)){{$pay_order_info->tracking_no}}@endif</h3>
                </div>
            </div>
            <div class="bd-card-content">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="flight-info-lists">
                            <ul>
                                <li>
                                    <span class="flight-info-list-title">Ref. No</span>
                                    <span class="flight-info-list-desc">@if(!empty($pay_order_info->ref_no)){{$pay_order_info->ref_no}}@endif</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Haj License No</span>
                                    <span class="flight-info-list-desc">@if(!empty($pay_order_info->HL)){{$pay_order_info->HL}}@endif</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Bank Name</span>
                                    <span class="flight-info-list-desc">@if(!empty($pay_order_info->bank_name)){{$pay_order_info->bank_name}}@endif</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Depositor Mobile No</span>
                                    <span class="flight-info-list-desc">@if(!empty($pay_order_info->depositor_mobile_no)){{$pay_order_info->depositor_mobile_no}}@endif</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Pay order Date</span>
                                    <span class="flight-info-list-desc">@if(!empty($pay_order_info->payorder_date)){{\Carbon\Carbon::parse($pay_order_info->payorder_date)->format('d-M-Y')}}@endif</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Pay order Number</span>
                                    <span class="flight-info-list-desc">@if(!empty($pay_order_info->pay_order)){{$pay_order_info->pay_order}}@endif</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="flight-info-lists">
                            <ul>
                                <li>
                                    <span class="flight-info-list-title">Agency</span>
                                    <span class="flight-info-list-desc">@if(!empty($pay_order_info->AgencyName)){{$pay_order_info->AgencyName}}@endif</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">No of pilgrim</span>
                                    <span class="flight-info-list-desc">@if(!empty($pay_order_info->no_of_pilgrim)){{$pay_order_info->no_of_pilgrim}}@endif</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Depositor Name</span>
                                    <span class="flight-info-list-desc">@if(!empty($pay_order_info->depositor_name)){{$pay_order_info->depositor_name}}@endif</span>
                                </li>
                                <li>
                                    <span class="flight-info-list-title">Ticketing Agent</span>
                                    <span class="flight-info-list-desc">@if(!empty($pay_order_info->ticketing_agent)){{$pay_order_info->ticketing_agent}}@endif</span>
                                </li>
                                @if(!empty($pay_order_info->ticketing_agent) && $pay_order_info->ticketing_agent != 'self')
                                <li>
                                    <span class="flight-info-list-title">Ticketing Agency</span>
                                    <span class="flight-info-list-desc">@if(!empty($pay_order_info->ticketing_agency)){{$pay_order_info->ticketing_agency}}@endif @if(!empty($pay_order_info->ticketing_agency_hl))({{$pay_order_info->ticketing_agency_hl}})@endif</span>
                                </li>
                                @endif
                                <li>
                                    <span class="flight-info-list-title">Certificate</span>
                                    <a href="@if(!empty($pay_order_info->crt_url)){{$pay_order_info->crt_url}}@elseif('#'){{'#'}}@endif" target="_blank" class="btn-outline-blue btn-squire" style="cursor: pointer"><i class="fa fa-eye"></i> View</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <?php $sl = 0; ?>
                    @if(count($pilgrims)==0)
                        <div class="text-center text-danger"><h4>No pilgrim found.</h4></div>
                    @else
                        <table class="table table-striped table-bordered dt-responsive nowrap"
                               cellspacing="0"
                               width="100%">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Tracking No</th>
                                <th>PID</th>
                                <th>Name</th>
                                <th>Passport</th>
                                {{--@if($ticketReqData->status_id == 0)--}}
{{--                                <th>Action</th>--}}
                                {{--@endif--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pilgrims as $pilgrim)
                                <tr class="{!! ($pilgrim->reg_payment_status == 12) ? 'text-danger':'' !!}">
                                    <td>{!! ++$sl; !!}</td>
                                    <td>{!! $pilgrim->tracking_no !!}</td>
                                    <td>{!! $pilgrim->pid !!}</td>
                                    <td>{!! $pilgrim->full_name_english !!}</td>
                                    <td>{!! $pilgrim->passport_no !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div><!-- /.table-responsive -->
            </div>
            <div class="bd-card-footer">
                @if($pay_order_info->status_id == 2)
                <div class="flex-space-btw info-btn-group">
                    <a href="{{url('pay-order-received')}}">
                        <button class="btn btn-default"><span>Close</span></button>
                    </a>
                    <a class="btn-outline-blue btn-squire" style="cursor: pointer" onclick="confirmPayOrder('{{\App\Libraries\Encryption::encodeId($pay_order_info->id)}}' , 'received')">Receive</a>
                </div>
                @else
                <div class="flex-space-btw info-btn-group">
                    <a href="{{url('pay-order-received')}}">
                        <button class="btn btn-default"><span>Close</span></button>
                    </a>
                    <a class="btn-outline-blue btn-squire" style="cursor: pointer" onclick="confirmPayOrder('{{\App\Libraries\Encryption::encodeId($pay_order_info->id)}}' , 'return')">Return</a>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('footer-script')
    @include('partials.datatable-js')
    <script>
        $(function() {

        });
        function confirmPayOrder(rowId, type) {
            var confirmMsg = "";
            if (type == "received"){
                confirmMsg = "Do you want to receive this item?";
            }else{
                confirmMsg = "Do you want to return this item?";
            }
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
                            window.location.reload();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                        return false;
                    }
                });
            }
        }
    </script>
@endsection
