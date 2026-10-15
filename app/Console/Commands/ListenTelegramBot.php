<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ListenTelegramBot extends Command
{
    protected $signature = 'telegram:listen';
    protected $description = 'Disabled in favor of Webhook';

    public function handle()
    {
        $this->info('Listener disabled. We are using Webhooks now! 🚀');
        return 0;
    }
}
