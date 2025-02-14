<?php

namespace Ijeyg\Larapayamak\Tests\Unit\Gateways;

use Ijeyg\Larapayamak\Gateways\PayamResan;
use Ijeyg\Larapayamak\Services\HttpClientService;
use Ijeyg\Larapayamak\Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Mockery;


class PayamResanTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_sends_a_simple_message_successfully()
    {
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaPost')
            ->once()
            ->andReturn(['Result' => true]);

        $smsProvider = new PayamResan('test_api_key');
        $smsProvider->setHttpClient($mockHttpClient);

        $response = $smsProvider->sendSimpleMessage('9121111111', 'Test Message');
        $responseData = json_decode($response->getContent(), true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertTrue($responseData['success']);
        $this->assertEquals(200, $response->status());
    }

    /** @test */
    public function it_fails_to_send_a_simple_message()
    {
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaPost')
            ->once()
            ->andReturn([]);

        $smsProvider = new PayamResan('test_api_key');
        $smsProvider->setHttpClient($mockHttpClient);

        $response = $smsProvider->sendSimpleMessage('9121111111', 'Test Message');
        $responseData = json_decode($response->getContent(), true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertFalse($responseData['success']);
        $this->assertEquals(400, $response->status());
    }

    /** @test */
    public function it_sends_a_pattern_message_successfully()
    {
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaGet')
            ->once()
            ->andReturn(['Result' => true]);

        $smsProvider = new PayamResan('test_api_key');
        $smsProvider->setHttpClient($mockHttpClient);

        $response = $smsProvider->sendPatternMessage('9121111111', '1234', ['p1' => 'value1', 'p2' => 'value2']);
        $responseData = json_decode($response->getContent(), true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertTrue($responseData['success']);
        $this->assertEquals(200, $response->status());
    }

    /** @test */
    public function it_fails_to_send_a_pattern_message()
    {
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaGet')
            ->once()
            ->andReturn([]);

        $smsProvider = new PayamResan('test_api_key');
        $smsProvider->setHttpClient($mockHttpClient);

        $response = $smsProvider->sendPatternMessage('9121111111', '1234', ['p1' => 'value1', 'p2' => 'value2']);
        $responseData = json_decode($response->getContent(), true);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertFalse($responseData['success']);
        $this->assertEquals(400, $response->status());
    }
}
