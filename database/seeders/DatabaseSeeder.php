<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Exercise;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. تعطيل قيود المفتاح الأجنبي وتفريغ الجداول في البداية
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Exercise::truncate();
        Schema::enableForeignKeyConstraints();

        // 2. إنشاء حساب المدير (Admin)
        User::create([
            'name' => 'Qusay Wahhoud',
            'email' => 'qwahhoud@gmail.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        // 3. ضخ قائمة التمارين الـ 27 المكتملة مع صور راقية ومناسبة
        $exercises = [
            // --- تصنيف العيون (Eyes) ---
            [
                'title' => '20-20-20 Eye Rule',
                'title_ar' => 'قاعدة إراحة العين (20-20-20)',
                'description' => 'Every 20 minutes, look at something 20 feet away for 20 seconds to reduce eye strain.',
                'description_ar' => 'كل 20 دقيقة، انظر لشيء يبعد 20 قدماً لمدة 20 ثانية لتخفيف إجهاد وتشنج عضلات العين.',
                'category' => 'eyes',
                'duration_seconds' => 20,
                'image_path' => 'image/exercises/Eyes/1.jpg'
            ],
            [
                'title' => 'Quick Blinking & Hydration',
                'title_ar' => 'الرمش السريع والترطيب',
                'description' => 'Blink rapidly for 10 seconds then close your eyes tightly for 5 seconds to stimulate natural tears.',
                'description_ar' => 'قم بالرمش بسرعة لمدة 10 ثوانٍ ثم أغلق عينيك بإحكام لـ 5 ثوانٍ لتحفيز إفراز الدموع الطبيعية.',
                'category' => 'eyes',
                'duration_seconds' => 15,
                'image_path' => 'image/exercises/Eyes/2.jpg',
            ],
            [
                'title' => 'Palming and Eye Relaxation',
                'title_ar' => 'إغلاق العينين بالراحة (Palming)',
                'description' => 'Rub your palms together until warm, then gently place them over your closed eyes to block out light and relax.',
                'description_ar' => 'فرك راحتي يديك حتى تسخنا، ثم ضعهما برفق فوق عينيك المغلقتين لحجب الضوء تماماً وإرخائهما.',
                'category' => 'eyes',
                'duration_seconds' => 30,
                'image_path' => 'image/exercises/Eyes/3.jpg',
            ],
            [
                'title' => 'Near and Far Focus',
                'title_ar' => 'تمرين التركيز البعيد والقريب',
                'description' => 'Focus on your finger 10 cm away for 5 seconds, then look at the farthest point outside the room for 5 seconds.',
                'description_ar' => 'انظر إلى إصبعك على بعد 10 سم لمدة 5 ثوانٍ، ثم انظر إلى أبعد نقطة ممكنة خارج الغرفة لـ 5 ثوانٍ.',
                'category' => 'eyes',
                'duration_seconds' => 30,
                'image_path' => 'image/exercises/Eyes/4.jpg',
            ],
            [
                'title' => 'Circular Eye Rolls',
                'title_ar' => 'تحريك بؤبؤ العين بشكل دائرى',
                'description' => 'Slowly roll your eyes in a full circular motion clockwise, then reverse direction to activate eye muscles.',
                'description_ar' => 'حرك عينيك ببطء في اتجاه عقارب الساعة في حركة دائرية كاملة، ثم عكس الاتجاه لتنشيط عضلات العين.',
                'category' => 'eyes',
                'duration_seconds' => 20,
                'image_path' => 'image/exercises/Eyes/5.jpg',
            ],

            // --- تصنيف الرقبة (Neck) ---
            [
                'title' => 'Side Neck Stretch',
                'title_ar' => 'تمدد الرقبة الجانبي',
                'description' => 'Sit upright and slowly tilt your head toward your shoulder until you feel a gentle stretch, then switch sides.',
                'description_ar' => 'اجلس أو قف بقامة مستقيمة، وأمِل رأسك ببطء نحو كتفك حتى تشعر بشد خفيف، ثم بدل للجهة الأخرى.',
                'category' => 'neck',
                'duration_seconds' => 30,
                'image_path' => 'image/exercises/Neck/6.jpg',
            ],
            [
                'title' => 'Chin Tuck Exercise',
                'title_ar' => 'تحريك الذقن للداخل (Chin Tuck)',
                'description' => 'Pull your chin straight back as if making a double chin to correct forward head posture from screen use.',
                'description_ar' => 'اسحب ذقنك للوراء قليلاً وكأنك تصنع "ذقناً مزدوجاً" لإصلاح انحناء الرقبة للأمام أمام الشاشة.',
                'category' => 'neck',
                'duration_seconds' => 20,
                'image_path' => 'image/exercises/Neck/7.jpg',
            ],
            [
                'title' => 'Neck Tilt (Front & Back)',
                'title_ar' => 'إمالة الرقبة للأمام والخلف',
                'description' => 'Lower your chin gently toward your chest for a few seconds, then carefully tilt your head back to relieve upper spine tension.',
                'description_ar' => 'اخفض ذقنك بحذر نحو صدرك لمدة ثوانٍ، ثم ارفع رأسك بحذر للخلف لتخفيف تشنج الفقرات العلوية.',
                'category' => 'neck',
                'duration_seconds' => 25,
                'image_path' => '',
            ],
            [
                'title' => 'Slow Neck Rotations',
                'title_ar' => 'دوران الرقبة البطيء',
                'description' => 'Gently and slowly roll your head in a smooth circle to the right and left to loosen stiffness from long focus.',
                'description_ar' => 'أدر رأسك بحركة دائرية بطيئة وناعمة للغاية يميناً ثم يساراً لتفكيك تيبس الرقبة الناتج عن التركيز الطويل.',
                'category' => 'neck',
                'duration_seconds' => 30,
                'image_path' => 'image/exercises/Neck/9.jpg',
            ],

            // --- تصنيف الأيدي والأصابع (Hands) ---
            [
                'title' => 'Wrist Rotations for Coders',
                'title_ar' => 'تدوير المعصم (للكتابة المستمرة)',
                'description' => 'Rotate your wrists clockwise and counter-clockwise to prevent joint pain resulting from continuous coding.',
                'description_ar' => 'قم بتحريك معصميك بشكل دائري في اتجاه عقارب الساعة ثم عكسها لتجنب آلام المفاصل الناتجة عن الكودينغ.',
                'category' => 'hands',
                'duration_seconds' => 30,
                'image_path' => 'image/exercises/Hands/10.jpg',
            ],
            [
                'title' => 'Isometric Palm Press',
                'title_ar' => 'ضغط كف اليد (Isometric Palm Press)',
                'description' => 'Press your palms firmly together in front of your chest with moderate force for 10 seconds to relax hand muscles.',
                'description_ar' => 'اضغط براحة يديك ببعضهما البعض أمام صدرك بقوة متوسطة لمدة 10 ثوانٍ لتقوية وإرخاء عضلات اليدين.',
                'category' => 'hands',
                'duration_seconds' => 20,
                'image_path' => 'image/exercises/Hands/11.jpg',
            ],
            [
                'title' => 'Finger Stretch & Spread',
                'title_ar' => 'إطالة الأصابع وبسطها',
                'description' => 'Spread your fingers as far apart as possible with force, then relax and repeat to stimulate hand nerves.',
                'description_ar' => 'افرد أصابع يديك لأقصى مدى ممكن وكأنك تبعدها عن بعضها بقوة، ثم استرخِ وكرر العملية لتنشيط الأعصاب.',
                'category' => 'hands',
                'duration_seconds' => 15,
                'image_path' => 'image/exercises/Hands/12.jpg',
            ],
            [
                'title' => 'Wrist Flex & Extension',
                'title_ar' => 'إطالة المعصم لأعلى ولأسفل',
                'description' => 'Extend your arm forward with fingers up, gently pull them back with your other hand, then point fingers down and repeat.',
                'description_ar' => 'امدد ذراعك للأمام واجعل أصابعك تشير للأعلى واسحبها برفق بيدك الأخرى، ثم اجعلها تشير للأسفل وكرر الشد.',
                'category' => 'hands',
                'duration_seconds' => 25,
                'image_path' => 'image/exercises/Hands/13.jpg',
            ],

            // --- تصنيف الظهر والكتفين (Back) ---
            [
                'title' => 'Back and Shoulder Alignment',
                'title_ar' => 'تمرين تقويم الظهر والكتفين',
                'description' => 'Stand and clasp your hands behind your back, pulling your shoulders back firmly to counteract slouching.',
                'description_ar' => 'قف واشبك يديك خلف ظهرك واسحب كتفيك للخلف بقوة لإلغاء انحناءة الجلوس على الكرسي.',
                'category' => 'back',
                'duration_seconds' => 30,
                'image_path' => 'image/exercises/Back_and_Sho/14.jpg',
            ],
            [
                'title' => 'Cross-Body Shoulder Stretch',
                'title_ar' => 'إطالة الكتفين والذراعين',
                'description' => 'Raise your arm across your chest and pull it gently with the other hand to relieve upper shoulder tension.',
                'description_ar' => 'ارفع ذراعك أمام صدرك واسحبه باليد الأخرى باتجاه جسمك لتخفيف توتر الكتف العلوي.',
                'category' => 'back',
                'duration_seconds' => 20,
                'image_path' => 'image/exercises/Back_and_Sho/15.jpg',
            ],
            [
                'title' => 'Backward Shoulder Rolls',
                'title_ar' => 'دوران الكتفين للوراء',
                'description' => 'Roll your shoulders slowly backward 10 times to open up the chest area and relieve back tightness.',
                'description_ar' => 'قم بتحريك كتفيك بحركات دائرية بطيئة باتجاه الخلف 10 مرات لفتح منطقة الصدر وإزالة تشنج الظهر.',
                'category' => 'back',
                'duration_seconds' => 25,
                'image_path' => 'image/exercises/Back_and_Sho/16.jpg',
            ],
            [
                'title' => 'Vertical Torso Stretch',
                'title_ar' => 'مد الجسم لأعلى (السحب العمودي)',
                'description' => 'Interlock your fingers and lift them above your head, stretching your torso upward as much as possible.',
                'description_ar' => 'شبك أصابع يديك واعتمد على رفعها فوق رأسك مع مد جذعك لأعلى قدر الإمكان لإطالة فقرات الظهر.',
                'category' => 'back',
                'duration_seconds' => 25,
                'image_path' => 'image/exercises/Back_and_Sho/17.jpg',
            ],
            [
                'title' => 'Seated Torso Twist',
                'title_ar' => 'تدوير الجذع أثناء الجلوس',
                'description' => 'Twist your torso right and left while seated using the chair armrest to relieve lower back stiffness.',
                'description_ar' => 'أدر جذعك وأنت على الكرسي يميناً ويساراً مستعيناً بمسند الكرسي لتخفيف التشنجات أسفل الظهر.',
                'category' => 'back',
                'duration_seconds' => 20,
                'image_path' => 'image/exercises/Back_and_Sho/18.jpg',
            ],

            // --- تصنيف الساقين (Legs) ---
            [
                'title' => 'Ankle Rotations',
                'title_ar' => 'تحريك الكاحلين والقدمين',
                'description' => 'Lift your feet off the floor and rotate your ankles in circles to boost circulation and prevent numbness.',
                'description_ar' => 'قم برفع قدميك عن الأرض وحرك كاحليك بشكل دائري لتنشيط الدورة الدموية في الساقين وتجنب الخدر.',
                'category' => 'legs',
                'duration_seconds' => 30,
                'image_path' => 'image/exercises/Legs/19.jpg',
            ],
            [
                'title' => 'Seated Heel Raises',
                'title_ar' => 'رفع كعب القدمين جلوساً',
                'description' => 'Raise your heels while keeping toes on the floor, then lower them to activate calf muscles and blood flow.',
                'description_ar' => 'ارفع كعب قدميك عن الأرض مع إبقاء أصابعهما ثابتة، ثم اخفضهما لتشغيل عضلات الساق وتحريك الدورة الدموية.',
                'category' => 'legs',
                'duration_seconds' => 30,
                'image_path' => 'image/exercises/Legs/20.jpg',
            ],
            [
                'title' => 'Calf Muscle Stretch',
                'title_ar' => 'إطالة عضلات الساق الخلفية (Calf Stretch)',
                'description' => 'Stand and step one foot back keeping your heel flat on the floor for a comforting leg release.',
                'description_ar' => 'قف وادفع إحدى قدميك للخلف مع الحفاظ على كعبك ملامساً للأرض لشعور بالراحة في الساق.',
                'category' => 'legs',
                'duration_seconds' => 30,
                'image_path' => 'image/exercises/Legs/21.jpg',
            ],
            [
                'title' => 'Seated Leg Extensions',
                'title_ar' => 'بسط الركبة ومد الساق',
                'description' => 'Sit and straighten one leg horizontally for a few seconds, lower it, and switch to keep knees active.',
                'description_ar' => 'اجلس وافرد إحدى ساقيك بشكل أفقية تماماً أمامك لمدة ثوانٍ ثم أنزلها، وبدل بين الساقين لتنشيط الركبة.',
                'category' => 'legs',
                'duration_seconds' => 25,
                'image_path' => 'image/exercises/Legs/22.jpg',
            ],

            // --- تصنيف الاسترخاء والذهن (Relaxation) ---
            [
                'title' => 'Deep Breathing Relaxation',
                'title_ar' => 'التنفس العميق لإرخاء الأعصاب',
                'description' => 'Take a deep breath from your belly for 4 seconds, hold for 4 seconds, and exhale slowly to reduce coding stress.',
                'description_ar' => 'خذ نفساً عميقاً من بطنك لمدة 4 ثوانٍ، احبسه لـ 4 ثوانٍ، ثم أخرجه ببطء لتقليل التوتر البرمجي.',
                'category' => 'relaxation',
                'duration_seconds' => 60,
                'image_path' => 'image/exercises/Relax/23.jpg',
            ],
            [
                'title' => 'Box Breathing Technique',
                'title_ar' => 'تمرين التنفس المربع (Box Breathing)',
                'description' => 'Inhale 4s -> Hold 4s -> Exhale 4s -> Hold 4s for complete mental clarity and focus.',
                'description_ar' => 'شهيق 4 ثوانٍ -> حبس النفس 4 ثوانٍ -> زفير 4 ثوانٍ -> حبس الفراغ 4 ثوانٍ لصفاء الذهن التام.',
                'category' => 'relaxation',
                'duration_seconds' => 45,
                'image_path' => 'image/exercises/Relax/24.jpg',
            ],
            [
                'title' => 'Conscious Muscle Unclench',
                'title_ar' => 'إرخاء عضلات الكتفين والرقبة الواعي',
                'description' => 'Shrug your shoulders up tightly toward your ears for 3 seconds then drop them suddenly to release stored tension.',
                'description_ar' => 'ارفع كتفيك لأعلى نحو أذنيك بقوة لـ 3 ثوانٍ ثم أسقطهما مفاجأة للأسفل لتفريغ التوتر المخزن.',
                'category' => 'relaxation',
                'duration_seconds' => 20,
                'image_path' => 'image/exercises/Relax/25.jpg',
            ],

            // --- تصنيف الرأس والوجه (Head) ---
            [
                'title' => 'Temple & Face Massage',
                'title_ar' => 'تدليك الصدغين والوجه',
                'description' => 'Use your fingertips to make gentle circular motions on your temples to relieve screen headaches.',
                'description_ar' => 'استخدم أطراف أصابعك لعمل حركات دائرية خفيفة على جانبي الرأس (الصدغين) لتخفيف صداع الشاشة.',
                'category' => 'head',
                'duration_seconds' => 20,
                'image_path' => 'image/exercises/Head/26.jpg',
            ],
            [
                'title' => 'Jaw Tension Release',
                'title_ar' => 'تفريغ توتر الفك والأسنان',
                'description' => 'Part your teeth, relax your jaw muscles completely, and take a deep breath to relieve stress during debugging.',
                'description_ar' => 'افرِق بين أسنانك وأرخِ عضلات فكك تماماً وخذ نفساً عميقاً لتخفيف الضغط العصبي أثناء الديباغينغ.',
                'category' => 'head',
                'duration_seconds' => 15,
                'image_path' => 'image/exercises/Head/27.jpg',
            ]
        ];

        foreach ($exercises as $data) {
            Exercise::create($data);
        }
    }
}
