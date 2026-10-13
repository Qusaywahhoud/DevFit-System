<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            [
                'title_ar' => 'قاعدة إراحة العين (20-20-20)',
                'title_en' => '20-20-20 Eye Rule',
                'description_ar' => 'كل 20 دقيقة، انظر لشيء يبعد 20 قدماً لمدة 20 ثانية لتخفيف إجهاد العين.',
                'description_en' => 'Every 20 minutes, look at something 20 feet away for 20 seconds to reduce eye strain.',
                'category' => 'eyes',
                'duration_seconds' => 20,
            ],
            [
                'title_ar' => 'تمدد الرقبة الجانبي',
                'title_en' => 'Side Neck Stretch',
                'description_ar' => 'اجلس بقامة مستقيم، وأمل رأسك ببطء نحو كتفك حتى تشعر بشد خفيف.',
                'description_en' => 'Sit upright and slowly tilt your head toward your shoulder until you feel a gentle stretch.',
                'category' => 'neck',
                'duration_seconds' => 30,
            ],
            [
                'title_ar' => 'إطالة عضلات الظهر والكتفين',
                'title_en' => 'Upper Back & Shoulder Stretch',
                'description_ar' => 'شبك أصابع يديك وافعلهما للأمام مع مد ذراعيك بالكامل وتقويس الظهر قليلاً للخلف.',
                'description_en' => 'Interlock your fingers and push them forward, extending your arms fully and arching your upper back.',
                'category' => 'back',
                'duration_seconds' => 45,
            ],
            [
                'title_ar' => 'تحريك الرسغين والأصابع',
                'title_en' => 'Wrist and Finger Rotations',
                'description_ar' => 'قم بتدوير الرسغين في اتجاه عقارب الساعة وعكسها لتجنب متلازمة النفق الرسغي.',
                'description_en' => 'Rotate your wrists clockwise and counter-clockwise to prevent carpal tunnel strain.',
                'category' => 'hands',
                'duration_seconds' => 25,
            ],
            [
                'title_ar' => 'تمارين استرخاء التنفس العميق',
                'title_en' => 'Deep Breathing Relaxation',
                'description_ar' => 'خذ شهيقاً عميقاً ببطء من الأنف لمدة 4 ثوانٍ، احبسه لـ 4 ثوانٍ، ثم زفير ببطء.',
                'description_en' => 'Inhale slowly through your nose for 4 seconds, hold for 4 seconds, and exhale slowly.',
                'category' => 'relaxation',
                'duration_seconds' => 60,
            ],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create($exercise);
        }
    }
}