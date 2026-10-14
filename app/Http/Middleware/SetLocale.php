<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // 1. التحقق إذا تم إرسال اللغة عبر Query Parameter مثل ?lang=ar
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, ['ar', 'en'])) {
                Session::put('locale', $lang);
            }
        }

        // 2. تطبيق اللغة المخزنة في الجلسة أو الاعتماد على العربية كافتراضية
        $locale = Session::get('locale', 'ar');

        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
