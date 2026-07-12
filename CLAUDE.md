# CLAUDE.md — CRM Innovation SaaS Platform

> المرجع الأساسي لـ Claude Code. اقرأه كاملاً قبل أي خطوة.
> المشروع مبني على قاعدة موجودة (laravel-vue-dashboard) — لا تعدّل على الكود الموجود.

---

## 1. نظرة عامة على المنظومة

```
CRM Innovation SaaS
├── Landing Page          ← تسويقية (Blade) — عرض الباقات + تسجيل
├── Super Admin Panel     ← /dashboard/* (موجود — يُكمَّل فقط)
└── Tenant App (CRM)      ← /crm/* (Vue 3 SPA — جديد كامل)
```

**Stack:**
- Laravel 11 + PHP 8.2
- Vue 3 (Composition API `<script setup>`) + Vite
- PrimeVue 4 + Tailwind أو Bootstrap (حسب الموجود)
- **Vuex** (موجود — لا تستبدله بـ Pinia)
- JWT Auth → `tymon/jwt-auth` ✅
- nWidart Laravel Modules ✅
- Spatie Laravel Permission ✅
- PDF → `carlos-meneses/laravel-mpdf` ✅
- Excel → `maatwebsite/excel` ✅

---

## 2. رحلة المستخدم الكاملة

```
زائر يفتح الموقع
    ↓
Landing Page (/)
    ├── يشوف Features + Pricing
    ├── يضغط "ابدأ مجاناً" → صفحة Register (/register)
    │       ← يسجل: اسم الشركة، إيميل، باسورد، يختار الباقة
    │       ← يتعمله Tenant + User (owner) تلقائياً
    │       ← يتبعتله إيميل ترحيب
    │       ← يتحول لـ /crm/dashboard
    │
    ├── يضغط "تسجيل الدخول" → /login
    │       ← JWT token → /crm/dashboard
    │
    └── Super Admin → /dashboard/auth/login (موجود)
```

---

## 3. Guards والـ Models

### Guards (في config/auth.php):
```php
'admin_api'  // موجود — Super Admin
'tenant_api' // جديد — Tenant Users (JWT)
```

| Guard | Model | الوصول |
|---|---|---|
| `admin_api` | `Admin` (موجود) | `/dashboard/*` |
| `tenant_api` | `User` (جديد) | `/api/crm/*` |
| — | Guest | `/` + `/register` + `/login` |

---

## 4. قاعدة البيانات

### ⚠️ لا تعدّل على الجداول الموجودة أبداً.

### ترتيب إنشاء الجداول الجديدة:

#### 1. `plans` (الباقات)
```sql
id
name          (varchar) — "Starter", "Professional", "Enterprise"
slug          (varchar, unique) — "starter", "pro", "enterprise"
description   (text, nullable)
monthly_price (decimal 10,2) — السعر الشهري
yearly_price  (decimal 10,2) — السعر السنوي (discount مدمج)
max_users     (int) — عدد المستخدمين المسموح بيهم (0 = unlimited)
max_contacts  (int) — عدد الـ contacts (0 = unlimited)
features      (json) — ['invoices', 'reports', 'kanban', ...]
is_active     (boolean, default: true)
is_featured   (boolean, default: false) — لتمييز الـ "Most Popular"
sort_order    (int, default: 0)
created_at, updated_at
```

#### 2. `tenants` (الشركات)
```sql
id
name          (varchar) — اسم الشركة
slug          (varchar, unique) — للـ URL أو التعريف
email         (varchar)
phone         (varchar, nullable)
plan_id       (FK → plans)
billing_cycle (enum: monthly, yearly, custom, default: monthly)
status        (enum: trial, active, suspended, cancelled)
trial_ends_at (timestamp, nullable)
plan_starts_at (timestamp, nullable)
plan_ends_at  (timestamp, nullable)
created_at, updated_at
```

#### 3. `users` (مستخدمو الـ Tenants)
```sql
id
tenant_id     (FK → tenants)
name          (varchar)
email         (varchar, unique)
password      (varchar)
role          (enum: owner, admin, member)
status        (enum: active, inactive)
remember_token
created_at, updated_at
```

