<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;




Route::group(['module' => 'Flight', 'middleware' => ['web', 'auth', 'checkAdmin', 'XssProtection']], function () {
    Route::get('/flight', 'FlightController@index');
    Route::get('/flight/create-flight', 'FlightController@createFlight');
    Route::post('/flight/store-flight', 'FlightController@storeFlight');
    Route::get('/flight/flight-details/{id}', 'FlightController@flightDetails');
    Route::get('/flight/edit-flight/{id}', 'FlightController@editFlight');
    Route::post('/flight/update-flight/{id}', 'FlightController@updateFlight');
    Route::get('/flight/get-dashboard-data', 'FlightController@getDashboardData');
    Route::get('/flight/get-flight-list', 'FlightController@getHajjFlilghtList');
    Route::get('/flight/get-flight-citys', 'FlightController@getHajjFlilghtCitys');
});
