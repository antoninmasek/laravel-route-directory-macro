<?php

beforeEach(function () {
    $basePath = __DIR__.'/../routes';

    \Illuminate\Support\Facades\Route::loadFromDirectory(
        "{$basePath}/app",
        ['web', 'auth'],
    );

    \Illuminate\Support\Facades\Route::loadFromDirectory(
        "{$basePath}/public",
    );
});

it('it does not load hidden files', function () {
    $routes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutes();

    $secretRoute = collect($routes)->first(function (\Illuminate\Routing\Route $route) {
        return $route->getName() === 'secret.index';
    });

    expect($secretRoute)->toBeNull('It is loading hidden files, but it should not.');
});

it('it loads routes from directories', function () {
    $routes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutes();

    $expectedRouteNames = [
        'media.index',
        'users.index',
        'auth.index',
    ];

    $routesCount = collect($routes)
        ->filter(function (\Illuminate\Routing\Route $route) use ($expectedRouteNames) {
            return in_array($route->getName(), $expectedRouteNames);
        })
        ->count();

    expect($routesCount)->toEqual(
        count($expectedRouteNames),
        'It did not load all routes',
    );
});

it('it assigns middleware', function () {
    $routes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutes();

    $expectedRouteNames = ['media.index', 'users.index'];
    $routesCount = collect($routes)
        ->filter(function (\Illuminate\Routing\Route $route) use ($expectedRouteNames) {
            return in_array($route->getName(), $expectedRouteNames);
        })
        ->filter(function (\Illuminate\Routing\Route $route) {
            return $route->action['middleware'] === ['web', 'auth'];
        })
        ->count();

    expect($routesCount)->toEqual(
        count($expectedRouteNames),
        'It did not assign middleware correctly',
    );

    $expectedRouteNames = ['auth.index'];
    $routesCount = collect($routes)
        ->filter(function (\Illuminate\Routing\Route $route) use ($expectedRouteNames) {
            return in_array($route->getName(), $expectedRouteNames);
        })
        ->filter(function (\Illuminate\Routing\Route $route) {
            return empty($route->action['middleware']);
        })
        ->count();

    expect($routesCount)->toEqual(
        count($expectedRouteNames),
        'It did assign middleware incorrectly',
    );
});

it('it can use a prefix', function () {
    $basePath = __DIR__.'/../routes';

    \Illuminate\Support\Facades\Route::loadFromDirectory(
        "{$basePath}/public",
        [],
        'admin',
    );

    $routes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutes();
    $count = collect($routes)
        ->filter(function (\Illuminate\Routing\Route $route) {
            return str($route->uri())->startsWith('admin');
        })
        ->count();

    expect($count)->toEqual(1);
});

it('it assigns correct default name with one dot suffix when prefix has one or more trailing slashes', function () {
    $basePath = __DIR__.'/../routes';

    \Illuminate\Support\Facades\Route::loadFromDirectory(
        "{$basePath}/public",
        [],
        'admin////',
    );

    $routes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutes();

    /** @var \Illuminate\Routing\Route $route */
    $route = collect($routes)->first(function (\Illuminate\Routing\Route $route) {
        return str($route->uri())->startsWith('admin');
    });

    expect($route->getName())->toEqual('admin.auth.index');
});

it('it can uses the prefix as a default name', function () {
    $basePath = __DIR__.'/../routes';

    \Illuminate\Support\Facades\Route::loadFromDirectory(
        "{$basePath}/public",
        [],
        'admin',
    );

    $routes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutes();
    $count = collect($routes)
        ->filter(function (\Illuminate\Routing\Route $route) {
            return str($route->getName())->startsWith('admin.');
        })
        ->count();

    expect($count)->toEqual(1);
});

it('it is possible to overwrite the name', function () {
    $basePath = __DIR__.'/../routes';

    \Illuminate\Support\Facades\Route::loadFromDirectory(
        "{$basePath}/public",
        [],
        'admin',
        'overwritten.'
    );

    $routes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutes();
    $count = collect($routes)
        ->filter(function (\Illuminate\Routing\Route $route) {
            return str($route->getName())->startsWith('overwritten.');
        })
        ->count();

    expect($count)->toEqual(1);
});

it('it is possible to use prefix without name', function () {
    $basePath = __DIR__.'/../routes';

    \Illuminate\Support\Facades\Route::loadFromDirectory(
        "{$basePath}/public",
        [],
        'admin',
        false,
    );

    $routes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutes();
    $count = collect($routes)
        ->filter(function (\Illuminate\Routing\Route $route) {
            return str($route->getName())->startsWith('admin.');
        })
        ->count();

    expect($count)->toEqual(0);
});