#### 4. `contacts`
```sql
id, tenant_id, first_name, last_name, email, phone,
company, address, notes,
status (enum: lead, customer, inactive),
created_by (FK → users), created_at, updated_at
```

#### 5. `deal_stages`
```sql
id, tenant_id, name, color (#hex), order (int),
created_at, updated_at
```

#### 6. `deals`
```sql
id, tenant_id, title, contact_id (FK), stage_id (FK),
value (decimal 15,2), currency (default: USD),
probability (tinyint 0-100), expected_close_date,
assigned_to (FK → users),
status (enum: open, won, lost),
notes (text, nullable), created_at, updated_at
```

#### 7. `tasks`
```sql
id, tenant_id, title, description (text, nullable),
due_date, priority (enum: low, medium, high),
status (enum: pending, in_progress, completed),
assigned_to (FK → users),
taskable_type, taskable_id (polymorphic → Contact أو Deal),
created_at, updated_at
```

#### 8. `invoices`
```sql
id, tenant_id,
invoice_number (varchar, unique per tenant — INV-0001),
contact_id (FK),
issue_date, due_date,
status (enum: draft, sent, paid, overdue, cancelled),
subtotal (decimal 15,2), tax_rate (decimal 5,2),
tax_amount (decimal 15,2), discount (decimal 15,2),
total (decimal 15,2),
notes (text, nullable), created_at, updated_at
```

#### 9. `invoice_items`
```sql
id, invoice_id (FK),
description, quantity (decimal 8,2),
unit_price (decimal 15,2), total (decimal 15,2)
```

#### 10. `crm_activities`
```sql
id, tenant_id, user_id (FK),
subject_type, subject_id (polymorphic),
action (varchar), description (text),
created_at, updated_at
```

#### 11. `plan_change_requests` (اختياري — لو الـ Enterprise manual)
```sql
id, tenant_id, requested_plan_id, message, status (enum: pending, approved, rejected),
created_at, updated_at
```

---

## 5. Modules الجديدة

أنشئها بهذا الترتيب:

```
Modules/
├── Core/         ← BelongsToTenant trait + ApiResponse + BaseService
├── Plans/        ← CRUD الباقات (Super Admin)
├── Subscriptions/← إدارة اشتراكات الـ Tenants
├── Tenants/      ← CRUD الشركات (Super Admin)
├── CrmAuth/      ← تسجيل + لوجن الـ Tenant Users
├── Contacts/
├── Deals/
├── Tasks/
├── Invoices/
└── Reports/
```

---

## 6. Core Module

### BelongsToTenant Trait:
```php
// Modules/Core/app/Traits/BelongsToTenant.php
protected static function booted(): void
{
    static::addGlobalScope('tenant', function ($query) {
        if (auth('tenant_api')->check()) {
            $query->where('tenant_id', auth('tenant_api')->user()->tenant_id);
        }
    });

    static::creating(function ($model) {
        if (auth('tenant_api')->check() && empty($model->tenant_id)) {
            $model->tenant_id = auth('tenant_api')->user()->tenant_id;
        }
    });
}
```

### ApiResponse Helper:
```php
// Modules/Core/app/Helpers/ApiResponse.php
class ApiResponse
{
    public static function success($data = null, string $message = 'تم بنجاح', int $code = 200): JsonResponse
    public static function error(string $message = 'حدث خطأ', int $code = 400, $errors = null): JsonResponse
    public static function paginated($paginator, string $resourceClass, string $message = 'تم بنجاح'): JsonResponse
}

// Response format موحّد:
{
  "success": true,
  "message": "string",
  "data": {},
  "meta": { "current_page": 1, "last_page": 5, "per_page": 15, "total": 72 }
}
```

---

## 7. API Routes

### في `routes/api.php` — أضف:

