# Easily load all defined routes from a directory with multiple route files.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/antoninmasek/laravel-route-directory-macro.svg?style=flat-square)](https://packagist.org/packages/antoninmasek/laravel-route-directory-macro)
[![Total Downloads](https://img.shields.io/packagist/dt/antoninmasek/laravel-route-directory-macro.svg?style=flat-square)](https://packagist.org/packages/antoninmasek/laravel-route-directory-macro)

This package registers `loadFromDirectory` macro on the `Route` facade. Using this macro you can easily load all defined
routes from multiple files within a directory.

> I also wrote an [article](https://tonymasek.com/blog/laravel-route-directory-macro) about this package 

## Installation

You can install the package via composer:

```bash
composer require antoninmasek/laravel-route-directory-macro
```

## Motivation behind the macro

While it is perfectly all right to have all routes inside a single file, I found myself reaching for multiple files
quite often. For some reason I just prefer the fact, that I have a single file per entity or domain. And to make the
route registration process easier for myself I wrote a simple piece of code which I was copying from project to project.
So I decided to package it up, so I don't have to do it anymore.

## Basic usage

Consider the following routes directory structure:

```
routes/
│
├── app/
│   ├── users.php
│   ├── tasks.php
│   └── media.php
│
└── public/
    └── auth.php
```

Now, traditionally we would have to require each file from our `web.php` or any other entry point for your routes. The
other option is to register each file in your `bootstrap/app.php` (or `RouteServiceProvider` for Laravel 10 and below).

Using this package you can just do the following:

```php
Route::loadFromDirectory(
    path: 'routes/app',
    middleware: ['web', 'auth'],
);

Route::loadFromDirectory(
    path: 'routes/public',
    middleware: ['web'],
);
```

This will register routes from all files inside these two directories. While the first call registers routes
from `routes/app` directory and assigns `web` and `auth` auth middleware, the second call registers routes
from `routes/public` directory and only assigns `web` middleware.

### Laravel 11.x or later

Use it inside your `bootstrap/app.php`

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::loadFromDirectory(
                'routes/app',
                ['web', 'auth'],
            );
        },
    )
```

### Laravel 10.x and below

Use it inside your `RouteServiceProvider.php`

```php
public function boot(): void
{
    $this->configureRateLimiting();

    $this->routes(function () {
        Route::loadFromDirectory(
            'routes/app',
            ['web', 'auth'],
        );
    });
}
```

## Parameters

### Path

The only required parameter is the path to the directory with your route files. The macro uses `base_path` helper in the
background, the path is relative to the base path of your project.

If you wish to use an absolute path, you have to have `/` at the start of your path string. Then the `base_path` helper
won't be used.

### Middleware

This is an array, where you can specify which middleware should be applied to routes in the specified directory.

### Prefix

If you specify prefix, then all routes in the directory will be prefixed. Let's look at the following example:

```php
Route::loadFromDirectory(
    path: 'routes/admin',
    middleware: ['web', 'auth', 'admin'],
    prefix: 'admin',
);
```

Then all routes would be `/admin/...`

### Name

By default, when you specify a prefix the name will automatically become the prefix with `.` at the end. So for
prefix `admin` the name would be `admin.`.

If you wish to use your name, just pass it as the forth argument. Or if you wish to just use prefix without any name set
the name argument to `false`.

## Notes

The macro is excluding files starting with `.` to prevent hidden files from being loaded. You can also use this to your
advantage in a scenario, where you have routes file, which you don't want to load yet. You can just prefix it with dot
and that's it. For example `routes/.secret.php`.

## Credits

- [Antonin Masek](https://github.com/antoninmasek)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
