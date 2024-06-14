<?php

namespace AntoninMasek\LaravelRouteDirectoryMacro;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use SplFileInfo;

class LaravelRouteDirectoryMacroServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-route-directory-macro')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        Route::macro('loadFromDirectory', function (string $path, array $middleware = [], ?string $prefix = null, string|bool|null $name = null) {
            if (! is_null($prefix) && $name !== false) {
                $name ??= str($prefix)
                    ->ltrim('/')
                    ->rtrim('/')
                    ->replace('/', '.');
            }

            if (! is_null($name)) {
                $name = str($name)->rtrim('.')->append('.');
            }

            $path = ! str($path)->startsWith('/')
                ? base_path($path)
                : $path;

            $allowHiddenFilesInEnvironments = config('route-directory-macro.register_hidden_routes_in_environments');
            $shouldLoadHiddenFiles = ! empty($allowHiddenFilesInEnvironments)
                ? App::environment($allowHiddenFilesInEnvironments)
                : false;

            $files = File::exists($path)
                ? File::allFiles($path, $shouldLoadHiddenFiles)
                : [];

            collect($files)
                ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php')
                ->each(function (SplFileInfo $fileInfo) use ($middleware, $prefix, $name) {
                    Route::middleware($middleware)
                        ->prefix($prefix)
                        ->name($name)
                        ->group($fileInfo->getRealPath());
                });
        });
    }
}
