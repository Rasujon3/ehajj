<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OSS-PID Configuration
    |--------------------------------------------------------------------------
    */

    'osspid_client_id' => env('osspid_client_id'),
    'osspid_client_secret_key' => env('osspid_client_secret_key'),
    'osspid_base_url' => env('osspid_base_url'),
    'osspid_base_url_ip' => env('osspid_base_url_ip'),

    /*

   |--------------------------------------------------------------------------
   | OSS-PID LOG Configuration
   |--------------------------------------------------------------------------
   */

    'osspid_log_grant_type' => env('osspid_log_grant_type'),
    'osspid_log_my_client_id' => env('osspid_log_client_id'),
    'osspid_log_my_secret_key' => env('osspid_log_my_secret_key'),
    'osspid_log_content_type' => env('osspid_log_content_type'),
    'osspid_log_token_url' => env('osspid_log_token_url'),
    'osspid_log_data_url' => env('osspid_log_data_url'),

];
