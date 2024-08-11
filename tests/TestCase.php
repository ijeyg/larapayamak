<?php

namespace Ijeyg\Larapayamak\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Ijeyg\Larapayamak\LarapayamakServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LarapayamakServiceProvider::class,
        ];
    }
}
