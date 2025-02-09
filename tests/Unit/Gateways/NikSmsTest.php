<?php

namespace Ijeyg\Larapayamak\Tests\Unit\Gateways;

use Ijeyg\Larapayamak\Gateways\NikSms;
use Ijeyg\Larapayamak\Services\HttpClientService;
use Ijeyg\Larapayamak\Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Mockery;

class NikSmsTest extends TestCase
{
    public function getEnvironmentSetUp($app)
    {
        // Correct configuration
        $app['config']->set('larapayamak', [
            'default' => 'niksms',
            'gateways' => [
                'smsir' => [
                    'username' => "",
                    'token' => "",
                    'line' => "",
                ],
            ],
        ]);
    }

    public function tearDown(): void
    {
        Mockery::close(); // Ensure Mockery resources are properly released after each test
    }

    public function it_sends_a_simple_message_successfully()
    {
        // Arrange
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaPost')
            ->once()
            ->andReturn(['status' => 'ok']);

        $smsir = new NikSms('test_username', 'test_line', 'test_password');
        $smsir->setHttpClient($mockHttpClient);

        // Act
        $response = $smsir->sendSimpleMessage('1234567890', 'Test message');

        // Assert
        $responseData = json_decode($response->getContent(), true);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJson($response->getContent());
        $this->assertTrue($responseData['success']);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    /** @test */
    public function it_fails_to_send_a_simple_message()
    {
        // Arrange
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaPost')
            ->once()
            ->andReturn(['status' => 'error', 'error' => 'Invalid credentials']);

        $smsir = new NikSms('test_username', 'test_line', 'test_password');
        $smsir->setHttpClient($mockHttpClient);

        // Act
        $response = $smsir->sendSimpleMessage('1234567890', 'Test message');

        // Assert
        $responseData = json_decode($response->getContent(), true);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJson($response->getContent());
        $this->assertFalse($responseData['success']);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->status());
    }

    /** @test */
    public function it_handles_exceptions_properly()
    {
        // Arrange
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaPost')
            ->once()
            ->andThrow(new \Exception('Connection timeout'));

        $smsir = new NikSms('test_username', 'test_line', 'test_password');
        $smsir->setHttpClient($mockHttpClient);

        // Act
        $response = $smsir->sendSimpleMessage('1234567890', 'Test message');

        // Assert
        $responseData = json_decode($response->getContent(), true);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJson($response->getContent());
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Connection timeout', $responseData['message']);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->status());
    }
}
