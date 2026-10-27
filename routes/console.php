<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// التذكير الصباحي يومياً الساعة 08:00
Schedule::command('telegram:send-morning-reminders')->dailyAt('08:00');

// تمرين عشوائي مع صورة كل ساعة بين 08:00 و22:00
Schedule::command('telegram:send-exercise')
    ->hourly()
    ->between('08:00', '22:00')
    ->withoutOverlapping();
