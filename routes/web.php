<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ProfileController;
use App\Services\TelegramService;
use App\Models\User;
use App\Models\CompletedExercise;
use App\Models\Exercise;

// إقلاع الموقع مباشرة على صفحة التمارين الرئيسية
Route::get('/', [ExerciseController::class, 'index'])->name('exercises.index');

// لوحة التحكم والإحصائيات
Route::get('/dashboard', function () {
    $userId = Auth::id();
    $completedExercises = $userId ? CompletedExercise::where('user_id', $userId)->latest()->take(10)->get() : collect();
    return view('dashboard', compact('completedExercises'));
})->middleware(['auth', 'verified'])->name('dashboard');

// مسارات التمارين
Route::post('/complete-exercise', [ExerciseController::class, 'completeExercise'])->name('exercises.complete');
Route::get('/export-log', [ExerciseController::class, 'exportCsv'])->middleware(['auth'])->name('export.log');

Route::middleware(['auth'])->group(function () {
    Route::get('/create', [ExerciseController::class, 'create'])->name('exercises.create');
    Route::post('/exercises', [ExerciseController::class, 'store'])->name('exercises.store');
    Route::get('/exercises/{id}/edit', [ExerciseController::class, 'edit'])->name('exercises.edit');
    Route::put('/exercises/{id}', [ExerciseController::class, 'update'])->name('exercises.update');
    Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy'])->name('exercises.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

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

Route::get('/fix-cache-devfit', function () {
    Artisan::call('optimize:clear');
    return response()->json(['status' => 'success', 'message' => 'All caches cleared successfully!']);
});
