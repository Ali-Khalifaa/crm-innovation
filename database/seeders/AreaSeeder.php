<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Area::truncate();

        $areas = [
            [
            'latitude' => 25.2048,
            'longitude' => 55.2708,
            'name' => 'دبي',
            'name_e' => 'Dubai',
            ],
            [
            'latitude' => 24.4539,
            'longitude' => 54.3773,
            'name' => 'أبو ظبي',
            'name_e' => 'Abu Dhabi',
            ],
            [
            'latitude' => 25.276987,
            'longitude' => 55.296249,
            'name' => 'الشارقة',
            'name_e' => 'Sharjah',
            ],
            [
            'latitude' => 25.4052165,
            'longitude' => 55.5136433,
            'name' => 'عجمان',
            'name_e' => 'Ajman',
            ],
            [
            'latitude' => 25.8006926,
            'longitude' => 55.9761994,
            'name' => 'أم القيوين',
            'name_e' => 'Umm Al Quwain',
            ],
            [
            'latitude' => 25.39124,
            'longitude' => 56.1468,
            'name' => 'رأس الخيمة',
            'name_e' => 'Ras Al Khaimah',
            ],
            [
            'latitude' => 24.1302,
            'longitude' => 56.3578,
            'name' => 'الفجيرة',
            'name_e' => 'Fujairah',
            ],
        ];

        foreach ($areas as $key => $value) {
            $area = Area::create([
                'latitude'   => $value['latitude'],
                'longitude'   => $value['longitude'],
                'status'   => 1,
            ]);

            $area->setTranslations([
                'ar' => [
                    'title'       => $value['name'],
                    'description' => '',
                ],
                'en' => [
                    'title'       => $value['name_e'],
                    'description' => '',
                ],
            ]);

        }

    }
}
