<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Services\TelegramService;
use App\Models\User;
use App\Models\CompletedExercise;
use App\Models\Exercise;

// 1. تطبيق اللغة مباشرة هنا كـ Middleware داخلي يغلف كل الموقع (يضمن التنفيذ 100%)
Route::middleware(function (Request $request, $next) {
    $locale = $request->cookie('app_locale', 'ar');
    App::setLocale(in_array($locale, ['ar', 'en']) ? $locale : 'ar');
    return $next($request);
})->group(function () {

    // مسار رئيسي يعيد التوجيه
    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('exercises.index');

    // لوحة التحكم
    Route::get('/dashboard', function () {
        $userId = Auth::id();
        $completedExercises = $userId ? CompletedExercise::where('user_id', $userId)->latest()->take(10)->get() : collect();
        return view('dashboard', compact('completedExercises'));
    })->middleware(['auth', 'verified'])->name('dashboard');

    // مسارات الملف الشخصي
    Route::middleware(['auth'])->group(function () {
        Route::get('/export-log', function () {
            return back();
        })->name('export.log');
        Route::get('/profile', function () {
            return redirect()->route('dashboard');
        })->name('profile.edit');
        Route::patch('/profile', function () {
            return redirect()->route('dashboard');
        })->name('profile.update');
        Route::delete('/profile', function () {
            return redirect()->route('dashboard');
        })->name('profile.destroy');
    });

    // 2. مسار تبديل اللغة (يقوم بتخزين الكوكي وتوجيه آمن للوحة التحكم)
    Route::get('/lang/{locale}', function ($locale) {
        if (in_array($locale, ['en', 'ar'])) {
            $cookie = cookie('app_locale', $locale, 525600); // سنة كاملة
            return redirect()->route('dashboard')->withCookie($cookie);
        }
        return redirect()->route('dashboard');
    })->name('lang.switch');

    if (file_exists(__DIR__ . '/auth.php')) {
        require __DIR__ . '/auth.php';
    }
}); // إغلاق مجموعة اللغة

// --- مسارات تيليجرام (تبقى خارج مجموعة اللغة لكي لا تتعطل الـ Webhooks) ---

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
                $title = $exercise->title_ar ?? $exercise->title ?? 'تمرين';
                CompletedExercise::create([
                    'user_id' => $user->id,
                    'exercise_id' => $exercise->id,
                    'exercise_title' => $title,
                ]);
                $telegram->sendMessage($chatId, "أحسنت يا بطل! 🥇\nتم تسجيل تمرين *{$title}* في سجل إنجازاتك بنجاح.");
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
                $telegram->sendMessage($chatId, "أهلاً بك يا {$user->name}! 🎉\n\nتم ربط حسابك في DevFit بنجاح.");
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
            $telegram->sendMessage($chatId, "📊 إجمالي التمارين المنجزة: *{$count}*");
        } else {
            $telegram->sendMessage($chatId, "أهلاً بك في DevFit 🤖\nاستخدم /exercise أو /stats");
        }
    }

    return response()->json(['status' => 'success']);
});

Route::get('/set-telegram-webhook', function () {
    $token = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
    $url = secure_url('/telegram/webhook');
    $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", ['url' => $url]);
    return $response->json();
});

Route::get('/secret-cron-runner-devfit', function () {
    Artisan::call('schedule:run');
    return response()->json(['status' => 'success', 'message' => 'Scheduler executed!']);
});
