<?php

use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\Dashboard\AreaController;
use App\Http\Controllers\Dashboard\AuthDashboardController;
use App\Http\Controllers\Dashboard\BackupController;
use App\Http\Controllers\Dashboard\BannerController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ColorController;
use App\Http\Controllers\Dashboard\CountryController;
use App\Http\Controllers\Dashboard\DashboardStatisticsController;
use App\Http\Controllers\Dashboard\FrequentlyAskedQuestionController;
use App\Http\Controllers\Dashboard\JoinUsController;
use App\Http\Controllers\Dashboard\LanguageController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\SendNotificationController;
use App\Http\Controllers\Dashboard\LandingFaqController;
use App\Http\Controllers\Dashboard\LandingPartnerController;
use App\Http\Controllers\Dashboard\LandingFeatureController;
use App\Http\Controllers\Dashboard\LandingHeroController;
use App\Http\Controllers\Dashboard\LandingHowItWorkController;
use App\Http\Controllers\Dashboard\LandingAboutController;
use App\Http\Controllers\Dashboard\LandingBlogPostController;
use App\Http\Controllers\Dashboard\LandingContactMessageController;
use App\Http\Controllers\Dashboard\LandingSettingController;
use App\Http\Controllers\Dashboard\LandingStatController;
use App\Http\Controllers\Dashboard\LandingTeamMemberController;
use App\Http\Controllers\Dashboard\LandingTestimonialController;
use App\Http\Controllers\LandingContactController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Web\WebPagesController;
use App\Http\Middleware\ChangeLang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Contacts\Http\Controllers\ContactController;
use Modules\CrmAuth\Http\Controllers\CrmAuthController;
use Modules\CrmAuth\Http\Controllers\SettingsController;
use Modules\Deals\Http\Controllers\DealController;
use Modules\Deals\Http\Controllers\DealStageController;
use Modules\Invoices\Http\Controllers\InvoiceController;
use Modules\Plans\Http\Controllers\PlanController;
use Modules\Reports\Http\Controllers\ReportController;
use Modules\Subscriptions\Http\Controllers\SubscriptionController;
use Modules\Tasks\Http\Controllers\TaskController;
use Modules\Tenants\Http\Controllers\TenantController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:admin_api');

// =====================================================
// CRM API Routes
// =====================================================

// Public CRM endpoints (no auth) + token-based file downloads
Route::prefix('crm')->middleware([ChangeLang::class])->group(function () {
    Route::get('plans', [PlanController::class, 'publicIndex']);
    Route::post('register', [CrmAuthController::class, 'register']);
    // File downloads — auth via ?token= query param
    Route::get('contacts/export', [\Modules\Contacts\Http\Controllers\ContactController::class, 'export']);
    Route::get('invoices/{invoice}/pdf', [\Modules\Invoices\Http\Controllers\InvoiceController::class, 'generatePdf']);
    Route::post('login', [CrmAuthController::class, 'login']);
});

// Authenticated CRM endpoints
Route::prefix('crm')->middleware(['auth:tenant_api', ChangeLang::class])->group(function () {
    Route::post('logout', [CrmAuthController::class, 'logout']);
    Route::post('refresh', [CrmAuthController::class, 'refresh']);
    Route::get('me', [CrmAuthController::class, 'me']);

    // Subscription
    Route::get('subscription', [SubscriptionController::class, 'current']);
    Route::post('subscription/request-upgrade', [SubscriptionController::class, 'requestUpgrade']);

    // CRM Resources
    Route::get('contacts/{contact}/activities', [\Modules\Contacts\Http\Controllers\ContactController::class, 'activities']);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('deals', DealController::class);
    Route::apiResource('deal-stages', DealStageController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::patch('invoices/{invoice}/status', [\Modules\Invoices\Http\Controllers\InvoiceController::class, 'updateStatus']);

    // Reports
    Route::get('reports/dashboard', [ReportController::class, 'dashboard']);
    Route::get('reports/deals', [ReportController::class, 'deals']);
    Route::get('reports/revenue', [ReportController::class, 'revenue']);

    // Settings
    Route::get('settings/company', [SettingsController::class, 'company']);
    Route::put('settings/company', [SettingsController::class, 'updateCompany']);
    Route::get('settings/users', [SettingsController::class, 'users']);
    Route::post('settings/users', [SettingsController::class, 'inviteUser']);
});

// PDF route outside auth middleware (handles token via query string)
Route::prefix('crm')->middleware([ChangeLang::class])->group(function () {
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'generatePdf']);
});

// =====================================================


Route::group(['prefix' => 'web', 'middleware' => [ChangeLang::class]], function () {
    Route::get('terms',[WebPagesController::class,'terms']);
    Route::get('privacy',[WebPagesController::class,'privacy']);
});

