<?php

use App\Modules\ProcessPath\Models\ProcessList;
use Illuminate\Support\Facades\Route;

Route::group(array('module' => 'ProcessPath', 'middleware' => ['web', 'auth', 'XssProtection', 'GlobalSecurity']), function () {

    //    List of all process
    Route::get('process/list', "ProcessPathController@processListById");
    Route::get('process/application_list', "ProcessPathController@applicationList");
    Route::post('process/list', "ProcessPathController@processListById");
    Route::get('process/list/{process_id}', "ProcessPathController@processListById");
    Route::post('process/check-guide', "ProcessPathController@checkGuide");

    Route::get('/client/{form_url}/list/{process_type_id}', "ProcessPathController@processListById");
    Route::get('/{form_url}/list/{process_type_id}', "ProcessPathController@processListById");

    Route::post('process-path/get-desk-by-status', "ProcessPathController@getDeskByStatus");
    Route::post('process-path/batch-process-update', "ProcessPathController@updateProcess");
    Route::get('process-path/check-process-validity', "ProcessPathController@checkApplicationValidity");

    Route::get('process-path/ajax/{param}', 'ProcessPathController@ajaxRequest');

    Route::get('process/get-list/{status?}/{desk?}', [
        'as' => 'process.getList',
        'uses' => 'ProcessPathController@getList'
    ]);
    Route::get('process/set-process-type', [
        'as' => 'process.setProcessType',
        'uses' => 'ProcessPathController@setProcessType'
    ]);
    Route::get('process/search-process-type', [
        'as' => 'process.searchProcessType',
        'uses' => 'ProcessPathController@searchProcessType'
    ]);


    // New Process route
    //    Route::get('process/{module}/add/{process_type_id}', "ProcessPathController@applicationAdd");
    Route::get('process/action/add/{process_type_id}', "ProcessPathController@applicationAdd");
    //TODO::Dynamic Add ajax form
    Route::get('process/action/content/{process_type_id}', "ProcessPathController@commonAddForm");
    //TODO::Dynamic Store form data
    Route::post('process/action/store/{process_type_id}', "ProcessPathController@commonStoreForm");
    // Define route for storing data
    Route::get('process/action/remove-pilgrim/{pid}/{id}/{process_type_id}', 'ProcessPathController@removePilgrimFromGuide')->name('delete-pilgrim');
    // get pilgirm list
    Route::post('process/action/getpilgrim/{process_type_id}', "ProcessPathController@getPilgrim");
    Route::post('process/action/getPilgrimDataByUser', "ProcessPathController@getPilgrimDataByUser");
    //get pilgrims-for-gender-change
    Route::post('process/action/getpilgrim-for-gender-change/{process_type_id}', "ProcessPathController@getPilgrimForGenderChange");
    //get pilgrims-for-gender-change
    Route::post('process/action/getpilgrim-for-hajj-canceled/{process_type_id}', "ProcessPathController@getPilgrimForHajjCanceled");
    // get hmis pilgirm list
    Route::post('process/action/getHmisPilgrimListing/{process_type_id}', "ProcessPathController@getHmisPilgrimListing");
    Route::post('process/action/getFlightDetails/{process_type_id}', "ProcessPathController@getFlightDetails");
    Route::post('process/action/getTripList/{process_type_id}', "ProcessPathController@getTripList");
    Route::post('process/action/getFlightList/{process_type_id}', "ProcessPathController@getFlightList");
    //TODO::Dynamic Preview ajax form
    Route::get('process/action/preview/{process_type_id}', "ProcessPathController@commonPreview");



    Route::get('process/{module}/view/{app_id}/{process_type_id}', "ProcessPathController@applicationOpen");
    //TODO::Dynamic View ajax form
    Route::get('{module}/view/{id}/{openMode}', "ProcessPathController@applicationView");

    Route::get('process/{module}/edit/{app_id}/{process_type_id}', "ProcessPathController@applicationEdit");
    //TODO::Dynamic Edit ajax form
    Route::get('{module}/edit/{id}/{openMode}', "ProcessPathController@commonFormEdit");

    //Route::resource('ProcessPath', 'ProcessPathController');
    Route::post('process/help-text', "ProcessPathController@getHelpText");

    Route::post('process/favorite-data-store', "ProcessPathController@favoriteDataStore");
    Route::post('process/favorite-data-remove', "ProcessPathController@favoriteDataRemove");
    Route::post('process/check-guide', "ProcessPathController@checkGuide");

    Route::get('process-path/request-shadow-file', "ProcessPathController@requestShadowFile");

    // Process flow graph route
    Route::get('process/graph/{process_type_id}/{app_id}/{cat_id}', 'ProcessPathController@getProcessData');
    // get shadow file history
    Route::get('process/get-shadow-file-hist/{process_type_id}/{ref_id}', 'ProcessPathController@getShadowFileHistory');
    // get application history
    Route::get('process/get-app-hist/{process_list_id}', 'ProcessPathController@getApplicationHistory');

    //get desk by user
    Route::post('process-path/get-user-by-desk', "ProcessPathController@getUserByDesk");

    //batch process
    Route::get('process/batch-process-set', "ProcessPathController@batchProcessSet");
    Route::get('process/batch-process-skip/{id}', "ProcessPathController@skipApplication");
    Route::get('process/batch-process-previous/{id}', "ProcessPathController@previousApplication");

    Route::get('process-path/verify_history/{process_list_id}', 'ProcessPathController@verifyProcessHistory');

    // Certificate Regeneration
    Route::get('process/certificate-regeneration/{app_id}/{process_type_id}', 'ProcessPathController@certificateRegeneration');

    // Service wise application count and list
    Route::post('process/get-servicewise-count', "ProcessPathController@statusWiseApps");

    Route::post('preview-excel-data', "ProcessPathController@previewExcelData");

    Route::get('medicine-store', "ProcessPathController@medicineStorePage");
    Route::get('medicine-store/get-total-inventory', "ProcessPathController@getTotalMedicineInventory");
});


