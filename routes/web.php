<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ProfileController;
use App\Services\TelegramService;
use App\Models\Exercise;

// صفحة التمارين الرئيسية (متاحة للجميع)
Route::get('/', [ExerciseController::class, 'index']);
Route::get('/export-log', [\App\Http\Controllers\ExerciseController::class, 'exportCsv'])->name('export.log')->middleware('auth');
// حفظ التمرين المنجز (للمستخدمين المسجلين)
Route::post('/complete-exercise', [ExerciseController::class, 'completeExercise'])->name('complete.exercise')->middleware('auth');

// === مسارات الإدارة والتحكم الكامل بالتمارين (محمية بـ IsAdmin) ===
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->group(function () {
    Route::get('/create', [ExerciseController::class, 'create']);
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::get('/exercises/{id}/edit', [ExerciseController::class, 'edit'])->name('exercises.edit');
    Route::put('/exercises/{id}', [ExerciseController::class, 'update'])->name('exercises.update');
    Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy'])->name('exercises.destroy');
});
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');
// مسارات الملف الشخصي (Profile)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// لوحة التحكم الافتراضية لـ Breeze
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';



Route::get('/test-telegram', function (TelegramService $telegram) {
    $exercise = Exercise::inRandomOrder()->first();

    if (!$exercise) {
        return "لا توجد تمارين في القاعدة حالياً!";
    }

    $message = "💪 *تمرين جديد من نظام DevFit*:\n\n" .
        "📌 *العنوان:* {$exercise->title_ar}\n" .
        "📝 *الوصف:* {$exercise->description_ar}\n" .
        "⏱️ *المدة:* {$exercise->duration_seconds} ثانية";

    // ضع الـ Chat ID الذي حصلت عليه بدل الأرقام بالأسفل
    $chatId = '8116578765';

    $response = $telegram->sendMessage($chatId, $message);

    if ($response->successful()) {
        return "تم إرسال التمرين إلى تيليجرام بنجاح! 🚀";
    }

    return "فشل الإرسال، تحقق من الـ Chat ID.";
});
