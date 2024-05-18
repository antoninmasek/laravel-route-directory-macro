<?php

use Illuminate\Support\Facades\Route;

Route::get('users', fn () => 'users')->name('users.index');