Route::group(['prefix' => 'dashboard', 'middleware' => [ChangeLang::class]], function () {

    // start Dashboard auth
    Route::group(['prefix' => 'auth'], function () {

        Route::post('login',[AuthDashboardController::class, 'login']);

        Route::post('checkToken', [AuthDashboardController::class,'authorizeUser']);

    });

    Route::get('get-language', [SettingController::class,'getLanguage']);

    // areas
    Route::get('areas/dropdown',[AreaController::class,'dropdown']);

    Route::group(['middleware' => 'auth:admin_api'], function () {

        Route::apiResource('banners', BannerController::class);

        // Category
        Route::get('categories/dropdown',[CategoryController::class,'dropdown']);
        Route::apiResource('categories', CategoryController::class);

        // country
        Route::get('countries/dropdown',[CountryController::class,'dropdown']);
        Route::apiResource('countries', CountryController::class);

        Route::post('logout', [AuthDashboardController::class,'logout']);

        Route::resource('roles', RoleController::class);

        Route::resource('admins', AdminController::class);
        Route::get('all_roles', [AdminController::class,'all_roles']);

        // backup
        Route::apiResource('backup', BackupController::class);

        // areas
        Route::apiResource('areas', AreaController::class);

        //statistics
        Route::get('statistics',[DashboardStatisticsController::class,'index']);
        Route::get('get-total-revenue-per-months',[DashboardStatisticsController::class,'getTotalRevenuePerMonths']);
        Route::get('get-total-revenue-for-each-year-per-months',[DashboardStatisticsController::class,'getTotalRevenueForEachYearPerMonths']);

        Route::apiResource('frequently-asked-questions', FrequentlyAskedQuestionController::class);

        // JoinUs
        Route::apiResource('join-us', JoinUsController::class);

        // Language resource
        Route::resource('language', LanguageController::class);

        Route::controller(NotificationController::class)->group(function () {
            Route::get('getAllNot', 'getAllNot');
            Route::get('getNotNotRead', 'getNotNotRead');
            Route::post('clearItem/{id}', 'clearItem');
            Route::post('getNotNotRead', 'clearAll');
        });

        Route::put('update-admin-profile', [ProfileController::class,'updateAdminProfile']);

        Route::post('send-notification', [SendNotificationController::class, 'sendNotification']);

        // color
        Route::get('color/dropdown',[ColorController::class,'dropdown']);
        Route::apiResource('color', ColorController::class);

        // CRM Management (Super Admin)
        Route::apiResource('plans', PlanController::class);
        Route::apiResource('tenants', TenantController::class);
        Route::get('tenants/{tenant}/subscription', [TenantController::class, 'subscription']);
        Route::put('tenants/{tenant}/subscription', [TenantController::class, 'updateSubscription']);

        // ─── Landing Page Content Management ───────────────────────────────────
        // Single-row sections
        Route::get('landing/settings', [LandingSettingController::class, 'show']);
        Route::put('landing/settings', [LandingSettingController::class, 'update']);
        Route::get('landing/hero', [LandingHeroController::class, 'show']);
        Route::put('landing/hero', [LandingHeroController::class, 'update']);
        // Multi-row sections
        Route::apiResource('landing/stats',          LandingStatController::class,       ['as' => 'landing']);
        Route::apiResource('landing/features',       LandingFeatureController::class,    ['as' => 'landing']);
        Route::apiResource('landing/how-it-works',   LandingHowItWorkController::class,  ['as' => 'landing', 'parameters' => ['how-it-works' => 'landingHowItWork']]);
        Route::apiResource('landing/faqs',           LandingFaqController::class,        ['as' => 'landing']);
        Route::apiResource('landing/partners',       LandingPartnerController::class,    ['as' => 'landing']);
        // New sections
        Route::get('landing/about',             [LandingAboutController::class, 'show']);
        Route::put('landing/about',             [LandingAboutController::class, 'update']);
        Route::apiResource('landing/testimonials',    LandingTestimonialController::class, ['as' => 'landing']);
        Route::apiResource('landing/team',           LandingTeamMemberController::class,  ['as' => 'landing', 'parameters' => ['team' => 'landingTeamMember']]);
        Route::apiResource('landing/blog',           LandingBlogPostController::class,    ['as' => 'landing', 'parameters' => ['blog' => 'landingBlogPost']]);
        Route::get('landing/contact-messages',                     [LandingContactMessageController::class, 'index']);
        Route::get('landing/contact-messages/{landingContactMessage}',          [LandingContactMessageController::class, 'show']);
        Route::post('landing/contact-messages/{landingContactMessage}/mark-replied', [LandingContactMessageController::class, 'markRead']);
        Route::delete('landing/contact-messages/{landingContactMessage}',       [LandingContactMessageController::class, 'destroy']);

        // CRM Statistics & Upgrade Requests
        Route::get('crm-statistics', [\App\Http\Controllers\Dashboard\CrmStatisticsController::class, 'index']);
        Route::get('crm-upgrade-requests', [\App\Http\Controllers\Dashboard\CrmStatisticsController::class, 'upgrades']);
        Route::post('crm-upgrade-requests/{id}/approve', [\App\Http\Controllers\Dashboard\CrmStatisticsController::class, 'approveUpgrade']);

    });

});

