<?php
namespace Database\Seeders;

use App\Models\LandingTestimonial;
use App\Models\LandingTestimonialSection;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class LandingNewSectionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'landing testimonial read',   'category' => 'Landing Page'],
            ['name' => 'landing testimonial create', 'category' => 'Landing Page'],
            ['name' => 'landing testimonial edit',   'category' => 'Landing Page'],
            ['name' => 'landing testimonial delete', 'category' => 'Landing Page'],
            ['name' => 'landing contact read',       'category' => 'Landing Page'],
            ['name' => 'landing contact delete',     'category' => 'Landing Page'],
        ];
        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }

        LandingTestimonialSection::firstOrCreate([], [
            'subtitle' => [
                'ar' => 'آراء عملائنا',
                'en' => 'Client Reviews',
            ],
            'headline' => [
                ['title' => ['ar' => 'ما ', 'en' => 'What '], 'check' => 0],
                ['title' => ['ar' => 'يقوله', 'en' => 'Our Clients'], 'check' => 1],
                ['title' => ['ar' => ' عملاؤنا', 'en' => ' Say'], 'check' => 0],
            ],
            'bg_image'  => null,
            'is_active' => true,
        ]);

        $testimonials = [
            [
                'name' => ['ar' => 'أحمد محمد', 'en' => 'Ahmed Mohamed'],
                'designation' => ['ar' => 'مدير شركة', 'en' => 'Company Manager'],
                'review' => [
                    'ar' => 'نظام CRM هذا غيّر طريقة إدارة أعمالنا تماماً. أصبحنا قادرين على متابعة عملائنا بشكل أفضل وزيادة مبيعاتنا بنسبة 40%.',
                    'en' => 'This CRM system completely changed how we manage our business. We can now track our customers better and increased our sales by 40%.',
                ],
                'rating' => 5,
                'sort_order' => 1,
            ],
            [
                'name' => ['ar' => 'فاطمة علي', 'en' => 'Fatma Ali'],
                'designation' => ['ar' => 'مديرة مبيعات', 'en' => 'Sales Manager'],
                'review' => [
                    'ar' => 'واجهة النظام سهلة الاستخدام جداً. فريق الدعم فني رائع وسريع الاستجابة. أنصح به أي شركة تريد تطوير أعمالها.',
                    'en' => 'The system interface is very easy to use. The support team is excellent and responsive. I recommend it to any company looking to grow.',
                ],
                'rating' => 5,
                'sort_order' => 2,
            ],
            [
                'name' => ['ar' => 'محمد خليل', 'en' => 'Mohamed Khalil'],
                'designation' => ['ar' => 'رائد أعمال', 'en' => 'Entrepreneur'],
                'review' => [
                    'ar' => 'الخطط السعرية مرنة وتناسب جميع أحجام الشركات. النظام آمن جداً ونتائجه واضحة في زيادة الإنتاجية.',
                    'en' => 'The pricing plans are flexible and suit all company sizes. The system is very secure with clear productivity results.',
                ],
                'rating' => 5,
                'sort_order' => 3,
            ],
        ];
        foreach ($testimonials as $t) {
            LandingTestimonial::firstOrCreate(
                ['sort_order' => $t['sort_order']],
                array_merge($t, ['image' => null, 'is_active' => true])
            );
        }
    }
}
