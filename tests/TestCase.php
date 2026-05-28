<?php

namespace Ijeyg\Larapayamak\Tests;

use Ijeyg\Larapayamak\LarapayamakServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LarapayamakServiceProvider::class,
        ];
    }
}
