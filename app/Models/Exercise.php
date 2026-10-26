<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'category',
        'duration_seconds',
        'image_path',
        'video_path',
    ];

    // جلب العنوان تلقائياً حسب اللغة الحالية للموقع
    public function getTitleAttribute()
    {
        $englishTitle = $this->attributes['title'] ?? null;
        return app()->getLocale() === 'ar'
            ? ($this->title_ar ?: $englishTitle)
            : ($englishTitle ?: $this->title_ar);
    }

    // جلب الوصف تلقائياً حسب اللغة الحالية للموقع
    public function getDescriptionAttribute()
    {
        $englishDescription = $this->attributes['description'] ?? null;
        return app()->getLocale() === 'ar'
            ? ($this->description_ar ?: $englishDescription)
            : ($englishDescription ?: $this->description_ar);
    }

    // ترجمة اسم التصنيف تلقائياً حسب اللغة
    public function getCategoryAttribute($value)
    {
        $map = [
            'eyes'       => ['ar' => 'العيون', 'en' => 'Eyes'],
            'neck'       => ['ar' => 'الرقبة', 'en' => 'Neck'],
            'hands'      => ['ar' => 'الأيدي', 'en' => 'Hands'],
            'back'       => ['ar' => 'الظهر', 'en' => 'Back'],
            'legs'       => ['ar' => 'الساقين', 'en' => 'Legs'],
            'relaxation' => ['ar' => 'الاسترخاء', 'en' => 'Relaxation'],
            'head'       => ['ar' => 'الرأس', 'en' => 'Head'],
        ];

        $locale = app()->getLocale();

        if (isset($map[$value])) {
            return $map[$value][$locale] ?? $value;
        }

        foreach ($map as $key => $translations) {
            if ($value === $translations['ar'] || $value === $translations['en']) {
                return $translations[$locale] ?? $value;
            }
        }

        return $value;
    }

    // علاقة التمارين المنجزة
    public function completedExercises()
    {
        return $this->hasMany(CompletedExercise::class);
    }
}
