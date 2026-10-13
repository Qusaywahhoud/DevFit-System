<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use App\Models\Exercise;

class ListenTelegramBot extends Command
{
    protected $signature = 'telegram:listen';
    protected $description = 'الاستماع للرسائل والأوامر الواردة من بوت تيليجرام والرد عليها محلياً';

    public function handle(TelegramService $telegram)
    {
        $this->info('🤖 جاري الاستماع لرسائل تيليجرام محلياً... (اضغط Ctrl+C للإيقاف)');
        $offset = 0;

        while (true) {
            $updates = $telegram->getUpdates($offset);

            if ($updates && isset($updates['result'])) {
                foreach ($updates['result'] as$update) {
                    $updateId =$update['update_id'];
                    $offset =$updateId + 1;

                    if (isset($update['message']['text'])) {
                        $chatId =$update['message']['chat']['id'];
                        $text = trim($update['message']['text']);

                        $this->info("📥 تم استلام أمر: [ {$text} ] من المحادثة: {$chatId}");

                        // الرد على أمر /start أو /help
                        if ($text === '/start' || $text === '/help') {$reply = "أهلاً بك في نظام **DevFit** 🤖💪\n\n" .
                                     "نظامك المفضل لتخفيف إجهاد الجلوس الطويل أثناء البرمجة.\n" .
                                     "اختر أو اكتب الأمر `/exercise` للحصول على تمرين عشوائي فوري! 🧉✨";
                            $telegram->sendMessage($chatId,$reply);
                        } 
                        // الرد على أمر /exercise لجلب تمرين عشوائي
                        elseif ($text === '/exercise') {$exercise = Exercise::inRandomOrder()->first();
                            
                            if ($exercise) {$reply = "💪 *تمرين فوري من DevFit*:\n\n" .
                                         "📌 *العنوان:* {$exercise->title_ar}\n" .
                                         "📝 *الوصف:* {$exercise->description_ar}\n" .
                                         "⏱️ *المدة:* {$exercise->duration_seconds} ثانية";
                            } else {
                                $reply = "عذراً، لا توجد تمارين متاحة في قاعدة البيانات حالياً.";
                            }
                            
                            $telegram->sendMessage($chatId,$reply);
                        }
                    }
                }
            }

            sleep(2); // انتظار ثانيتين لتجنب الضغط على الـ API
        }
    }
}