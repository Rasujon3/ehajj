<?php

use Illuminate\Support\Facades\Route;

// Route::get('bulletin', 'BulletinController@welcome');


Route::group(['module' => 'Bulletin', 'middleware' => ['web', 'auth', 'checkAdmin', 'XssProtection']], function () {
    Route::get('/bulletin', 'BulletinController@list')->name('bulletin_list');
    Route::get('/bulletin/create', 'BulletinController@create')->name('create_bulletin');
    Route::post('/bulletin/preview', 'BulletinController@preview')->name('preview_bulletin');
    Route::post('/bulletin/store', 'BulletinController@store')->name('store_bulletin');
    Route::get('/bulletin/list', 'BulletinController@getBulletinList')->name('get_bulletin_list');
    Route::get('/bulletin/edit/{id}', 'BulletinController@edit')->name('edit_bulletin');
});
