<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;
use App\Services\ExerciseService;

class ExerciseController extends Controller
{
    protected ExerciseService $exerciseService;

    public function __construct(ExerciseService $exerciseService)
    {
        $this->exerciseService = $exerciseService;
    }

    // عرض جميع التمارين مع إمكانية التصفية حسب التصنيف
    public function index(Request $request)
    {
        $category = $request->query('category');
        $exercises = $this->exerciseService->getFilteredExercises($category);
        $categories = $this->exerciseService->getCategories();
        $categoriesMap = $this->exerciseService->getCategoriesMap();

        return view('exercises', compact('exercises', 'categories', 'category', 'categoriesMap'));
    }

    // عرض صفحة نموذج الإضافة
    public function create()
    {
        return view('create');
    }

    // حفظ تمرين جديد مع دعم رفع الصور/الفيديوهات
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'duration_seconds' => 'required|integer|min:1',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:10240', // حد أقصى 10 ميجابايت للوسائط
        ]);

        if ($request->hasFile('media')) {
            $data['media_path'] = $request->file('media')->store('exercises', 'public');
        }

        $this->exerciseService->createExercise($data);

        return redirect('/')->with('success', 'تم إضافة التمرين الجديد بنجاح! 🎉');
    }

    // عرض صفحة التعديل
    public function edit($id)
    {
        $exercise = Exercise::findOrFail($id);
        return view('exercises.edit', compact('exercise'));
    }

    // حفظ التعديلات مع دعم تحديث الوسائط
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'duration_seconds' => 'required|integer|min:1',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4|max:10240',
        ]);

        $exercise = Exercise::findOrFail($id);

        if ($request->hasFile('media')) {
            $data['media_path'] = $request->file('media')->store('exercises', 'public');
        }

        $this->exerciseService->updateExercise($exercise, $data);

        return redirect('/')->with('success', 'تم تحديث التمرين بنجاح! ✏️');
    }

    // حذف التمرين
    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        $this->exerciseService->deleteExercise($exercise);

        return redirect('/')->with('success', 'تم حذف التمرين بنجاح! 🗑️');
    }
}
