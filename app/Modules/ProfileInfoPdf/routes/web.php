<?php

use Illuminate\Support\Facades\Route;

Route::group(array('module' => 'ProfileInfoPdf', 'middleware' => ['web', 'auth', 'XssProtection', 'GlobalSecurity']), function () {
    Route::get('profile-pdf-generate/{tracking_no}','ProfileInfoPdfController@profileInfo');
    Route::get('list-of-group-members-pdf-generate/{tracking_no}','ProfileInfoPdfController@groupMembersInfo');

});


/** the code only for client */


