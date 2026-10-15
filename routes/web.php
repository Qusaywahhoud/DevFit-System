<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use App\Services\TelegramService;
use App\Models\User;
use App\Models\CompletedExercise;
use App\Models\Exercise;

/*
|--------------------------------------------------------------------------
| 1. مسارات الموقع الأساسية
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// حل جذري نهائي: تعريف مسار profile.edit مباشرة ليتجاهل أي خطأ في القائمة القديمة
Route::get('/profile', function () {
    return redirect()->route('dashboard');
})->middleware(['auth'])->name('profile.edit');

Route::patch('/profile', function () {
    return redirect()->route('dashboard');
})->middleware(['auth']);

Route::delete('/profile', function () {
    return redirect()->route('dashboard');
})->middleware(['auth']);

// مسار تغيير اللغة
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

if (file_exists(__DIR__ . '/auth.php')) {
    require __DIR__ . '/auth.php';
}

/*
|--------------------------------------------------------------------------
| 2. مسارات بوت تيليجرام
|--------------------------------------------------------------------------
*/

Route::post('/telegram/webhook', function (Request $request, TelegramService $telegram) {
    $update = $request->all();

    if (isset($update['callback_query'])) {
        $chatId = $update['callback_query']['message']['chat']['id'];
        $data = $update['callback_query']['data'];

        $user = User::where('telegram_chat_id', $chatId)->first();

        if ($user && str_starts_with($data, 'complete_')) {
            $exerciseId = str_replace('complete_', '', $data);
            $exercise = Exercise::find($exerciseId);

            if ($exercise) {
                CompletedExercise::create([
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
            $userId = str_replace('/start ', '', $text);
            $user = User::find($userId);

            if ($user) {
                $user->telegram_chat_id = $chatId;
                $user->save();
                $telegram->sendMessage($chatId, "أهلاً بك يا {$user->name}! 🎉\n\nتم ربط حسابك في DevFit بنجاح. أنا الآن مدربك الشخصي هنا! 💪");
            }
            return response()->json(['status' => 'success']);
        }

        $user = User::where('telegram_chat_id', $chatId)->first();
        if (!$user) {
            $telegram->sendMessage($chatId, "مرحباً! لربط حسابك، يرجى الضغط على زر تيليجرام من داخل لوحة تحكم موقع DevFit.");
            return response()->json(['status' => 'success']);
        }

        if ($text === '/stats') {
            $count = CompletedExercise::where('user_id', $user->id)->count();
            $telegram->sendMessage($chatId, "📊 *إحصائياتك يا {$user->name}:*\n\n🏆 إجمالي التمارين المنجزة: *{$count}*\nاستمر يا بطل ولا تتوقف! 🔥");
        } elseif ($text === '/exercise' || str_contains($text, 'جاهز') || str_contains($text, 'نشيط')) {
            $exercise = Exercise::inRandomOrder()->first();
            if ($exercise) {
                $reply = "🔥 *أعجبني حماسك! إليك هذا التمرين:* \n\n📌 *{$exercise->title_ar}*\n📝 {$exercise->description_ar}\n⏱️ *المدة:* {$exercise->duration_seconds} ثانية";

                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '✅ أتممت التمرين', 'callback_data' => 'complete_' . $exercise->id]]
                    ]
                ];
                $telegram->sendMessage($chatId, $reply, json_encode($keyboard));
            }
        } elseif (str_contains($text, 'تعبت') || str_contains($text, 'مرهق') || str_contains($text, 'مصدع')) {
            $exercise = Exercise::orderBy('duration_seconds', 'asc')->first();
            if ($exercise) {
                $reply = "خذ نفساً عميقاً 🧘‍♂️ صحتك أولاً. \n*إليك تمرين استرخاء خفيف جداً لا يتطلب مجهوداً:*\n\n📌 *{$exercise->title_ar}*\n📝 {$exercise->description_ar}";

                $keyboard = [
                    'inline_keyboard' => [
                        [['text' => '✅ أتممت بسلام', 'callback_data' => 'complete_' . $exercise->id]]
                    ]
                ];
                $telegram->sendMessage($chatId, $reply, json_encode($keyboard));
            }
        } else {
            $reply = "أهلاً بك في DevFit 🤖\n\nإليك الأوامر المتاحة:\n🏋️‍♂️ /exercise - اطلب تمريناً الآن\n📊 /stats - عرض إحصائياتك المنجزة\n\n💡 *نصيحة:* يمكنك ببساطة كتابة حالتك المزاجية (مثلاً: جاهز، تعبت، مرهق) وسأختار لك ما يناسبك!";
            $telegram->sendMessage($chatId, $reply);
        }
    }

    return response()->json(['status' => 'success']);
});

Route::get('/set-telegram-webhook', function () {
    $token = env('TELEGRAM_BOT_TOKEN');
    $url = secure_url('/telegram/webhook');
    $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", ['url' => $url]);
    return $response->json();
});

Route::get('/secret-cron-runner-devfit', function () {
    Artisan::call('schedule:run');
    return response()->json(['status' => 'success', 'message' => 'Scheduler executed!']);
});
