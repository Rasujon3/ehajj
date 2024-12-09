<?php

use App\Modules\PayOrderReceived\Http\Controllers\PayOrderReceivedController;
use Illuminate\Support\Facades\Route;

Route::group(['module' => 'PayOrderReceived', 'middleware' => ['web', 'auth', 'checkAdmin', 'XssProtection']], function () {
    Route::get('pay-order-received', 'PayOrderReceivedController@index');
    Route::get('pay-order-received/pending','PayOrderReceivedController@pendingList');
    Route::get('pay-order-received/received','PayOrderReceivedController@receivedList');
    Route::get('pay-order-received/confirm','PayOrderReceivedController@confirmPayOrder');
    Route::get('pay-order-received/hl-search','PayOrderReceivedController@hlSearch');
    Route::get('pay-order-received/voucher-search','PayOrderReceivedController@voucherSearch');
});

