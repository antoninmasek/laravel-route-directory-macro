<?php

namespace AntoninMasek\LaravelRouteDirectoryMacro\Tests\Helpers;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;

class RoutesInspector
{
    public static function getRoutes(): Collection
    {
        return collect(\Route::getRoutes()->getRoutes());
    }

    public static function getRoute(string $name): ?Route
    {
        return self::getRoutes()->first(function (Route $route) use ($name) {
            return $route->getName() === $name;
        });
    }

    public static function getRouteMiddleware(string $name): ?array
    {
        return self::getRoute($name)?->middleware();
    }

    public static function getRoutesCount(): int
    {
        return self::getRoutes()->count();
    }
}
