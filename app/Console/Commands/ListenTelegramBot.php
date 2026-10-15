<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ListenTelegramBot extends Command
{
    protected $signature = 'telegram:listen';

    public function handle()
    {
        // إرجاع 0 يعني إيقاف الأمر مباشرة بدون أي عملية اتصال
        return 0;
    }
}
