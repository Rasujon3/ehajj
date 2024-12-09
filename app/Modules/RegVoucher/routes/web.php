<?php
namespace App\Modules\Pilgrims\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::post('/hajjpg/returnurl', 'HajjPgPaymentController@hajjPgReturnURL');
Route::get('/hajjpg-payment-confirmation/{id}/{tracking_no}/{gid}/{source}', 'HajjPgPaymentController@hajjPgPaymentConfirmation');


Route::group(array('module' => 'RegVoucher', 'prefix'=>'registration', 'middleware' => ['web', 'auth', 'checkAdmin']), function () {

    Route::get('/reg-voucher/get-package', "RegVoucherController@getPackage");
    Route::get('/reg-voucher/get-voucher-detail/{id}', "RegVoucherController@getVoucherDetail");
    Route::get('/reg-voucher/get-voucher', "RegVoucherController@getVoucher");
    Route::post('/reg-voucher/store-voucher', "RegVoucherController@storeVoucher");
    Route::post('/reg-voucher/get-pilgrim', "RegVoucherController@getPilgrim");
    Route::get('/reg-voucher/get-payment-info', "RegVoucherController@getPaymentInfo");
    Route::post('/reg-voucher/add-delete-action', "RegVoucherController@addDeleteAction");
    Route::post('/reg-voucher/lock-unlock', "RegVoucherController@lockAndloack");
    Route::post('/reg-voucher/delete-all-pilgrim', "RegVoucherController@deleteAllPilgrim");
    Route::post('/reg-voucher/payment-submit', "RegVoucherController@submitPayment");
    Route::get('/reg-voucher/get-voucher_edit_info/{id}', "RegVoucherController@getVoucherEditInfo");
    Route::post('/reg-voucher/update-voucher', "RegVoucherController@updateVoucher");
    Route::post('/reg-voucher/pdf-generate-request', "RegVoucherController@pdfGenerator");
    Route::post('/reg-voucher/add-pilgrim-by-serial', "RegVoucherController@addPilgrimBySerialNo");


    Route::get('/reg/voucher/{index?}', 'RegVoucherController@index')->where('index', '(.*)');

});

