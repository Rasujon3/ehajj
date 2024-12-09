<?php
namespace App\Modules\Pilgrims\Http\Controllers;

use Illuminate\Support\Facades\Route;


Route::group(array('module' => 'Registration', 'prefix'=>'registration', 'middleware' => ['web', 'auth', 'checkAdmin']), function () {

    Route::get('/get-list', "RegistrationController@getList");
    Route::get('/get-venue-list/{districtId}', "RegistrationController@getVenue");
    Route::post('/get-pilgrim-data', "RegistrationController@getPilgrimData");
    Route::post('/get-edit-reg-pilgrim', "RegistrationController@getEditRegPilgrimData");
    Route::post('/pilgrim-update', "RegistrationController@updateRegPilgrim");
    Route::post('/pilgrim-search', "RegistrationController@searchPreRegPilgrim");
    Route::post('/passport-verification', "RegistrationController@passportVerification");
    Route::post('/image/store', "RegistrationController@imgStoreRequest");
    Route::post('/passport-verify-request', "RegistrationController@submitPassportVerifyRequest");

    // Route to handle page reload in Vue except for api routes
    Route::get('/reg/{index?}', 'RegistrationController@index')->where('index', '(.*)');
});

