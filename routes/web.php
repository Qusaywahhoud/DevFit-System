<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use App\Services\TelegramService;
use App\Models\User;

// ==========================================
// 1. مسارات موقعك الأساسية
// (الصق هنا الكود الذي نسخته من نسخة 2 days ago)
// ==========================================
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

if (file_exists(__DIR__ . '/auth.php')) {
    require __DIR__ . '/auth.php';
}

// ==========================================
// 2. مسارات البوت القديمة (كما طلبتها أنت)
// ==========================================
Route::post('/telegram/webhook', function (Request $request, TelegramService $telegram) {
    $update = $request->all();

    if (isset($update['message']['text'])) {
        $chatId = $update['message']['chat']['id'];
        $text = $update['message']['text'];

        if (str_starts_with($text, '/start ')) {
            $userId = str_replace('/start ', '', $text);
            $user = User::find($userId);

            if ($user) {
                $user->telegram_chat_id = $chatId;
                $user->save();
                $reply = "أهلاً بك يا {$user->name}! 🎉\n\nتم ربط حسابك في DevFit بنجاح. ستصلك التمارين هنا.";
            } else {
                $reply = "عذراً، لم أتمكن من العثور على حسابك.";
            }
        } else {
            $reply = "مرحباً! لربط حسابك، يرجى الضغط على زر تيليجرام من داخل لوحة تحكم موقع DevFit.";
        }
        $telegram->sendMessage($chatId, $reply);
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
