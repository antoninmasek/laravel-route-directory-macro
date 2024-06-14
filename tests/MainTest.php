<?php

use AntoninMasek\LaravelRouteDirectoryMacro\Tests\Helpers\RoutesInspector;

beforeEach(function () {
    $this->app->setBasePath(__DIR__.'/..');
});

it('can get route by name', function () {
    $testRoute = RoutesInspector::getRoute('test');
    expect($testRoute)->toBeNull();

    Route::get('test', fn () => 'test')->name('test');

    $testRoute = RoutesInspector::getRoute('test');
    expect($testRoute)->not()->toBeNull(
        'The RoutesInspector::getRouteByName() does not work'
    );
});

it('can count registered routes', function () {
    expect(RoutesInspector::getRoutesCount())->toEqual(0);

    Route::get('test', fn () => 'test')->name('test');

    expect(RoutesInspector::getRoutesCount())->toEqual(1);
});

it('can count get route middleware', function () {
    Route::get('test', fn () => 'test')
        ->middleware(['web', 'auth'])
        ->name('test');

    expect(RoutesInspector::getRouteMiddleware('test'))
        ->toEqual(['web', 'auth']);
});

it('only loads php files', function () {
    Route::loadFromDirectory('routes/app');

    expect($this->output())->not()->toContain('This file should not be loaded');
});

it('loads routes from directories', function () {
    Route::loadFromDirectory('routes/app');

    expect(RoutesInspector::getRoutesCount())->toEqual(2)
        ->and(RoutesInspector::getRoute('media.index'))->not()->toBeNull()
        ->and(RoutesInspector::getRoute('users.index'))->not()->toBeNull()
        ->and(RoutesInspector::getRoute('auth.index'))->toBeNull();

    Route::loadFromDirectory('routes/public');
    expect(RoutesInspector::getRoutesCount())->toEqual(3)
        ->and(RoutesInspector::getRoute('auth.index'))->not()->toBeNull();
});

it('assigns middleware', function () {
    Route::loadFromDirectory('routes/public');
    Route::loadFromDirectory('routes/app', [
        'web', 'auth',
    ]);

    expect(RoutesInspector::getRouteMiddleware('media.index'))
        ->toEqual(['web', 'auth'])
        ->and(RoutesInspector::getRouteMiddleware('users.index'))
        ->toEqual(['web', 'auth'])
        ->and(RoutesInspector::getRouteMiddleware('auth.index'))
        ->toBeEmpty();
});

it('can use a prefix', function () {
    Route::loadFromDirectory('routes/app', prefix: $prefix = 'admin');

    RoutesInspector::getRoutes()->each(function (\Illuminate\Routing\Route $route) use ($prefix) {
        expect($route->uri())->toStartWith($prefix);
    });
});

it('uses default name based on prefix', function () {
    Route::loadFromDirectory('routes/app', prefix: $prefix = 'admin');

    RoutesInspector::getRoutes()->each(function (\Illuminate\Routing\Route $route) use ($prefix) {
        expect($route->getName())->toStartWith("{$prefix}.");
    });
});

it('is possible to overwrite the default name', function () {
    Route::loadFromDirectory(
        'routes/app',
        [],
        'admin',
        $name = 'auth',
    );

    RoutesInspector::getRoutes()->each(function (\Illuminate\Routing\Route $route) use ($name) {
        expect($route->getName())->toStartWith("{$name}.");
    });
});

it('is possible to use prefix without name', function () {
    Route::loadFromDirectory(
        'routes/app',
        [],
        $prefix = 'admin',
        false,
    );

    RoutesInspector::getRoutes()->each(function (\Illuminate\Routing\Route $route) use ($prefix) {
        expect($route->getName())->not()->toStartWith("{$prefix}.");
    });
});

it('normalizes dot suffix in the name when prefix has one or more trailing slashes', function () {
    $prefix = 'admin';
    Route::loadFromDirectory('routes/public', prefix: "{$prefix}////");

    RoutesInspector::getRoutes()->each(function (\Illuminate\Routing\Route $route) use ($prefix) {
        expect($route->getName())->not()->toStartWith("{$prefix}..")
            ->and($route->getName())->tostartWith("{$prefix}.");
    });

    expect(RoutesInspector::getRoute('admin.auth.index'))->not()->toBeNull();
});

it('does not fail when directory does not exist', function () {
    Route::loadFromDirectory('routes/not-exists');
})->throwsNoExceptions();

it('can use absolute path', function () {
    Route::loadFromDirectory('/routes/public');
    expect(RoutesInspector::getRoutesCount())->toBeEmpty();

    Route::loadFromDirectory('routes/public');
    expect(RoutesInspector::getRoutesCount())->toEqual(1);
});

it('does not load hidden files by default', function () {
    Route::loadFromDirectory('routes/app');
    config()->set('route-directory-macro.register_hidden_routes_in_environments', null);

    $secretRoute = RoutesInspector::getRoute('secret.index');
    expect($secretRoute)->toBeNull('It loaded hidden file');
});

it('does not load hidden files when config value is empty', function () {
    Route::loadFromDirectory('routes/app');

    $secretRoute = RoutesInspector::getRoute('secret.index');
    expect($secretRoute)->toBeNull('It loaded hidden file');
});

it('does not load hidden files when config value does not contain current environment', function () {
    $currentEnvironment = \Illuminate\Support\Facades\App::environment();
    config()->set('route-directory-macro.register_hidden_routes_in_environments', [$currentEnvironment.'_test']);

    Route::loadFromDirectory('routes/app');

    $secretRoute = RoutesInspector::getRoute('secret.index');
    expect($secretRoute)->toBeNull('It loaded hidden file');
});

it('does load hidden files in specified environment', function () {
    $currentEnvironment = \Illuminate\Support\Facades\App::environment();
    config()->set('route-directory-macro.register_hidden_routes_in_environments', [$currentEnvironment]);

    Route::loadFromDirectory('routes/app');

    $secretRoute = RoutesInspector::getRoute('secret.index');
    expect($secretRoute)->not()->toBeNull(
        'It did not load hidden file when explicitly allowed via config',
    );
});

it('does load hidden files in specified environment even when more environments are specified', function () {
    $currentEnvironment = \Illuminate\Support\Facades\App::environment();
    config()->set('route-directory-macro.register_hidden_routes_in_environments', [
        'local',
        $currentEnvironment,
    ]);

    Route::loadFromDirectory('routes/app');

    $secretRoute = RoutesInspector::getRoute('secret.index');
    expect($secretRoute)->not()->toBeNull(
        'It did not load hidden file when explicitly allowed via config',
    );
});
