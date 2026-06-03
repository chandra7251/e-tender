<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS Configuration — API Mobile App (Development)
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Content-Disposition'],

    'max_age' => 86400,

    'supports_credentials' => false,

];