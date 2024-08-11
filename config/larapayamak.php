<?php

// config for ijeyg/Larapayamak
return [
    'default' => env('SMS_GATEWAY', 'smsir'),
    'gateways' => [
        'smsir' => [
            'username' => env('SMSIR_USERNAME', ''),
            'token' => env('SMSIR_TOKEN', ''),
            'line' => env('SMSIR_LINE', ''),
        ],
        'farapayamak' => [
            'username' => env('FARAPAYAMAK_USERNAME', ''),
            'password' => env('FARAPAYAMAK_PASSWORD', ''),
            'line' => env('FARAPAYAMAK_LINE','' ),
        ]
    ],
];
