<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $token;

    public function __construct()
    {
        $this->token = config('services.telegram.token') ?? env('TELEGRAM_BOT_TOKEN');
    }

    public function sendMessage($chatId, $message)
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        return Http::withoutVerifying()->post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
    }

    public function getUpdates($offset = null)
    {
        $url = "https://api.telegram.org/bot{$this->token}/getUpdates";

        $response = Http::withoutVerifying()->get($url, [
            'offset' => $offset,
            'timeout' => 30
        ]);

        return $response->json();
    }
}