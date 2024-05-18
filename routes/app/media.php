<?php

use Illuminate\Support\Facades\Route;

Route::get('media', fn () => 'media')->name('media.index');
