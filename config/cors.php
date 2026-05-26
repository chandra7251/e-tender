<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS Configuration — API Mobile App
    |--------------------------------------------------------------------------
    |
    | Batasi akses API hanya dari origin yang diizinkan.
    | Ubah 'allowed_origins' sesuai domain mobile app / web admin production.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        // Development — izinkan localhost untuk testing
        'http://localhost',
        'http://localhost:3000',
        'http://localhost:8080',
        'http://127.0.0.1',
        'http://127.0.0.1:8080/',

        // TODO: Ganti/tambah dengan domain production saat deploy
        // 'https://vendor-app.domain-kamu.com',
        // 'https://admin.domain-kamu.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept'],

    'exposed_headers' => ['Content-Disposition'], // perlu untuk response download file

    'max_age' => 86400, // 24 jam preflight cache

    'supports_credentials' => false, // JWT = stateless, tidak butuh cookies/session

];