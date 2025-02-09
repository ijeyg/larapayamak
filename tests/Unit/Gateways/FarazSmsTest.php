<?php

namespace Ijeyg\Larapayamak\Tests\Unit\Gateways;

use Ijeyg\Larapayamak\Gateways\FarazSms;
use Ijeyg\Larapayamak\Gateways\Smsir;
use Ijeyg\Larapayamak\Services\HttpClientService;
use Ijeyg\Larapayamak\Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Mockery;

class FarazSmsTest extends TestCase
{
    public function getEnvironmentSetUp($app)
    {
        // Correct configuration
        $app['config']->set('larapayamak', [
            'default' => 'farazsms',
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

    /**
     * @test
     */
    public function it_sends_a_simple_message_successfully()
    {
        // Arrange
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaPost')
            ->once()
            ->andReturn(['OK', 'Message sent successfully']);

        $smsService = new FarazSms('test_username', 'test_password', 'test_from');
        $smsService->setHttpClient($mockHttpClient);

        // Act
        $response = $smsService->sendSimpleMessage('9121111111', 'Test message');
        $responseData = json_decode($response->getContent(), true);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertTrue($responseData['success']);
        $this->assertEquals(200, $response->status());
        $this->assertEquals('Message sent successfully', $responseData['message']);
    }

    /**
     * @test
     */
    public function it_fails_to_send_a_simple_message()
    {
        // Arrange
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaPost')
            ->once()
            ->andReturn(['ERROR', 'Invalid credentials']);

        $smsService = new FarazSms('test_username', 'test_password', 'test_from');
        $smsService->setHttpClient($mockHttpClient);

        // Act
        $response = $smsService->sendSimpleMessage('9121111111', 'Test message');
        $responseData = json_decode($response->getContent(), true);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertFalse($responseData['success']);
        $this->assertEquals(400, $response->status());
        $this->assertEquals('Invalid credentials', $responseData['message']);
    }

    /**
     * @test
     */
    public function it_handles_exception_properly_in_send_simple_message()
    {
        // Arrange
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaPost')
            ->once()
            ->andThrow(new \Exception('Connection timeout'));

        $smsService = new FarazSms('test_username', 'test_password', 'test_from');
        $smsService->setHttpClient($mockHttpClient);

        // Act
        $response = $smsService->sendSimpleMessage('9121111111', 'Test message');
        $responseData = json_decode($response->getContent(), true);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertFalse($responseData['success']);
        $this->assertEquals(500, $response->status());
        $this->assertEquals('Connection timeout', $responseData['message']);
    }

    /**
     * @test
     */
    public function it_sends_a_pattern_message_successfully()
    {
        // Arrange
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaPost')
            ->once()
            ->andReturn(['status' => 'OK']);

        $smsService = new FarazSms('test_username', 'test_password', 'test_from');
        $smsService->setHttpClient($mockHttpClient);

        $parameters = ['name' => 'John Doe'];

        // Act
        $response = $smsService->sendPatternMessage('9121111111', '13334', $parameters);
        $responseData = json_decode($response->getContent(), true);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertTrue($responseData['success']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * @test
     */
    public function it_fails_to_send_a_pattern_message()
    {
        // Arrange
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaPost')
            ->once()
            ->andReturn(['status' => 'ERROR', 'message' => 'Invalid pattern']);

        $smsService = new FarazSms('test_username', 'test_password', 'test_from');
        $smsService->setHttpClient($mockHttpClient);

        $parameters = ['name' => 'John Doe'];

        // Act
        $response = $smsService->sendPatternMessage('9121111111', '13334', $parameters);
        $responseData = json_decode($response->getContent(), true);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertFalse($responseData['success']);
        $this->assertEquals(400, $response->status());
        $this->assertEquals('Invalid pattern', $responseData['message']);
    }

    /**
     * @test
     */
    public function it_handles_exception_properly_in_send_pattern_message()
    {
        // Arrange
        $mockHttpClient = Mockery::mock(HttpClientService::class);
        $mockHttpClient->shouldReceive('connectViaPost')
            ->once()
            ->andThrow(new \Exception('Connection timeout'));

        $smsService = new FarazSms('test_username', 'test_password', 'test_from');
        $smsService->setHttpClient($mockHttpClient);

        $parameters = ['name' => 'John Doe'];

        // Act
        $response = $smsService->sendPatternMessage('9121111111', '13334', $parameters);
        $responseData = json_decode($response->getContent(), true);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertFalse($responseData['success']);
        $this->assertEquals(500, $response->status());
        $this->assertEquals('Connection timeout', $responseData['message']);
    }
}
