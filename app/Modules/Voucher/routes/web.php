<?php
namespace App\Modules\Pilgrims\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::post('/hajjpg/returnurl', 'HajjPgPaymentController@hajjPgReturnURL');
Route::get('/hajjpg-payment-confirmation/{id}/{tracking_no}/{gid}/{source}', 'HajjPgPaymentController@hajjPgPaymentConfirmation');


Route::group(array('module' => 'Voucher', 'prefix'=>'pilgrim', 'middleware' => ['web', 'auth', 'checkAdmin']), function () {

    Route::get('/voucher/get-district', "VoucherController@getDistrict");
    Route::get('/voucher/get-voucher-detail/{id}', "VoucherController@getVoucherDetail");
    Route::get('/voucher/get-voucher', "VoucherController@getVoucher");
    Route::get('/voucher/get-police-station/{id}', "VoucherController@getPoliceStation");
    Route::get('/voucher/get-bank_branch/{id}', "VoucherController@getBankBranch");
    Route::post('/voucher/store-voucher', "VoucherController@storeVoucher");
    Route::post('/voucher/get-pilgrim', "VoucherController@getPilgrim");
    Route::get('/voucher/get-payment-info', "VoucherController@getPaymentInfo");
    Route::post('/voucher/add-delete-action', "VoucherController@addDeleteAction");
    Route::post('/voucher/lock-unlock', "VoucherController@lockAndloack");
    Route::post('/voucher/delete-all-pilgrim', "VoucherController@deleteAllPilgrim");
    Route::post('/voucher/payment-submit', "VoucherController@submitPayment");
    Route::get('/voucher/get-voucher_edit_info/{id}', "VoucherController@getVoucherEditInfo");
    Route::post('/voucher/update-voucher', "VoucherController@updateVoucher");
    Route::post('/voucher/pdf-generate-request', "VoucherController@pdfGenerator");
    Route::post('/voucher/pay-verify-request', "VoucherController@paymentVerify");
    Route::post('/voucher/pay-cancel-request', "VoucherController@paymentCancel");
    Route::get('voucher/counter-pay-voucher-request/{tracking_no}/{pay_unique_id}', "VoucherController@counterPaymentVoucher");
    Route::get('voucher/counter-payslip-request/{tracking_no}/{pay_unique_id}', "VoucherController@counterPaySlip");


    Route::get('voucher/{index?}', 'VoucherController@index')->where('index', '(.*)');

});

