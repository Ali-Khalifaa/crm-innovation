<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * بيانات تجريبية مصرية كاملة لجميع شاشات الـ CRM
 * تشغيل: php artisan db:seed --class=EgyptianDemoDataSeeder
 */
class EgyptianDemoDataSeeder extends Seeder
{
    private int $tenantId;
    private int $ownerId;
    private int $adminId;
    private int $memberId;
    private array $stageIds   = [];
    private array $companyIds = [];
    private array $contactIds = [];
    private array $dealIds    = [];
    private array $productIds = [];

    public function run(): void
    {
        $tenant = DB::table('tenants')->where('slug', 'demo')->first();

        if (! $tenant) {
            $this->command->error('Demo tenant not found. Run DemoTenantSeeder first.');
            return;
        }

        $this->tenantId = $tenant->id;

        $this->command->info('🌍  بدء إنشاء البيانات التجريبية المصرية...');

        $this->cleanOldData();
        $this->setupUsers();
        $this->loadStages();
        $this->seedProducts();
        $this->seedCompanies();
        $this->seedContacts();
        $this->seedDeals();
        $this->seedTasks();
        $this->seedInvoices();
        $this->seedMeetings();
        $this->seedCalls();
        $this->seedNotifications();

        $this->command->info('✅  تم إنشاء البيانات التجريبية المصرية بنجاح!');
        $this->command->table(
            ['الحساب', 'البريد الإلكتروني', 'كلمة المرور', 'الدور'],
            [
                ['مصطفى البدوي (المالك)', env('CRM_DEMO_EMAIL', 'demo@crm-innovation.com'), env('CRM_DEMO_PASSWORD', 'Demo@123456'), 'owner'],
                ['مريم أحمد',    'mariam.ahmed@nova-tech.eg',  'Demo@123456', 'admin'],
                ['خالد السيد',   'khaled.sayed@nova-tech.eg',  'Demo@123456', 'member'],
            ]
        );
    }

    /* ══════════════════════════════════════════════
     *  Clean
     * ══════════════════════════════════════════════ */
    private function cleanOldData(): void
    {
        $tid = $this->tenantId;

        // Delete in dependency order
        DB::table('crm_activities')    ->where('tenant_id', $tid)->delete();
        DB::table('crm_notifications') ->where('tenant_id', $tid)->delete();
        DB::table('calls')             ->where('tenant_id', $tid)->delete();
        DB::table('meetings')          ->where('tenant_id', $tid)->delete();
        DB::table('invoice_payments')  ->where('tenant_id', $tid)->delete();

        $invoiceIds = DB::table('invoices')->where('tenant_id', $tid)->pluck('id');
        DB::table('invoice_items')->whereIn('invoice_id', $invoiceIds)->delete();
        DB::table('invoices')    ->where('tenant_id', $tid)->delete();

        DB::table('tasks')       ->where('tenant_id', $tid)->delete();
        DB::table('deals')       ->where('tenant_id', $tid)->delete();
        DB::table('contacts')    ->where('tenant_id', $tid)->delete();
        DB::table('companies')   ->where('tenant_id', $tid)->delete();
        DB::table('products')    ->where('tenant_id', $tid)->delete();

        // Remove extra users (keep the owner)
        $owner = DB::table('users')
            ->where('tenant_id', $tid)
            ->where('role', 'owner')
            ->first();

        DB::table('users')
            ->where('tenant_id', $tid)
            ->when($owner, fn($q) => $q->where('id', '!=', $owner->id))
            ->delete();

        $this->ownerId = $owner?->id ?? 0;

        // Update owner name to Egyptian
        if ($this->ownerId) {
            DB::table('users')->where('id', $this->ownerId)->update([
                'name'       => 'مصطفى البدوي',
                'title'      => 'الرئيس التنفيذي',
                'phone'      => '+20 10 0000 1234',
                'updated_at' => now(),
            ]);
        }

        $this->command->info('  🗑️  تم مسح البيانات القديمة.');
    }

    /* ══════════════════════════════════════════════
     *  Users
     * ══════════════════════════════════════════════ */
    private function setupUsers(): void
    {
        $this->adminId = DB::table('users')->insertGetId([
            'tenant_id'  => $this->tenantId,
            'name'       => 'مريم أحمد',
            'email'      => 'mariam.ahmed@nova-tech.eg',
            'password'   => Hash::make('Demo@123456'),
            'role'       => 'admin',
            'title'      => 'مديرة المبيعات',
            'phone'      => '+20 11 1234 5678',
            'status'     => 'active',
            'created_at' => now()->subMonths(5),
            'updated_at' => now(),
        ]);

        $this->memberId = DB::table('users')->insertGetId([
            'tenant_id'  => $this->tenantId,
            'name'       => 'خالد السيد',
            'email'      => 'khaled.sayed@nova-tech.eg',
            'password'   => Hash::make('Demo@123456'),
            'role'       => 'member',
            'title'      => 'مندوب مبيعات',
            'phone'      => '+20 12 9876 5432',
            'status'     => 'active',
            'created_at' => now()->subMonths(3),
            'updated_at' => now(),
        ]);

        $this->command->info('  👤  تم إنشاء المستخدمين.');
    }

