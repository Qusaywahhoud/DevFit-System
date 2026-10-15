<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use App\Services\TelegramService;
use App\Models\User;

// ===== مسارات بوت تيليجرام =====
Route::post('/telegram/webhook', function (\Illuminate\Http\Request $request, \App\Services\TelegramService $telegram) {
    $update = $request->all();

    if (isset($update['callback_query'])) {
        $chatId = $update['callback_query']['message']['chat']['id'];
        $data = $update['callback_query']['data'];
        $user = \App\Models\User::where('telegram_chat_id', $chatId)->first();

        if ($user && str_starts_with($data, 'complete_')) {
            $exercise = \App\Models\Exercise::find(str_replace('complete_', '', $data));
            if ($exercise) {
                \App\Models\CompletedExercise::create([
                    'user_id' => $user->id,
                    'exercise_id' => $exercise->id,
                    'exercise_title' => $exercise->title_ar ?? 'تمرين',
                ]);
                $telegram->sendMessage($chatId, "أحسنت يا بطل! 🥇\nتم تسجيل تمرين *{$exercise->title_ar}* في سجل إنجازاتك بنجاح.");
            }
        }
        return response()->json(['status' => 'success']);
    }

    if (isset($update['message']['text'])) {
        $chatId = $update['message']['chat']['id'];
        $text = $update['message']['text'];

        if (str_starts_with($text, '/start ')) {
            $user = \App\Models\User::find(str_replace('/start ', '', $text));
            if ($user) {
                $user->telegram_chat_id = $chatId;
                $user->save();
                $telegram->sendMessage($chatId, "أهلاً بك يا {$user->name}! 🎉\n\nتم ربط حسابك في DevFit بنجاح. أنا الآن مدربك الشخصي هنا! 💪");
            }
            return response()->json(['status' => 'success']);
        }

        $user = \App\Models\User::where('telegram_chat_id', $chatId)->first();
        if (!$user) {
            $telegram->sendMessage($chatId, "مرحباً! لربط حسابك، يرجى الضغط على زر تيليجرام من داخل لوحة تحكم موقع DevFit.");
            return response()->json(['status' => 'success']);
        }

        if ($text === '/stats') {
            $count = \App\Models\CompletedExercise::where('user_id', $user->id)->count();
            $telegram->sendMessage($chatId, "📊 *إحصائياتك يا {$user->name}:*\n\n🏆 إجمالي التمارين المنجزة: *{$count}*\nاستمر يا بطل ولا تتوقف! 🔥");
        } elseif ($text === '/exercise' || str_contains($text, 'جاهز') || str_contains($text, 'نشيط')) {
            $exercise = \App\Models\Exercise::inRandomOrder()->first();
            if ($exercise) {
                $reply = "🔥 *إليك هذا التمرين:*\n\n📌 *{$exercise->title_ar}*\n📝 {$exercise->description_ar}\n⏱️ *المدة:* {$exercise->duration_seconds} ثانية";
                $keyboard = ['inline_keyboard' => [[['text' => '✅ أتممت التمرين', 'callback_data' => 'complete_' . $exercise->id]]]];
                $telegram->sendMessage($chatId, $reply, json_encode($keyboard));
            }
        } elseif (str_contains($text, 'تعبت') || str_contains($text, 'مرهق') || str_contains($text, 'مصدع')) {
            $exercise = \App\Models\Exercise::orderBy('duration_seconds', 'asc')->first();
            if ($exercise) {
                $reply = "خذ نفساً عميقاً 🧘‍♂️ صحتك أولاً.\n\n📌 *{$exercise->title_ar}*\n📝 {$exercise->description_ar}";
                $keyboard = ['inline_keyboard' => [[['text' => '✅ أتممت بسلام', 'callback_data' => 'complete_' . $exercise->id]]]];
                $telegram->sendMessage($chatId, $reply, json_encode($keyboard));
            }
        } else {
            $telegram->sendMessage($chatId, "أهلاً بك في DevFit 🤖\n\n🏋️‍♂️ /exercise - اطلب تمريناً\n📊 /stats - إحصائياتك\n\n💡 اكتب: جاهز، تعبت، مرهق");
        }
    }

    return response()->json(['status' => 'success']);
});

Route::get('/set-telegram-webhook', function () {
    $token = env('TELEGRAM_BOT_TOKEN');
    $response = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/setWebhook", [
        'url' => secure_url('/telegram/webhook'),
    ]);
    return $response->json();
});

Route::get('/secret-cron-runner-devfit', function () {
    \Illuminate\Support\Facades\Artisan::call('schedule:run');
    return response()->json(['status' => 'success', 'message' => 'Scheduler executed!']);
});
