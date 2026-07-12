<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Schema::disableForeignKeyConstraints();
        Country::truncate();

        $country = Country::create([
            'phone_code'   => '+966',
            'domain' =>'dorr.co',
            'alpha_code' =>'SA',
            'number_of_phone_digits' =>9,
            'status' => 1,
        ]);

        $country->setTranslations([
            'ar' => [
                'title'       => 'المملكة العربية السعودية',
                'description' => 'المملكة العربية السعودية',
            ],
            'en' => [
                'title'       => 'Saudi Arabia',
                'description' => 'Saudi Arabia',
            ],

        ]);

        // add kuwait
        $country = Country::create([
            'phone_code'   => '+965',
            'domain' =>'dorr.co',
            'alpha_code' =>'KW',
            'number_of_phone_digits' => 8,
            'status' => 1,
        ]);
        $country->setTranslations([
            'ar' => [
                'title'       => 'الكويت',
                'description' => 'الكويت',
            ],
            'en' => [
                'title'       => 'Kuwait',
                'description' => 'Kuwait',
            ],
        ]);

          // add Bahrain
        $country = Country::create([
            'phone_code'   => '+973',
            'domain' =>'dorr.co',
            'alpha_code' =>'BH',
            'number_of_phone_digits' => 8,
            'status' => 1,
        ]);
        $country->setTranslations([
            'ar' => [
            'title'       => 'البحرين',
            'description' => 'البحرين',
            ],
            'en' => [
            'title'       => 'Bahrain',
            'description' => 'Bahrain',
            ],
        ]);

        // add Qatar
        $country = Country::create([
            'phone_code'   => '+974',
            'domain' =>'dorr.co',
            'alpha_code' =>'QA',
            'number_of_phone_digits' => 8,
            'status' => 1,
        ]);
        $country->setTranslations([
            'ar' => [
            'title'       => 'قطر',
            'description' => 'قطر',
            ],
            'en' => [
            'title'       => 'Qatar',
            'description' => 'Qatar',
            ],
        ]);

         // add united arab emirates
        $country = Country::create([
            'phone_code'   => '+971',
            'domain' =>'dorr.co',
            'alpha_code' =>'AE',
            'number_of_phone_digits' => 9,
            'status' => 1,
        ]);
        $country->setTranslations([
            'ar' => [
            'title'       => 'الإمارات العربية المتحدة',
            'description' => 'الإمارات العربية المتحدة',
            ],
            'en' => [
            'title'       => 'United Arab Emirates',
            'description' => 'United Arab Emirates',
            ],
        ]);

        // add Oman
        $country = Country::create([
            'phone_code'   => '+968',
            'domain' =>'dorr.co',
            'alpha_code' =>'OM',
            'number_of_phone_digits' =>8,
            'status' => 1,
        ]);
        $country->setTranslations([
            'ar' => [
            'title'       => 'سلطنة عمان',
            'description' => 'سلطنة عمان',
            ],
            'en' => [
            'title'       => 'Sultanate of Oman',
            'description' => 'Sultanate of Oman',
            ],
        ]);

        // add egypt
        $country = Country::create([
            'phone_code'   => '+20',
            'domain' =>'dorr.co',
            'alpha_code' =>'EG',
            'number_of_phone_digits' =>10,
            'status' => 1,
        ]);
        $country->setTranslations([
            'ar' => [
            'title'       => 'مصر',
            'description' => 'مصر',
            ],
            'en' => [
            'title'       => 'Egypt',
            'description' => 'Egypt',
            ],
        ]);

        // add Yemen
        $country = Country::create([
            'phone_code'   => '+967',
            'domain' =>'dorr.co',
            'alpha_code' =>'YE',
            'number_of_phone_digits' =>9,
            'status' => 1,
        ]);
        $country->setTranslations([
            'ar' => [
            'title'       => 'اليمن',
            'description' => 'اليمن',
            ],
            'en' => [
            'title'       => 'Yemen',
            'description' => 'Yemen',
            ],
        ]);

        // add Algeria

        $country = Country::create([
            'phone_code'   => '+213',
            'domain' =>'dorr.co',
            'alpha_code' =>'DZ',
            'number_of_phone_digits' =>9,
            'status' => 1,
        ]);
        $country->setTranslations([
            'ar' => [
            'title'       => 'الجزائر',
            'description' => 'الجزائر',
            ],
            'en' => [
            'title'       => 'Algeria',
            'description' => 'Algeria',
            ],
        ]);

        // add Malaysia

        // add Malaysia
        $country = Country::create([
            'phone_code'   => '+60',
            'domain' =>'dorr.co',
            'alpha_code' =>'MY',
            'number_of_phone_digits' =>9,
            'status' => 1,
        ]);
        $country->setTranslations([
            'ar' => [
            'title'       => 'ماليزيا',
            'description' => 'ماليزيا',
            ],
            'en' => [
            'title'       => 'Malaysia',
            'description' => 'Malaysia',
            ],
        ]);

    }
}
