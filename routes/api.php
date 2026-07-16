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
use App\Http\Controllers\Dashboard\LandingFaqSectionController;
use App\Http\Controllers\Dashboard\LandingPartnerController;
use App\Http\Controllers\Dashboard\LandingFeatureSectionController;
use App\Http\Controllers\Dashboard\LandingFeatureItemController;
use App\Http\Controllers\Dashboard\LandingHeroController;
use App\Http\Controllers\Dashboard\LandingHeroSlideController;
use App\Http\Controllers\Dashboard\LandingProblemSectionController;
use App\Http\Controllers\Dashboard\LandingProblemItemController;
use App\Http\Controllers\Dashboard\LandingHowSectionController;
use App\Http\Controllers\Dashboard\LandingHowItemController;
use App\Http\Controllers\Dashboard\LandingWhySectionController;
use App\Http\Controllers\Dashboard\LandingWhyItemController;
use App\Http\Controllers\Dashboard\LandingContactMessageController;
use App\Http\Controllers\Dashboard\LandingContactSectionController;
use App\Http\Controllers\Dashboard\LandingNewsletterSectionController;
use App\Http\Controllers\Dashboard\LandingNewsletterSubscriberController;
use App\Http\Controllers\Dashboard\LandingLegalPageController;
use App\Http\Controllers\Dashboard\LandingSettingController;
use App\Http\Controllers\Dashboard\LandingSeoController;
use App\Http\Controllers\Dashboard\LandingStatSectionController;
use App\Http\Controllers\Dashboard\LandingStatItemController;
use App\Http\Controllers\Dashboard\LandingSolutionSectionController;
use App\Http\Controllers\Dashboard\LandingSolutionSlideController;
use App\Http\Controllers\Dashboard\LandingSolutionPointController;
use App\Http\Controllers\Dashboard\LandingTestimonialSectionController;
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
use Modules\Companies\Http\Controllers\CompanyController;
use Modules\Core\Http\Controllers\SearchController;
use Modules\Core\Http\Controllers\NotificationController as CrmNotificationController;
use Modules\Meetings\Http\Controllers\MeetingController;
use Modules\Meetings\Http\Controllers\CallController;
use Modules\Products\Http\Controllers\ProductController;
use Modules\Invoices\Http\Controllers\InvoicePaymentController;
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

    // Notifications
    Route::get('notifications/unread-count', [CrmNotificationController::class, 'unreadCount']);
    Route::put('notifications/read-all', [CrmNotificationController::class, 'markAllRead']);
    Route::put('notifications/{notification}/read', [CrmNotificationController::class, 'markRead']);
    Route::delete('notifications/{notification}', [CrmNotificationController::class, 'destroy']);
    Route::get('notifications', [CrmNotificationController::class, 'index']);

    // CRM Resources
    Route::get('contacts/{contact}/activities', [\Modules\Contacts\Http\Controllers\ContactController::class, 'activities']);
    Route::post('contacts/bulk', [ContactController::class, 'bulkAction']);
    Route::post('contacts/import', [ContactController::class, 'import']);
    Route::apiResource('contacts', ContactController::class);
    Route::post('deals/bulk', [DealController::class, 'bulkAction']);
    Route::apiResource('deals', DealController::class);
    Route::put('deal-stages/reorder', [DealStageController::class, 'reorder']);
    Route::apiResource('deal-stages', DealStageController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::patch('invoices/{invoice}/status', [InvoiceController::class, 'updateStatus']);
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send']);
    Route::get('invoices/{invoice}/payments', [InvoicePaymentController::class, 'index']);
    Route::post('invoices/{invoice}/payments', [InvoicePaymentController::class, 'store']);
    Route::delete('invoices/{invoice}/payments/{payment}', [InvoicePaymentController::class, 'destroy']);

    // Meetings & Calls
    Route::apiResource('meetings', MeetingController::class);
    Route::apiResource('calls', CallController::class)->only(['index', 'store', 'show', 'destroy']);

    // Products
    Route::get('products/dropdown', [ProductController::class, 'dropdown']);
    Route::apiResource('products', ProductController::class);

    // Companies
    Route::get('companies/dropdown', [CompanyController::class, 'dropdown']);
    Route::apiResource('companies', CompanyController::class);

    // Reports
    Route::get('reports/dashboard', [ReportController::class, 'dashboard']);
    Route::get('reports/deals', [ReportController::class, 'deals']);
    Route::get('reports/revenue', [ReportController::class, 'revenue']);

    // Global Search
    Route::get('search', [SearchController::class, 'search']);

    // Settings
    Route::get('settings/company', [SettingsController::class, 'company']);
    Route::put('settings/company', [SettingsController::class, 'updateCompany']);
    Route::get('settings/users', [SettingsController::class, 'users']);
    Route::post('settings/users', [SettingsController::class, 'inviteUser']);
    Route::get('settings/profile', [SettingsController::class, 'profile']);
    Route::put('settings/profile', [SettingsController::class, 'updateProfile']);
    Route::put('settings/password', [SettingsController::class, 'updatePassword']);
});

