<?php

namespace Ijeyg\Larapayamak;

use Ijeyg\Larapayamak\Gateways\Smsir;
use Ijeyg\Larapayamak\Services\SmsService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LarapayamakServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
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
    }

    protected function createSmsProvider($app)
    {
        $defaultGateway = config('larapayamak.default');
        $providerConfig = config("larapayamak.gateways.{$defaultGateway}");

        if (is_null($providerConfig)) {
            throw new \Exception("Configuration for the gateway '{$defaultGateway}' not found.");
        }

        switch ($defaultGateway) {
            case 'smsir':
                return new Smsir(
                    $providerConfig['username'],
                    $providerConfig['line'],
                    $providerConfig['token']
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
