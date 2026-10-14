<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;
use App\Models\CompletedExercise;
use Illuminate\Support\Facades\Auth;

class ExerciseController extends Controller
{
 // دالة لعرض جميع التمارين مع إمكانية التصفية حسب التصنيف
public function index(Request $request)
    {
        $category = $request->query('category');

        $categoriesMap = [
            'eyes' => 'العيون',
            'neck' => 'الرقبة',
            'hands' => 'الأيدي',
            'back' => 'الظهر',
            'legs' => 'الساقين',
            'relaxation' => 'الاسترخاء',
            'head' => 'الرأس',
        ];

        if ($category && $category !== 'all') {
            $englishKey = array_search($category, $categoriesMap);
            $arabicValue = $categoriesMap[$category] ?? $category;

            $exercises = Exercise::where(function($query) use ($category, $englishKey, $arabicValue) {
                $query->whereRaw('TRIM(category) ILIKE ?', ['%' . trim($category) . '%']);
                
                if ($englishKey) {
                    $query->orWhereRaw('TRIM(category) ILIKE ?', ['%' . trim($englishKey) . '%']);
                }
                
                if ($arabicValue) {
                    $query->orWhereRaw('TRIM(category) ILIKE ?', ['%' . trim($arabicValue) . '%']);
                }
            })->get();
        } else {
            $exercises = Exercise::all();
        }

        $categories = Exercise::select('category')->distinct()->pluck('category');

        return view('exercises', compact('exercises', 'categories', 'category', 'categoriesMap'));
    }

    // دالة لعرض صفحة نموذج الإضافة
    public function create()
    {
        return view('create');
    }

    // عرض صفحة التعديل
    public function edit($id)
    {
        $exercise = Exercise::findOrFail($id);
        return view('exercises.edit', compact('exercise'));
    }

    // حفظ التعديلات (تم توحيد الحقول لتتطابق مع دالة الإضافة)
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'duration_seconds' => 'required|integer|min:1',
        ]);

        $exercise = Exercise::findOrFail($id);
        $exercise->update($request->all());

        return redirect('/')->with('success', 'تم تحديث التمرين بنجاح! ✏️');
    }

    // حذف التمرين
    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        $exercise->delete();

        return redirect('/')->with('success', 'تم حذف التمرين بنجاح! 🗑️');
    }

    // دالة لاستقبال البيانات وحفظها في قاعدة البيانات
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'duration_seconds' => 'required|integer|min:1',
        ]);

        Exercise::create($request->all());

        return redirect('/')->with('success', 'تم إضافة التمرين الجديد بنجاح! 🎉');
    }

    // دالة لتسجيل التمرين كمنجز للمستخدم الحالي في لوحة التحكم
    public function completeExercise(Request $request)
    {
        if (Auth::check()) {
            CompletedExercise::create([
                'user_id' => Auth::id(),
                'exercise_title' => $request->title,
            ]);
            return response()->json(['status' => 'success', 'message' => 'تم حفظ التمرين بنجاح!']);
        }

        return response()->json(['status' => 'guest']);
    }

    // دالة تصدير سجل التمارين إلى ملف Excel/CSV
    public function exportCsv()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $exercises = \App\Models\CompletedExercise::where('user_id', $userId)->latest()->get();

        $filename = "DevFit_Log_" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($exercises) {
            $file = fopen('php://output', 'w');
            // إضافة كود BOM لضمان قراءة اللغة العربية بشكل صحيح في برنامج Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // عناوين الأعمدة
            fputcsv($file, ['اسم التمرين', 'تاريخ وتوقت الإنجاز']);

            // تعبئة البيانات
            foreach ($exercises as $exercise) {
                fputcsv($file, [
                    $exercise->exercise_title,
                    $exercise->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