```php
// ===== CRM Public (no auth) =====
Route::prefix('crm')->group(function () {
    Route::get('plans', [PlanController::class, 'publicIndex']); // للـ landing page
    Route::post('register', [CrmAuthController::class, 'register']); // تسجيل شركة جديدة
    Route::post('login', [CrmAuthController::class, 'login']);
});

// ===== CRM Authenticated =====
Route::prefix('crm')->middleware('auth:tenant_api')->group(function () {
    Route::post('logout', [CrmAuthController::class, 'logout']);
    Route::post('refresh', [CrmAuthController::class, 'refresh']);
    Route::get('me', [CrmAuthController::class, 'me']);

    // Subscription
    Route::get('subscription', [SubscriptionController::class, 'current']);
    Route::post('subscription/request-upgrade', [SubscriptionController::class, 'requestUpgrade']);

    // CRM Resources
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('deals', DealController::class);
    Route::apiResource('deal-stages', DealStageController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf']);
    Route::get('reports/dashboard', [ReportController::class, 'dashboard']);
    Route::get('reports/deals', [ReportController::class, 'deals']);
    Route::get('reports/revenue', [ReportController::class, 'revenue']);

    // Settings
    Route::get('settings/company', [SettingsController::class, 'company']);
    Route::put('settings/company', [SettingsController::class, 'updateCompany']);
    Route::get('settings/users', [SettingsController::class, 'users']);
    Route::post('settings/users', [SettingsController::class, 'inviteUser']);
});

// ===== Super Admin — يُضاف داخل middleware('auth:admin_api') الموجود =====
Route::apiResource('plans', PlanController::class);
Route::apiResource('tenants', TenantController::class);
Route::get('tenants/{tenant}/subscription', [TenantController::class, 'subscription']);
Route::put('tenants/{tenant}/subscription', [TenantController::class, 'updateSubscription']);
Route::get('crm-statistics', [CrmStatisticsController::class, 'index']);
```

---

## 8. Register Flow (مهم جداً)

```php
// POST /api/crm/register
// Request:
{
    "company_name": "string|required",
    "email":        "string|email|unique:users",
    "password":     "string|min:8|confirmed",
    "plan_id":      "integer|exists:plans,id",
    "billing_cycle":"monthly|yearly"
}

// الـ RegisterService يعمل:
// 1. ينشئ Tenant
// 2. ينشئ User (role: owner, tenant_id)
// 3. يربط الـ Tenant بالـ Plan
// 4. يعمل الـ Tenant trial لو الخطة تدعم trial
// 5. ينشئ default deal_stages للـ Tenant
// 6. يبعت welcome email (queue)
// 7. يرجع JWT token

// Response:
{
    "success": true,
    "message": "تم إنشاء حسابك بنجاح",
    "data": {
        "token": "eyJ...",
        "user": { ... },
        "tenant": { ... }
    }
}
```

---

## 9. Subscription & Plan Limits

```php
// Middleware للتحقق من الـ Plan limits
// Modules/Core/app/Http/Middleware/CheckPlanLimit.php

class CheckPlanLimit
{
    public function handle($request, Closure $next, string $feature)
    {
        $tenant = auth('tenant_api')->user()->tenant;
        $plan = $tenant->plan;

        // مثال: التحقق من عدد الـ contacts
        if ($feature === 'contacts' && $plan->max_contacts > 0) {
            $count = Contact::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenant->id)->count();
            if ($count >= $plan->max_contacts) {
                return ApiResponse::error('لقد وصلت للحد الأقصى للخطة الحالية', 403);
            }
        }

        return $next($request);
    }
}

// يُطبَّق على Routes:
Route::post('contacts', [ContactController::class, 'store'])
    ->middleware('check.plan:contacts');
```

---

## 10. Landing Page (Blade)

```
resources/views/
├── landing/
│   ├── index.blade.php     ← الصفحة الرئيسية
│   ├── partials/
│   │   ├── _navbar.blade.php
│   │   ├── _hero.blade.php
│   │   ├── _features.blade.php
│   │   ├── _pricing.blade.php   ← تجيب الـ plans من DB
│   │   ├── _testimonials.blade.php
│   │   ├── _faq.blade.php
│   │   └── _footer.blade.php
│   ├── register.blade.php  ← نموذج التسجيل
│   └── login.blade.php     ← نموذج تسجيل الدخول
```

### Pricing Section (ديناميكية):
```php
// LandingController.php
public function index() {
    $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
    return view('landing.index', compact('plans'));
}
```

---

## 11. Frontend (Vue 3 CRM App)

