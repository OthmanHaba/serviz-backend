<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return to_route('/admin');
});

require __DIR__.'/auth.php';
