<?php

namespace App\Services;

use App\Models\Exercise;
use Illuminate\Http\Request;

class ExerciseService
{
    protected array $categoriesMap = [
        'eyes' => 'العيون',
        'neck' => 'الرقبة',
        'hands' => 'الأيدي',
        'back' => 'الظهر',
        'legs' => 'الساقين',
        'relaxation' => 'الاسترخاء',
        'head' => 'الرأس',
    ];

    /**
     * Get filtered exercises based on category request with case-insensitive matching.
     */
    public function getFilteredExercises(?string $category)
    {
        if ($category && $category !== 'all') {
            $englishKey = array_search($category, $this->categoriesMap);
            $arabicValue = $this->categoriesMap[$category] ?? $category;

            return Exercise::where(function($query) use ($category, $englishKey, $arabicValue) {
                $query->whereRaw('LOWER(TRIM(category)) LIKE LOWER(?)', ['%' . trim($category) . '%']);
                
                if ($englishKey) {
                    $query->orWhereRaw('LOWER(TRIM(category)) LIKE LOWER(?)', ['%' . trim($englishKey) . '%']);
                }
                
                if ($arabicValue) {
                    $query->orWhereRaw('LOWER(TRIM(category)) LIKE LOWER(?)', ['%' . trim($arabicValue) . '%']);
                }
            })->get();
        }

        return Exercise::all();
    }

    /**
     * Get distinct categories list.
     */
    public function getCategories()
    {
        return Exercise::select('category')->distinct()->pluck('category');
    }

    /**
     * Get categories map dictionary.
     */
    public function getCategoriesMap(): array
    {
        return $this->categoriesMap;
    }

    /**
     * Create a new exercise.
     */
    public function createExercise(array $data): Exercise
    {
        return Exercise::create($data);
    }

    /**
     * Update an existing exercise.
     */
    public function updateExercise(Exercise $exercise, array $data): bool
    {
        return $exercise->update($data);
    }

    /**
     * Delete an exercise.
     */
    public function deleteExercise(Exercise $exercise): ?bool
    {
        return $exercise->delete();
    }
}
