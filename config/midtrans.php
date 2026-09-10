<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    |
    | Your Midtrans server key for authenticating API requests.
    | Get this from your Midtrans Dashboard.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    |
    | Your Midtrans client key for frontend Snap integration.
    | Get this from your Midtrans Dashboard.
    |
    */

    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Production Mode
    |--------------------------------------------------------------------------
    |
    | Set to true for production environment, false for sandbox/testing.
    | Sandbox mode is used for testing without actual transactions.
    |
    */

    'is_production' => env(
        'MIDTRANS_IS_PRODUCTION',
        str_starts_with((string) env('MIDTRANS_SERVER_KEY', ''), 'Mid-server-')
    ),

    /*
    |--------------------------------------------------------------------------
    | Enable Sanitization
    |--------------------------------------------------------------------------
    |
    | Enable automatic input sanitization for security.
    |
    */

    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),

    /*
    |--------------------------------------------------------------------------
    | Enable 3D Secure
    |--------------------------------------------------------------------------
    |
    | Enable 3D Secure for credit card transactions.
    |
    */

    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    /*
    |--------------------------------------------------------------------------
    | Payment Mode
    |--------------------------------------------------------------------------
    |
    | Set to 'simulation' to skip Midtrans API and use fake tokens for testing.
    | Set to 'live' to use real Midtrans Snap integration.
    | Default: 'live'
    |
    */

    'payment_mode' => env('PAYMENT_MODE', 'live'),

];
