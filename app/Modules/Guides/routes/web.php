<?php
namespace App\Modules\Guides\Http\Controllers;

use App\Modules\Guides\Http\Controllers\GuidesController;
use Illuminate\Support\Facades\Route;


Route::group(array('module' => 'Guides', 'middleware' => ['web', 'auth', 'checkAdmin']), function () {

    Route::get('guides/get-application-list', "GuidesController@getGuideApplicationList");
    Route::get('guides/is-guide-application-exist', "GuidesController@isGuideApplicationExist");
    Route::get('guides/check-guide-application-last-date', "GuidesController@checkGuideApplicationLastDate");
    Route::get('guides/get-guide-data', "GuidesController@getGuideData");
    Route::get('guides/get-division-list', "GuidesController@getDivisionListData");
    Route::get('guides/get-all-district-list', "GuidesController@getAllDistrictListData");
    Route::get('guides/get-police-station-list', "GuidesController@getPoliceStationListData");
    Route::get('guides/get-occupation-list', "GuidesController@getOccupationListData");
    Route::post('guides/store-haj-guide', "GuidesController@storeHajGuideData");
    Route::post('guides/nid-file-upload', "GuidesController@nidFileUpload");
    Route::get('guides/add-voucher-modal-data', 'GuidesController@addVoucherModalView'); // no need
    Route::get('guides/get-voucher-row-details-data', "GuidesController@getVoucherRowDetailsData"); // no need
    Route::get('guides/get-already-added-voucher-list', "GuidesController@getAlreadyVoucherListData"); // no need
    Route::get('guides/add-voucher-to-guide', "GuidesController@addVoucherToGuide"); // no need
    Route::get('guides/get-voucher-added-pilgrims-list', "GuidesController@getVoucherAddedPilgrims"); // no need
    Route::get('guides/lock-pilgrims', "GuidesController@lockPilgrims");
    Route::get('guides/delete-pilgrim', "GuidesController@removePilgrim");
    Route::post('guides/get-guide-profile-details', "GuidesController@getGuideProfileDetails");
    Route::get('guides/get-own-pilgrims', "GuidesController@getOwnPilgrims");
    Route::post('guides/get-registered-pilgrim-by-tracking-no', "GuidesController@getRegisteredPilgrimByTrackingNo");
    Route::post('guides/add-pilgrim-to-guide', "GuidesController@addPilgrimToGuide");
    // update
    Route::get('guides/get-guide-application/{id}', "GuidesController@getApplicationList");
    Route::post('guides/update-haj-guide/{id}', "GuidesController@updateApplication");

    // To submit guide request
    Route::post('guides/submit-guide-request', "GuidesController@submitGuideRequest");
    Route::post('guides/re-submit-guide-request', "GuidesController@reSubmitGuideRequest");
    Route::post('guides/cancel-guide-request/{param1}', "GuidesController@cancelGuideRequest");
//    Route::post('pre-reg/get-list', "PilgrimController@getList");

    // pdf generate
    Route::get('guides/pdf-generate', "GuidesController@pdfGenerate");

    // Route to handle page reload in Vue except for api routes
    Route::get('guides/{index?}', 'GuidesController@index')->where('index', '(.*)');
});

