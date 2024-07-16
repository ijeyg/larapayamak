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

    public function test_send_pattern_sms()
    {
        $smsService = $this->app->make(SmsService::class);
        $data = [
            'code' => 1213234
        ];
        $response = $smsService->sendPatternMessage('09374837726', 100000, $data);
        $this->assertNotNull($response);
        $this->assertEquals(1, $response['status']);
    }
}
