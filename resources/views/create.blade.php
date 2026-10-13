<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إضافة تمرين جديد - DevFit</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; padding: 40px; }
        .form-container { background: white; max-width: 600px; margin: 0 auto; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #1e3a8a; text-align: center; margin-top: 0; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #374151; }
        input[type="text"], input[type="number"], textarea { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box; font-family: inherit; }
        textarea { resize: vertical; height: 100px; }
        .submit-btn { background: #2563eb; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; }
        .submit-btn:hover { background: #1d4ed8; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #4b5563; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .error-list { background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 4px; margin-bottom: 20px; list-style: none; }
    </style>
</head>
<body>

    <div class="form-container">
        <h1>إضافة تمرين جديد ➕</h1>

        <!-- عرض أخطاء التحقق إن وجدت -->
        @if ($errors->any())
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>⚠️ {{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form action="{{ url('/exercises') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>عنوان التمرين:</label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="مثال: إراحة عضلات الرقبة">
            </div>

            <div class="form-group">
                <label>التصنيف (العيون، الظهر، الرقبة...):</label>
                <input type="text" name="category" value="{{ old('category') }}" placeholder="مثال: العيون">
            </div>

            <div class="form-group">
                <label>المدة (بالثواني):</label>
                <input type="number" name="duration_seconds" value="{{ old('duration_seconds') }}" placeholder="مثال: 30">
            </div>

            <div class="form-group">
                <label>وصف التمرين وطريقة أدائه:</label>
                <textarea name="description" placeholder="اشرح تفاصيل التمرين هنا...">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="submit-btn">حفظ التمرين 💾</button>
        </form>

        <a href="{{ url('/') }}" class="back-link">← العودة إلى القائمة الرئيسية</a>
    </div>

</body>
</html>