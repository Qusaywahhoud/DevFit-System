<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use App\Models\Exercise;
use App\Models\User; // استدعاء مودل المستخدمين

class SendTelegramExercise extends Command
{
    protected $signature = 'telegram:send-exercise';
    protected $description = 'إرسال تمرين عشوائي عبر تيليجرام لجميع المستخدمين المسجلين';

    public function handle(TelegramService $telegram)
    {
        // 1. جلب تمرين عشوائي
        $exercise = Exercise::inRandomOrder()->first();

        if (!$exercise) {
            $this->error('لا توجد تمارين متاحة في قاعدة البيانات!');
            return;
        }

        // 2. تجهيز رسالة التنبيه
        $message = "⏰ *تذكير دوري من نظام DevFit*:\n\n" .
                   "💪 *التمرين:* {$exercise->title_ar}\n" .
                   "📝 *الوصف:* {$exercise->description_ar}\n" .
                   "⏱️ *المدة:* {$exercise->duration_seconds} ثانية\n\n" .
                   "خذ استراحة قصيرة وامنح جسمك وعينيك بعض الراحة! ☕";

        // 3. جلب جميع المستخدمين الذين قاموا بربط حسابات تيليجرام (حقل الـ id ليس فارغاً)
        $users = User::whereNotNull('telegram_chat_id')->get();

        if ($users->isEmpty()) {
            $this->info('لا يوجد أي مستخدم لديه حساب تيليجرام مربوط حالياً.');
            return;
        }

        $successCount = 0;

        // 4. حلقة تكرارية: المرور على كل مستخدم وإرسال الرسالة لحسابه الخاص
        foreach ($users as $user) {
            $response = $telegram->sendMessage($user->telegram_chat_id, $message);

            if ($response->successful()) {
                $successCount++;
            }
        }

        $this->info("تم إرسال التمرين بنجاح إلى {$successCount} مستخدم(ين)! 🚀");
    }
}
