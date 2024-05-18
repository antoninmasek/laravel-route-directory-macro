<?php

namespace AntoninMasek\LaravelRouteDirectoryMacro;

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRouteDirectoryMacroServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-route-directory-macro')
            ->hasConfigFile();
    }

    public function packageRegistered()
    {
        Route::macro('loadFromDirectory', function (string $path, array $middleware = [], ?string $prefix = null, ?string $name = null) {
            $name ??= ! is_null($prefix)
                ? str($prefix)->replace('/', '.')->append('.')
                : null;

            collect(scandir(base_path($path)))
                ->filter(fn (string $filename) => ! str($filename)->startsWith('.'))
                ->each(function (string $filename) use ($path, $middleware, $prefix, $name) {
                    Route::middleware($middleware)
                        ->prefix($prefix)
                        ->name($name)
                        ->group(base_path("$path/$filename"));
                });
        });
    }
}
