<?php
namespace Database\Seeders;

use App\Models\LandingAbout;
use App\Models\LandingBlogPost;
use App\Models\LandingTeamMember;
use App\Models\LandingTestimonial;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class LandingNewSectionsSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Permissions =====
        $permissions = [
            ['name' => 'landing testimonial read',   'category' => 'Landing Page'],
            ['name' => 'landing testimonial create', 'category' => 'Landing Page'],
            ['name' => 'landing testimonial edit',   'category' => 'Landing Page'],
            ['name' => 'landing testimonial delete', 'category' => 'Landing Page'],
            ['name' => 'landing about read',         'category' => 'Landing Page'],
            ['name' => 'landing about edit',         'category' => 'Landing Page'],
            ['name' => 'landing team read',          'category' => 'Landing Page'],
            ['name' => 'landing team create',        'category' => 'Landing Page'],
            ['name' => 'landing team edit',          'category' => 'Landing Page'],
            ['name' => 'landing team delete',        'category' => 'Landing Page'],
            ['name' => 'landing blog read',          'category' => 'Landing Page'],
            ['name' => 'landing blog create',        'category' => 'Landing Page'],
            ['name' => 'landing blog edit',          'category' => 'Landing Page'],
            ['name' => 'landing blog delete',        'category' => 'Landing Page'],
            ['name' => 'landing contact read',       'category' => 'Landing Page'],
            ['name' => 'landing contact delete',     'category' => 'Landing Page'],
        ];
        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }

        // ===== About =====
        LandingAbout::firstOrCreate([], [
            'subtitle_en'   => 'ABOUT US',
            'subtitle_ar'   => 'من نحن',
            'title_en'      => 'The <span>Smart CRM</span> Built for Growing Businesses',
            'title_ar'      => 'نظام CRM <span>ذكي</span> مصمم لنمو أعمالك',
            'description_en'=> 'CRM Innovation helps your team manage contacts, close deals faster, and deliver outstanding customer experiences — all from one powerful platform.',
            'description_ar'=> 'يساعد CRM Innovation فريقك على إدارة جهات الاتصال وإغلاق الصفقات بشكل أسرع وتقديم تجارب عملاء استثنائية — كل ذلك من منصة واحدة قوية.',
            'box1_icon'     => 'fa-bullseye',
            'box1_title_en' => 'Our Mission',
            'box1_title_ar' => 'مهمتنا',
            'box1_desc_en'  => 'Empower businesses of all sizes with intelligent CRM tools that save time and accelerate growth.',
            'box1_desc_ar'  => 'تمكين الشركات بجميع أحجامها بأدوات CRM ذكية توفر الوقت وتسرّع النمو.',
            'box2_icon'     => 'fa-rocket',
            'box2_title_en' => 'Our Vision',
            'box2_title_ar' => 'رؤيتنا',
            'box2_desc_en'  => 'Become the leading CRM platform in the region, trusted by thousands of growing companies.',
            'box2_desc_ar'  => 'أن نصبح منصة CRM الرائدة في المنطقة، موثوقة من آلاف الشركات النامية.',
            'is_active'     => true,
        ]);

        // ===== Testimonials =====
        $testimonials = [
            ['name' => 'Ahmed Al-Rashid',  'designation_en' => 'CEO, TechStart', 'designation_ar' => 'المدير التنفيذي، TechStart',
             'review_en' => 'CRM Innovation transformed how we manage our client relationships. Our sales team closed 40% more deals in the first quarter.', 'review_ar' => 'غيّر CRM Innovation طريقة إدارتنا لعلاقات العملاء. أغلق فريق المبيعات لدينا 40% من الصفقات أكثر في الربع الأول.', 'rating' => 5, 'sort_order' => 1],
            ['name' => 'Sara Mahmoud',    'designation_en' => 'Sales Director, GrowthCo', 'designation_ar' => 'مديرة المبيعات، GrowthCo',
             'review_en' => 'The intuitive interface and powerful reporting made our daily work much easier. Highly recommended for any growing business.', 'review_ar' => 'الواجهة السهلة والتقارير القوية جعلت عملنا اليومي أسهل بكثير. أنصح به لأي عمل تجاري في طور النمو.', 'rating' => 5, 'sort_order' => 2],
            ['name' => 'Omar Khalil',     'designation_en' => 'Founder, NextWave', 'designation_ar' => 'المؤسس، NextWave',
             'review_en' => 'From contact management to invoice generation, everything we need is in one place. Our team productivity increased by 60%.', 'review_ar' => 'من إدارة جهات الاتصال إلى إنشاء الفواتير، كل ما نحتاجه في مكان واحد. زادت إنتاجية فريقنا بنسبة 60%.', 'rating' => 5, 'sort_order' => 3],
        ];
        foreach ($testimonials as $t) {
            LandingTestimonial::firstOrCreate(['name' => $t['name']], $t);
        }

        // ===== Team Members =====
        $team = [
            ['name_en' => 'Mohammed Al-Sayed', 'name_ar' => 'محمد السيد',   'role_en' => 'Chief Executive Officer',  'role_ar' => 'الرئيس التنفيذي',   'sort_order' => 1],
            ['name_en' => 'Fatima Hassan',      'name_ar' => 'فاطمة حسن',     'role_en' => 'Head of Product',          'role_ar' => 'رئيسة المنتج',       'sort_order' => 2],
            ['name_en' => 'Khalid Al-Omar',     'name_ar' => 'خالد العمر',    'role_en' => 'Lead Developer',           'role_ar' => 'كبير المطورين',      'sort_order' => 3],
        ];
        foreach ($team as $m) {
            LandingTeamMember::firstOrCreate(['name_en' => $m['name_en']], array_merge($m, ['is_active' => true]));
        }

        // ===== Blog Posts =====
        $posts = [
            ['title_en' => 'How CRM Can Double Your Sales Team Performance', 'title_ar' => 'كيف يمكن لـ CRM أن يضاعف أداء فريق المبيعات', 'excerpt_en' => 'Discover the key strategies that top sales teams use to leverage CRM data for closing more deals.', 'excerpt_ar' => 'اكتشف الاستراتيجيات الرئيسية التي تستخدمها فرق المبيعات الأفضل للاستفادة من بيانات CRM لإغلاق المزيد من الصفقات.', 'author_name' => 'Ahmed Al-Rashid', 'is_published' => true, 'published_at' => now()->subDays(5),  'sort_order' => 1],
            ['title_en' => 'Top 5 Features Every CRM Should Have in 2025',  'title_ar' => 'أهم 5 ميزات يجب أن يمتلكها كل CRM في 2025',      'excerpt_en' => 'From AI-powered insights to seamless integrations, here is what to look for in a modern CRM.', 'excerpt_ar' => 'من الرؤى المدعومة بالذكاء الاصطناعي إلى التكاملات السلسة، هذا ما تبحث عنه في CRM حديث.',                'author_name' => 'Fatima Hassan',    'is_published' => true, 'published_at' => now()->subDays(12), 'sort_order' => 2],
            ['title_en' => 'Building Customer Loyalty Through Better Data',  'title_ar' => 'بناء ولاء العملاء من خلال بيانات أفضل',           'excerpt_en' => 'Learn how data-driven customer management strategies lead to longer, more profitable relationships.', 'excerpt_ar' => 'تعلم كيف تؤدي استراتيجيات إدارة العملاء المبنية على البيانات إلى علاقات أطول وأكثر ربحية.',      'author_name' => 'Khalid Al-Omar',   'is_published' => true, 'published_at' => now()->subDays(20), 'sort_order' => 3],
        ];
        foreach ($posts as $post) {
            LandingBlogPost::firstOrCreate(['title_en' => $post['title_en']], $post);
        }
    }
}
