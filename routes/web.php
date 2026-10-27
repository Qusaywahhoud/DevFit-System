
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ProfileController;
use App\Services\TelegramService;
use App\Models\User;
use App\Models\Exercise;
use App\Models\CompletedExercise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

// الصفحة الرئيسية

Route::get('/', [ExerciseController::class, 'index'])
    ->name('exercises.index');

Route::get('/export-log', [ExerciseController::class, 'exportCsv'])
    ->name('export.log')
    ->middleware('auth');

Route::post('/complete-exercise', [ExerciseController::class, 'completeExercise'])
    ->name('complete.exercise')
    ->middleware('auth');

// مسارات الإدارة
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->group(function () {
    Route::get('/create', [ExerciseController::class, 'create'])
        ->name('exercises.create');

    Route::post('/exercises', [ExerciseController::class, 'store'])
        ->name('exercises.store');

    Route::get('/exercises/{id}/edit', [ExerciseController::class, 'edit'])
        ->name('exercises.edit');

    Route::put('/exercises/{id}', [ExerciseController::class, 'update'])
        ->name('exercises.update');

    Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy'])
        ->name('exercises.destroy');
});

// تبديل اللغة
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        Cookie::queue('app_locale', $locale, 525600);
    }

    return redirect()->back();
})->name('lang.switch');

// تبديل المظهر
Route::get('/theme/{mode}', function ($mode) {
    if (in_array($mode, ['light', 'dark'])) {
        session(['app_theme' => $mode]);
        session()->save();
        Cookie::queue('app_theme', $mode, 525600);
    }

    return redirect()->back();
})->name('theme.switch');

// الملف الشخصي
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// لوحة التحكم
Route::get('/dashboard', function () {
    $userId = Auth::id();

    $completedExercises = $userId
        ? CompletedExercise::where('user_id', $userId)->latest()->take(10)->get()
        : collect();

    return view('dashboard', compact('completedExercises'));
})->middleware(['auth', 'verified'])->name('dashboard');

// مسارات المصادقة
require __DIR__ . '/auth.php';