### Structure:
```
resources/js/crm/
├── main.js                 ← entry point منفصل
├── router/index.js
├── store/
│   ├── index.js            ← Vuex root
│   └── modules/
│       ├── auth.js         ← token, user, tenant
│       ├── contacts.js
│       ├── deals.js
│       ├── tasks.js
│       ├── invoices.js
│       └── ui.js           ← sidebar, notifications
├── layouts/
│   └── CrmLayout.vue       ← sidebar + header + slot
├── views/
│   ├── auth/Login.vue
│   ├── dashboard/Index.vue
│   ├── contacts/
│   │   ├── Index.vue       ← list + search + filter
│   │   ├── Form.vue        ← create + edit
│   │   └── Show.vue        ← profile + timeline
│   ├── deals/
│   │   ├── Kanban.vue      ← drag & drop
│   │   └── List.vue
│   ├── tasks/Index.vue
│   ├── invoices/
│   │   ├── Index.vue
│   │   ├── Create.vue
│   │   └── Show.vue
│   ├── reports/Index.vue
│   └── settings/
│       ├── Company.vue
│       └── Team.vue
└── composables/
    ├── useApi.js           ← axios wrapper
    ├── useToast.js
    └── usePagination.js
```

### Vue Router:
```js
// /crm/login     ← guest only
// /crm/dashboard ← requires auth
// /crm/contacts
// /crm/contacts/create
// /crm/contacts/:id
// /crm/deals          (Kanban default)
// /crm/deals/list
// /crm/tasks
// /crm/invoices
// /crm/invoices/create
// /crm/invoices/:id
// /crm/reports
// /crm/settings/company
// /crm/settings/team
```

### Vuex Auth Store:
```js
// resources/js/crm/store/modules/auth.js
state: () => ({
    token: localStorage.getItem('crm_token') || null,
    user: null,
    tenant: null,
    subscription: null,
}),
```

### Axios Instance:
```js
// resources/js/crm/composables/useApi.js
const api = axios.create({ baseURL: '/api/crm' });
// interceptor: Authorization: Bearer {token}
// interceptor: 401 → dispatch('auth/logout') → router.push('/crm/login')
// interceptor: 403 plan limit → toast 'upgrade your plan'
```

---

## 12. Plan Upgrade Flow (Manual)

```
Tenant يضغط "Upgrade Plan"
    ↓
يشوف مقارنة الباقات
    ↓
يختار الباقة الجديدة + يكتب ملاحظة
    ↓
POST /api/crm/subscription/request-upgrade
    ↓
يُنشأ plan_change_request (status: pending)
    ↓
Super Admin يوافق من Dashboard
    ↓
يُحدَّث tenant.plan_id + tenant.status = active
    ↓
إيميل للـ tenant: "تمت ترقية خطتك"
```

---

## 13. Design System (CRM App)

```css
--crm-primary:      #2563EB;
--crm-primary-dark: #1D4ED8;
--crm-accent:       #7C3AED;
--crm-success:      #10B981;
--crm-warning:      #F59E0B;
--crm-danger:       #EF4444;
--crm-bg:           #F8FAFC;
--crm-surface:      #FFFFFF;
--crm-border:       #E2E8F0;
--crm-text:         #1E293B;
--crm-text-muted:   #64748B;
--crm-sidebar-bg:   #1E293B;
--crm-sidebar-w:    260px;
```

- **Font:** Plus Jakarta Sans (Google Fonts)
- **Border radius:** 12px للكروت، 8px للعناصر الصغيرة
- **Style:** Premium SaaS — مشابه Linear و Stripe
- لا gradients مبالغة، لا neon، لا تأثيرات ثقيلة

---

## 14. Packages إضافية

### Backend:
```bash
# لو مش موجودة
composer require spatie/laravel-activitylog
```

### Frontend:
```bash
npm install vuedraggable chart.js vue-chartjs
```

---

## 15. Web Routes (Blade — Landing)

```php
// routes/web.php
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/register', [LandingController::class, 'register'])->name('register');
Route::get('/login', [LandingController::class, 'login'])->name('login');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');

// CRM SPA entry point — كل الـ /crm/* يرجع Vue
Route::get('/crm/{any?}', function () {
    return view('crm');
})->where('any', '.*')->name('crm');
```

---

## 16. Default Data (Seeders)

