<?php

// config for ijeyg/Larapayamak
return [
    'default' => env('SMS_GATEWAY', 'smsir'),
    'gateways' => [
        'smsir' => [
            'username' => env('SMSIR_USERNAME', '09374837726'),
            'token' => env('SMSIR_TOKEN', 'I5753zYoTx3ysROB5KJUV3uh6GWFGGzICE3dwECz5VArZAm1pIuyPk1IcNPDLSXI'),
            'line' => env('SMSIR_LINE', 30007487126685),
        ]
    ],
];
