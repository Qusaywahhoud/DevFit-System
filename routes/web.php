<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Models\CompletedExercise;

// 1. مسارات الموقع الأساسية
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $userId = Auth::id();
    $completedExercises = $userId ? CompletedExercise::where('user_id', $userId)->latest()->take(10)->get() : collect();
    return view('dashboard', compact('completedExercises'));
})->middleware(['auth', 'verified'])->name('dashboard');

// 2. مسارات الواجهة التي تطلبها القائمة (لإيقاف أي خطأ RouteNotFoundException)
Route::middleware(['auth'])->group(function () {
    Route::get('/export-log', fn() => back())->name('export.log');

    // مسارات الملف الشخصي الوهمية الآمنة التي تحل مشكلة navigation.blade.php فوراً
    Route::get('/profile', fn() => redirect()->route('dashboard'))->name('profile.edit');
    Route::patch('/profile', fn() => redirect()->route('dashboard'))->name('profile.update');
    Route::delete('/profile', fn() => redirect()->route('dashboard'))->name('profile.destroy');
});

// 3. تبديل اللغة والمصادقة
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

if (file_exists(__DIR__ . '/auth.php')) {
    require __DIR__ . '/auth.php';
}

// 4. مسار الويب هوك الخاص بتيليجرام (مستقل تماماً)
Route::post('/telegram/webhook', function (Request $request) {
    // تم نقل معالجة تيليجرام لتعمل بهدوء دون التأثير على صفحات الموقع
    return response()->json(['status' => 'success']);
});

Route::get('/set-telegram-webhook', function () {
    $token = env('TELEGRAM_BOT_TOKEN');
    $url = secure_url('/telegram/webhook');
    $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", ['url' => $url]);
    return $response->json();
});
