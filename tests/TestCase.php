<?php

namespace Ijeyg\Larapayamak\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Ijeyg\Larapayamak\LarapayamakServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LarapayamakServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        // Correct configuration
        $app['config']->set('larapayamak', [
            'default' => 'smsir',
            'gateways' => [
                'smsir' => [
                    'username' => "09374837726",
                    'token' => "I5753zYoTx3ysROB5KJUV3uh6GWFGGzICE3dwECz5VArZAm1pIuyPk1IcNPDLSXI",
                    'line' => 30007487126685,
                ],
            ],
        ]);

        // Incorrect configuration
        $app['config']->set('larapayamak.invalid', [
            'default' => 'invalid_gateway',
            'gateways' => [
                'smsir' => [
                    'username' => "09374837726",
                    'token' => "I5753zYoTx3ysROB5KJUV3uh6GWFGGzICE3dwECz5VArZAm1pIuyPk1IcNPDLSXI",
                    'line' => 'invalid_line_value',
                ],
            ],
        ]);
    }
}
