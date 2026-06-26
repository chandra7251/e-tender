<?php

return [

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'midtrans' => [
        'server_key'    => env('MIDTRANS_SERVER_KEY', ''),
        'client_key'    => env('MIDTRANS_CLIENT_KEY', ''),
        'is_production' => env('MIDTRANS_PRODUCTION', false),
    ],

    'blockchain' => [
        'network' => env('BLOCKCHAIN_NETWORK', 'local'),
        'rpc_url' => env('BLOCKCHAIN_RPC_URL', ''),
    ],

    'fcm' => [
        'project_id'       => env('FCM_PROJECT_ID', ''),
        'credentials_path' => storage_path('app/fcm-service-account.json'),
        'server_key'       => env('FCM_SERVER_KEY', ''),
    ],

];