// Webhook تيليجرام
Route::post('/telegram/webhook', function (Request $request, TelegramService $telegram) {
    $update = $request->all();

    // معالجة النقر على زر إكمال التمرين
    if (isset($update['callback_query'])) {
        $callback = $update['callback_query'];
        $chatId = $callback['message']['chat']['id'];
        $data = $callback['data'] ?? '';

        $user = User::where('telegram_chat_id', $chatId)->first();

        if ($user && str_starts_with($data, 'complete_')) {
            $exerciseId = (int) str_replace('complete_', '', $data);
            $exercise = Exercise::find($exerciseId);

            if ($exercise) {
                $title = $exercise->title_ar
                    ?: ($exercise->getRawOriginal('title') ?: 'تمرين');

                CompletedExercise::create([
                    'user_id' => $user->id,
                    'exercise_id' => $exercise->id,
                    'exercise_title' => $title,
                ]);

                $telegram->sendMessage(
                    $chatId,
                    "أحسنت يا بطل! 🥇\nتم تسجيل تمرين {$title} في سجل إنجازاتك."
                );
            }
        }

        return response()->json(['status' => 'success']);
    }

    // تجاهل أي تحديث ليس رسالة نصية
    if (!isset($update['message']['text'])) {
        return response()->json(['status' => 'success']);
    }

    $chatId = $update['message']['chat']['id'];
    $text = trim($update['message']['text']);

    // ربط حساب المستخدم عبر /start
    if (str_starts_with($text, '/start ')) {
        $userId = trim(substr($text, 7));
        $user = User::find($userId);

        if ($user) {
            $user->telegram_chat_id = $chatId;
            $user->save();

            $telegram->sendMessage(
                $chatId,
                "أهلاً بك يا {$user->name}! 🎉\n\nتم ربط حسابك في DevFit بنجاح."
            );
        } else {
            $telegram->sendMessage(
                $chatId,
                'لم يتم العثور على الحساب. تأكد من رابط الربط.'
            );
        }

        return response()->json(['status' => 'success']);
    }

    // التحقق من ربط الحساب
    $user = User::where('telegram_chat_id', $chatId)->first();

    if (!$user) {
        $telegram->sendMessage(
            $chatId,
            'مرحباً! لربط حسابك، يرجى استخدام زر تيليجرام من لوحة تحكم DevFit.'
        );

        return response()->json(['status' => 'success']);
    }

    // إحصائيات الإنجاز
    if ($text === '/stats') {
        $count = CompletedExercise::where('user_id', $user->id)->count();

        $telegram->sendMessage(
            $chatId,
            "📊 إجمالي التمارين المنجزة: {$count}"
        );

        return response()->json(['status' => 'success']);
    }

    // إرسال تمرين واحد فقط عند طلب التمارين
    if (in_array(strtolower(explode('@', $text, 2)[0]), ['/exercise', '/exercises'])) {
        $exercise = Exercise::inRandomOrder()->first();

        if (!$exercise) {
            $telegram->sendMessage($chatId, 'لا توجد تمارين متاحة حالياً. 😅');
            return response()->json(['status' => 'success']);
        }

        $title = $exercise->title_ar
            ?: ($exercise->getRawOriginal('title') ?: 'تمرين');

        $description = $exercise->description_ar
            ?: ($exercise->getRawOriginal('description') ?: 'نفّذ التمرين بهدوء وبدون ألم.');

        $caption = "💪 تمرينك لهذا الطلب\n\n🏋️ {$title}\n\n{$description}";

        $keyboard = [
            'inline_keyboard' => [[
                [
                    'text' => '✅ إكمال التمرين',
                    'callback_data' => 'complete_' . $exercise->id,
                ],
            ]],
        ];

        $token = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
        $rawPhoto = trim((string) $exercise->image_path);
        $localPath = null;
        $photoUrl = null;

        if ($rawPhoto !== '') {
            if (preg_match('#^https?://#i', $rawPhoto)) {
                $photoUrl = $rawPhoto;
            } else {
                $cleanPath = ltrim(preg_replace('#^public/#i', '', $rawPhoto), '/');
                $cleanPath = ltrim(preg_replace('#^storage/#i', '', $cleanPath), '/');

                if (is_file(public_path($cleanPath))) {
                    $localPath = public_path($cleanPath);
                    $photoUrl = asset($cleanPath);
                } elseif (is_file(public_path('storage/' . $cleanPath))) {
                    $localPath = public_path('storage/' . $cleanPath);
                    $photoUrl = asset('storage/' . $cleanPath);
                } else {
                    $photoUrl = asset($cleanPath);
                }
            }
        }

        $sent = false;

        // 1) رفع ملف الصورة المحلي مباشرة من داخل الحاوية إلى تيليجرام
        if ($localPath) {
            try {
                $response = Http::connectTimeout(5)
                    ->timeout(15)
                    ->attach('photo', file_get_contents($localPath), basename($localPath))
                    ->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                        'chat_id' => $chatId,
                        'caption' => $caption,
                        'reply_markup' => json_encode($keyboard, JSON_UNESCAPED_UNICODE),
                    ]);

                $sent = $response->successful() && $response->json('ok') === true;

                Log::info('Telegram exercise local photo upload result', [
                    'http_status' => $response->status(),
                    'telegram_ok' => $response->json('ok'),
                    'telegram_error' => $response->json('description'),
                    'exercise_id' => $exercise->id,
                    'local_path' => $localPath,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Telegram exercise local photo upload exception', [
                    'exercise_id' => $exercise->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 2) خطة احتياطية: الإرسال عبر الرابط المباشر بدون storage/
        if (!$sent && $photoUrl) {
            try {
                $response = Http::connectTimeout(5)
                    ->timeout(10)
                    ->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                        'chat_id' => $chatId,
                        'photo' => $photoUrl,
                        'caption' => $caption,
                        'reply_markup' => json_encode($keyboard, JSON_UNESCAPED_UNICODE),
                    ]);

                $sent = $response->successful() && $response->json('ok') === true;

                Log::info('Telegram exercise URL photo result', [
                    'http_status' => $response->status(),
                    'telegram_ok' => $response->json('ok'),
                    'telegram_error' => $response->json('description'),
                    'exercise_id' => $exercise->id,
                    'photo_url' => $photoUrl,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Telegram exercise URL photo exception', [
                    'exercise_id' => $exercise->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 3) خطة الطوارئ: إرسال التمرين كنص إذا لم توجد صورة أو تعذر الإرسال
        if (!$sent) {
            try {
                $response = Http::connectTimeout(5)
                    ->timeout(8)
                    ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $caption . ($rawPhoto !== '' ? "\n\n📷 تعذر إرسال الصورة." : "\n\n📷 لا توجد صورة مسجلة لهذا التمرين."),
                        'reply_markup' => json_encode($keyboard, JSON_UNESCAPED_UNICODE),
                    ]);

                Log::info('Telegram exercise text fallback result', [
                    'http_status' => $response->status(),
                    'telegram_ok' => $response->json('ok'),
                    'telegram_error' => $response->json('description'),
                    'exercise_id' => $exercise->id,
                ]);
            } catch (\Throwable $e) {
                Log::error('Telegram exercise text fallback exception', [
                    'exercise_id' => $exercise->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }


    // الأمر غير معروف
    $telegram->sendMessage(
        $chatId,
        "أهلاً بك في DevFit 🤖\n\n"
            . "🏋️ /exercises - إرسال تمرين واحد\n"
            . "📊 /stats - إحصائياتك"
    );

    return response()->json(['status' => 'success']);
});

// ضبط Webhook تيليجرام
Route::get('/set-telegram-webhook', function () {
    $token = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
    $url = secure_url('/telegram/webhook');

    $response = Http::post(
        "https://api.telegram.org/bot{$token}/setWebhook",
        ['url' => $url]
    );

    return response()->json($response->json(), $response->successful() ? 200 : 500);
});

// إرسال التذكيرات
Route::get('/cron/send-reminders', function (Request $request, TelegramService $telegram) {
    if ($request->query('key') !== env('TELEGRAM_CRON_SECRET')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $users = User::whereNotNull('telegram_chat_id')->get();
    $count = 0;

    foreach ($users as $user) {
        try {
            $telegram->sendMessage(
                $user->telegram_chat_id,
                "🌅 صباح النشاط والحيوية يا {$user->name}!\n\n"
                    . "لا تنس تمرينك اليومي. أرسل /exercises للحصول على تمرين واحد اليوم! 💪🔥"
            );

            $count++;
        } catch (\Exception $e) {
            Log::error(
                "Failed to send reminder to user {$user->id}: " . $e->getMessage()
            );
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => "تم إرسال التذكيرات إلى {$count} مستخدم بنجاح.",
    ]);
});
