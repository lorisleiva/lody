<?php

namespace Lorisleiva\Lody\Tests;

use Lorisleiva\Lody\LodyServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LodyServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        //
    }
}
