<?php

return [
    'oss_agent_name' => env('OSS_AGENT_NAME'),
    'report_token_expired_time' => env('REPORT_TOKEN_EXPIRED_TIME'), // time in minute
    'report_client_id' => env('REPORT_CLIENT_ID'),
    'report_client_secret' => env('REPORT_CLIENT_SECRET'),
    'report_token_url' => env('REPORT_TOKEN_URL'),
    'report_api_url' => env('REPORT_API_URL'),
    'report_api_url_replace_params' => env('REPORT_API_URL_REPLACE_PARAMS'),
];
