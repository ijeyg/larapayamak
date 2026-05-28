<?php

namespace Ijeyg\Larapayamak\Tests\Feature;

use Ijeyg\Larapayamak\Facades\Larapayamak;
use Ijeyg\Larapayamak\Gateways\FarazSms;
use Ijeyg\Larapayamak\Gateways\Smsir;
use Ijeyg\Larapayamak\LarapayamakServiceProvider;
use Ijeyg\Larapayamak\Services\SmsService;
use Ijeyg\Larapayamak\Tests\TestCase;
use Illuminate\Support\Facades\Http;

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

    /** @test */
    public function gateway_method_resolves_smsir_provider()
    {
        $service = $this->app->make(SmsService::class);

        $this->assertInstanceOf(Smsir::class, $service->gateway('smsir'));
    }

    /** @test */
    public function gateway_method_resolves_farazsms_provider()
    {
        $service = $this->app->make(SmsService::class);

        $this->assertInstanceOf(FarazSms::class, $service->gateway('farazsms'));
    }

    /** @test */
    public function gateway_method_throws_for_invalid_gateway()
    {
        $service = $this->app->make(SmsService::class);

        $this->expectException(\InvalidArgumentException::class);
        $service->gateway('invalid');
    }

    /** @test */
    public function existing_default_send_methods_still_work()
    {
        Http::fake([
            'https://api.sms.ir/v1/send*' => Http::response(['status' => 1, 'message' => 'ok'], 200),
        ]);

        $response = Larapayamak::sendSimpleMessage('09121111111', 'hello');

        $this->assertTrue($response->getData(true)['success']);
    }

    /** @test */
    public function facade_gateway_chaining_works()
    {
        Http::fake([
            'https://ippanel.com/services.jspd' => Http::response(['OK', 'sent'], 200),
        ]);

        $response = Larapayamak::gateway('farazsms')->sendSimpleMessage('09121111111', 'hello');

        $this->assertTrue($response->getData(true)['success']);
    }
}