// Note: PDF is served from the public CRM group above (token-based auth via ?token=)

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
        Route::get('landing/seo', [LandingSeoController::class, 'show']);
        Route::put('landing/seo', [LandingSeoController::class, 'update']);
        Route::get('landing/hero', [LandingHeroController::class, 'show']);
        Route::put('landing/hero', [LandingHeroController::class, 'update']);
        Route::apiResource('landing/hero-slides', LandingHeroSlideController::class, ['as' => 'landing', 'parameters' => ['hero-slides' => 'landingHeroSlide']]);
        Route::get('landing/problems', [LandingProblemSectionController::class, 'show']);
        Route::put('landing/problems', [LandingProblemSectionController::class, 'update']);
        Route::apiResource('landing/problem-items', LandingProblemItemController::class, ['as' => 'landing', 'parameters' => ['problem-items' => 'landingProblemItem']]);
        Route::get('landing/features', [LandingFeatureSectionController::class, 'show']);
        Route::put('landing/features', [LandingFeatureSectionController::class, 'update']);
        Route::apiResource('landing/feature-items', LandingFeatureItemController::class, ['as' => 'landing', 'parameters' => ['feature-items' => 'landingFeatureItem']]);
        Route::get('landing/how-it-works', [LandingHowSectionController::class, 'show']);
        Route::put('landing/how-it-works', [LandingHowSectionController::class, 'update']);
        Route::apiResource('landing/how-items', LandingHowItemController::class, ['as' => 'landing', 'parameters' => ['how-items' => 'landingHowItem']]);
        Route::get('landing/why', [LandingWhySectionController::class, 'show']);
        Route::put('landing/why', [LandingWhySectionController::class, 'update']);
        Route::apiResource('landing/why-items', LandingWhyItemController::class, ['as' => 'landing', 'parameters' => ['why-items' => 'landingWhyItem']]);
        Route::get('landing/stats', [LandingStatSectionController::class, 'show']);
        Route::put('landing/stats', [LandingStatSectionController::class, 'update']);
        Route::apiResource('landing/stat-items', LandingStatItemController::class, ['as' => 'landing', 'parameters' => ['stat-items' => 'landingStatItem']]);
        Route::get('landing/solutions', [LandingSolutionSectionController::class, 'show']);
        Route::put('landing/solutions', [LandingSolutionSectionController::class, 'update']);
        Route::apiResource('landing/solution-slides', LandingSolutionSlideController::class, ['as' => 'landing', 'parameters' => ['solution-slides' => 'landingSolutionSlide']]);
        Route::apiResource('landing/solution-points', LandingSolutionPointController::class, ['as' => 'landing', 'parameters' => ['solution-points' => 'landingSolutionPoint']]);
        Route::get('landing/testimonials', [LandingTestimonialSectionController::class, 'show']);
        Route::put('landing/testimonials', [LandingTestimonialSectionController::class, 'update']);
        Route::apiResource('landing/testimonial-items', LandingTestimonialController::class, ['as' => 'landing', 'parameters' => ['testimonial-items' => 'landingTestimonial']]);
        Route::get('landing/faqs', [LandingFaqSectionController::class, 'show']);
        Route::put('landing/faqs', [LandingFaqSectionController::class, 'update']);
        Route::apiResource('landing/faq-items', LandingFaqController::class, ['as' => 'landing', 'parameters' => ['faq-items' => 'landingFaq']]);
        Route::apiResource('landing/partners',       LandingPartnerController::class,    ['as' => 'landing']);
        Route::get('landing/contact', [LandingContactSectionController::class, 'show']);
        Route::put('landing/contact', [LandingContactSectionController::class, 'update']);
        Route::get('landing/contact-messages',                     [LandingContactMessageController::class, 'index']);
        Route::get('landing/contact-messages/{landingContactMessage}',          [LandingContactMessageController::class, 'show']);
        Route::post('landing/contact-messages/{landingContactMessage}/mark-replied', [LandingContactMessageController::class, 'markRead']);
        Route::delete('landing/contact-messages/{landingContactMessage}',       [LandingContactMessageController::class, 'destroy']);
        Route::get('landing/newsletter', [LandingNewsletterSectionController::class, 'show']);
        Route::put('landing/newsletter', [LandingNewsletterSectionController::class, 'update']);
        Route::get('landing/newsletter-subscribers', [LandingNewsletterSubscriberController::class, 'index']);
        Route::delete('landing/newsletter-subscribers/{landingNewsletterSubscriber}', [LandingNewsletterSubscriberController::class, 'destroy']);
        Route::get('landing/legal-pages', [LandingLegalPageController::class, 'index']);
        Route::get('landing/legal-pages/{landingLegalPage}', [LandingLegalPageController::class, 'show']);
        Route::put('landing/legal-pages/{landingLegalPage}', [LandingLegalPageController::class, 'update']);

        // CRM Statistics & Upgrade Requests
        Route::get('crm-statistics', [\App\Http\Controllers\Dashboard\CrmStatisticsController::class, 'index']);
        Route::get('crm-upgrade-requests', [\App\Http\Controllers\Dashboard\CrmStatisticsController::class, 'upgrades']);
        Route::post('crm-upgrade-requests/{id}/approve', [\App\Http\Controllers\Dashboard\CrmStatisticsController::class, 'approveUpgrade']);

    });

});

