<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return to_route('filament.home');
});

require __DIR__.'/auth.php';
