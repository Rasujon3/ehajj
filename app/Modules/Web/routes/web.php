<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;


Route::group(array('module' => 'Web', 'middleware' => ['web', 'XssProtection']), function () {

    Route::get('/login/{lang}', 'WebController@index');


    Route::get('lang/{lang}', ['as' => 'lang.switch', 'uses' => 'WebController@switchLang']);

    Route::get('/', 'WebController@index');
    Route::get('/f/l', 'WebController@flightListPage');
    Route::get('/f/l/get-percentage', 'WebController@getParcentage');

    Route::post('/ajax-flight-list','WebController@ajaxFlightList');
    Route::get('/login', 'WebController@index');
    Route::post('login', ['as' => 'login', 'uses' => 'WebController@index']);


    Route::get('/login/{lang}', 'WebController@index');

    Route::get('web/notice', 'WebController@notice');

    Route::get('all-news', 'WebController@allNews');
    Route::post('fetch-all-news', 'WebController@fetchAllNews');

    Route::get('web/service-list', 'WebController@serviceList');
    Route::post('web/get-service-list', 'WebController@getServiceList');

    Route::get('web/application-chart', 'WebController@applicationChart');
    Route::post('web/get-service-list', 'WebController@getServiceList');

    Route::get('/viewNotice/{id}/{slug}', 'WebController@viewNotice');
    Route::get('/industrial-city-details/{id}', 'WebController@industrialCityDetails');
    Route::get('/need-help', "WebController@support");

    Route::get('/log', '\Srmilon\LogViewer\LogViewerController@index');

    Route::get('/docs/{pdftype}/{id}', 'VerifyDocController@verifyDoc');
    Route::get('/docs/{id}', 'VerifyDocController@verifyDoc');

    Route::get('available-services', 'FrontPagesController@availableServices');
    Route::get('bscic-industrial-city-list', 'FrontPagesController@industrialCityMap')->name('industrialCity.cityList');
    Route::get('bscic-industrial-city-map-data', 'FrontPagesController@industrialCityMapData');
    Route::get('bscic-industrial-city/{city_id?}', 'FrontPagesController@industrialCity')->name('industrialCity.details');
    Route::get('document-and-downloads', 'FrontPagesController@documentAndDownloads');
    Route::get('new-business', 'FrontPagesController@newBusiness');
    Route::get('location-map', 'FrontPagesController@locationMap');



    Route::get('article/{page_name}', 'FrontPagesController@articlePage');
    Route::get('web/load-more-notice', 'WebController@loadMoreNotice');
    Route::get('web/load-city-office', 'WebController@loadCityOffice');
    Route::get('getResourcesLinksData', 'WebController@getResourcesLinksData');

    Route::get('/complain','ComplainController@index');
    Route::post('/ajax-fetch-data-by-tracking_no','ComplainController@fetchData');
    Route::post('/ajax-fetch-data-by-pid','ComplainController@fetchDataByPID');
    Route::post('/submit-complain','ComplainController@submitComplain');
    Route::get('/show-pdf/{data}','ComplainController@showPdf');
    Route::post('/captcha/validate', function (Illuminate\Http\Request $request) {
        $captcha = $request->input('captcha');
        if (captcha_check($captcha)) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 422);
        }
    })->name('captcha.validate');
    });

    Route::get('/refresh_captcha',function (){
        return response()->json(['captcha'=> captcha_img()]);
    })->name('refresh_captcha');