    /* ══════════════════════════════════════════════
     *  Stages
     * ══════════════════════════════════════════════ */
    private function loadStages(): void
    {
        $stages = DB::table('deal_stages')
            ->where('tenant_id', $this->tenantId)
            ->orderBy('order')
            ->pluck('id', 'name');

        // Map names flexibly
        $map = [
            'lead'         => $stages['Lead']        ?? null,
            'qualified'    => $stages['Qualified']   ?? null,
            'proposal'     => $stages['Proposal']    ?? null,
            'negotiation'  => $stages['Negotiation'] ?? null,
            'won'          => $stages['Closed Won']  ?? null,
            'lost'         => $stages['Closed Lost'] ?? null,
        ];

        // Fallback: grab by order if names differ
        $all = DB::table('deal_stages')
            ->where('tenant_id', $this->tenantId)
            ->orderBy('order')->get();

        $keys = ['lead','qualified','proposal','negotiation','won','lost'];
        foreach ($keys as $idx => $key) {
            $this->stageIds[$key] = $map[$key] ?? ($all[$idx]->id ?? null);
        }
    }

    /* ══════════════════════════════════════════════
     *  Products
     * ══════════════════════════════════════════════ */
    private function seedProducts(): void
    {
        $products = [
            ['نظام إدارة المخزون',        'تطبيق ويب متكامل لإدارة المخزون والمستودعات', 'INV-SYS-001', 'EGP', 18000,  'product',      true],
            ['تطبيق موبايل مخصص',         'تطوير تطبيق iOS وAndroid حسب متطلبات العميل', 'APP-MOB-001', 'EGP', 35000,  'service',      true],
            ['خدمة استضافة المواقع',       'استضافة سحابية بضمان تشغيل 99.9% + SSL',     'HOST-001',    'USD',   299,   'subscription', true],
            ['استشارات تقنية',             'جلسة استشارية متخصصة في التحول الرقمي',       'CONS-001',    'USD',   150,   'service',      true],
            ['تصميم الهوية البصرية',       'شعار + دليل هوية كامل + تصاميم وسائل التواصل','BRAND-001',   'EGP', 12000,  'service',      true],
            ['نظام نقطة البيع (POS)',       'نظام كاشير متكامل مع جهاز وبرنامج وتدريب',   'POS-HW-001',  'EGP', 22000,  'product',      true],
            ['خدمة النسخ الاحتياطي السحابي','نسخ احتياطي يومي تلقائي — تخزين حتى 1 TB',  'BACKUP-001',  'USD',    99,   'subscription', true],
            ['برنامج المحاسبة',            'محاسبة متكاملة مع ضريبة القيمة المضافة المصرية','ACC-ERP-001', 'EGP',  8500,  'product',      true],
        ];

        foreach ($products as [$name,$desc,$sku,$cur,$price,$type,$active]) {
            $this->productIds[] = DB::table('products')->insertGetId([
                'tenant_id'  => $this->tenantId,
                'name'        => $name,
                'description' => $desc,
                'sku'         => $sku,
                'currency'    => $cur,
                'unit_price'  => $price,
                'type'        => $type,
                'is_active'   => $active,
                'created_at'  => now()->subMonths(rand(1, 6)),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('  📦  تم إنشاء ' . count($this->productIds) . ' منتج.');
    }

    /* ══════════════════════════════════════════════
     *  Companies
     * ══════════════════════════════════════════════ */
    private function seedCompanies(): void
    {
        $companies = [
            [
                'name' => 'مجموعة النيل للاستثمار',
                'industry' => 'Finance',
                'website'  => 'https://nile-invest.eg',
                'phone'    => '+20 2 2580 1234',
                'email'    => 'info@nile-invest.eg',
                'address'  => 'برج النيل، كورنيش النيل، القاهرة',
                'employees_count' => 450,
                'annual_revenue'  => 85000000,
                'status'          => 'customer',
                'notes'           => 'عميل استراتيجي — شراكة منذ 2022. يهتمون بالتحول الرقمي لقطاعاتهم المختلفة.',
            ],
            [
                'name' => 'الشركة المصرية للتكنولوجيا',
                'industry' => 'Technology',
                'website'  => 'https://egytech.com.eg',
                'phone'    => '+20 2 3300 5678',
                'email'    => 'hello@egytech.com.eg',
                'address'  => 'مدينة نصر، القاهرة، الطابق الخامس',
                'employees_count' => 120,
                'annual_revenue'  => 22000000,
                'status'          => 'customer',
                'notes'           => 'شريك تقني — يعيدون بيع خدماتنا لعملائهم.',
            ],
            [
                'name' => 'الدلتا للتجارة والتوزيع',
                'industry' => 'Retail',
                'website'  => 'https://delta-trade.eg',
                'phone'    => '+20 55 340 2200',
                'email'    => 'sales@delta-trade.eg',
                'address'  => 'المنصورة، الدقهلية',
                'employees_count' => 280,
                'annual_revenue'  => 41000000,
                'status'          => 'prospect',
                'notes'           => 'عميل محتمل قوي — يبحثون عن نظام POS وإدارة مخزون متكامل.',
            ],
            [
                'name' => 'مصر للإنشاء والمقاولات',
                'industry' => 'Construction',
                'website'  => 'https://misr-construction.eg',
                'phone'    => '+20 2 4560 9900',
                'email'    => 'contact@misr-construction.eg',
                'address'  => '6 أكتوبر، الجيزة',
                'employees_count' => 1200,
                'annual_revenue'  => 320000000,
                'status'          => 'customer',
                'notes'           => 'من أكبر عملائنا — يستخدمون نظام المحاسبة ونقطة البيع.',
            ],
            [
                'name' => 'سيناء للسياحة والضيافة',
                'industry' => 'Tourism',
                'website'  => 'https://sinai-hospitality.eg',
                'phone'    => '+20 69 360 4455',
                'email'    => 'reservations@sinai-hospitality.eg',
                'address'  => 'شرم الشيخ، جنوب سيناء',
                'employees_count' => 340,
                'annual_revenue'  => 58000000,
                'status'          => 'prospect',
                'notes'           => 'مهتمون بنظام إدارة الحجوزات والتطبيق المتنقل.',
            ],
            [
                'name' => 'القاهرة لإدارة الأصول',
                'industry' => 'Finance',
                'website'  => 'https://cairo-assets.eg',
                'phone'    => '+20 2 7730 1100',
                'email'    => 'info@cairo-assets.eg',
                'address'  => 'وسط البلد، القاهرة',
                'employees_count' => 85,
                'annual_revenue'  => 15000000,
                'status'          => 'churned',
                'notes'           => 'توقف التواصل بعد تغيير الإدارة — فرصة إعادة تفعيل.',
            ],
        ];

        foreach ($companies as $c) {
            $this->companyIds[] = DB::table('companies')->insertGetId([
                'tenant_id'       => $this->tenantId,
                'name'            => $c['name'],
                'industry'        => $c['industry'],
                'website'         => $c['website'],
                'phone'           => $c['phone'],
                'email'           => $c['email'],
                'address'         => $c['address'],
                'employees_count' => $c['employees_count'],
                'annual_revenue'  => $c['annual_revenue'],
                'status'          => $c['status'],
                'notes'           => $c['notes'],
                'created_by'      => $this->ownerId,
                'created_at'      => now()->subMonths(rand(3, 14)),
                'updated_at'      => now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('  🏢  تم إنشاء ' . count($this->companyIds) . ' شركة.');
    }

    /* ══════════════════════════════════════════════
     *  Contacts
     * ══════════════════════════════════════════════ */
    private function seedContacts(): void
    {
        // lead_source enum: website | referral | social | cold | event | other
        // [first, last, email, phone, company_idx, status, lead_source, notes]
        $contacts = [
            ['محمد', 'الشافعي',  'm.shafei@nile-invest.eg',       '+20 10 1234 5001', 0, 'customer', 'referral', 'مدير التحول الرقمي — متحمس جداً لتبني الحلول التقنية، قرار الشراء بيده.'],
            ['فاطمة','عبد الرحمن','f.abdelrahman@egytech.com.eg',  '+20 11 2345 6002', 1, 'customer', 'website',  'مديرة المشاريع — تتابع التسليمات أسبوعياً، دقيقة جداً في المتطلبات.'],
            ['أحمد', 'حسن',      'a.hassan@delta-trade.eg',        '+20 12 3456 7003', 2, 'lead',     'cold',     'مدير المخازن — طلب عرض سعر لنظام POS + إدارة مخزون لـ 12 فرع.'],
            ['نورهان','مصطفى',   'noorhan@misr-construction.eg',   '+20 10 4567 8004', 3, 'customer', 'referral', 'المدير المالي — تتعامل مع فريق المحاسبة لدينا مباشرة.'],
            ['هشام', 'الجوهري',  'h.algohary@sinai-hospitality.eg','+20 12 5678 9005', 4, 'lead',     'social',   'مدير العمليات — تواصل عبر لينكد إن، يريد demo للتطبيق المتنقل.'],
            ['ريهام','السيد',    'r.sayed@cairo-assets.eg',        '+20 11 6789 0006', 5, 'inactive', 'website',  'مساعدة المدير التنفيذي — توقف التواصل منذ 4 أشهر، نجرب إعادة الاتصال.'],
            ['كريم', 'عبد العزيز','k.abdelaziz@nile-invest.eg',    '+20 10 7890 1007', 0, 'customer', 'referral', 'رئيس قسم تقنية المعلومات — يقيّم الحلول ويوصي بها للإدارة.'],
            ['سارة', 'خالد',     'sara.khaled@egytech.com.eg',     '+20 11 8901 2008', 1, 'customer', 'other',    'مهندسة تطبيقات — تتعامل مع فريقنا التقني في التكاملات.'],
            ['عمر',  'عبد الله', 'omar@delta-trade.eg',            '+20 12 9012 3009', 2, 'lead',     'event',    'نائب المدير التجاري — التقينا في معرض Cairo ICT، طلب مقارنة بين 3 حلول.'],
            ['منى',  'فتحي',     'mona.fathy@misr-construction.eg','+20 10 0123 4010', 3, 'customer', 'referral', 'مدير مشروع — تتابع مشروع توسعة نظام المحاسبة.'],
            ['طارق', 'رمضان',    'tarek.r@sinai-hospitality.eg',   '+20 11 1234 5011', 4, 'lead',     'website',  'مدير الحجوزات — ملأ نموذج التواصل على موقعنا، يبحث عن نظام حجوزات.'],
            ['ياسمين','نبيل',    'yasmin.nabil@nile-invest.eg',    '+20 12 2345 6012', 0, 'customer', 'referral', 'مدير إدارة المخاطر — عميل قديم، يجدد الاشتراك كل سنة بدون تفاوض.'],
            ['باسم', 'فؤاد',     'basem.fouad@egytech.com.eg',     '+20 10 3456 7013', 1, 'lead',     'cold',     'مطور برمجيات أول — محتاج مراجعة تقنية قبل رفع التوصية للإدارة.'],
            ['داليا','المنصوري', 'dalia@delta-trade.eg',           '+20 11 4567 8014', 2, 'customer', 'event',    'مدير التسويق — تستخدم لوحة التقارير يومياً، راضية جداً عن الخدمة.'],
            ['يوسف', 'الطيب',   'youssef.t@misr-construction.eg', '+20 12 5678 9015', 3, 'lead',     'social',   'مدير تطوير الأعمال — قابلنا في ندوة، يقيّم التوسع للفروع الجديدة.'],
        ];

        $assignees = [$this->ownerId, $this->adminId, $this->memberId];

        foreach ($contacts as $i => [$fn, $ln, $email, $phone, $coIdx, $status, $source, $notes]) {
            $this->contactIds[] = DB::table('contacts')->insertGetId([
                'tenant_id'   => $this->tenantId,
                'first_name'  => $fn,
                'last_name'   => $ln,
                'email'       => $email,
                'phone'       => $phone,
                'company'     => DB::table('companies')->find($this->companyIds[$coIdx])?->name ?? '',
                'company_id'  => $this->companyIds[$coIdx],
                'status'      => $status,
                'lead_source' => $source,
                'notes'       => $notes,
                'owner_id'    => $assignees[$i % 3],
                'created_by'  => $this->ownerId,
                'created_at'  => now()->subMonths(rand(1, 12)),
                'updated_at'  => now()->subDays(rand(0, 20)),
            ]);
        }

        $this->command->info('  👥  تم إنشاء ' . count($this->contactIds) . ' جهة اتصال.');
    }

    /* ══════════════════════════════════════════════
     *  Deals
     * ══════════════════════════════════════════════ */
    private function seedDeals(): void
    {
        $deals = [
            // [title, contact_idx, stage_key, value, currency, prob, status, assigned_idx, notes, close_date_offset_days]
            ['نظام إدارة مخزون — مجموعة النيل',      0,  'proposal',    85000,  'EGP', 70, 'open', 0, 'يريدون نشر النظام على 8 مستودعات. في انتظار عرض السعر النهائي.',       30],
            ['تطبيق موبايل — الشركة المصرية للتك',   1,  'negotiation', 45000,  'EGP', 85, 'open', 1, 'اتفقنا على المواصفات. المفاوضة على التسعير وجدول التسليم.',            15],
            ['نظام POS للدلتا — 12 فرع',             2,  'qualified',   264000, 'EGP', 40, 'open', 2, 'طلب عرض سعر لـ 12 فرع. يقارن مع منافسين آخرين.',                     45],
            ['تجديد عقد استضافة — مصر للإنشاء',      3,  'won',           3588, 'USD', 100,'won',  0, 'تم التجديد السنوي. العميل راضٍ جداً عن الخدمة.', -10],
            ['تطبيق حجوزات — سيناء للسياحة',         4,  'lead',        120000, 'EGP', 20, 'open', 1, 'في مرحلة استكشاف — لم يحددوا الميزانية بعد.',                         90],
            ['استشارات تقنية — القاهرة لإدارة الأصول',5, 'lost',          4500, 'USD',  0, 'lost', 2, 'فضّلوا حلاً داخلياً. السبب: قيود الميزانية الجديدة.', -30],
            ['هوية بصرية + موقع — مجموعة النيل',     6,  'won',         28000,  'EGP', 100,'won',  0, 'تم التسليم في الوقت المحدد. العميل سعيد جداً.',                       -20],
            ['برنامج محاسبة — الدلتا للتجارة',       8,  'proposal',    34000,  'EGP', 60, 'open', 1, 'اجتماع عرض الأسبوع القادم. محتاجين customization للضريبة.',           20],
            ['نسخ احتياطي سحابي — الشركة المصرية',   7,  'negotiation',  1188, 'USD', 90, 'open', 2, 'متفقون على المواصفات — يناقشون العقد السنوي مقابل الشهري.',             7],
            ['نظام POS — مصر للإنشاء (توسعة)',       9,  'qualified',   58000,  'EGP', 55, 'open', 0, 'توسعة النظام الحالي لـ 4 فروع جديدة.',                               35],
            ['استشارات + نظام تقارير — سيناء',       10, 'lead',        22000,  'EGP', 15, 'open', 1, 'في مرحلة تحديد الاحتياجات مع مدير العمليات.',                        60],
            ['تجديد + توسعة — مجموعة النيل',         11, 'won',         54000,  'EGP', 100,'won',  0, 'تجديد العقد الرئيسي + إضافة خدمات جديدة.',                           -5],
        ];

        $assignees = [$this->ownerId, $this->adminId, $this->memberId];

        foreach ($deals as $i => [$title,$cIdx,$stage,$value,$cur,$prob,$status,$aIdx,$notes,$closeOffset]) {
            $stageId = $this->stageIds[$stage] ?? $this->stageIds['lead'];
            $closedAt = ($status === 'won' || $status === 'lost') ? now()->addDays($closeOffset) : null;

            $this->dealIds[] = DB::table('deals')->insertGetId([
                'tenant_id'           => $this->tenantId,
                'title'               => $title,
                'contact_id'          => $this->contactIds[$cIdx] ?? null,
                'stage_id'            => $stageId,
                'value'               => $value,
                'currency'            => $cur,
                'probability'         => $prob,
                'expected_close_date' => now()->addDays($closeOffset)->format('Y-m-d'),
                'assigned_to'         => $assignees[$aIdx],
                'status'              => $status,
                'notes'               => $notes,
                'closed_at'           => $closedAt,
                'lost_reason'         => $status === 'lost' ? 'no_budget' : null,
                'created_at'          => now()->subMonths(rand(1, 8)),
                'updated_at'          => now()->subDays(rand(0, 10)),
            ]);
        }

        $this->command->info('  💼  تم إنشاء ' . count($this->dealIds) . ' صفقة.');
    }

    /* ══════════════════════════════════════════════
     *  Tasks
     * ══════════════════════════════════════════════ */
    private function seedTasks(): void
    {
        $assignees = [$this->ownerId, $this->adminId, $this->memberId];

        $tasks = [
            // [title, priority, status, assigned_idx, due_offset_days, taskable: 'c'|'d', idx, description]
            ['إرسال عرض السعر لنظام المخزون',      'high',   'pending',     0,  2, 'd', 0, 'إرسال العرض التفصيلي لمحمد الشافعي — يشمل التسعير ومخطط التنفيذ والجدول الزمني.'],
            ['متابعة تفاوض عقد التطبيق',           'high',   'in_progress', 1,  1, 'd', 1, 'الاتصال بفاطمة عبد الرحمن لمناقشة شروط العقد النهائية وتواريخ التسليم.'],
            ['تجهيز demo نظام POS للدلتا',          'medium', 'pending',     2,  5, 'd', 2, 'إعداد بيئة تجريبية كاملة لعرض نظام نقطة البيع أمام فريق الدلتا.'],
            ['إصدار فاتورة تجديد الاستضافة',       'low',    'completed',   0, -3, 'd', 3, 'إصدار فاتورة التجديد السنوي وإرسالها للمدير المالي.'],
            ['تحليل متطلبات تطبيق الحجوزات',       'medium', 'in_progress', 1,  7, 'c', 4, 'جمع متطلبات هشام الجوهري لتطبيق الحجوزات وإعداد وثيقة المتطلبات.'],
            ['الاتصال بريهام للتعرف على أسباب التوقف','high', 'pending',    2, -1, 'c', 5, 'إعادة التواصل مع ريهام السيد لفهم أسباب التوقف وطرح حلول جديدة.'],
            ['اجتماع مراجعة مشروع الهوية البصرية', 'low',    'completed',   0,-15, 'd', 6, 'اجتماع التسليم النهائي وعرض الملفات للعميل والحصول على التوقيع.'],
            ['إعداد عرض تقني لبرنامج المحاسبة',    'medium', 'pending',     1,  4, 'd', 7, 'تجهيز عرض تقني تفصيلي يوضح كيفية التعامل مع ضريبة القيمة المضافة المصرية.'],
            ['مراجعة عقد الاستضافة السحابية',      'high',   'in_progress', 2,  3, 'd', 8, 'مراجعة بنود العقد مع فريق قانوني قبل الإرسال للعميل.'],
            ['تقرير شهري — المبيعات والتوقعات',    'medium', 'pending',     0,  6, 'c', 0, 'إعداد تقرير شهري لأداء المبيعات وتوقعات الربع القادم.'],
            ['متابعة صفقة توسعة POS مصر للإنشاء', 'high',   'pending',     1,  2, 'd', 9, 'التنسيق مع منى فتحي لتحديد المواقع الجديدة وجدول التركيب.'],
            ['تحديث بيانات العملاء في النظام',     'low',    'completed',   2, -7, 'c', 5, 'مراجعة وتحديث بيانات جميع جهات الاتصال بعد آخر جولة تواصل.'],
        ];

        foreach ($tasks as $i => [$title,$priority,$status,$aIdx,$dueOffset,$type,$idx,$desc]) {
            $morphType = $type === 'c'
                ? 'Modules\\Contacts\\Models\\Contact'
                : 'Modules\\Deals\\Models\\Deal';
            $morphId = $type === 'c'
                ? ($this->contactIds[$idx] ?? $this->contactIds[0])
                : ($this->dealIds[$idx]    ?? $this->dealIds[0]);

            DB::table('tasks')->insert([
                'tenant_id'      => $this->tenantId,
                'title'          => $title,
                'description'    => $desc,
                'priority'       => $priority,
                'status'         => $status,
                'due_date'       => now()->addDays($dueOffset)->format('Y-m-d'),
                'assigned_to'    => [$this->ownerId, $this->adminId, $this->memberId][$aIdx],
                'taskable_type'  => $morphType,
                'taskable_id'    => $morphId,
                'created_at'     => now()->subDays(rand(3, 30)),
                'updated_at'     => now()->subDays(rand(0, 5)),
            ]);
        }

        $this->command->info('  ✅  تم إنشاء ' . count($tasks) . ' مهمة.');
    }

    /* ══════════════════════════════════════════════
     *  Invoices
     * ══════════════════════════════════════════════ */
    private function seedInvoices(): void
    {
        $invoices = [
            // [contact_idx, status, issue_offset, due_offset, tax_rate, discount, items]
            [
                'contact' => 0, 'status' => 'paid', 'issue' => -60, 'due' => -30,
                'tax' => 14, 'discount' => 0,
                'items' => [
                    ['نظام إدارة المخزون — ترخيص سنوي', 1, 18000, 'EGP'],
                    ['خدمة التركيب والتدريب',            1,  5000, 'EGP'],
                    ['دعم تقني لمدة 12 شهر',             1,  3600, 'EGP'],
                ],
            ],
            [
                'contact' => 1, 'status' => 'sent', 'issue' => -10, 'due' => 20,
                'tax' => 14, 'discount' => 2000,
                'items' => [
                    ['تطوير تطبيق موبايل — المرحلة الأولى', 1, 20000, 'EGP'],
                    ['تصميم UI/UX',                          1,  8000, 'EGP'],
                ],
            ],
            [
                'contact' => 3, 'status' => 'paid', 'issue' => -380, 'due' => -350,
                'tax' => 14, 'discount' => 500,
                'items' => [
                    ['استضافة سحابية — اشتراك سنوي', 1, 3588, 'USD'],
                    ['شهادة SSL Premium',             1,   99, 'USD'],
                ],
            ],
            [
                'contact' => 6, 'status' => 'paid', 'issue' => -25, 'due' => -5,
                'tax' => 14, 'discount' => 0,
                'items' => [
                    ['تصميم شعار الشركة',               1, 8000,  'EGP'],
                    ['دليل الهوية البصرية الكامل',       1, 12000, 'EGP'],
                    ['تصاميم وسائل التواصل الاجتماعي',  1,  5000, 'EGP'],
                    ['موشن جرافيك — فيديو تعريفي',      1,  8000, 'EGP'],
                ],
            ],
            [
                'contact' => 9, 'status' => 'overdue', 'issue' => -45, 'due' => -15,
                'tax' => 14, 'discount' => 0,
                'items' => [
                    ['برنامج المحاسبة — ترخيص سنوي', 1, 8500, 'EGP'],
                    ['تخصيص الضرائب والتقارير',       8,  500, 'EGP'],
                ],
            ],
            [
                'contact' => 11, 'status' => 'draft', 'issue' => 0, 'due' => 30,
                'tax' => 14, 'discount' => 5000,
                'items' => [
                    ['نظام POS — حزمة 12 فرع',      12, 22000, 'EGP'],
                    ['تركيب وتدريب لكل فرع',        12,  3000, 'EGP'],
                    ['دعم تقني سنوي — 12 فرع',       1, 14400, 'EGP'],
                ],
            ],
        ];

        $counter = 1;
        foreach ($invoices as $inv) {
            $contactId = $this->contactIds[$inv['contact']] ?? $this->contactIds[0];
            $contactEmail = DB::table('contacts')->find($contactId)?->email ?? '';

            $items    = $inv['items'];
            $subtotal = array_sum(array_map(fn($it) => $it[1] * $it[2], $items));
            $taxAmt   = round($subtotal * $inv['tax'] / 100, 2);
            $total    = round($subtotal + $taxAmt - $inv['discount'], 2);
            $invNum   = 'INV-' . str_pad($counter++, 4, '0', STR_PAD_LEFT);

            $invId = DB::table('invoices')->insertGetId([
                'tenant_id'      => $this->tenantId,
                'invoice_number' => $invNum,
                'contact_id'     => $contactId,
                'issue_date'     => now()->addDays($inv['issue'])->format('Y-m-d'),
                'due_date'       => now()->addDays($inv['due'])->format('Y-m-d'),
                'status'         => $inv['status'],
                'subtotal'       => $subtotal,
                'tax_rate'       => $inv['tax'],
                'tax_amount'     => $taxAmt,
                'discount'       => $inv['discount'],
                'total'          => $total,
                'notes'          => 'يُرجى التحويل على حساب شركة نوفا للتقنية — بنك القاهرة — رقم الحساب: 1234567890.',
                'created_at'     => now()->addDays($inv['issue']),
                'updated_at'     => now(),
            ]);

            foreach ($items as [$desc, $qty, $price]) {
                DB::table('invoice_items')->insert([
                    'invoice_id'  => $invId,
                    'description' => $desc,
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                    'total'       => $qty * $price,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            // Payments for paid invoices
            if ($inv['status'] === 'paid') {
                DB::table('invoice_payments')->insert([
                    'tenant_id'    => $this->tenantId,
                    'invoice_id'   => $invId,
                    'amount'       => $total,
                    'payment_date' => now()->addDays($inv['due'])->addDays(rand(-3, 0))->format('Y-m-d'),
                    'method'       => ['bank_transfer','bank_transfer','cash','card'][rand(0,3)],
                    'reference'    => 'TXN-' . strtoupper(substr(md5($invId . rand()), 0, 8)),
                    'notes'        => 'تم الاستلام والتأكيد.',
                    'created_by'   => $this->ownerId,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            // Partial payment for overdue
            if ($inv['status'] === 'overdue') {
                DB::table('invoice_payments')->insert([
                    'tenant_id'    => $this->tenantId,
                    'invoice_id'   => $invId,
                    'amount'       => round($total * 0.5, 2),
                    'payment_date' => now()->addDays($inv['issue'] + 15)->format('Y-m-d'),
                    'method'       => 'bank_transfer',
                    'reference'    => 'TXN-PART-' . strtoupper(substr(md5($invId), 0, 6)),
                    'notes'        => 'دفعة مقدمة 50%. الباقي معلق.',
                    'created_by'   => $this->adminId,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        $this->command->info('  🧾  تم إنشاء ' . count($invoices) . ' فاتورة مع بنودها ومدفوعاتها.');
    }

    /* ══════════════════════════════════════════════
     *  Meetings
     * ══════════════════════════════════════════════ */
    private function seedMeetings(): void
    {
        $assignees = [$this->ownerId, $this->adminId, $this->memberId];

        $meetings = [
            // [title, status, scheduled_offset_days, duration_min, location, assigned_idx, meetable: 'c'|'d', idx, outcome]
            ['킥-off اجتماع — نظام المخزون',           'completed',  -14, 60, 'مكاتب مجموعة النيل، القاهرة',           0, 'd', 0,  'تحديد المتطلبات الكاملة — جاهزون للمرحلة التالية.'],
            ['عرض تقني — تطبيق الموبايل',              'completed',   -7, 90, 'مكاتب الشركة المصرية للتكنولوجيا',      1, 'd', 1,  'اعتمدوا التصاميم — بدأنا التطوير رسمياً.'],
            ['Demo نظام POS — الدلتا',                  'scheduled',    3, 120,'مقر الدلتا، المنصورة',                   2, 'd', 2,  null],
            ['اجتماع مراجعة ربع سنوي — مصر للإنشاء',  'scheduled',    7, 60, 'عبر زووم',                               0, 'c', 3,  null],
            ['استعراض متطلبات تطبيق الحجوزات',         'scheduled',   10, 90, 'فندق سيناء جراند، شرم الشيخ',            1, 'c', 4,  null],
            ['اجتماع تسليم الهوية البصرية',             'completed',  -20, 45, 'مكاتب مجموعة النيل',                    0, 'd', 6,  'تم التسليم والاعتماد. العميل سعيد جداً بالنتيجة.'],
            ['عرض برنامج المحاسبة — الدلتا',            'scheduled',    4, 60, 'مقر الدلتا التجاري',                     1, 'd', 7,  null],
            ['مراجعة عقد الاستضافة السحابية',           'scheduled',    2, 30, 'مكالمة Google Meet',                    2, 'd', 8,  null],
            ['اجتماع إعادة تواصل — القاهرة للأصول',    'no_show',    -3,  45, 'مقر القاهرة لإدارة الأصول',              0, 'c', 5,  'لم يحضر المسؤول. تم إرسال بريد للمتابعة.'],
            ['تخطيط مشروع توسعة POS',                   'scheduled',    5, 60, 'عبر Microsoft Teams',                   1, 'd', 9,  null],
        ];

        foreach ($meetings as [$title,$status,$offset,$dur,$location,$aIdx,$type,$idx,$outcome]) {
            $morphType = $type === 'c'
                ? 'Modules\\Contacts\\Models\\Contact'
                : 'Modules\\Deals\\Models\\Deal';
            $morphId = $type === 'c'
                ? ($this->contactIds[$idx] ?? $this->contactIds[0])
                : ($this->dealIds[$idx]    ?? $this->dealIds[0]);

            DB::table('meetings')->insert([
                'tenant_id'        => $this->tenantId,
                'title'            => $title,
                'scheduled_at'     => now()->addDays($offset)->setTime(rand(9,16), [0,30][rand(0,1)]),
                'duration_minutes' => $dur,
                'location'         => $location,
                'status'           => $status,
                'outcome'          => $outcome,
                'meetable_type'    => $morphType,
                'meetable_id'      => $morphId,
                'assigned_to'      => $assignees[$aIdx],
                'created_at'       => now()->subDays(rand(5, 30)),
                'updated_at'       => now(),
            ]);
        }

        $this->command->info('  📅  تم إنشاء ' . count($meetings) . ' اجتماع.');
    }

    /* ══════════════════════════════════════════════
     *  Calls
     * ══════════════════════════════════════════════ */
    private function seedCalls(): void
    {
        $calls = [
            // [direction, outcome, duration_sec, called_offset_days, callable: 'c'|'d', idx, user_idx, notes]
            ['outbound', 'answered',   900, -1,  'c', 0,  0, 'ناقشنا تفاصيل نظام المخزون. يريد الاجتماع الأسبوع القادم لمراجعة العرض.'],
            ['inbound',  'answered',   360, -2,  'c', 1,  1, 'اتصلت فاطمة لتأكيد موعد جلسة مراجعة التصاميم. تم التأكيد.'],
            ['outbound', 'no_answer',    0, -3,  'c', 2,  2, 'لم يرد أحمد حسن. تم إرسال رسالة واتساب.'],
            ['outbound', 'answered',   480, -5,  'd', 3,  0, 'تحديث نورهان بشأن تجديد الاستضافة. سترسل التفاصيل للإدارة.'],
            ['inbound',  'answered',  1200, -6,  'c', 4,  1, 'هشام يسأل عن المواصفات التقنية للتطبيق. أرسلت له وثيقة المتطلبات.'],
            ['outbound', 'voicemail',    0, -8,  'c', 5,  0, 'تركت رسالة صوتية لريهام. لم تعود بالاتصال بعد.'],
            ['outbound', 'answered',   720, -10, 'c', 6,  2, 'كريم أكد جاهزية فريقهم التقني لمراجعة الحل. موعد قريباً.'],
            ['inbound',  'answered',   540, -12, 'd', 7,  1, 'سارة خالد تسأل عن جدول التسليم للمرحلة القادمة من التطبيق.'],
            ['outbound', 'busy',          0, -15, 'c', 8,  2, 'عمر عبد الله مشغول. سنعيد الاتصال غداً.'],
            ['outbound', 'answered',  1800, -18, 'c', 9,  0, 'اجتماع هاتفي مع منى فتحي لمناقشة متطلبات التوسعة الجديدة.'],
            ['inbound',  'answered',   300, -20, 'd', 1,  1, 'فاطمة أرسلت تعليقاتها على النماذج الأولية وطلبت تعديلات بسيطة.'],
            ['outbound', 'answered',   420, -22, 'c', 10, 2, 'طارق رمضان مهتم بالتطبيق — طلب عرض تجريبي للأسبوع القادم.'],
        ];

        $users = [$this->ownerId, $this->adminId, $this->memberId];

        foreach ($calls as [$dir,$outcome,$dur,$offset,$type,$idx,$uIdx,$notes]) {
            $morphType = $type === 'c'
                ? 'Modules\\Contacts\\Models\\Contact'
                : 'Modules\\Deals\\Models\\Deal';
            $morphId = $type === 'c'
                ? ($this->contactIds[$idx] ?? $this->contactIds[0])
                : ($this->dealIds[$idx]    ?? $this->dealIds[0]);

            DB::table('calls')->insert([
                'tenant_id'     => $this->tenantId,
                'direction'     => $dir,
                'outcome'       => $outcome,
                'duration_seconds' => $dur,
                'called_at'     => now()->addDays($offset)->setTime(rand(9,17), [0,15,30,45][rand(0,3)]),
                'callable_type' => $morphType,
                'callable_id'   => $morphId,
                'user_id'       => $users[$uIdx],
                'notes'         => $notes,
                'created_at'    => now()->addDays($offset),
                'updated_at'    => now(),
            ]);
        }

        $this->command->info('  📞  تم إنشاء ' . count($calls) . ' مكالمة.');
    }

    /* ══════════════════════════════════════════════
     *  Notifications
     * ══════════════════════════════════════════════ */
    private function seedNotifications(): void
    {
        $notifs = [
            [$this->ownerId,  'deal_won',      'صفقة جديدة مُغلقة! 🎉',          'تم إغلاق صفقة "تجديد + توسعة — مجموعة النيل" بقيمة 54,000 جنيه.',      false,  -1],
            [$this->adminId,  'deal_assigned',  'صفقة معيّنة لك',                  'تم تعيين صفقة "عرض برنامج المحاسبة — الدلتا" لك من قِبل مصطفى البدوي.', false,  -2],
            [$this->memberId, 'task_assigned',  'مهمة جديدة',                      'تم تعيين مهمة "مراجعة عقد الاستضافة السحابية" لك.',                      false,  -2],
            [$this->ownerId,  'deal_lost',      'صفقة خُسرت',                      'للأسف، تم خسارة صفقة "استشارات تقنية — القاهرة لإدارة الأصول" بسبب قيود الميزانية.', true, -5],
            [$this->adminId,  'task_assigned',  'مهمة بأولوية عالية',              'مهمة "إرسال عرض السعر لنظام المخزون" مستحقة خلال يومين.',               false,  -3],
            [$this->ownerId,  'deal_won',      'إغلاق ناجح! 💪',                   'تم إغلاق صفقة "هوية بصرية + موقع — مجموعة النيل" بقيمة 28,000 جنيه.',  true, -22],
        ];

        foreach ($notifs as [$userId, $type, $title, $body, $isRead, $offset]) {
            DB::table('crm_notifications')->insert([
                'tenant_id'  => $this->tenantId,
                'user_id'    => $userId,
                'type'       => $type,
                'title'      => $title,
                'body'       => $body,
                'data'       => json_encode([]),
                'read_at'    => $isRead ? now()->addDays($offset)->addHours(2) : null,
                'created_at' => now()->addDays($offset),
                'updated_at' => now()->addDays($offset),
            ]);
        }

        $this->command->info('  🔔  تم إنشاء ' . count($notifs) . ' إشعار.');
    }
}
