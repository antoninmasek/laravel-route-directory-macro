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
        Route::macro('loadFromDirectory', function (string $path, array $middleware = [], ?string $prefix = null, string|bool|null $name = null) {
            if (! is_null($prefix)) {
                $prefix = str($prefix)->endsWith('/')
                    ? str($prefix)->replaceLast('/', '')
                    : $prefix;

                if ($name !== false) {
                    $name ??= str($prefix)->replace('/', '.')->append('.');
                }
            }

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
