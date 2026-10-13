<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            تعديل التمرين ✏️
        </h2>
    </x-slot>

    <div class="py-12" dir="rtl">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 shadow-xl sm:rounded-2xl p-6 md:p-8 border border-gray-200 dark:border-gray-800">
                
                <form action="{{ route('exercises.update', $exercise->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">عنوان التمرين</label>
                        <input type="text" name="title" value="{{ $exercise->title }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">وصف التمرين</label>
                        <textarea name="description" rows="3" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm" required>{{ $exercise->description }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">التصنيف</label>
                        <input type="text" name="category" value="{{ $exercise->category }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 font-bold mb-2">المدة (بالثواني)</label>
                        <input type="number" name="duration_seconds" value="{{ $exercise->duration_seconds }}" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm" required>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl transition shadow-md">
                            حفظ التعديلات 💾
                        </button>
                        <a href="{{ url('/') }}" class="text-gray-600 dark:text-gray-400 hover:underline">إلغاء</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>