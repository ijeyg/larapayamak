<?php

namespace Ijeyg\Larapayamak;

use Ijeyg\Larapayamak\Gateways\FaraPayamak;
use Ijeyg\Larapayamak\Gateways\FarazSms;
use Ijeyg\Larapayamak\Gateways\MeliPayamak;
use Ijeyg\Larapayamak\Gateways\NikSms;
use Ijeyg\Larapayamak\Gateways\PayamResan;
use Ijeyg\Larapayamak\Gateways\Smsir;
use Ijeyg\Larapayamak\Services\SmsService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LarapayamakServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/larapayamak.php' => config_path('larapayamak.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/larapayamak.php',
            'larapayamak'
        );
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('larapayamak')
            ->hasConfigFile();
    }

    public function register()
    {
        parent::register();

        $this->app->singleton(SmsService::class, function ($app) {
            return new SmsService($this->createSmsProvider($app));
        });

        $this->app->singleton('larapayamak', function ($app) {
            return $app->make(SmsService::class);
        });
    }

    protected function createSmsProvider($app)
    {
        $defaultGateway = config('larapayamak.default');
        $providerConfig = config("larapayamak.gateways.{$defaultGateway}");

        if (is_null($providerConfig)) {
            throw new \Exception("Configuration for the gateway '{$defaultGateway}' not found.");
        }

        switch ($defaultGateway) {
            case 'farapayamak':
                return new FaraPayamak(
                    $providerConfig['username'],
                    $providerConfig['line'],
                    $providerConfig['password']
                );
            case 'melipayamak':
                return new MeliPayamak(
                    $providerConfig['username'],
                    $providerConfig['line'],
                    $providerConfig['password']
                );
            case 'farazsms':
                return new FarazSms(
                    $providerConfig['username'],
                    $providerConfig['line'],
                    $providerConfig['password']
                );
            case 'niksms':
                return new NikSms(
                    $providerConfig['username'],
                    $providerConfig['line'],
                    $providerConfig['password']
                );
            case 'payamresan':
                return new PayamResan(
                    $providerConfig['api_token'],
                );
            default:
                return new Smsir(
                    $providerConfig['username'],
                    $providerConfig['line'],
                    $providerConfig['token']
                );
        }
    }
}
