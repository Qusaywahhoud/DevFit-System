<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// جدولة التذكير الصباحي يومياً في تمام الساعة 08:00 صباحاً
Schedule::command('telegram:send-morning-reminders')->dailyAt('08:00');

// جدولة التمارين الدورية (مثلاً كل 4 ساعات أو حسب الحاجة)
Schedule::command('telegram:send-exercise')->everyFourHours();
