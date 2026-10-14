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
        // إذا تم إرسال لغة جديدة عبر الرابط (مثل ?lang=ar أو ?lang=en)
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, ['ar', 'en'])) {
                Session::put('locale', $lang);
            }
        }

        // تطبيق اللغة المخزنة في الجلسة، أو الافتراضية (العربية مثلاً)
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            App::setLocale('ar'); // اللغة الافتراضية
        }

        return $next($request);
    }
}
