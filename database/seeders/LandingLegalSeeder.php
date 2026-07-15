<?php

namespace Database\Seeders;

use App\Models\LandingLegalPage;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class LandingLegalSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'landing legal read', 'category' => 'Landing Page'],
            ['name' => 'landing legal edit', 'category' => 'Landing Page'],
        ] as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }

        LandingLegalPage::updateOrCreate(
            ['type' => LandingLegalPage::TYPE_PRIVACY],
            [
                'title' => [
                    'ar' => 'سياسة الخصوصية',
                    'en' => 'Privacy Policy',
                ],
                'content' => [
                    'ar' => "نحن في CRM Innovation نلتزم بحماية خصوصيتك. توضح هذه السياسة كيفية جمع واستخدام وحماية بياناتك الشخصية عند استخدام موقعنا وخدماتنا.\n\n1. البيانات التي نجمعها: الاسم، البريد الإلكتروني، رقم الهاتف، وبيانات الاستخدام.\n2. كيفية الاستخدام: لتقديم الخدمة، تحسين التجربة، والتواصل معك.\n3. حماية البيانات: نستخدم إجراءات أمنية تقنية وإدارية لحماية معلوماتك.\n4. حقوقك: يمكنك طلب الوصول أو التعديل أو حذف بياناتك عبر التواصل معنا.",
                    'en' => "At CRM Innovation, we are committed to protecting your privacy. This policy explains how we collect, use, and safeguard your personal data when you use our website and services.\n\n1. Data we collect: name, email, phone number, and usage data.\n2. How we use it: to provide the service, improve experience, and communicate with you.\n3. Data protection: we use technical and administrative security measures.\n4. Your rights: you may request access, correction, or deletion of your data by contacting us.",
                ],
                'is_active' => true,
            ]
        );

        LandingLegalPage::updateOrCreate(
            ['type' => LandingLegalPage::TYPE_TERMS],
            [
                'title' => [
                    'ar' => 'الشروط والأحكام',
                    'en' => 'Terms & Conditions',
                ],
                'content' => [
                    'ar' => "باستخدامك لموقع وخدمات CRM Innovation، فإنك توافق على الشروط والأحكام التالية.\n\n1. الخدمة: نوفر منصة CRM سحابية وفق الباقة المختارة.\n2. الحساب: أنت مسؤول عن سرية بيانات الدخول ونشاط حسابك.\n3. الاستخدام المقبول: يُمنع إساءة استخدام المنصة أو محاولة اختراقها.\n4. الاشتراك والدفع: تخضع الباقات لشروط التجديد والإلغاء المعروضة عند التسجيل.\n5. التعديل: نحتفظ بحق تحديث هذه الشروط مع إشعار المستخدمين عند الحاجة.",
                    'en' => "By using the CRM Innovation website and services, you agree to the following terms and conditions.\n\n1. Service: we provide a cloud CRM platform according to your selected plan.\n2. Account: you are responsible for keeping your login credentials secure.\n3. Acceptable use: misuse or unauthorized access attempts are prohibited.\n4. Subscription & billing: plans are subject to renewal and cancellation terms shown at signup.\n5. Changes: we may update these terms and notify users when required.",
                ],
                'is_active' => true,
            ]
        );
    }
}
