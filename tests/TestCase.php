<?php

namespace Druc\Langscanner\Tests;

use Druc\Langscanner\LangscannerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LangscannerServiceProvider::class,
        ];
    }
}
