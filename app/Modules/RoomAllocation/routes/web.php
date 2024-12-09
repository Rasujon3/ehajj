<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'RoomAllocation', 'middleware' => ['web', 'auth', 'checkAdmin', 'XssProtection']], function () {

    Route::get('room-allocation/', 'RoomAllocationController@index');

    Route::get('room-allocation/first-step', 'RoomAllocationController@firstStep');
    Route::post('room-allocation/second-step', 'RoomAllocationController@secondStep');
    Route::post('room-allocation/third-step', 'RoomAllocationController@thirdStep');
    Route::post('room-allocation/setToRoom', 'RoomAllocationController@setToRoom');
    Route::post('room-allocation/removeFromRoom', 'RoomAllocationController@removeFromRoom');

    Route::post('room-allocation/getGuideListByFlightId', 'RoomAllocationController@getGuideListByFlightId');
    Route::post('room-allocation/fetchFloorList', 'RoomAllocationController@fetchFloorList');


});

