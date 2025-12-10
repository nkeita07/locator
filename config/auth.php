<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | IMPORTANT :
    | - web  => utilisé pour les pages Laravel (session IAM)
    | - api  => utilisé pour les routes API (pas de redirection, retourne 401 JSON)
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'collaborateurs',
        ],

        'api' => [
            'driver' => 'session',      // On garde les sessions IAM pour API
            'provider' => 'collaborateurs',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'collaborateurs' => [
            'driver' => 'eloquent',
            'model' => App\Models\Collaborateur::class,
        ],

        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
