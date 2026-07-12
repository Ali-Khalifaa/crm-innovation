<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LandingTeamMember extends Model {
    protected $fillable = ['name_en','name_ar','role_en','role_ar','photo','facebook','twitter','linkedin','instagram','sort_order','is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
