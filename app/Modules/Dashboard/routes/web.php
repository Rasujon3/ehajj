<?php
use Illuminate\Support\Facades\Route;

Route::group(array('Module'=>'Dashboard', 'middleware' => ['web','auth','GlobalSecurity']), function () {

//    Route::get('dashboard', 'DashboardController@dashboard');
    Route::get('/notifications/count', "DashboardController@notificationCount");
    Route::get('/notifications/show', "DashboardController@notifications");
    Route::get('/single-notification/{id}', "DashboardController@notificationSingle");
    Route::get('/notification-all', "DashboardController@notificationAll");
    // Route::resource('dashboard', 'DashboardController');
    Route::get('/my-desk', "DashboardController@myDesk");
    Route::get('/dashboard', "DashboardController@index");
    Route::get('server-info', 'DashboardController@serverInfo');
    Route::get('/my-profile', 'DashboardController@pilgrimProfile');
});
