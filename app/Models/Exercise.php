<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'category',
        'duration_seconds'
    ];

    // جلب العنوان تلقائياً حسب اللغة
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : ($this->title_en ?: $this->title_ar);
    }

    // جلب الوصف تلقائياً حسب اللغة
    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : ($this->description_en ?: $this->description_ar);
    }

    // ترجمة التصنيف تلقائياً حسب اللغة (سواء كان مخزناً كمفتاح أو نص)
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

        // إذا كان مخزناً كمفتاح (مثل eyes)
        if (isset($map[$value])) {
            return $map[$value][$locale] ?? $value;
        }

        // إذا كان مخزناً كنص عربي سابقاً، نحاول مطابقته أو إرجاعه
        foreach ($map as $key => $translations) {
            if ($value === $translations['ar'] || $value === $translations['en']) {
                return $translations[$locale] ?? $value;
            }
        }

        return $value;
    }
}