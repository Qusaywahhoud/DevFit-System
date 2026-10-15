<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ProfileController;
use App\Services\TelegramService;
use App\Models\User;
use App\Models\Exercise;
use App\Models\CompletedExercise;

// --- مسارات الموقع الأساسية ---
Route::get('/', [ExerciseController::class, 'index'])->name('exercises.index');
Route::get('/export-log', [ExerciseController::class, 'exportCsv'])->name('export.log')->middleware('auth');
Route::post('/complete-exercise', [ExerciseController::class, 'completeExercise'])->name('complete.exercise')->middleware('auth');

Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->group(function () {
    Route::get('/create', [ExerciseController::class, 'create']);
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::get('/exercises/{id}/edit', [ExerciseController::class, 'edit'])->name('exercises.edit');
    Route::put('/exercises/{id}', [ExerciseController::class, 'update'])->name('exercises.update');
    Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy'])->name('exercises.destroy');
});

// --- مسار تبديل اللغة (Cookie Based) ---
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        \Illuminate\Support\Facades\Cookie::queue('app_locale', $locale, 525600);
    }
    return redirect()->back();
})->name('lang.switch');

// --- مسار تبديل الثيم (مماثل لتبديل اللغة تماماً) ---
Route::get('/theme/{mode}', function ($mode) {
    if (in_array($mode, ['light', 'dark'])) {
        \Illuminate\Support\Facades\Cookie::queue('app_theme', $mode, 525600);
    }
    return redirect()->back();
})->name('theme.switch');

// --- مسارات الملف الشخصي ولوحة التحكم ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', function () {
    $userId = \Illuminate\Support\Facades\Auth::id();
    $completedExercises = $userId ? CompletedExercise::where('user_id', $userId)->latest()->take(10)->get() : collect();
    return view('dashboard', compact('completedExercises'));
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';

// --- مسارات تيليجرام (المحدثة مع الأزرار التفاعلية) ---
Route::post('/telegram/webhook', function (Request $request, TelegramService $telegram) {
    $update = $request->all();

    // 1. معالجة النقرات على الأزرار (Callback Queries)
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

    // 2. معالجة الرسائل النصية والأوامر
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

            // دعم الكلمتين (مفرد وجمع) مع عرض الأزرار
        } elseif (in_array($text, ['/exercise', '/exercises'])) {
            $exercises = Exercise::take(10)->get();

            if ($exercises->isEmpty()) {
                $telegram->sendMessage($chatId, "لا توجد تمارين متاحة حالياً. 😅");
            } else {
                $keyboard = [];
                foreach ($exercises as $exercise) {
                    $title = $exercise->title_ar ?? $exercise->title ?? 'تمرين';
                    $keyboard[] = [
                        ['text' => "✅ إكمال: " . $title, 'callback_data' => 'complete_' . $exercise->id]
                    ];
                }

                $token = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
                \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "💪 تفضل قائمة التمارين المتاحة:\n(اضغط على الزر لتسجيل إنجازك فوراً)",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => $keyboard
                    ])
                ]);
            }
            // رسالة الترحيب الجديدة المحدثة
        } else {
            $telegram->sendMessage($chatId, "أهلاً بك في DevFit 🤖\n\nقائمة الأوامر المتاحة:\n🏋️‍♂️ /exercises - عرض التمارين\n📊 /stats - إحصائياتك");
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

use Illuminate\Support\Facades\Log;

Route::get('/cron/send-reminders', function (Request $request, TelegramService $telegram) {
    // 🛡️ حماية المسار برمز سري لمنع أي شخص من إرسال الرسائل عشوائياً
    if ($request->query('key') !== 'DevFitSecretCron2026') {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // جلب جميع المستخدمين الذين لديهم معرف تيليجرام مرتبط
    $users = User::whereNotNull('telegram_chat_id')->get();
    $count = 0;

    foreach ($users as $user) {
        try {
            $telegram->sendMessage(
                $user->telegram_chat_id,
                "🌅 صباح النشاط والحيوية يا {$user->name}!\n\nلا تنس تمرينك اليومي لتبقى في قمة لياقتك. أرسل الأمر /exercises لتسجيل تمرينك اليوم والتقدم نحو أهدافك! 💪🔥"
            );
            $count++;
        } catch (\Exception $e) {

            Log::error("Failed to send reminder to user {$user->id}: " . $e->getMessage());
        }
    }
    return response()->json([
        'status' => 'success',
        'message' => "تم إرسال التذكيرات إلى {$count} مستخدم بنجاح."
    ]);
});
