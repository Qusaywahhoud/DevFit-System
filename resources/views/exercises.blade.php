<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ app()->getLocale() == 'ar' ? 'تمارين DevFit' : 'DevFit Exercises' }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; color: #111827; padding: 20px; transition: 0.3s; }
        .card { background: white; padding: 20px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-right: 5px solid #2563eb; transition: 0.3s; }
        html[dir="ltr"] .card { border-right: none; border-left: 5px solid #2563eb; }
        h1, h2 { color: #1e3a8a; }
        p { color: #374151; }
        .badge { background: #d97706; color: white; padding: 4px 8px; border-radius: 4px; font-size: 13px; }
        .timer-btn { background: #10b981; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 14px; margin-top: 10px; }
        .timer-btn:hover { background: #059669; }
        .timer-display { font-weight: bold; color: #dc2626; margin-right: 10px; font-size: 15px; }
        html[dir="ltr"] .timer-display { margin-right: 0; margin-left: 10px; }

        .filters { text-align: center; margin-bottom: 15px; }
        .filter-btn { background: #e5e7eb; color: #374151; padding: 8px 16px; margin: 0 4px; text-decoration: none; border-radius: 20px; font-size: 14px; transition: 0.2s; display: inline-block; }
        .filter-btn:hover, .filter-btn.active { background: #2563eb; color: white; }

        .exercise-img { width: 100%; max-height: 260px; object-fit: cover; border-radius: 6px; margin-bottom: 12px; }

        /* تنسيقات الوضع الليلي (Dark Mode) */
        body.dark-mode { background-color: #111827; color: #f3f4f6; }
        body.dark-mode .card { background: #1f2937; box-shadow: 0 2px 5px rgba(255,255,255,0.05); border-right: 5px solid #3b82f6; }
        html[dir="ltr"] body.dark-mode .card { border-right: none; border-left: 5px solid #3b82f6; }
        body.dark-mode h1, body.dark-mode h2 { color: #60a5fa; }
        body.dark-mode p { color: #d1d5db; }
        body.dark-mode .filter-btn { background: #374151; color: #d1d5db; }
        body.dark-mode .filter-btn.active { background: #3b82f6; color: white; }
    </style>
</head>
<body id="body-element">

    <!-- زر تغيير اللغة والتحكم -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <div>
            <!-- زر تبديل اللغة -->
            <a href="{{ url('lang/' . (app()->getLocale() == 'ar' ? 'en' : 'ar')) }}" style="background: #2563eb; color: white; padding: 6px 14px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold;">
                🌐 {{ app()->getLocale() == 'ar' ? 'English' : 'العربية' }}
            </a>
        </div>

        <div>
            @auth
                <span style="font-weight: bold; margin-left: 15px;">{{ app()->getLocale() == 'ar' ? 'مرحباً،' : 'Welcome,' }} {{ Auth::user()->name }} 👋</span>
                <a href="{{ url('/dashboard') }}" style="background: #4f46e5; color: white; padding: 6px 14px; text-decoration: none; border-radius: 4px; margin-left: 5px; display: inline-block;">{{ app()->getLocale() == 'ar' ? 'لوحة التحكم ⚙️' : 'Dashboard ⚙️' }}</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #dc2626; color: white; border: none; padding: 7px 14px; border-radius: 4px; cursor: pointer; font-size: 14px;">{{ app()->getLocale() == 'ar' ? 'تسجيل الخروج 🚪' : 'Log Out 🚪' }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" style="background: #2563eb; color: white; padding: 6px 14px; text-decoration: none; border-radius: 4px; margin-left: 5px; display: inline-block;">{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول 🔑' : 'Log in 🔑' }}</a>
                <a href="{{ route('register') }}" style="background: #10b981; color: white; padding: 6px 14px; text-decoration: none; border-radius: 4px; display: inline-block;">{{ app()->getLocale() == 'ar' ? 'حساب جديد 📝' : 'Register 📝' }}</a>
            @endauth
        </div>
    </div>

    <h1 style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'نظام DevFit 💻🏋️‍♂️' : 'DevFit System 💻🏋️‍♂️' }}</h1>
    <p style="text-align: center; margin-bottom: 15px;">
        {{ app()->getLocale() == 'ar' ? 'تمارين مخصصة لتخفيف إجهاد الجلوس المطول للمبرمجين' : 'Custom exercises to relieve prolonged sitting fatigue for programmers' }}
    </p>

    <!-- زر تبديل الوضع الليلي -->
    <div style="text-align: center; margin-bottom: 15px;">
        <button onclick="toggleDarkMode()" id="dark-mode-btn" style="background: #374151; color: white; border: none; padding: 8px 15px; border-radius: 20px; cursor: pointer; font-size: 14px;">
            🌙 {{ app()->getLocale() == 'ar' ? 'الوضع الليلي' : 'Dark Mode' }}
        </button>
    </div>

    <!-- زر إضافة تمرين جديد -->
    @auth
        @if(Auth::user()->is_admin)
            <div style="text-align: center; margin-bottom: 25px;">
                <a href="{{ url('/create') }}" style="background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold;">
                    {{ app()->getLocale() == 'ar' ? 'إضافة تمرين جديد ➕' : 'Add New Exercise ➕' }}
                </a>
            </div>
        @endif
    @endauth

    <!-- أزرار التصنيفات -->
    <div class="filters">
        <a href="{{ url('/') }}" class="filter-btn {{ !isset($category) ? 'active' : '' }}">
            {{ app()->getLocale() == 'ar' ? 'الكل 🌐' : 'All 🌐' }}
        </a>
        @foreach($categories as $cat)
            <a href="{{ url('/?category=' . urlencode($cat)) }}" class="filter-btn {{ isset($category) && $category == $cat ? 'active' : '' }}">
                {{ __($cat) }}
            </a>
        @endforeach
    </div>


    <!-- عرض التمارين -->
    @foreach($exercises as $index => $exercise)
    <style>
        /* الألوان الافتراضية (الوضع النهاري / الصباحي) */
:root {
    --card-bg: #ffffff;
    --card-border: #e2e8f0;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --img-container-bg: #f8fafc;
}

/* الألوان الخاصة بالوضع الليلي (تطبق عندما يكون الكلاس dark موجوداً في الـ body أو الـ wrapper) */
body.dark, .dark-mode {
    --card-bg: #1e293b;         /* رمادي زرقاء داكن ومريح */
    --card-border: #334155;     /* حدود خفيفة للفصل بين الكروت */
    --text-main: #f8fafc;       /* أبيض ساطع للعناوين */
    --text-muted: #94a3b8;      /* رمادي هادئ للنصوص والوصف */
    --img-container-bg: #0f172a;  /* خلفية داكنة نظيفة داخل إطار الصورة */
}

/* تطبيق المتغيرات على الكارت والعناصر */
/* الألوان الأساسية للوضع النهاري (الصباحي) */
.card,
body:not(.dark):not(.dark-mode) .card {
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    color: #1e293b !important;
}

.exercise-image-container,
body:not(.dark):not(.dark-mode) .exercise-image-container {
    background-color: #f8fafc !important;
}

/* الألوان الخاصة بالوضع الليلي (تتفاعل تلقائياً مع أي كلاس ليلي شائع) */
body.dark .card,
body.dark-mode .card,
.dark .card,
[data-theme="dark"] .card {
    background-color: #1e293b !important; /* رمادي زرقاء داكن ومريح */
    border: 1px solid #334155 !important;
    color: #f8fafc !important;
}

body.dark .exercise-image-container,
body.dark-mode .exercise-image-container,
.dark .exercise-image-container,
[data-theme="dark"] .exercise-image-container {
    background-color: #1e293b !important; /* خلفية داكنة نظيفة لإطار الصورة */
}

/* ثبات أبعاد الصورة ومنع التمدد في الحالتين */
.exercise-image-container {
    width: 100%;
    max-width: 650px;       /* تحديد أقصى عرض لكي لا تتمدد الصورة بشكل مزعج في الشاشات العريضة */
    height: 220px;          /* تقليل الارتفاع ليصبح أكثر تناسقاً ورشاقة */
    margin: 0 auto 16px auto; /* توسيط الحاوية داخل الكارت مع ترك مسافة سفلية */
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 12px;
    overflow: hidden;
    background-color: var(--img-container-bg, #0f172a);
}

.exercise-img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* يحافظ على تفاصيل الصورة بالكامل دون أي قص أو تشوه */
}
/* توحيد ألوان النصوص والعناوين داخل الكارت لضمان وضوحها */
.card h3, .card .exercise-title {
    font-weight: 600;

    }
</style>
        <div class="card">
            <!-- عرض الصورة التوضيحية -->
@if($exercise->image_path)
       <div class="exercise-image-container">
    <img src="{{ $exercise->image_path }}" alt="Exercise Step" class="exercise-img">
</div>
@endif

            <h2>{{ $exercise->title }}</h2>
            <p>
                <strong>{{ app()->getLocale() == 'ar' ? 'التصنيف:' : 'Category:' }}</strong>
                <span class="badge">{{ __($exercise->category) }}</span> |
                <strong>{{ app()->getLocale() == 'ar' ? 'المدة:' : 'Duration:' }}</strong>
                {{ $exercise->duration_seconds }} {{ app()->getLocale() == 'ar' ? 'ثانية' : 'seconds' }}
            </p>
            <p style="line-height: 1.6;">{{ $exercise->description }}</p>

            <div>
                <button class="timer-btn" onclick="startTimer('{{ $index }}', '{{ $exercise->duration_seconds }}', '{{ $exercise->title }}')">
                    {{ app()->getLocale() == 'ar' ? 'بدء التمرين ⏱️' : 'Start Exercise ⏱️' }}
                </button>
                <span id="timer-display-{{ $index }}" class="timer-display"></span>
            </div>

            <!-- أزرار التعديل والحذف -->
            @auth
                @if(Auth::user()->is_admin)
                    <div style="margin-top: 15px; display: flex; gap: 10px; border-top: 1px solid #e5e7eb; padding-top: 10px;">
                        <a href="{{ route('exercises.edit', $exercise->id) }}" style="background: #d97706; color: white; padding: 5px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold;">
                            {{ app()->getLocale() == 'ar' ? 'تعديل ✏️' : 'Edit ✏️' }}
                        </a>

                        <form action="{{ route('exercises.destroy', $exercise->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() == 'ar' ? 'هل أنت متأكد من حذف هذا التمرين؟' : 'Are you sure you want to delete this exercise?' }}');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: #dc2626; color: white; border: none; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;">
                                {{ app()->getLocale() == 'ar' ? 'حذف 🗑️' : 'Delete 🗑️' }}
                            </button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    @endforeach

    <script>
        const isAr = "{{ app()->getLocale() }}" === "ar";
        let activeInterval = null;
        let activeDisplaySpan = null;

        function playSuccessSound() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(523.25, audioCtx.currentTime);
                gain1.gain.setValueAtTime(0.1, audioCtx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.6);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start();
                osc1.stop(audioCtx.currentTime + 0.6);
            } catch (e) {
                console.log("Audio not supported");
            }
        }

        function startTimer(index, duration, exerciseTitle) {
            const display = document.getElementById('timer-display-' + index);
            let timer = parseInt(duration);

            if (activeInterval !== null) {
                alert(isAr ? "⚠ يوجد تمرين قيد التشغيل بالفعل!" : "⚠️ An exercise is already running!");
                return;
            }

            activeDisplaySpan = display;
            activeDisplaySpan.dataset.running = "true";

            activeInterval = setInterval(function () {
                display.textContent = (isAr ? "⏱️ الوقت المتبقي: " : "⏱️ Time remaining: ") + timer + (isAr ? " ثانية" : "s");

                if (--timer < 0) {
                    clearInterval(activeInterval);
                    activeInterval = null;
                    display.textContent = isAr ? "✅ انتهى الوقت! أحسنت العمل 🎉" : "✅ Time's up! Great job 🎉";

                    playSuccessSound();

                    fetch('complete-exercise', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ title: exerciseTitle })
                    }).catch(() => {});

                    if (activeDisplaySpan) {
                        activeDisplaySpan.dataset.running = "false";
                    }
                }
            }, 1000);
        }

        function toggleDarkMode() {
            const body = document.getElementById('body-element');
            const btn = document.getElementById('dark-mode-btn');
            body.classList.toggle('dark-mode');

            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
                btn.textContent = isAr ? "☀️ الوضع الصباحي" : "☀️ Light Mode";
            } else {
                localStorage.setItem('theme', 'light');
                btn.textContent = isAr ? "🌙 الوضع الليلي" : "🌙 Dark Mode";
            }
        }

        window.onload = function() {
            if (localStorage.getItem('theme') === 'dark') {
                document.getElementById('body-element').classList.add('dark-mode');
                document.getElementById('dark-mode-btn').textContent = isAr ? "☀️ الوضع الصباحي" : "☀️ Light Mode";
            }
        };
    </script>
</body>
</html>
