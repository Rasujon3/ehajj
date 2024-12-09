<?php

return [
    'spg_settings_stack_holder' => [
        'web_service_url' => env('SPG_WEB_SERVICE_URL'),
        'web_portal_url' => env('SPG_WEB_PORTAL_URL'),
        'user_id' => env('SPG_USER_ID'),
        'password' => env('SPG_PASSWORD'),
        'SBL_account' => env('SPG_SBL_ACCOUNT'),
        'st_code' => env('ST_CODE'),
        'request_id_prefix' => env('SPG_REQUEST_ID_PREFIX'),
        'return_url' => env('PROJECT_ROOT') . '/spg/stack-holder/callback',
    ],

    'spg_settings' => [
        'payment_mode' => env('SPG_PAYMENT_MODE'),
        'web_service_url' => env('SPG_WEB_SERVICE_URL'),
        'web_portal_url' => env('SPG_WEB_PORTAL_URL'),
        'single_details_url' => env('SPG_SINGLE_DETAILS_URL'),
        'user_id' => env('SPG_USER_ID'),
        'password' => env('SPG_PASSWORD'),
        'sbl_account' => env('SPG_SBL_ACCOUNT'),
        'st_code' => env('SPG_ST_CODE'),
        'request_id_prefix' => env('SPG_REQUEST_ID_PREFIX'),
        'return_url' => env('PROJECT_ROOT') . env('SPG_CALLBACK_URL'),
        'return_url_m' => env('PROJECT_ROOT') . env('SPG_CALLBACK_URL_M'),
    ]
];
