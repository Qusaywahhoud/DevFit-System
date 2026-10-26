<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ app()->getLocale() == 'ar' ? 'لوحة تحكم DevFit 📊' : 'DevFit Dashboard 📊' }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 dark:bg-gray-900 min-h-screen" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-2xl p-6 md:p-8 border border-gray-200 dark:border-gray-700">

                <!-- رأس اللوحة ومعلومات المستخدم -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">
                            {{ app()->getLocale() == 'ar' ? 'أهلاً بك، ' . Auth::user()->name . ' 👋' : 'Welcome Back, ' . Auth::user()->name . ' 👋' }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 mt-2 text-base">
                            {{ app()->getLocale() == 'ar' ? 'إليك ملخص أدائك وسجل تمارينك للحفاظ على صحتك.' : 'Here is a summary of your performance and exercise log.' }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ url('/') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition shadow-md inline-flex items-center gap-2">
                            <span>{{ app()->getLocale() == 'ar' ? 'العودة لصفحة التمارين' : 'Back to Exercises' }}</span>
                            <span>🏋‍♂️</span>
                        </a>
                    </div>
                </div>

                <!-- 🤖 قسم ربط تيليجرام -->
                <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-700 rounded-2xl border border-blue-100 dark:border-gray-600 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4 transition hover:shadow-md">
                    <div>
                        <h3 class="text-lg font-bold text-blue-800 dark:text-blue-300 mb-1">
                            {{ app()->getLocale() == 'ar' ? '🤖 تفعيل إشعارات تيليجرام' : '🤖 Enable Telegram Notifications' }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ app()->getLocale() == 'ar' ? 'اربط حسابك بالبوت لتستلم التمارين اليومية المخصصة لك مباشرة على تيليجرام!' : 'Link your account with our bot to receive your daily exercises directly on Telegram!' }}
                        </p>
                    </div>
                    <div>
                        @if(auth()->user()->telegram_chat_id)
                            <span class="inline-flex items-center px-4 py-2.5 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300 rounded-xl font-bold text-sm border border-green-200 dark:border-green-700">
                                {{ app()->getLocale() == 'ar' ? '✅ حسابك مربوط بنجاح' : '✅ Account Linked Successfully' }}
                            </span>
                        @else
                            <a href="https://t.me/devfit_reminder_bot?start={{ auth()->id() }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md transition">
                                <span>{{ app()->getLocale() == 'ar' ? 'اربط حسابك الآن' : 'Link Account Now' }}</span>
                                <span>🚀</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- حساب الإحصائيات برمجياً -->
                @php
                    $userId = Auth::id();
                    $completedExercises = \App\Models\CompletedExercise::where('user_id', $userId)->latest()->get();

                    $totalExercises = $completedExercises->count();

                    $thisWeek = \App\Models\CompletedExercise::where('user_id', $userId)
                                            ->where('created_at', '>=', now()->startOfWeek())
                                            ->count();

                    $uniqueDays = $completedExercises->map(function ($ex) {
                        return $ex->created_at->format('Y-m-d');
                    })->unique()->sortDesc()->values();

                    $streak = 0;
                    $expectedDate = now()->format('Y-m-d');

                    if ($uniqueDays->contains($expectedDate) || $uniqueDays->contains(now()->subDay()->format('Y-m-d'))) {
                        $currentCheck = $uniqueDays->contains($expectedDate) ? now() : now()->subDay();
                        foreach ($uniqueDays as $day) {
                            if ($day === $currentCheck->format('Y-m-d')) {
                                $streak++;
                                $currentCheck->subDay();
                            } else {
                                break;
                            }
                        }
                    }

                    $dailyGoal = 3;
                    $todayExercises = \App\Models\CompletedExercise::where('user_id', $userId)
                                            ->whereDate('created_at', now()->toDateString())
                                            ->count();

                    $displayedTodayExercises = min($todayExercises, $dailyGoal);
                    $progressPercentage = $todayExercises > 0 ? min(($todayExercises / $dailyGoal) * 100, 100) : 0;

                    $startDate = now()->subDays(119);
                    $endDate = now();

                    $dailyActivity = \App\Models\CompletedExercise::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                        ->where('user_id', $userId)
                                        ->where('created_at', '>=', $startDate->startOfDay())
                                        ->groupBy('date')
                                        ->pluck('count', 'date')->toArray();

                    $activityGrid = [];
                    $currentDate = clone $startDate;

                    while ($currentDate <= $endDate) {
                        $dateStr = $currentDate->format('Y-m-d');
                        $count = $dailyActivity[$dateStr] ?? 0;

                        $level = 0;
                        if ($count == 1) $level = 1;
                        elseif ($count == 2) $level = 2;
                        elseif ($count >= 3) $level = 3;

                        $activityGrid[] = [
                            'date' => $dateStr,
                            'count' => $count,
                            'level' => $level
                        ];

                        $currentDate->addDay();
                    }
                @endphp

                <!-- شريط الهدف اليومي -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-8 transition hover:shadow-md">
                    <div class="flex justify-between items-end mb-3">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                                {{ app()->getLocale() == 'ar' ? 'الهدف اليومي 🎯' : 'Daily Goal 🎯' }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if(app()->getLocale() == 'ar')
                                    أنجزت <span class="font-bold text-blue-600 dark:text-blue-400">{{ $displayedTodayExercises }}</span> من أصل {{ $dailyGoal }} تمارين لليوم
                                @else
                                    Completed <span class="font-bold text-blue-600 dark:text-blue-400">{{ $displayedTodayExercises }}</span> out of {{ $dailyGoal }} exercises for today
                                @endif
                            </p>
                        </div>
                        <div class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">
                            {{ round($progressPercentage) }}%
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-4 rounded-full transition-all duration-1000 ease-out" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                    @if($todayExercises >= $dailyGoal)
                        <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-xl text-sm font-bold text-center flex items-center justify-center gap-2">
                            <span>🎉</span>
                            {{ app()->getLocale() == 'ar' ? 'مذهل يا وحش! لقد حققت هدفك اليومي بالكامل، استمر!' : 'Amazing! You have fully achieved your daily goal, keep it up!' }}
                        </div>
                    @endif
                </div>

                <!-- قسم الإحصائيات -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-700 p-6 rounded-2xl border border-blue-200 dark:border-gray-600 shadow-sm flex items-center gap-4 transition hover:shadow-md">
                        <div class="text-4xl">🏆</div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-1">{{ app()->getLocale() == 'ar' ? 'إجمالي التمارين' : 'Total Exercises' }}</p>
                            <p class="text-2xl font-extrabold text-blue-700 dark:text-blue-400">{{ $totalExercises }}</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-700 p-6 rounded-2xl border border-blue-200 dark:border-gray-600 shadow-sm flex items-center gap-4 transition hover:shadow-md">
                        <div class="text-4xl">📅</div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-1">{{ app()->getLocale() == 'ar' ? 'هذا الأسبوع' : 'This Week' }}</p>
                            <p class="text-2xl font-extrabold text-blue-700 dark:text-blue-400">{{ $thisWeek }}</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-700 p-6 rounded-2xl border border-blue-200 dark:border-gray-600 shadow-sm flex items-center gap-4 transition hover:shadow-md">
                        <div class="text-4xl">🎯</div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-1">{{ app()->getLocale() == 'ar' ? 'أيام الالتزام' : 'Active Days' }}</p>
                            <p class="text-2xl font-extrabold text-blue-700 dark:text-blue-400">{{ $uniqueDays->count() }}</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-700 p-6 rounded-2xl border border-blue-200 dark:border-gray-600 shadow-sm flex items-center gap-4 transition hover:shadow-md">
                        <div class="text-4xl">🔥</div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-1">{{ app()->getLocale() == 'ar' ? 'السلسلة المتتالية' : 'Streak' }}</p>
                            <p class="text-2xl font-extrabold text-blue-700 dark:text-blue-400">{{ $streak }} {{ app()->getLocale() == 'ar' ? 'أيام' : 'days' }}</p>
                        </div>
                    </div>
                </div>

                <!-- قسم الأوسمة 🏅 -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-10 transition hover:shadow-md">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                        {{ app()->getLocale() == 'ar' ? 'أوسمة الإنجاز 🏅' : 'Achievement Badges 🏅' }}
                    </h3>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-3 p-3 rounded-xl border {{ $totalExercises >= 1 ? 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-400 opacity-100' : 'bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 opacity-50 grayscale' }}">
                            <div class="text-4xl">🌟</div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                    {{ app()->getLocale() == 'ar' ? 'البداية القوية' : 'Strong Start' }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ app()->getLocale() == 'ar' ? 'إنجاز أول تمرين' : 'Complete first exercise' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl border {{ $streak >= 3 ? 'bg-orange-50 dark:bg-orange-900/30 border-orange-400 opacity-100' : 'bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 opacity-50 grayscale' }}">
                            <div class="text-4xl">⚡</div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                    {{ app()->getLocale() == 'ar' ? 'بطل الاستمرارية' : 'Streak Champion' }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ app()->getLocale() == 'ar' ? 'سلسلة 3 أيام متتالية' : '3-day streak' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl border {{ $totalExercises >= 10 ? 'bg-purple-50 dark:bg-purple-900/30 border-purple-400 opacity-100' : 'bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600 opacity-50 grayscale' }}">
                            <div class="text-4xl">👑</div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                    {{ app()->getLocale() == 'ar' ? 'أسطورة DevFit' : 'DevFit Legend' }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ app()->getLocale() == 'ar' ? 'إنجاز 10 تمارين' : 'Complete 10 exercises' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- لوحة نشاط التمارين -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 mb-10 transition hover:shadow-md overflow-hidden">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 text-right">
                        {{ app()->getLocale() == 'ar' ? 'نشاط التمارين (آخر 4 أشهر) 🟩' : 'Exercise Activity (Last 4 Months) 🟩' }}
                    </h3>

                    <div class="overflow-x-auto pb-2 flex justify-start" dir="ltr">
                        <div class="inline-block">
                            <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500 mb-1 pr-6" style="width: 100%;">
                                @php
                                    $monthsFlow = [];
                                    for ($i = 3; $i >= 0; $i--) {
                                        $monthsFlow[] = now()->subMonths($i)->format('M');
                                    }
                                @endphp
                                @foreach($monthsFlow as $m)
                                    <span>{{ $m }}</span>
                                @endforeach
                            </div>

                            <div class="flex items-start">
                                <div class="flex flex-col justify-between text-xs text-gray-400 dark:text-gray-500 pr-2" style="height: 114px; padding-top: 2px; padding-bottom: 2px;">
                                    <span></span>
                                    <span>Mon</span>
                                    <span></span>
                                    <span>Wed</span>
                                    <span></span>
                                    <span>Fri</span>
                                    <span></span>
                                </div>

                                <div class="flex gap-1">
                                    @php
                                        $weeks = collect($activityGrid)->chunk(7);
                                    @endphp
                                    @foreach($weeks as $week)
                                        <div class="flex flex-col gap-1">
                                            @foreach($week as $day)
                                                <div style="width: 14px; height: 14px;"
                                                     class="rounded-sm
                                                     @if($day['level'] == 0) bg-gray-100 dark:bg-gray-700
                                                     @elseif($day['level'] == 1) bg-green-300 dark:bg-green-800
                                                     @elseif($day['level'] == 2) bg-green-500 dark:bg-green-600
                                                     @elseif($day['level'] == 3) bg-green-700 dark:bg-green-400
                                                     @endif"
                                                     title="{{ $day['count'] }} تمارين في تاريخ {{ $day['date'] }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end items-center mt-4 gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ app()->getLocale() == 'ar' ? 'أقل' : 'Less' }}</span>
                        <div style="width: 12px; height: 12px;" class="rounded-sm bg-gray-100 dark:bg-gray-700"></div>
                        <div style="width: 12px; height: 12px;" class="rounded-sm bg-green-300 dark:bg-green-800"></div>
                        <div style="width: 12px; height: 12px;" class="rounded-sm bg-green-500 dark:bg-green-600"></div>
                        <div style="width: 12px; height: 12px;" class="rounded-sm bg-green-700 dark:bg-green-400"></div>
                        <span>{{ app()->getLocale() == 'ar' ? 'أكثر' : 'More' }}</span>
                    </div>
                </div>



            </div>
        </div>
    </div>
</x-app-layout>