```php
// PlansSeeder — ينشئ 3 باقات افتراضية
Plan::create([
    'name' => 'Starter', 'slug' => 'starter',
    'monthly_price' => 19, 'yearly_price' => 190,
    'max_users' => 1, 'max_contacts' => 500,
    'features' => ['contacts', 'deals', 'tasks'],
    'sort_order' => 1,
]);
Plan::create([
    'name' => 'Professional', 'slug' => 'pro',
    'monthly_price' => 49, 'yearly_price' => 490,
    'max_users' => 10, 'max_contacts' => 0,
    'features' => ['contacts', 'deals', 'tasks', 'invoices', 'reports'],
    'is_featured' => true, 'sort_order' => 2,
]);
Plan::create([
    'name' => 'Enterprise', 'slug' => 'enterprise',
    'monthly_price' => 99, 'yearly_price' => 990,
    'max_users' => 0, 'max_contacts' => 0,
    'features' => ['contacts', 'deals', 'tasks', 'invoices', 'reports', 'priority_support'],
    'sort_order' => 3,
]);

// DemoTenantSeeder — ينشئ tenant تجريبي
// DealStagesSeeder — ينشئ stages افتراضية لكل tenant جديد:
// ['Lead', 'Qualified', 'Proposal', 'Negotiation', 'Closed Won', 'Closed Lost']
```

---

## 17. ترتيب التنفيذ (Phases)

### Phase 1 — الأساس
```
1. إضافة 'tenant_api' guard في config/auth.php
2. إنشاء الـ Modules الـ 9
3. Core Module: BelongsToTenant + ApiResponse + CheckPlanLimit middleware
4. Migrations بالترتيب (plans → tenants → users → contacts → ...)
5. Seeders: Plans + DemoTenant + DealStages
```

### Phase 2 — Auth & Plans
```
6. Plans Module: CRUD للـ Super Admin + public endpoint للـ Landing
7. CrmAuth Module: register (مع إنشاء tenant تلقائي) + login + logout + me
8. Subscriptions Module: current + request-upgrade
9. Tenants Module: Super Admin CRUD + subscription management
```

### Phase 3 — CRM Backend
```
10. Contacts Module (CRUD + search + filter)
11. Deals Module (CRUD + stages CRUD)
12. Tasks Module (CRUD + polymorphic)
13. Invoices Module (CRUD + items + PDF + auto-number)
14. Reports Module (dashboard KPIs + deals chart + revenue chart)
```

### Phase 4 — Landing Page (Blade)
```
15. LandingController
16. Landing views: index + pricing (ديناميكي) + register + login
17. تطبيق design الـ template الموجود (agiletech assets)
```

### Phase 5 — Frontend CRM (Vue 3)
```
18. Vite config: إضافة crm/main.js entry
19. Vuex stores + Axios + Router
20. CrmLayout.vue (sidebar + header)
21. Auth pages (Login)
22. Dashboard (KPIs + Charts)
23. Contacts (List + Form + Profile)
24. Deals Kanban (vuedraggable)
25. Tasks
26. Invoices (List + Create + Print)
27. Reports
28. Settings (Company + Team)
```

### Phase 6 — Polish
```
29. Plan limit warnings في الـ UI
30. Upgrade Plan modal
31. Notifications
32. Dark mode
33. Responsive mobile
```

---

## 18. معايير الكود

### PHP:
- كل CRM Model يستخدم `BelongsToTenant` trait
- كل Controller يستخدم `FormRequest` للـ validation
- Business logic في `Service` class
- Response عبر `ApiResponse` helper دائماً
- `JsonResource` لكل response

### Vue 3:
- `<script setup>` Composition API فقط — لا Options API
- **Vuex** للـ global state (لا Pinia)
- **Vuelidate** للـ form validation
- **PrimeVue 4** للـ UI components

---

## 19. Environment Variables

```env
# في .env
CRM_TRIAL_DAYS=14
CRM_DEMO_EMAIL=demo@crm-innovation.com
CRM_DEMO_PASSWORD=Demo@123456
```

---

## 20. القاعدة الذهبية لـ Claude Code

1. **لا تلمس** الكود الموجود في `app/Http/Controllers/Dashboard/`
2. **لا تلمس** الـ migrations الموجودة
3. **لا تلمس** الـ Vue files الموجودة خارج `/crm/`
4. كل كود CRM جديد → داخل `Modules/` أو `resources/js/crm/`
5. Landing Page → `resources/views/landing/`
6. ابدأ دائماً بـ Phase 1 ولا تنتقل للـ Phase التالية قبل اكتماله
7. عند الشك في أي قرار → ارجع لهذا الملف


