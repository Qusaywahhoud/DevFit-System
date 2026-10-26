<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $token;

    public function __construct()
    {
        $this->token = config('services.telegram.token') ?? config('services.telegram.bot_token') ?? env('TELEGRAM_BOT_TOKEN');
    }

    public function sendMessage($chatId, $message, $replyMarkup = null)
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        $payload = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ];

        // إذا تم إرسال أزرار، قم بدمجها مع الرسالة
        if ($replyMarkup) {
            $payload['reply_markup'] = $replyMarkup;
        }

        return Http::withoutVerifying()->post($url, $payload);
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

