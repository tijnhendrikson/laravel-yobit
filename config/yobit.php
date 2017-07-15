<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Yobit authentication
    |--------------------------------------------------------------------------
    |
    | Authentication key and secret for Yobit API.
    |
     */

    'auth' => [
        'key'    => env('YOBIT_KEY', ''),
        'secret' => env('YOBIT_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Api URLS
    |--------------------------------------------------------------------------
    |
    | Urls for Yobit public, and trading api's
    |
     */

    'urls' => [
        'publicv2'  => 'https://yobit.net/api/2/',
        'publicv3'  => 'https://yobit.net/api/3/',
        'trade' => 'https://yobit.net/tapi/',
    ],

];
