<?php

namespace AntoninMasek\LaravelRouteDirectoryMacro;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use AntoninMasek\LaravelRouteDirectoryMacro\Commands\LaravelRouteDirectoryMacroCommand;

class LaravelRouteDirectoryMacroServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-route-directory-macro')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-route-directory-macro_table')
            ->hasCommand(LaravelRouteDirectoryMacroCommand::class);
    }
}
