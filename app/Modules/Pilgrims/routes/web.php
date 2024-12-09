<?php
namespace App\Modules\Pilgrims\Http\Controllers;

use Illuminate\Support\Facades\Route;


Route::group(array('module' => 'Pilgrims', 'prefix'=>'pilgrim', 'middleware' => ['web', 'auth', 'checkAdmin']), function () {

    Route::get('pre-reg/get-list', "PilgrimController@getList");
    Route::get('pre-reg/get-district-police-station-by-postCode/{postCode}', "PilgrimController@getDistrictPoliceStationbyPostCode");
    Route::get('pre-reg/configuration-verification/nid-passport', "PilgrimController@configNidPassport");
    Route::get('pre-reg/get-index-data', "PilgrimController@getIndexData");
    Route::get('pre-reg/get-bank-branch/{bankId}/{bankDistrictId}', "PilgrimController@getBankBranch");
    Route::get('pre-reg/get-police-station/{districtId}', "PilgrimController@getPoliceStation");
    Route::post('pre-reg/passport-verification', "PilgrimController@passportVerification");
    Route::post('pre-reg/check-duplicate-pilgrim', "PilgrimController@checkDuplicatePilgrim");
    Route::post('pre-reg/check-duplicate-pilgrim-edit', "PilgrimController@checkDuplicatePilgrimEdit");
    Route::post('pre-reg/store', "PilgrimController@store");
    Route::post('pre-reg/get-pilgrim-data', "PilgrimController@getPilgrimData");
    Route::post('pre-reg/get-edit-pilgrim-data', "PilgrimController@getEditPilgrimData");
    Route::post('pre-reg/pilgrim-update', "PilgrimController@updatePreRegPilgrim");
    Route::get('reg/get-reg-index-data', "PilgrimController@getRegIndexData");
    Route::get('reg/get-maharam-relation', "PilgrimController@getMaharamRelation");
    Route::post('reg/search-maharam-by-tracking-no', "PilgrimController@searchMaharamByTrackingNo");

    // Route to handle page reload in Vue except for api routes
    Route::get('pre-registration/{index?}', 'PilgrimController@index')->where('index', '(.*)');
});

