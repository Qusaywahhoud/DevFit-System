<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Exercise;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema; // <-- مهم جداً لتعطيل القيود

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // تعطيل قيود المفتاح الأجنبي مؤقتًا لتجنب خطأ التفريد
        Schema::disableForeignKeyConstraints();

        // 1. تفريغ الجداول بأمان
        User::truncate();
        Exercise::truncate();

        // إعادة تفعيل قيود المفتاح الأجنبي
        Schema::enableForeignKeyConstraints();

        // 2. إنشاء حساب المدير (Admin) تلقائياً
        User::create([
            'name' => 'Qusay Wahhoud',
            'email' => 'qwahhoud@gmail.com',
            'password' => Hash::make('password123'),
            'is_admin' => true, // تفعيل صلاحيات الإدارة الكاملة
        ]);

        // 3. ضخ قائمة التمارين الشاملة باللغتين
        $exercises = [
            // --- تصنيف العيون (Eyes) ---
            [
                'title_ar' => 'قاعدة إراحة العين (20-20-20)',
                'title_en' => '20-20-20 Eye Rule',
                'description_ar' => 'كل 20 دقيقة، انظر لشيء يبعد 20 قدماً لمدة 20 ثانية لتخفيف إجهاد وتشنج عضلات العين.',
                'description_en' => 'Every 20 minutes, look at something 20 feet away for 20 seconds to reduce eye strain.',
                'category' => 'eyes',
                'duration_seconds' => 20
            ],
            [
                'title_ar' => 'الرمش السريع والترطيب',
                'title_en' => 'Quick Blinking & Hydration',
                'description_ar' => 'قم بالرمش بسرعة لمدة 10 ثوانٍ ثم أغلق عينيك بإحكام لـ 5 ثوانٍ لتحفيز إفراز الدموع الطبيعية.',
                'description_en' => 'Blink rapidly for 10 seconds then close your eyes tightly for 5 seconds to stimulate natural tears.',
                'category' => 'eyes',
                'duration_seconds' => 15
            ],
            [
                'title_ar' => 'إغلاق العينين بالراحة (Palming)',
                'title_en' => 'Palming and Eye Relaxation',
                'description_ar' => 'فرك راحتي يديك حتى تسخنا، ثم ضعهما برفق فوق عينيك المغلقتين لحجب الضوء تماماً وإرخائهما.',
                'description_en' => 'Rub your palms together until warm, then gently place them over your closed eyes to block out light and relax.',
                'category' => 'eyes',
                'duration_seconds' => 30
            ],
            [
                'title_ar' => 'تمرين التركيز البعيد والقريب',
                'title_en' => 'Near and Far Focus',
                'description_ar' => 'انظر إلى إصبعك على بعد 10 سم لمدة 5 ثوانٍ، ثم انظر إلى أبعد نقطة ممكنة خارج الغرفة لـ 5 ثوانٍ.',
                'description_en' => 'Focus on your finger 10 cm away for 5 seconds, then look at the farthest point outside the room for 5 seconds.',
                'category' => 'eyes',
                'duration_seconds' => 30
            ],
            [
                'title_ar' => 'تحريك بؤبؤ العين بشكل دائرى',
                'title_en' => 'Circular Eye Rolls',
                'description_ar' => 'حرك عينيك ببطء في اتجاه عقارب الساعة في حركة دائرية كاملة، ثم عكس الاتجاه لتنشيط عضلات العين.',
                'description_en' => 'Slowly roll your eyes in a full circular motion clockwise, then reverse direction to activate eye muscles.',
                'category' => 'eyes',
                'duration_seconds' => 20
            ],

            // --- تصنيف الرقبة (Neck) ---
            [
                'title_ar' => 'تمدد الرقبة الجانبي',
                'title_en' => 'Side Neck Stretch',
                'description_ar' => 'اجلس أو قف بقامة مستقيمة، وأمِل رأسك ببطء نحو كتفك حتى تشعر بشد خفيف، ثم بدل للجهة الأخرى.',
                'description_en' => 'Sit upright and slowly tilt your head toward your shoulder until you feel a gentle stretch, then switch sides.',
                'category' => 'neck',
                'duration_seconds' => 30
            ],
            [
                'title_ar' => 'تحريك الذقن للداخل (Chin Tuck)',
                'title_en' => 'Chin Tuck Exercise',
                'description_ar' => 'اسحب ذقنك للوراء قليلاً وكأنك تصنع "ذقناً مزدوجاً" لإصلاح انحناء الرقبة للأمام أمام الشاشة.',
                'description_en' => 'Pull your chin straight back as if making a double chin to correct forward head posture from screen use.',
                'category' => 'neck',
                'duration_seconds' => 20
            ],
            [
                'title_ar' => 'إمالة الرقبة للأمام والخلف',
                'title_en' => 'Neck Tilt (Front & Back)',
                'description_ar' => 'اخفض ذقنك بحستر نحو صدرك لمدة ثوانٍ، ثم ارفع رأسك بحذر للخلف لتخفيف تشنج الفقرات العلوية.',
                'description_en' => 'Lower your chin gently toward your chest for a few seconds, then carefully tilt your head back to relieve upper spine tension.',
                'category' => 'neck',
                'duration_seconds' => 25
            ],
            [
                'title_ar' => 'دوران الرقبة البطئ',
                'title_en' => 'Slow Neck Rotations',
                'description_ar' => 'أدر رأسك بحركة دائرية بطيئّة وناعمة للغاية يميناً ثم يساراً لتفكيك تيبس الرقبة الناتج عن التركيز الطويل.',
                'description_en' => 'Gently and slowly roll your head in a smooth circle to the right and left to loosen stiffness from long focus.',
                'category' => 'neck',
                'duration_seconds' => 30
            ],

            // --- تصنيف الأيدي والأصابع (Hands) ---
            [
                'title_ar' => 'تدوير المعصم (للكتابة المستمرة)',
                'title_en' => 'Wrist Rotations for Coders',
                'description_ar' => 'قم بتحريك معصميك بشكل دائري في اتجاه عقارب الساعة ثم عكسها لتجنب آلام المفاصل الناتجة عن الكودينغ.',
                'description_en' => 'Rotate your wrists clockwise and counter-clockwise to prevent joint pain resulting from continuous coding.',
                'category' => 'hands',
                'duration_seconds' => 30
            ],
            [
                'title_ar' => 'ضغط كف اليد (Isometric Palm Press)',
                'title_en' => 'Isometric Palm Press',
                'description_ar' => 'اضغط براحة يديك ببعضهما البعض أمام صدرك بقوة متوسطة لمدة 10 ثوانٍ لتقوية وإرخاء عضلات اليدين.',
                'description_en' => 'Press your palms firmly together in front of your chest with moderate force for 10 seconds to relax hand muscles.',
                'category' => 'hands',
                'duration_seconds' => 20
            ],
            [
                'title_ar' => 'إطالة الأصابع وبسطها',
                'title_en' => 'Finger Stretch & Spread',
                'description_ar' => 'افرد أصابع يديك لأقصى مدى ممكن وكأنك تبعدها عن بعضها بقوة، ثم استرخِ وكرر العملية لتنشيط الأعصاب.',
                'description_en' => 'Spread your fingers as far apart as possible with force, then relax and repeat to stimulate hand nerves.',
                'category' => 'hands',
                'duration_seconds' => 15
            ],
            [
                'title_ar' => 'إطالة المعصم لأعلى ولأسفل',
                'title_en' => 'Wrist Flex & Extension',
                'description_ar' => 'امدد ذراعك للأمام واجعل أصابعك تشير للأعلى واسحبها برفق بيدك الأخرى، ثم اجعلها تشير للأسفل وكرر الشد.',
                'description_en' => 'Extend your arm forward with fingers up, gently pull them back with your other hand, then point fingers down and repeat.',
                'category' => 'hands',
                'duration_seconds' => 25
            ],

            // --- تصنيف الظهر والكتفين (Back) ---
            [
                'title_ar' => 'تمرين تقويم الظهر والكتفين',
                'title_en' => 'Back and Shoulder Alignment',
                'description_ar' => 'قف واشبك يديك خلف ظهرك واسحب كتفيك للخلف بقوة لإلغاء انحناءة الجلوس على الكرسي.',
                'description_en' => 'Stand and clasp your hands behind your back, pulling your shoulders back firmly to counteract slouching.',
                'category' => 'back',
                'duration_seconds' => 30
            ],
            [
                'title_ar' => 'إطالة الكتفين والذراعين',
                'title_en' => 'Cross-Body Shoulder Stretch',
                'description_ar' => 'ارفع ذراعك أمام صدرك واسحبه باليد الأخرى باتجاه جسمك لتخفيف توتر الكتف العلوي.',
                'description_en' => 'Raise your arm across your chest and pull it gently with the other hand to relieve upper shoulder tension.',
                'category' => 'back',
                'duration_seconds' => 20
            ],
            [
                'title_ar' => 'دوران الكتفين للوراء',
                'title_en' => 'Backward Shoulder Rolls',
                'description_ar' => 'قم بتحريك كتفيك بحركات دائرية بطيئة باتجاه الخلف 10 مرات لفتح منطقة الصدر وإزالة تشنج الظهر.',
                'description_en' => 'Roll your shoulders slowly backward 10 times to open up the chest area and relieve back tightness.',
                'category' => 'back',
                'duration_seconds' => 25
            ],
            [
                'title_ar' => 'مد الجسم لأعلى (السحب العمودي)',
                'title_en' => 'Vertical Torso Stretch',
                'description_ar' => 'شبك أصابع يديك واعتمد على رفعها فوق رأسك مع مد جذعك لأعلى قدر الإمكان لإطالة فقرات الظهر.',
                'description_en' => 'Interlock your fingers and lift them above your head, stretching your torso upward as much as possible.',
                'category' => 'back',
                'duration_seconds' => 25
            ],
            [
                'title_ar' => 'تدوير الجذع أثناء الجلوس',
                'title_en' => 'Seated Torso Twist',
                'description_ar' => 'أدر جذعك وأنت على الكرسي يميناً ويساراً مستعيناً بمسند الكرسي لتخفيف التشنجات أسفل الظهر.',
                'description_en' => 'Twist your torso right and left while seated using the chair armrest to relieve lower back stiffness.',
                'category' => 'back',
                'duration_seconds' => 20
            ],

            // --- تصنيف الساقين (Legs) ---
            [
                'title_ar' => 'تحريك الكاحلين والقدمين',
                'title_en' => 'Ankle Rotations',
                'description_ar' => 'قم برفع قدميك عن الأرض وحرك كاحليك بشكل دائري لتنشيط الدورة الدموية في الساقين وتجنب الخدر.',
                'description_en' => 'Lift your feet off the floor and rotate your ankles in circles to boost circulation and prevent numbness.',
                'category' => 'legs',
                'duration_seconds' => 30
            ],
            [
                'title_ar' => 'رفع كعب القدمين جلوساً',
                'title_en' => 'Seated Heel Raises',
                'description_ar' => 'ارفع كعب قدميك عن الأرض مع إبقاء أصابعهما ثابتة، ثم اخفضهما لتشغيل عضلات الساق وتحريك الدورة الدموية.',
                'description_en' => 'Raise your heels while keeping toes on the floor, then lower them to activate calf muscles and blood flow.',
                'category' => 'legs',
                'duration_seconds' => 30
            ],
            [
                'title_ar' => 'إطالة عضلات الساق الخلفية (Calf Stretch)',
                'title_en' => 'Calf Muscle Stretch',
                'description_ar' => 'قف وادفع إحدى قدميك للخلف مع الحفاظ على كعبك ملامساً للأرض لشعور بالراحة في الساق.',
                'description_en' => 'Stand and step one foot back keeping your heel flat on the floor for a comforting leg release.',
                'category' => 'legs',
                'duration_seconds' => 30
            ],
            [
                'title_ar' => 'بسط الركبة ومد الساق',
                'title_en' => 'Seated Leg Extensions',
                'description_ar' => 'اجلس وافرد إحدى ساقيك بشكل أفقية تماماً أمامك لمدة ثوانٍ ثم أنزلها، وبدل بين الساقين لتنشيط الركبة.',
                'description_en' => 'Sit and straighten one leg horizontally for a few seconds, lower it, and switch to keep knees active.',
                'category' => 'legs',
                'duration_seconds' => 25
            ],

            // --- تصنيف الاسترخاء والذهن (Relaxation) ---
            [
                'title_ar' => 'التنفس العميق لإرخاء الأعصاب',
                'title_en' => 'Deep Breathing Relaxation',
                'description_ar' => 'خذ نفساً عميقاً من بطنك لمدة 4 ثوانٍ، احبسه لـ 4 ثوانٍ، ثم أخرجه ببطء لتقليل التوتر البرمجي.',
                'description_en' => 'Take a deep breath from your belly for 4 seconds, hold for 4 seconds, and exhale slowly to reduce coding stress.',
                'category' => 'relaxation',
                'duration_seconds' => 60
            ],
            [
                'title_ar' => 'تمرين التنفس المربع (Box Breathing)',
                'title_en' => 'Box Breathing Technique',
                'description_ar' => 'شهيق 4 ثوانٍ -> حبس النفس 4 ثوانٍ -> زفير 4 ثوانٍ -> حبس الفراغ 4 ثوانٍ لصفاء الذهن التام.',
                'description_en' => 'Inhale 4s -> Hold 4s -> Exhale 4s -> Hold 4s for complete mental clarity and focus.',
                'category' => 'relaxation',
                'duration_seconds' => 45
            ],
            [
                'title_ar' => 'إرخاء عضلات الكتفين والرقبة الواعي',
                'title_en' => 'Conscious Muscle Unclench',
                'description_ar' => 'ارفع كتفيك لأعلى نحو أذنيك بقوة لـ 3 ثوانٍ ثم أسقطهما مفاجأة للأسفل لتفريغ التوتر المخزن.',
                'description_en' => 'Shrug your shoulders up tightly toward your ears for 3 seconds then drop them suddenly to release stored tension.',
                'category' => 'relaxation',
                'duration_seconds' => 20
            ],

            // --- تصنيف الرأس والوجه (Head) ---
            [
                'title_ar' => 'تدليك الصدغين والوجه',
                'title_en' => 'Temple & Face Massage',
                'description_ar' => 'استخدم أطراف أصابعك لعمل حركات دائرية خفيفة على جانبي الرأس (الصدغين) لتخفيف صداع الشاشة.',
                'description_en' => 'Use your fingertips to make gentle circular motions on your temples to relieve screen headaches.',
                'category' => 'head',
                'duration_seconds' => 20
            ],
            [
                'title_ar' => 'تفريغ توتر الفك والأسنان',
                'title_en' => 'Jaw Tension Release',
                'description_ar' => 'افرِق بين أسنانك وأرخِ عضلات فكك تماماً وخذ نفساً عميقاً لتخفيف الضغط العصبي أثناء الديباغينغ.',
                'description_en' => 'Part your teeth, relax your jaw muscles completely, and take a deep breath to relieve stress during debugging.',
                'category' => 'head',
                'duration_seconds' => 15
            ]
        ];

        foreach ($exercises as $data) {
            Exercise::create($data);
        }
    }
}