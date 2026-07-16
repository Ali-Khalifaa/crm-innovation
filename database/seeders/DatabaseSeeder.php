<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // ===== 1. كل الـ Permissions الأول =====
        $this->call(PermissionSeeder::class);
        $this->call(LandingPermissionSeeder::class);
        $this->call(LandingPartnerSeeder::class); // فيه permissions الشركاء
        $this->call(LandingNewSectionsSeeder::class); // permissions الأقسام الجديدة

        // ===== 2. CreateAdminSeeder بعد كل الـ permissions =====
        // بيعمل sync لكل الـ permissions الموجودة للـ SuperAdmin
        $this->call(CreateAdminSeeder::class);

        // ===== 3. باقي الـ Data Seeders =====
        $this->call(LanguageSeeder::class);
        $this->call(JoinUsSeeder::class);
        $this->call(AreaSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(CountrySeeder::class);

        // CRM
        $this->call(PlansSeeder::class);
        $this->call(DemoTenantSeeder::class);
        $this->call(EgyptianDemoDataSeeder::class);

        // Landing Page Content
        $this->call(LandingSettingSeeder::class);
        $this->call(LandingSeoSeeder::class);
        $this->call(LandingHeroSeeder::class);
        $this->call(LandingProblemSeeder::class);
        $this->call(LandingStatSeeder::class);
        $this->call(LandingFeatureSeeder::class);
        $this->call(LandingHowItWorkSeeder::class);
        $this->call(LandingWhySeeder::class);
        $this->call(LandingFaqSeeder::class);
        $this->call(LandingSolutionSeeder::class);
        $this->call(LandingTestimonialSeeder::class);
    }
}
