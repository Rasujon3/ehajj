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
                        <h3>HL : @if(!empty($pay_order_info->HL)){{$pay_order_info->HL}}@endif</h3>
                    </div>
                </div>
                <div class="bd-card-content">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="flight-info-lists">
                                <ul>
                                    <li>
                                        <span class="flight-info-list-title">Agency</span>
                                        <span class="flight-info-list-desc">@if(!empty($pay_order_info->AgencyName)){{$pay_order_info->AgencyName}}@endif</span>
                                    </li>
                                    <li>
                                        <span class="flight-info-list-title">Haj License No</span>
                                        <span class="flight-info-list-desc">@if(!empty($pay_order_info->HL)){{$pay_order_info->HL}}@endif</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="flight-info-lists">
                                <ul>
                                    <li>
                                        <span class="flight-info-list-title">Depositor Name</span>
                                        <span class="flight-info-list-desc">@if(!empty($pay_order_info->depositor_name)){{$pay_order_info->depositor_name}}@endif</span>
                                    </li>
                                    <li>
                                        <span class="flight-info-list-title">No of pilgrim</span>
                                        <span class="flight-info-list-desc">@if(!empty($pay_order_info->no_of_total_pilgrim)){{$pay_order_info->no_of_total_pilgrim}}@endif</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="border-card-block mt-4">
                        <div class="bd-card-head">
                            <div class="bd-card-title">
                                <h3>List of Pending Voucher</h3>
                            </div>
                        </div>
                        <div class="bd-card-content">
                            <div class="ehajj-list-table">
                                <div class="table-responsive">
                                    <table id="table_list" class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th width="50"><input class="table-checkbox" type="checkbox" id="checkAll"></th>
                                            <th>SL No.</th>
                                            <th>Voucher Number</th>
                                            <th>Bank Name</th>
                                            <th>Pay Order No</th>
                                            <th>No of Pilgrim</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(!empty($pay_order_lists))
                                            @php $i =0; @endphp
                                            @foreach($pay_order_lists as $pay_order_list)
                                                <tr>
                                                    <td><input class="table-checkbox row-checkbox" type="checkbox" data-id="{{\App\Libraries\Encryption::encodeId($pay_order_list->id)}}"></td>
                                                    <td>{{++$i}}</td>
                                                    <td>@if(!empty($pay_order_list->tracking_no)){{$pay_order_list->tracking_no}}@endif</td>
                                                    <td>@if(!empty($pay_order_list->bank_name)){{$pay_order_list->bank_name}}@endif</td>
                                                    <td>@if(!empty($pay_order_list->pay_order)){{$pay_order_list->pay_order}}@endif</td>
                                                    <td>@if(!empty($pay_order_list->no_of_pilgrim)){{$pay_order_list->no_of_pilgrim}}@endif</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bd-card-footer">
                    <div class="flex-space-btw info-btn-group">
                        <a href="{{url('pay-order-received')}}">
                            <button class="btn btn-default"><span>Close</span></button>
                        </a>
                        <button type="button" onclick="confirmPayOrder('', 'received')" class="btn btn-outline-blue btn-squire" ><span>Receive all Voucher</span></button>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('footer-script')
    @include('partials.datatable-js')
    <script>
        $(document).ready(function() {
            $('#checkAll').on('click', function () {
                $('.row-checkbox').prop('checked', this.checked);
            });
            $('#table_list tbody').on('change', '.row-checkbox', function () {
                if ($('.row-checkbox:checked').length === $('.row-checkbox').length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
            });
        });

        function confirmPayOrder(rowId, type) {
            var confirmMsg = "Do you want to receive this item?";
            if (confirm(confirmMsg)) {
                if($('.row-checkbox:checked').length <= 0){
                    alert('Please select at least one voucher');
                    return false;
                }
                rowId = $('.row-checkbox:checked').map(function () {
                    return $(this).data('id');
                }).get();
                $.ajax({
                    url: "{{ url('pay-order-received/confirm') }}",
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        id: rowId,
                        type: type
                    },
                    success: function (response) {
                        if(response.responseCode == 1){
                            window.location.href = "{{url('/pay-order-received')}}";
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }
        }
    </script>
@endsection
