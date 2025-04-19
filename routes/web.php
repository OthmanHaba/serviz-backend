<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return to_route('filament.admin.pages.dashboard');
});

require __DIR__.'/auth.php';
