<?php

namespace AntoninMasek\LaravelRouteDirectoryMacro\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AntoninMasek\LaravelRouteDirectoryMacro\LaravelRouteDirectoryMacro
 */
class LaravelRouteDirectoryMacro extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AntoninMasek\LaravelRouteDirectoryMacro\LaravelRouteDirectoryMacro::class;
    }
}
