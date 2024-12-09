<style>
    .payment-info {
        font-weight: normal;
    }

</style>

<div class="card">
    <div>
        <h5 class="card-header bg-success"><strong>Payment Information</strong></h5>
    </div>
    <div class="card-body payment-info">
        @foreach ($payment_info as $payment)
            <div class="card">
                <div >
                    <h5 class="card-header bg-info"><b>{{ $payment->payment_name }}</b></h5>
                </div>
                <div class="card-body">
                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Contact name</span>
                                    <span class="float-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $payment->contact_name }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Contact email</span>
                                    <span class="float-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $payment->contact_email }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Contact phone</span>
                                    <span class="float-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $payment->contact_no }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Contact address</span>
                                    <span class="float-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $payment->address }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Pay amount</span>
                                    <span class="float-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $payment->pay_amount }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">VAT on pay amount</span>
                                    <span class="float-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $payment->vat_on_pay_amount }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Transaction charge</span>
                                    <span class="float-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $payment->transaction_charge_amount }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">VAT on transaction charge</span>
                                    <span class="float-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $payment->vat_on_transaction_charge }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Total Amount</span>
                                    <span class="float-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $payment->total_amount }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Payment status</span>
                                    <span class="float-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    @if ($payment->payment_status == 0)
                                        <span class="label label-warning">Pending</span>
                                    @elseif($payment->payment_status == -1)
                                        <span class="label label-info">In-progress</span>
                                    @elseif($payment->payment_status == 1)
                                        <span class="label label-success">Paid</span>
                                    @elseif($payment->payment_status == 2)
                                        <span class="label label-danger">Exception</span>
                                    @elseif($payment->payment_status == 3)
                                        <span class="label label-warning">Waiting for payment confirmation</span>
                                    @elseif($payment->payment_status == -3)
                                        <span class="label label-warning">Payment cancelled</span>
                                    @else
                                        <span class="label label-warning">Invalid status</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-5">
                                    <span class="v_label">Payment mode</span>
                                    <span class="float-right">&#58;</span>
                                </div>
                                <div class="col-md-7">
                                    {{ $payment->pay_mode }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-12">
                            <a class="btn btn-default btn-block" data-toggle="collapse"
                                href="#multiCollapseExample{{ $payment->id }}" role="button" aria-expanded="false"
                                aria-controls="multiCollapseExample{{ $payment->id }}">
                                <b><i class="fa fa-list-alt"></i> View Amount Distribution Details</b>
                            </a>

                            <div class="collapse" id="multiCollapseExample{{ $payment->id }}">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0 no-margin">
                                        <thead>
                                            <tr>
                                                <th>Account No.</th>
                                                <th>Fees Type</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (explode(';', $payment->payment_details) as $distribution)
                                                @php
                                                    $distribution_info = explode(',', $distribution);
                                                @endphp
                                                <tr>
                                                    <td>{{ isset($distribution_info[0]) ? $distribution_info[0] : 'N/A' }}
                                                    </td>
                                                    <td>{{ isset($distribution_info[1]) ? $payment_distribution_types[trim($distribution_info[1])] : 'N/A' }}
                                                    </td>
                                                    <td>{{ isset($distribution_info[2]) ? $distribution_info[2] : 'N/A' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="float-left">
                        <a href="/spg/{{ $payment->pay_mode_code == 'A01' ? 'counter-' : '' }}payment-voucher/{{ Encryption::encodeId($payment->id) }}"
                            target="_blank" class="btn btn-info btn-sm">
                            <strong> Download voucher</strong>
                        </a>
                    </div>

                    {{-- Counter payment, 3 = Waiting for Payment Confirmation --}}
                    @if ($payment->payment_status == 3 && $app_status == 3 && in_array(Auth::user()->user_type, ['5x505', '6x606']))
                        <div class="float-right">
                            <a href="/spg/counter-payment-check/{{ Encryption::encodeId($payment->id) }}/{{ Encryption::encodeId(0) }}"
                                class="btn btn-danger btn-sm cancelcounterpayment">
                                <strong> Cancel payment request</strong>
                            </a>
                            <a href="/spg/counter-payment-check/{{ Encryption::encodeId($payment->id) }}/{{ Encryption::encodeId(1) }}"
                                class="btn btn-primary btn-sm">
                                <strong> Confirm payment request</strong>
                            </a>
                        </div>
                    @endif
                    <div class="clearfix"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
