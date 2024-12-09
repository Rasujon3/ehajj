<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::group(array('module' => 'GoPassport', 'middleware' => ['web', 'auth','XssProtection']), function () {
    Route::get('go-passport/list/{encoded_process_type_id}', 'GoPassportController@indexGo');
    Route::get('go-passport/{type_key}/getGoList/{process_type_id}', "GoPassportController@getGoList");
    Route::post('go-passport/store/{process_type_id}', "GoPassportController@storeGoPassport");
    Route::get('go-passport/edit/{refId}/{process_type_id}', "GoPassportController@editGoPassport");
    Route::post('go-passport/store-sticker-pilgrims/{refId}/{process_type_id}', "GoPassportController@storeStickerPilgrims");
    Route::post('go-passport/update/{refId}/{process_type_id}', "GoPassportController@updateGoPassport");
    Route::post('go-passport/passport-member-entry/update/{id}/{encode_ref_id}/{encode_process_type_id}', "GoPassportController@updateMemberEntryPassport");
    Route::post('go-passport/delete-members/{id}/{encode_ref_id}/{encode_process_type_id}', "GoPassportController@deleteMember");
    Route::get('go-passport/edit-members/{id}', "GoPassportController@editMember");
    Route::get('go-passport/delete-members/{id}', "GoPassportController@getDeleteModal");
    Route::post('go-passport/passport_verification', "GoPassportController@passportVerifyRequest");

    Route::get('go-passport/{type_key}/get-delivery-List/{process_type_id}', "GoPassportController@getDeliveryList");
    Route::get('go-passport/delivery-passport/{process_type_id}', "GoPassportController@createDeliveryPassport");
    Route::post('go-passport/add/delivery-passport/{process_type_id}', "GoPassportController@addDeliveryPassport");
    Route::post('go-passport/remove/delivery-passport', "GoPassportController@passportRemoveFromChartReturn");
    Route::post('go-passport/save/delivery-passport', "GoPassportController@saveReturnPassport");
    Route::get('go-passport/view/delivery-passport/{id}/{process_type_id}', "GoPassportController@viewReturnPassport");

    // Route::post('go-passport/process/action/{refId}/{process_type_id}', "GoPassportController@GoPassportProcess");
    Route::post('go-passport/genarate-pdf/{refId}/{process_type_id}', "GoPassportController@pdfGenarate");
    Route::post('go-passport/genarate-pdf/{refId}/{process_type_id}/{pilgrim_id}', "GoPassportController@pdfGenarateSingle");
    Route::post('go-passport/delivery/genarate-pdf/{id}/{process_type_id}', "GoPassportController@returnPdfGenarate");
});

Route::post('getToken', 'GoPassportController@externalToken')->withoutMiddleware('web');
Route::post('get-verification-key', 'GoPassportController@externalToken')->withoutMiddleware('web');
Route::post('store-pilgrims-house-color', 'GoPassportController@storePilgrimHouseColorData')->withoutMiddleware('web');
Route::post('store-ocr-medicine-data', 'GoPassportController@storeOcrMedicineData')->withoutMiddleware('web');

