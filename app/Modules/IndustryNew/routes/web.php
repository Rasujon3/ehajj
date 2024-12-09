<?php

use App\Modules\IndustryNew\Http\Controllers\IndustryNewControllerVue;
use App\Modules\ProcessPath\Http\Controllers\ProcessPathController;
use Illuminate\Support\Facades\Route;

Route::group(['module' => 'IndustryNew', 'middleware' => ['web', 'auth', 'checkAdmin']], function () {
    Route::get('industry-new/add', 'IndustryNewController@appForm');
    Route::get('industry-new/edit/{id}/{openMode}', 'IndustryNewController@appFormEdit');
    Route::post('industry-new/store', 'IndustryNewController@appStore');
    Route::post('industry-new/ajax-store', 'IndustryNewController@ajaxAppStore');
    Route::get('industry-new/view/{id}/{openMode}', 'IndustryNewController@appFormView');

    Route::post('industry-new/get-local-machinery', 'IndustryNewController@getLocalMachinery');
    Route::get('industry-new/local-machinery/add/{app_id}', 'IndustryNewController@localMachineryAdd');
    Route::post('industry-new/local-machinery/store', 'IndustryNewController@localMachineryStore');
    Route::get('industry-new/edit-local-machinery/{machine_id}', 'IndustryNewController@localMachineryEdit');
    Route::get('industry-new/delete-local-machinery/{machine_id}/{app_id}', 'IndustryNewController@localMachineryDelete');

    Route::post('industry-new/get-imported-machinery', 'IndustryNewController@getImportedMachinery');
    Route::get('industry-new/imported-machinery/add/{app_id}', 'IndustryNewController@importedMachineryAdd');
    Route::post('industry-new/imported-machinery/store', 'IndustryNewController@importedMachineryStore');
    Route::get('industry-new/edit-imported-machinery/{machine_id}', 'IndustryNewController@importedMachineryEdit');
    Route::get('industry-new/delete-imported-machinery/{machine_id}/{app_id}', 'IndustryNewController@importedMachineryDelete');

    Route::get('industry-new/afterPayment/{payment_id}', 'IndustryNewController@afterPayment');
    Route::get('industry-new/afterCounterPayment/{payment_id}', 'IndustryNewController@afterCounterPayment');
    Route::get('industry-new/preview', "IndustryNewController@preview");
});

// Process path related route
Route::group(array('module' => 'IndustryNew', 'prefix' => 'client', 'middleware' => ['web', 'auth', 'checkAdmin', 'GlobalSecurity']), function () {

    Route::get('industry-new/list/{process_type_id}', [ProcessPathController::class, 'processListById']);
});

// Process path related route
Route::group(array('module' => 'IndustryNew', 'middleware' => ['web', 'auth', 'checkAdmin']), function () {

    Route::get('industry-new/list/{process_type_id}', [ProcessPathController::class, 'processListById']);
});


// for client
Route::group(['module' => 'IndustryNew', 'prefix' => 'client', 'middleware' => ['web', 'auth', 'checkAdmin']], function () {
    Route::post('industry-new/local-machinery/attachment', 'IndustryNewController@localMachineryStoreFromAttachment');
    Route::post('industry-new/imported-machinery/attachment', 'IndustryNewController@importedMachineryStoreFromAttachment');
});



Route::group(['module' => 'IndustryNew', 'prefix' => 'vue', 'middleware' => ['web', 'auth', 'checkAdmin']], function () {
    Route::get('industry-new/view/{id}', [IndustryNewControllerVue::class, 'appFormView']);
});
