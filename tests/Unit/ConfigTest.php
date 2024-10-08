<?php

namespace Ijeyg\Larapayamak\Tests\Unit;

use Ijeyg\Larapayamak\Services\SmsService;
use Ijeyg\Larapayamak\Tests\TestCase;
use Illuminate\Support\Facades\Config;

class ConfigTest extends TestCase
{
    public function test_invalid_configuration()
    {
        Config::set('larapayamak', Config::get('larapayamak.invalid'));
        $this->expectException(\Exception::class);
        $smsService = $this->app->make(SmsService::class);
        $response = $smsService->sendSimpleMessage('1234567890', 'Test message');
        $this->assertEquals(16, $response['status']);
    }
}
