<?php

use Illuminate\Support\Facades\Route;

Route::get('secret', fn() => 'secret')->name('secret.index');
