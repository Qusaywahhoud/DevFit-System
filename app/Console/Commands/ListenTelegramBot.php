<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use App\Models\User;

class ListenTelegramBot extends Command
{
    protected $signature = 'telegram:listen';
    protected $description = 'Listen for Telegram bot messages and link users';

    public function handle(TelegramService $telegram)
    {
        $this->info('Starting Telegram listener...');
        $offset = 0;

        while (true) {
            $updates = $telegram->getUpdates($offset);

            if (isset($updates['result']) && is_array($updates['result'])) {
                foreach ($updates['result'] as $update) {
                    $offset = $update['update_id'] + 1;

                    if (isset($update['message']['text'])) {
                        $chatId = $update['message']['chat']['id'];
                        $text = $update['message']['text'];

                        // التحقق إذا كان المستخدم أتى من زر الربط في الموقع
                        if (str_starts_with($text, '/start ')) {
                            // استخراج رقم المستخدم من الكود المرسل
                            $userId = str_replace('/start ', '', $text);

                            $user = User::find($userId);

                            if ($user) {
                                // حفظ الـ Chat ID
                                $user->telegram_chat_id = $chatId;
                                $user->save();

                                $reply = "أهلاً بك يا {$user->name}! 🎉\n\nتم ربط حسابك في DevFit بنجاح. ستصلك التمارين المجدولة هنا.";
                            } else {
                                $reply = "عذراً، لم أتمكن من العثور على حسابك. يرجى التأكد من الضغط على الرابط من داخل لوحة التحكم.";
                            }
                        } else {
                            // رد افتراضي لأي رسالة أخرى
                            $reply = "مرحباً بك في بوت DevFit! 💪\nلربط حسابك، يرجى الدخول إلى لوحة التحكم في الموقع والضغط على زر الربط.";
                        }

                        $telegram->sendMessage($chatId, $reply);
                    }
                }
            }
            sleep(3);
        }
    }
}