/** the code only for client */
Route::group(['module' => 'ProcessPath', 'prefix' => 'client', 'middleware' => ['web', 'auth', 'GlobalSecurity']], function () {

    Route::get('process/list', "ClientProcessPathController@processListById");
    Route::get('process/details/{id}', "ClientProcessPathController@processDetails");
    // New Process route
    Route::get('process/{module}/add/{process_type_id}', "ProcessPathController@applicationAdd");
    Route::get('process/{module}/view/{app_id}/{process_type_id}', "ProcessPathController@applicationOpen");
    Route::get('process/{module}/edit/{app_id}/{process_type_id}', "ProcessPathController@applicationEdit");

    Route::get('process/check-cancellation', "ClientProcessPathController@checkCancellation");
    Route::get('process/set-can-app', "ClientProcessPathController@setCanApp");
});


Route::group(['module' => 'ProcessPath', 'prefix' => 'vue', 'middleware' => ['web', 'auth', 'GlobalSecurity']], function () {

    Route::get('get-auth-data', "ProcessListController@getAuthData");

    Route::get('process', "ProcessListController@index");
    Route::get('process/get-list/{status?}/{desk?}', 'ProcessListController@getList');
    Route::get('process-type', 'ProcessListController@getProcessTypes');
    Route::get('process-type/{process_type_id}', 'ProcessListController@getProcessTypeInfo');

    Route::post('process/favorite-data-store', 'ProcessPathController@favoriteDataStore');
    Route::post('process/favorite-data-remove', 'ProcessPathController@favoriteDataRemove');


    Route::get('process-type/{process_type_id}/status', 'ProcessListController@getStatusListByProcessType');


    Route::get('process/view/{app_id}/{process_type_id}', "ProcessListController@applicationView");


    Route::post('process/update', 'ProcessListController@updateProcessVue');


    Route::get('process/shadow-file-hist/{process_type_id}/{ref_id}', 'ProcessListController@getShadowFileHistory');
    Route::get('process/history/{process_list_id}', 'ProcessListController@getApplicationHistory');

    Route::get('process/status-wise-app-count/{process_type_id}', 'ProcessListController@statusWiseAppsCount');
});
