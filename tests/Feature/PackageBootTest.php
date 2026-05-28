<?php

namespace Ijeyg\Larapayamak\Tests\Feature;

use Ijeyg\Larapayamak\Facades\Larapayamak;
use Ijeyg\Larapayamak\Gateways\FarazSms;
use Ijeyg\Larapayamak\LarapayamakServiceProvider;
use Ijeyg\Larapayamak\Services\SmsService;
use Ijeyg\Larapayamak\Tests\TestCase;

class PackageBootTest extends TestCase
{
    /** @test */
    public function it_registers_the_package_service_provider()
    {
        $loaded = $this->app->getLoadedProviders();

        $this->assertArrayHasKey(LarapayamakServiceProvider::class, $loaded);
        $this->assertTrue($loaded[LarapayamakServiceProvider::class]);
    }

    /** @test */
    public function it_loads_package_configuration()
    {
        $this->assertSame('smsir', config('larapayamak.default'));
        $this->assertIsArray(config('larapayamak.gateways'));
    }

    /** @test */
    public function it_registers_config_publish_path()
    {
        $paths = LarapayamakServiceProvider::pathsToPublish(LarapayamakServiceProvider::class, 'config');

        $this->assertNotEmpty($paths);
        $this->assertContains(config_path('larapayamak.php'), $paths);
    }

    /** @test */
    public function it_resolves_facade_and_container_binding()
    {
        $instance = $this->app->make('larapayamak');

        $this->assertInstanceOf(SmsService::class, $instance);
        $this->assertSame($instance, Larapayamak::getFacadeRoot());
    }

    /** @test */
    public function it_resolves_gateway_from_configuration()
    {
        config()->set('larapayamak.default', 'farazsms');
        config()->set('larapayamak.gateways.farazsms', [
            'username' => 'u',
            'password' => 'p',
            'line' => '3000',
        ]);

        $this->app->forgetInstance(SmsService::class);
        $service = $this->app->make(SmsService::class);

        $provider = (fn () => $this->provider)->call($service);
        $this->assertInstanceOf(FarazSms::class, $provider);
    }

    /** @test */
    public function it_throws_for_unsupported_gateway()
    {
        config()->set('larapayamak.default', 'unknown_gateway');
        config()->set('larapayamak.gateways.unknown_gateway', ['foo' => 'bar']);

        $this->app->forgetInstance(SmsService::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->app->make(SmsService::class);
    }
}