---

## 21. Design Reference — Landing Page

مسار الـ template الأصلي على جهاز المطور:
```
D:\templates\crm-ennovation\Saas landpage
```

**تعليمات لـ Claude Code:**
1. افتح المسار ده واقرأ الـ CSS files (خصوصاً `style.css`)
2. استخرج منه: الألوان، الـ fonts، الـ spacing، الـ components style
3. طبّق نفس الـ design على Landing Page الـ CRM في `resources/views/landing/`
4. استخدم نفس الـ assets (CSS, JS, images) من التمبلت مباشرةً أو انسخها لـ `public/landing/`
5. لا تخترع design جديد — الهدف تطابق الـ template بمحتوى CRM


---

## 22. Design Reference — Dashboard (CRM App)

مسار الـ template الأصلي على جهاز المطور:
```
D:\templates\crm-ennovation\dashboard
```

**تعليمات لـ Claude Code:**
1. افتح المسار ده واقرأ كل الـ CSS files
2. استخرج منه: الألوان، الـ fonts، الـ sidebar style، الـ cards، الـ components
3. طبّق نفس الـ design على الـ CRM Vue App في `resources/js/crm/`
4. انسخ الـ assets المطلوبة (CSS, JS, images, fonts) لـ `public/dashboard/`
5. **احذف** أي styles أو variables أو classes مش مستخدمة في الـ CRM
6. **احذف** أي CSS خاص بصفحات مش موجودة في المشروع
7. لا تخترع design جديد — الهدف تطابق الـ template بمحتوى CRM بالظبط


---

## 23. Logo

مسار اللوجو الرسمي للمشروع:
```
C:\Users\ali khalifa\Downloads\INNOVATION LOGO.png
```

**تعليمات لـ Claude Code:**
1. انسخ الملف ده لـ `public/images/logo.png`
2. استخدمه في كل الأماكن اللي فيها logo:
   - Navbar الـ Landing Page
   - Sidebar الـ CRM Dashboard
   - Login + Register pages
   - الـ Footer
   - الـ PDF الـ Invoices
3. لا تستخدم أي logo أو placeholder تاني غيره


---

## 24. اللغات (i18n)

المشروع يدعم لغتين كاملتين:
- **العربية (ar)** — RTL
- **الإنجليزية (en)** — LTR

### Backend (Laravel):
- ملفات اللغة موجودة بالفعل في `lang/ar/` و `lang/en/`
- أضف ملفات جديدة خاصة بالـ CRM:
  - `lang/ar/crm.php`
  - `lang/en/crm.php`
- كل رسائل الـ API (`ApiResponse`) تكون بالعربية أو الإنجليزية حسب `app()->getLocale()`
- الـ Middleware الموجود `ChangeLang` يُطبَّق على كل routes الـ CRM

### Frontend (Vue 3):
- استخدم `laravel-vue-i18n` الموجود بالفعل في `package.json`
- ملفات الترجمة في `lang/ar/` و `lang/en/`
- اللغة تُحفظ في Vuex store + localStorage
- زر تبديل اللغة في الـ Navbar والـ Sidebar

### RTL/LTR:
- عند تغيير اللغة لعربي: `document.dir = 'rtl'` + class `rtl` على الـ `<html>`
- عند تغيير اللغة لإنجليزي: `document.dir = 'ltr'`
- الـ CSS يدعم الاتجاهين — استخدم `margin-inline-start` بدل `margin-left` في أماكن الـ layout
- الـ Sidebar يتعكس تلقائياً مع RTL
- ملف `assets/css/rtl-style.css` موجود بالفعل في التمبلت — استخدمه

### Landing Page:
- زر تبديل اللغة في الـ Navbar
- الـ Pricing والـ Features تترجم ديناميكياً
- الـ Register/Login forms تدعم RTL

### أولوية الترجمة (ابدأ بيها):
1. Navigation & Sidebar
2. Auth pages (Login, Register)
3. Dashboard KPIs
4. Contacts, Deals, Tasks, Invoices
5. Landing Page
6. Emails

