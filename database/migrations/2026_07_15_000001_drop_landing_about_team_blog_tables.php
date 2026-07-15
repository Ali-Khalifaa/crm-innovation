<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('landing_blog_posts');
        Schema::dropIfExists('landing_team_members');
        Schema::dropIfExists('landing_about');

        $permissions = [
            'landing about read',
            'landing about edit',
            'landing team read',
            'landing team create',
            'landing team edit',
            'landing team delete',
            'landing blog read',
            'landing blog create',
            'landing blog edit',
            'landing blog delete',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }

    public function down(): void
    {
        // Intentionally empty — removed sections are not restored automatically.
    }
};
