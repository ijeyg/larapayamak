<?php

namespace Ijeyg\Larapayamak\Tests\Unit\Gateways;

use Ijeyg\Larapayamak\Gateways\MeliPayamak;
use Ijeyg\Larapayamak\Services\HttpClientService;
use Ijeyg\Larapayamak\Tests\TestCase;
use Mockery;


class MeliPayamakTest extends TestCase
{

    public function getEnvironmentSetUp($app)
    {
        // Correct configuration
        $app['config']->set('larapayamak', [
            'default' => 'farapayamak',
            'gateways' => [
                'farapayamak' => [
                    'username' => "",
                    'password' => "",
                    'line' =>"",
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
            ->andReturn(['ReturnValue' => 1]); // Simulate a successful response

        $smsir = new MeliPayamak('test_username', 'test_line', 'test_token');
        $smsir->setHttpClient($mockHttpClient);

        // Act
        $response = $smsir->sendSimpleMessage('1234567890', 'Test message');
        $responseData = json_decode($response->getContent(), true);

        // Assert
        $this->assertJson($response->getContent());
        $this->assertTrue($responseData['success']);
        $this->assertEquals(200, $response->status());
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
            ->andReturn(['ReturnValue' => 10]); // Simulate unsuccessful response

        $smsir = new MeliPayamak('test_username', 'test_line', 'test_token');
        $smsir->setHttpClient($mockHttpClient);

        // Act
        $response = $smsir->sendSimpleMessage('1234567890', 'Test message');
        $responseData = json_decode($response->getContent(), true);

        // Assert
        $this->assertJson($response->getContent());
        $this->assertFalse($responseData['success']);
        $this->assertNotEquals(200, $response->status());
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
            ->andReturn(['ReturnValue' => 1]); // Simulate a successful response

        $parameters = ['name' => 'John Doe'];
        $smsir = new MeliPayamak('test_username', 'test_line', 'test_token');
        $smsir->setHttpClient($mockHttpClient);

        // Act
        $response = $smsir->sendPatternMessage('1234567890', '13334', $parameters);
        $responseData = json_decode($response->getContent(), true);

        // Assert
        $this->assertJson($response->getContent());
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
            ->andReturn(['ReturnValue' => 10]); // Simulate unsuccessful response

        $parameters = ['name' => 'John Doe'];
        $smsir = new MeliPayamak('test_username', 'test_line', 'test_token');
        $smsir->setHttpClient($mockHttpClient);

        // Act
        $response = $smsir->sendPatternMessage('1234567890', '13334', $parameters);
        $responseData = json_decode($response->getContent(), true);

        // Assert
        $this->assertJson($response->getContent());
        $this->assertFalse($responseData['success']);
        $this->assertNotEquals(200, $response->status());
    }

    /**
     * @test
     */
    public function it_sets_parameters_correctly()
    {
        $smsir = new MeliPayamak('test_username', 'test_line', 'test_token');

        $reflection = new \ReflectionClass($smsir);
        $method = $reflection->getMethod('setParameters');
        $method->setAccessible(true);

        $parameters = ['name' => 'John Doe', 'code' => '1234'];
        $expected = 'John Doe;1234';

        $result = $method->invokeArgs($smsir, [$parameters]);

        $this->assertEquals($expected, $result);
    }
}
