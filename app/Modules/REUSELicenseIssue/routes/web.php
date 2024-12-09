<?php

use App\Modules\ProcessPath\Http\Controllers\ProcessPathController;

Route::post('get-sub-team', 'ApiRequestHandleController@getSubTeamDataFromHMIS');
Route::post('get-sticker-go-member', 'AjaxRequestController@getStickerVisaGoMember');


Route::post('/add-row', "ReuseController@addRow");

Route::post('process/action/get-hmis-pilgrims-for-guide/{process_type_id}', "AjaxRequestController@getHmisPilgrimListing");

Route::post('process/action/getGuideDetails/{process_type_id}', "AjaxRequestController@getGuideDetails");

Route::group(array('module' => 'RUSELicenseIssue', 'middleware' => ['web', 'auth', 'XssProtection', 'GlobalSecurity']), function () {
    Route::get('medicine-issue', "MedicineIssueController@index");
    Route::post('medicine-issue/change-pharmacy', "MedicineIssueController@changePharmacy");
    Route::post('medicine-issue/save-pharmacy', "MedicineIssueController@savePharmacy");
    Route::post('medicine-issue/store', "MedicineIssueController@store");
    Route::post('medicine-issue/scan-medicine-store', "MedicineIssueController@scanMedicineStore");
    Route::get('medicine-issue/search-pilgrim', "MedicineIssueController@searchPilgrim");

    Route::get('medicine-receive', "MedicineReceiveController@index");
    Route::get('medicine-receive/search-pilgrim', "MedicineReceiveController@searchPilgrimReceived");
    Route::get('medicine-issue/search-pilgrim-by-passport', "MedicineIssueController@searchPilgrimByPassport");
    Route::get('medicine-issue/search-pilgrim-by-pid-or-passport', "MedicineIssueController@searchPilgrimByPidOrPassport");
    Route::post('medicine-receive/upload-prescription', 'MedicineIssueController@imageUploadApi');
    Route::post('medicine-receive/drafted-image-upload', 'MedicineIssueController@draftedImageUpload');
    Route::get('medicine-draft', "MedicineIssueController@draftMedicineList");
    Route::get('medicine-draft/list', "MedicineIssueController@getDraftMedicineList");
    Route::get('medicine-draft/edit/{id}', "MedicineIssueController@imageUploadApi");
    Route::get('medicine-issue/draft-reject/{id}', "MedicineIssueController@draftRejectAction");
    Route::get('search-by-agency-licence', "AjaxRequestController@searchAgencyLicence");
    Route::get('get-thana-by-district-id', 'AjaxRequestController@getThanaByDistrictId');
    Route::get('get-all-district-list', "AjaxRequestController@getAllDistrictListData");
});

