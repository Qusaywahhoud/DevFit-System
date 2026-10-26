<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SendMorningReminders extends Command
{
    protected $signature = 'telegram:send-morning-reminders';
    protected $description = 'إرسال تذكير صباحي لجميع المستخدمين الذين ربطوا حساباتهم على تيليجرام';

    public function handle(TelegramService $telegram)
    {
        $users = User::whereNotNull('telegram_chat_id')->get();
        $count = 0;

        if ($users->isEmpty()) {
            $this->info('لا يوجد أي مستخدم لديه حساب تيليجرام مرتبط حالياً.');
            return;
        }

        foreach ($users as $user) {
            try {
                $response = $telegram->sendMessage(
                    $user->telegram_chat_id,
                    "🌅 صباح النشاط والحيوية يا {$user->name}!\n\nلا تنس تمرينك اليومي لتبقى في قمة لياقتك. أرسل الأمر /exercises لتسجيل تمرينك اليوم والتقدم نحو أهدافك! 💪🔥"
                );

                if ($response->successful()) {
                    $count++;
                }
            } catch (\Exception $e) {
                Log::error("Failed to send morning reminder to user {$user->id}: " . $e->getMessage());
            }
        }

        $this->info("تم إرسال التذكيرات الصباحية بنجاح إلى {$count} مستخدم(ين)! ☀️");
    }
}

