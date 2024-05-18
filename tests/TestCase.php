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

    protected function getPackageProviders($app)
    {
        return [
            LaravelRouteDirectoryMacroServiceProvider::class,
        ];
    }
}
