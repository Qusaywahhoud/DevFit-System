<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// يمكنك اختيار التكرار المناسب: hourly() كل ساعة، أو everyMinute() للتجربة الفورية
Schedule::command('telegram:send-exercise')->everyFourHours();