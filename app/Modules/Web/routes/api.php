<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Web\Http\Controllers\DraftPrescriptionController;

Route::group(['prefix'=>'draft'],function(){
    Route::post('getToken', 'DraftPrescriptionController@getDraftToken')
        ->withoutMiddleware('web');
    Route::post('store-draft-prescription', 'DraftPrescriptionController@storeDraftPescription')
        ->withoutMiddleware('web');

    Route::post('store-draft-prescriptionv2', 'DraftPrescriptionController@storeDraftPescriptionv2')
        ->withoutMiddleware('web');
});

