<?php

namespace Ijeyg\Larapayamak\Tests\Unit;

use Ijeyg\Larapayamak\Contracts\SmsProviderInterface;
use Ijeyg\Larapayamak\Services\SmsService;
use Ijeyg\Larapayamak\Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Mockery;

class SmsServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_forwards_simple_message_to_gateway_provider()
    {
        $provider = Mockery::mock(SmsProviderInterface::class);
        $provider->shouldReceive('sendSimpleMessage')
            ->once()
            ->with('9121111111', 'hello')
            ->andReturn(new JsonResponse(['success' => true], 200));

        $service = new SmsService($provider);
        $response = $service->sendSimpleMessage('9121111111', 'hello');

        $this->assertTrue($response->getData(true)['success']);
    }

    /** @test */
    public function it_forwards_pattern_message_to_gateway_provider()
    {
        $provider = Mockery::mock(SmsProviderInterface::class);
        $provider->shouldReceive('sendPatternMessage')
            ->once()
            ->with('9121111111', '100', ['code' => '1234'])
            ->andReturn(new JsonResponse(['success' => true], 200));

        $service = new SmsService($provider);
        $response = $service->sendPatternMessage('9121111111', '100', ['code' => '1234']);

        $this->assertTrue($response->getData(true)['success']);
    }
}
