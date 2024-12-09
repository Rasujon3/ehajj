<?php

namespace App\Modules\SonaliPayment\Services;

use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\SonaliPayment\Models\SonaliPayment;
use App\Modules\Users\Models\Users;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

trait SPAfterPaymentManager
{

    use OnlinePaymentPostProcessing;

    /**
     *
     */
    public function onlinePaymentCallbackProcessing($paymentInfo)
    {
        try {
            DB::beginTransaction();

            $process_type_info = ProcessType::where('id', $paymentInfo->process_type_id)
                ->orderBy('id', 'desc')
                ->first([
                    'form_url',
                    'process_type.process_desk_status_json',
                    'process_type.name',
                ]);

            $processData = ProcessList::where('ref_id', $paymentInfo->app_id)
                ->where('process_type_id', $paymentInfo->process_type_id)
                ->first();

            // Initiate null array for dynamic submission sql, if needed then pass any parameter
            $submission_sql_param = [
                'app_id' => $processData->ref_id,
                'process_type_id' => $processData->process_type_id,
            ];

            $payment_json_name = $this->getPaymentJsonName($paymentInfo->payment_step_id);
            $general_submission_process_data = $this->getProcessDeskStatus($payment_json_name, $process_type_info->process_desk_status_json, $submission_sql_param);

            // Assign application submission date if application status is draft (-1)
            if ($processData->status_id == '-1') {
                $processData->submitted_at = Carbon::now();
            }

            $processData->status_id = $general_submission_process_data['process_starting_status'];
            $processData->desk_id = $general_submission_process_data['process_starting_desk'];
            $processData->user_id = $general_submission_process_data['process_starting_user'];
            $processData->process_desc = 'Payment completed successfully.';
            $resultData = $processData->id . '-' . $processData->tracking_no .
                $processData->desk_id . '-' . $processData->status_id . '-' . $processData->user_id . '-' .
                $processData->updated_by;

            $processData->previous_hash = $processData->hash_value ?? "";
            $processData->hash_value = Encryption::encode($resultData);
            $processData->save();

            /**
             * Application related tasks will be done here
             * As like as:
             * Email sending after payment submission
             * Certificate generation after payment submission
             */
            $this->applicationRelatedTasks($process_type_info->name, $processData, $paymentInfo);

            DB::commit();
            Session::flash('success', 'Payment submitted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            //dd($e->getLine(),$e->getFile() );
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage() . '[SPAPM-102]'));
        }
    }

    /**
     *
     */
    private function counterPaymentCallbackProcessing($paymentInfo)
    {
        try {
            $process_type_info = ProcessType::where('id', $paymentInfo->process_type_id)
                ->orderBy('id', 'desc')
                ->first([
                    'form_url',
                    'process_type.process_desk_status_json',
                    'process_type.name',
                ]);
            $processData = ProcessList::where('ref_id', $paymentInfo->app_id)
                ->where('process_type_id', $paymentInfo->process_type_id)
                ->first();

            $processData->status_id = 3;
            $processData->desk_id = 0;
            $processData->process_desc = 'Waiting for Payment Confirmation.';
            $processData->save();
            Session::flash('success', 'Application is waiting for Payment Confirmation');
        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage() . '[SPAPM-102]'));
        }
    }

    /**
     *
     */
    public function getPaymentJsonName($payment_step_id)
    {
        return 'fees_' . $payment_step_id;
    }

    /**
     *
     */
    public function getProcessDeskStatus($payment_json_name, $json_data, $sql_params)
    {
        $decoded_json = json_decode($json_data, true);
        if (!isset($decoded_json[$payment_json_name]) or empty($decoded_json[$payment_json_name])) {
            throw new Exception('Proper Json data found for this payment processing. Please configure proper json data.');
        }

        if (!isset($decoded_json[$payment_json_name]['process_starting_desk_sql']) or empty($decoded_json[$payment_json_name]['process_starting_desk_sql'])) {
            throw new Exception('Process desk SQL not found for this payment processing. Please configure proper json data.');
        }

        if (!isset($decoded_json[$payment_json_name]['process_starting_status_sql']) or empty($decoded_json[$payment_json_name]['process_starting_status_sql'])) {
            throw new Exception('Process status SQL not found for this payment processing. Please configure proper json data.');
        }

        $process_desk_sql = $decoded_json[$payment_json_name]['process_starting_desk_sql'];
        $process_desk_sql = str_replace("{app_id}", $sql_params['app_id'], $process_desk_sql);

        $process_status_sql = $decoded_json[$payment_json_name]['process_starting_status_sql'];
        $process_status_sql = str_replace("{app_id}", $sql_params['app_id'], $process_status_sql);

        $process_desk_result = DB::select(DB::raw($process_desk_sql));
        $process_status_result = DB::select(DB::raw($process_status_sql));

        if (!isset($decoded_json[$payment_json_name]['process_starting_user_sql']) or empty($decoded_json[$payment_json_name]['process_starting_user_sql'])) {
            $process_starting_user = 0;
        } else {
            $process_user_sql = $decoded_json[$payment_json_name]['process_starting_user_sql'];
            $process_user_sql = str_replace("{app_id}", $sql_params['app_id'], $process_user_sql);

            $process_user_result = DB::select(DB::raw($process_user_sql));
            $process_starting_user = (int) $process_user_result[0]->process_starting_user;
        }

        $data = [
            'process_starting_desk' => (int)$process_desk_result[0]->process_starting_desk,
            'process_starting_status' => (int) $process_status_result[0]->process_starting_status,
            'process_starting_user' => $process_starting_user
        ];

        return $data;
    }
}
