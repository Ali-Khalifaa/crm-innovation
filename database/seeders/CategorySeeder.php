<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Category::truncate();

        $categories = [
            [
                'title_e' => 'Category A - School accommodation (Fully booked - Waiting list)',
                'title' => 'الفئة أ - الإقامة المدرسية (محجوزة بالكامل - قائمة الانتظار)',
                'description_e' => 'Players and leaders are accommodated in one classroom together',
                'description' => 'يتم إقامة اللاعبين والقادة في فصل دراسي واحد معًا',
            ],
            [
                'title_e' => 'Category B - Hotel accommodation (Fully booked - Waiting list)',
                'title' => 'الفئة ب - الإقامة في الفندق (محجوزة بالكامل - قائمة الانتظار)',
                'description_e' => 'Hotel booked through Gothia Cup',
                'description' => 'الفندق محجوز من خلال كأس غوثيا',
            ],
            [
                'title_e' => 'Category C - Own accommodation (Fully booked)',
                'title' => 'الفئة ج - الإقامة الخاصة (محجوزة بالكامل)',
                'description_e' => 'Teams arranging their own accommodation',
                'description' => 'الفرق ترتب إقامتها الخاصة',
            ],
          
        ];

        foreach ($categories as $key => $value) {
            $category = Category::create([
                'status'   => 1,
            ]);

            $category->setTranslations([
                'ar' => [
                    'title'       => $value['title'],
                    'description' => $value['description'],
                ],
                'en' => [
                    'title'       => $value['title_e'],
                    'description' => $value['description_e'],
                ],

            ]);

        }

    }
}
