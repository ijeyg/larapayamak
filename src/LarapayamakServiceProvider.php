<?php

namespace Ijeyg\Larapayamak;

use Ijeyg\Larapayamak\Gateways\Farapayamak;
use Ijeyg\Larapayamak\Gateways\Smsir;
use Ijeyg\Larapayamak\Services\SmsService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LarapayamakServiceProvider extends PackageServiceProvider
{
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
                return new Farapayamak(
                    $providerConfig['username'],
                    $providerConfig['line'],
                    $providerConfig['password']
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
