<?php

namespace Ijeyg\Larapayamak\Tests\Gateways;

use Ijeyg\Larapayamak\Services\SmsService;
use Ijeyg\Larapayamak\Tests\TestCase;
use Illuminate\Support\Facades\Config;

class SmsirTest extends TestCase
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
        Config::set('larapayamak', Config::get('larapayamak.invalid'));
        $this->expectException(\Exception::class);
        $smsService = $this->app->make(SmsService::class);
        $response = $smsService->sendSimpleMessage('1234567890', 'Test message');
        $this->assertEquals(16,$response['status']);
    }
}
