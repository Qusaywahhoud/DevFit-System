<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use App\Services\TelegramService;
use App\Models\User;
use App\Models\CompletedExercise;
use App\Models\Exercise;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $completedExercises = auth()->check() ? CompletedExercise::where('user_id', auth()->id())->latest()->take(10)->get() : collect();
    return view('dashboard', ['completedExercises' => $completedExercises]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/export-log', function () {
    return back();
})->middleware(['auth'])->name('export.log');

Route::get('/profile', function () {
    return redirect()->route('dashboard');
})->middleware(['auth'])->name('profile.edit');

Route::patch('/profile', function () {
    return redirect()->route('dashboard');
})->middleware(['auth'])->name('profile.update');

Route::delete('/profile', function () {
    return redirect()->route('dashboard');
})->middleware(['auth'])->name('profile.destroy');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

if (file_exists(__DIR__ . '/auth.php')) {
    require __DIR__ . '/auth.php';
}

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
    $token = env('TELEGRAM_BOT_TOKEN');
    $url = secure_url('/telegram/webhook');
    $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", ['url' => $url]);
    return $response->json();
});

Route::get('/secret-cron-runner-devfit', function () {
    Artisan::call('schedule:run');
    return response()->json(['status' => 'success', 'message' => 'Scheduler executed!']);
});
