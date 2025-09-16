<?php

namespace AntoninMasek\LaravelRouteDirectoryMacro\Tests;

use AntoninMasek\LaravelRouteDirectoryMacro\LaravelRouteDirectoryMacroServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'AntoninMasek\\LaravelRouteDirectoryMacro\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelRouteDirectoryMacroServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('filesystems.disks.local.serve', false);
    }
}
