<?php

namespace Ijeyg\Larapayamak;

use Ijeyg\Larapayamak\Services\GatewayManager;
use Ijeyg\Larapayamak\Services\SmsService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LarapayamakServiceProvider extends PackageServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/larapayamak.php' => config_path('larapayamak.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../config/larapayamak.php',
            'larapayamak'
        );
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('larapayamak')
            ->hasConfigFile();
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(GatewayManager::class, function ($app) {
            return new GatewayManager;
        });

        $this->app->singleton(SmsService::class, function ($app) {
            $gatewayManager = $app->make(GatewayManager::class);

            return new SmsService(
                $gatewayManager->gateway(),
                $gatewayManager
            );
        });

        $this->app->singleton('larapayamak', function ($app) {
            return $app->make(SmsService::class);
        });
    }
}
