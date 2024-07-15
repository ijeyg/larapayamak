<?php

namespace Ijeyg\Larapayamak\Tests;

use Ijeyg\Larapayamak\LarapayamakServiceProvider;
use Ijeyg\Larapayamak\Services\SmsService;
use Illuminate\Support\Facades\Config;

class SmsirServiceTest extends TestCase
{
    public function test_send_simple_sms()
    {
        $smsService = $this->app->make(SmsService::class);
        $response = $smsService->sendSimpleMessage('09374837726', 'Test message');
        $this->assertNotNull($response);
        $this->assertEquals(1, $response['status']);
    }
    public function test_invalid_configuration()
    {
        // Use invalid configuration by switching config
        Config::set('larapayamak', Config::get('larapayamak.invalid'));

        // Attempt to use the SmsService with invalid configuration
        $smsService = $this->app->make(SmsService::class);

        $response = $smsService->sendSimpleMessage('1234567890', 'Test message');

        // Assert that the service throws an exception or handles the invalid configuration appropriately
        $this->assertEquals(16,$response['status']);
    }
}
