<?php

namespace App\Modules\SonaliPayment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SonaliPayment\Models\PaymentConfiguration;
use App\Modules\SonaliPayment\Models\PaymentDistribution;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use Illuminate\Http\Request;
use App\Libraries\Encryption;
use App\Modules\IndustryNew\Http\Controllers\IndustryNewController;
use App\Modules\IndustryNew\Models\IndustryNew;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\SonaliPayment\Models\PaymentDistributionType;
use App\Modules\SonaliPayment\Services\SPPaymentManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PaymentPanelController extends Controller
{

    use SPPaymentManager;

    public function __construct()
    {
        // this will need for a function inside SPPaymentManager
        $this->process_type_id = 0;
    }

    public function getPaymentPanel(Request $request)
    {
        $process_type_id = $request->get('process_type_id');
        $payment_step_id = $request->get('payment_step_id');

        $form_data['app_status_id'] = -1;

        if (!empty($request->get('app_id'))) {
            $process_info = ProcessList::where([
                'ref_id' => $request->get('app_id'),
                'process_type_id' => $request->get('process_type_id')
            ])->first([
                'ref_id',
                'process_type_id',
                'company_id',
                'status_id',
            ]);
            $form_data['app_status_id'] = $process_info->status_id;
            $form_data['encoded_process_type_id'] = Encryption::encodeId($process_info->process_type_id);
            $form_data['encoded_app_id'] = Encryption::encodeId($process_info->ref_id);
        }

        $form_data['encoded_payment_step_id'] = Encryption::encodeId($payment_step_id);

        // Need to validate process_type_id and payment_step_id

        // if (is_object($request->get('unfixed_amount_array')) === false) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Error: Unfixed amounts variable should be an object, ' . gettype($request->get('unfixed_amount_array')) . ' given'
        //     ]);
        // }

        $unfixed_amount_array = json_decode($request->get('unfixed_amount_array'), true);

        // Checking the payment configuration for this service
        $payment_config = PaymentConfiguration::leftJoin(
            'sp_payment_steps',
            'sp_payment_steps.id',
            '=',
            'sp_payment_configuration.payment_step_id'
        )
            ->where([
                'sp_payment_configuration.process_type_id' => $process_type_id,
                'sp_payment_configuration.payment_step_id' => $payment_step_id,
                'sp_payment_configuration.status' => 1,
                'sp_payment_configuration.is_archive' => 0
            ])->first(['sp_payment_configuration.*', 'sp_payment_steps.id as payment_step_id', 'sp_payment_steps.name as step_name']);
        if (empty($payment_config)) {
            return response()->json([
                'status' => false,
                'message' => 'Error: payment configuration not found'
            ]);
        }


        // Checking the payment distributor under payment configuration
        $stakeDistribution = PaymentDistribution::where('sp_pay_config_id', $payment_config->id)
            ->where('status', 1)
            ->where('is_archive', 0)
            ->get(['id', 'stakeholder_ac_no', 'pay_amount', 'purpose', 'purpose_sbl', 'fix_status', 'distribution_type']);
        if ($stakeDistribution->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Error: payment distribution not found for this configuration'
            ]);
        }

        $form_data['payment_name'] = $payment_config->payment_name;
        $form_data['contact_name'] = $request->get('contact_name');
        $form_data['contact_email'] = $request->get('contact_email');
        $form_data['contact_phone'] = $request->get('contact_phone');
        $form_data['contact_address'] = $request->get('contact_address');

        $unfixed_amount_calculation = $this->unfixedAmountsForPayment($stakeDistribution, $unfixed_amount_array);

        $form_data['pay_amount'] = $unfixed_amount_calculation['pay_amount_total'];
        $form_data['vat_on_pay_amount'] = $unfixed_amount_calculation['vat_on_pay_amount_total'];
        $form_data['total_amount'] = $form_data['pay_amount'] + $form_data['vat_on_pay_amount'];

        $data['html'] = (string)view('SonaliPayment::payment-ui.create-payment', $form_data);
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }


    public static function unfixedAmountsForPayment($stakeDistribution, $unfixed_amount_array)
    {
        $pay_amount_total = 0;
        $vat_on_pay_amount_total = 0;

        foreach ($stakeDistribution as $stakeholder) {
            if (in_array($stakeholder->distribution_type, [4, 5, 6])) {
                $vat_on_pay_amount_total += empty($stakeholder->pay_amount) ? $unfixed_amount_array[$stakeholder->distribution_type] : $stakeholder->pay_amount;
            } else {
                $pay_amount_total += empty($stakeholder->pay_amount) ? $unfixed_amount_array[$stakeholder->distribution_type] : $stakeholder->pay_amount;
            }
        }

        return [
            'pay_amount_total' => $pay_amount_total,
            'vat_on_pay_amount_total' => $vat_on_pay_amount_total,
        ];
    }

    public function getViewPaymentPanel($process_type_id, $app_id)
    {
        $process_type_id = Encryption::decodeId($process_type_id);
        $app_id = Encryption::decodeId($app_id);
        $data['app_status'] = ProcessList::where([
            'process_type_id' => $process_type_id,
            'ref_id' => $app_id,
        ])->value('status_id');
        $data['payment_info'] = SonaliPayment::leftJoin('sp_payment_configuration', 'sp_payment_configuration.id', '=', 'sp_payment.payment_config_id')
            ->leftJoin('sp_payment_details', 'sp_payment_details.sp_payment_id', '=', 'sp_payment.id')
            ->where('sp_payment.app_id', $app_id)
            ->where('sp_payment.process_type_id', $process_type_id)
            ->groupBy('sp_payment.id')
            ->get([
                'sp_payment.id',
                'sp_payment_configuration.payment_name',
                'sp_payment.contact_name',
                'sp_payment.contact_email',
                'sp_payment.contact_no',
                'sp_payment.address',
                'sp_payment.pay_amount',
                'sp_payment.vat_on_pay_amount',
                'sp_payment.transaction_charge_amount',
                'sp_payment.vat_on_transaction_charge',
                'sp_payment.total_amount',
                'sp_payment.payment_status',
                'sp_payment.pay_mode',
                'sp_payment.pay_mode_code',
                DB::raw('GROUP_CONCAT(CONCAT_WS(", ", sp_payment_details.receiver_ac_no, sp_payment_details.distribution_type, sp_payment_details.pay_amount) SEPARATOR ";") as payment_details')
            ]);
        $data['payment_distribution_types'] = PaymentDistributionType::pluck('name', 'id')->toArray();
        // dd($data['payment_distribution_types']);
        $content = strval(view('SonaliPayment::payment-ui.view-payment', $data));
        return response()->json(['response' => $content]);
    }

    public function getViewPaymentPanelVue($process_type_id, $app_id)
    {
        $process_type_id = Encryption::decodeId($process_type_id);
        $app_id = Encryption::decodeId($app_id);
        $data['app_status'] = ProcessList::where([
            'process_type_id' => $process_type_id,
            'ref_id' => $app_id,
        ])->value('status_id');
        $data['payment_info'] = SonaliPayment::leftJoin('sp_payment_configuration', 'sp_payment_configuration.id', '=', 'sp_payment.payment_config_id')
            ->leftJoin('sp_payment_details', 'sp_payment_details.sp_payment_id', '=', 'sp_payment.id')
            ->where('sp_payment.app_id', $app_id)
            ->where('sp_payment.process_type_id', $process_type_id)
            ->groupBy('sp_payment.id')
            ->get([
                'sp_payment.id',
                'sp_payment_configuration.payment_name',
                'sp_payment.contact_name',
                'sp_payment.contact_email',
                'sp_payment.contact_no',
                'sp_payment.address',
                'sp_payment.pay_amount',
                'sp_payment.vat_on_pay_amount',
                'sp_payment.transaction_charge_amount',
                'sp_payment.vat_on_transaction_charge',
                'sp_payment.total_amount',
                'sp_payment.payment_status',
                'sp_payment.pay_mode',
                'sp_payment.pay_mode_code',
                DB::raw('GROUP_CONCAT(CONCAT_WS(", ", sp_payment_details.receiver_ac_no, sp_payment_details.distribution_type, sp_payment_details.pay_amount) SEPARATOR ";") as payment_details')
            ]);

        foreach ($data['payment_info'] as $payment) {
            $payment->voucher_url = url('/spg/' . ($payment->pay_mode_code == 'A01' ? 'counter-' : '') . 'payment-voucher/' . Encryption::encodeId($payment->id));

            if ($payment->payment_status == 3 && $data['app_status'] == 3 && in_array(
                Auth::user()->user_type,
                ['5x505', '6x606']
            )) {
                $payment->counter_payment_cancel_url = url('/spg/counter-payment-check/' . Encryption::encodeId($payment->id) . '/' . Encryption::encodeId(0));
                $payment->counter_payment_confirm_url = url('/spg/counter-payment-check/' . Encryption::encodeId($payment->id) . '/' . Encryption::encodeId(1));
            } else {
                $payment->counter_payment_cancel_url = '';
                $payment->counter_payment_confirm_url = '';
            }
        }

        $data['payment_distribution_types'] = PaymentDistributionType::pluck('name', 'id')->toArray();

        return response()->json($data);
    }


    public function submitPayment(Request $request)
    {
        try {

            DB::beginTransaction();

            $app_id = Encryption::decodeId($request->get('encoded_app_id'));
            $process_type_id = Encryption::decodeId($request->get('encoded_process_type_id'));
            $payment_step_id = Encryption::decodeId($request->get('encoded_payment_step_id'));

            $unfixed_amount_array = [
                1 => 0, // Vendor-Service-Fee
                2 => 0, // Govt-Service-Fee
                3 => 0, // Govt. Application Fee
                4 => 0, // Vendor-Vat-Fee
                5 => 0, // Govt-Vat-Fee
                6 => 0 // Govt-Vendor-Vat-Fee
            ];

            switch ($process_type_id) {
                    // New Registration
                case 1:
                    $ind_category_id = IndustryNew::where('id', $app_id)->value('ind_category_id');
                    $industry_controller = new IndustryNewController();
                    $unfixed_amount_array = $industry_controller->unfixedAmountsForGovtApplicationFee($ind_category_id, 2);
                    break;
            }

            $contact_info = [
                'contact_name' => $request->get('contact_name'),
                'contact_email' => $request->get('contact_email'),
                'contact_no' => $request->get('contact_no'),
                'contact_address' => $request->get('contact_address'),
            ];

            $this->process_type_id = $process_type_id;
            $payment_id = $this->storeSubmissionFeeData($app_id, $payment_step_id, $contact_info, $unfixed_amount_array);

            DB::commit();

            return SonaliPaymentController::RedirectToPaymentPortal(Encryption::encodeId($payment_id));
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', "Sorry something went wrong [PPC-001]");
            return redirect()->back()->withInput();
        }
    }
}
