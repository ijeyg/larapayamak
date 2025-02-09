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
        ],
        'melipayamak' => [
            'username' => env('FARAPAYAMAK_USERNAME', ''),
            'password' => env('FARAPAYAMAK_PASSWORD', ''),
            'line' => env('FARAPAYAMAK_LINE','' ),
        ],
        'farazsms' => [
            'username' => env('FARAZSMS_USERNAME', ''),
            'password' => env('FARAZSMS_PASSWORD', ''),
            'line' => env('FARAZSMS_LINE','' ),
        ]
    ],
];
