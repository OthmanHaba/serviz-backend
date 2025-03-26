<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Artisan::command('notify', function () {
    broadcast(new \App\Events\NewNotification('a7a'));
})->purpose('Send a notification to the notifications channel')->hourly();
