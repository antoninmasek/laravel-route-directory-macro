<?php

use Illuminate\Support\Facades\Route;

Route::get('auth', fn () => 'auth')->name('auth.index');
