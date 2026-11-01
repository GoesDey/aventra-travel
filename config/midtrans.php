<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Pengaturan kredensial dan mode integrasi Midtrans Snap Sandbox/Production.
    |
    */
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => (bool) env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => (bool) env('MIDTRANS_IS_3DS', true),

    /*
    | Durasi kedaluwarsa pembayaran Snap dalam menit.
    */
    'expiry_duration' => (int) env('MIDTRANS_EXPIRY_DURATION', 60),

    /*
    | URL JS Snap (Sandbox vs Production)
    */
    'snap_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',
];
