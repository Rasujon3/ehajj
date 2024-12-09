<?php

namespace App\Modules\SonaliPayment\Services;

use App\Libraries\CommonFunction;
use App\Modules\SonaliPayment\Http\Controllers\PaymentPanelController;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\SonaliPayment\Models\PaymentDetails;
use App\Modules\SonaliPayment\Models\PaymentDistribution;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait SPPaymentManager
{
    public function storeSubmissionFeeData($app_id, $payment_step_id, array $contact_info, array $unfixed_amounts = [])
    {
        // Checking the Service Fee Payment(SFP) configuration for this service
        $payment_config = PaymentConfiguration::leftJoin(
            'sp_payment_steps',
            'sp_payment_steps.id',
            '=',
            'sp_payment_configuration.payment_step_id'
        )
            ->where([
                'sp_payment_configuration.process_type_id' => $this->process_type_id,
                'sp_payment_configuration.payment_step_id' => $payment_step_id,
                'sp_payment_configuration.status' => 1,
                'sp_payment_configuration.is_archive' => 0,
            ])->first(['sp_payment_configuration.*', 'sp_payment_steps.name']);
        if (!$payment_config) {
            Session::flash('error', "Payment configuration not found [SPPM-100]");
            // $messages = 'Payment configuration not found [SPPM-100]';
            // return response()->json(['responseCode' => 0, 'status' => false,  'html' => $messages]);
            // return $messages;
            return redirect()->back()->withInput();
        }

        // Checking the payment distributor under payment configuration
        $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
            ->where('status', 1)
            ->where('is_archive', 0)
            ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'purpose', 'purpose_sbl', 'fix_status', 'distribution_type']);
        if ($stakeDistribution->isEmpty()) {
            Session::flash('error', "Stakeholder not found [SPPM-101]");
            return redirect()->back()->withInput();
        }

        // Store payment info
        $paymentInfo = SonaliPayment::firstOrNew([
            'app_id' => $app_id,
            'process_type_id' => $this->process_type_id,
            'payment_config_id' => $payment_config->id
        ]);
        $paymentInfo->payment_step_id = $payment_step_id;

        //Concat Account no of stakeholder
        $account_no = "";
        foreach ($stakeDistribution as $distribution) {
            $account_no .= $distribution->stakeholder_ac_no . "-";
        }
        $account_numbers = rtrim($account_no, '-');
        //Concat Account no of stakeholder End

        $paymentInfo->receiver_ac_no = $account_numbers;
        $payment_amounts =  PaymentPanelController::unfixedAmountsForPayment($stakeDistribution, $unfixed_amounts);

        /*
         * The amount of unfixed Amount defaults to 0, if there is an unfixed Amount for this payment,
         * then this Amount will come when you arrive at this function.
         * And it will be added to the pay_amount, charge_amount, vat_amount
         */
        $paymentInfo->pay_amount = $payment_amounts['pay_amount_total'];
        $paymentInfo->vat_on_pay_amount = $payment_amounts['vat_on_pay_amount_total'];
        $paymentInfo->total_amount = ($paymentInfo->pay_amount + $paymentInfo->vat_on_pay_amount);
        $paymentInfo->contact_name = $contact_info['contact_name'];
        $paymentInfo->contact_email = $contact_info['contact_email'];
        $paymentInfo->contact_no = $contact_info['contact_no'];
        $paymentInfo->address = $contact_info['contact_address'];
        $paymentInfo->sl_no = 1; // Always 1
        $paymentInfo->save();


        //Payment Details By Stakeholders
        foreach ($stakeDistribution as $distribution) {
            $paymentDetails = PaymentDetails::firstOrNew([
                'sp_payment_id' => $paymentInfo->id,
                'payment_distribution_id' => $distribution->id
            ]);

            if ($distribution->fix_status == 1) {
                $paymentDetails->pay_amount = $distribution->pay_amount;
            } else {
                $paymentDetails->pay_amount = $unfixed_amounts[$distribution->distribution_type];
            }

            $paymentDetails->receiver_ac_no = $distribution->stakeholder_ac_no;
            $paymentDetails->purpose = $distribution->purpose;
            $paymentDetails->purpose_sbl = $distribution->purpose_sbl;
            $paymentDetails->fix_status = $distribution->fix_status;
            $paymentDetails->distribution_type = $distribution->distribution_type;
            $paymentDetails->save();
        }
        //Payment Details By Stakeholders End

        return $paymentInfo->id;
    }
}
