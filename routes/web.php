use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\TelegramService;
use App\Models\User;

// 1. مسار استقبال بيانات المستخدم من تيليجرام (Webhook)
Route::post('/telegram/webhook', function (Request $request, TelegramService $telegram) {
$update = $request->all();

if (isset($update['message']['text'])) {
$chatId = $update['message']['chat']['id'];
$text = $update['message']['text'];

// إذا ضغط المستخدم على زر الربط في موقعك
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

// 2. مسار تفعيل الـ Webhook (سنزوره مرة واحدة فقط لربط الموقع بتيليجرام)
Route::get('/set-telegram-webhook', function () {
$token = env('TELEGRAM_BOT_TOKEN');
// استخدمنا secure_url لضمان أن الرابط يبدأ بـ https كما يفرض تيليجرام
$url = secure_url('/telegram/webhook');

$response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", [
'url' => $url
]);

return $response->json();
});

use Illuminate\Support\Facades\Artisan;

// مسار سري لتشغيل المجدول (لا تعطِ هذا الرابط لأحد)
Route::get('/secret-cron-runner-devfit', function () {
Artisan::call('schedule:run');
return response()->json(['status' => 'success', 'message' => 'Scheduler executed!']);
});