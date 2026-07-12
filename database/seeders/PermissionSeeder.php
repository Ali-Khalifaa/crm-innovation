<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Notify;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [

            ['name' => 'banner read',  'category' => 'Banners'],
            ['name' => 'banner create',  'category' => 'Banners'],
            ['name' => 'banner edit',  'category' => 'Banners'],
            ['name' => 'banner delete',  'category' => 'Banners'],

            ['name' => 'admin read',  'category' => 'Admins'],
            ['name' => 'admin create',  'category' => 'Admins'],
            ['name' => 'admin edit',  'category' => 'Admins'],
            ['name' => 'admin delete',  'category' => 'Admins'],
            ['name' => 'admin send notification'  ,  'category' => 'Admins'],


            ['name' => 'role read',  'category' => 'Roles'],
            ['name' => 'role create',  'category' => 'Roles'],
            ['name' => 'role edit',  'category' => 'Roles'],
            ['name' => 'role delete',  'category' => 'Roles'],

            ['name' => 'language read',  'category' => 'Languages'],
            ['name' => 'language create',  'category' => 'Languages'],
            ['name' => 'language edit',  'category' => 'Languages'],
            ['name' => 'language delete',  'category' => 'Languages'],

            ['name' => 'category read'  ,  'category' => 'Categories'],
            ['name' => 'category create',  'category' => 'Categories'],
            ['name' => 'category edit'  ,  'category' => 'Categories'],
            ['name' => 'category delete',  'category' => 'Categories'],

            ['name' => 'country read'  ,  'category' => 'Countries'],
            ['name' => 'country create',  'category' => 'Countries'],
            ['name' => 'country edit'  ,  'category' => 'Countries'],
            ['name' => 'country delete',  'category' => 'Countries'],

            ['name' => 'frequently asked question read'  ,  'category' => 'Frequently Asked Question'],
            ['name' => 'frequently asked question create',  'category' => 'Frequently Asked Question'],
            ['name' => 'frequently asked question edit'  ,  'category' => 'Frequently Asked Question'],
            ['name' => 'frequently asked question delete',  'category' => 'Frequently Asked Question '],

            ['name' => 'database backup read',  'category' => 'Database Backup'],
            ['name' => 'database backup create',  'category' => 'Database Backup'],

            ['name' => 'color read'  ,  'category' => 'Colors'],
            ['name' => 'color create',  'category' => 'Colors'],
            ['name' => 'color edit'  ,  'category' => 'Colors'],
            ['name' => 'color delete',  'category' => 'Colors'],

            ['name' => 'join us read',  'category' => 'Join Us'],
            ['name' => 'join us edit',  'category' => 'Join Us'],

            ['name' => 'area read'  ,  'category' => 'Areas'],
            ['name' => 'area create',  'category' => 'Areas'],
            ['name' => 'area edit'  ,  'category' => 'Areas'],
            ['name' => 'area delete',  'category' => 'Areas'],

        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']],[
                'name' => $permission['name'],
                'category' => $permission['category'],
            ]);
        }
    }
}
