<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LandingAbout extends Model {
    protected $table = 'landing_about';
    protected $fillable = ['subtitle_en','subtitle_ar','title_en','title_ar','description_en','description_ar','image','box1_icon','box1_title_en','box1_title_ar','box1_desc_en','box1_desc_ar','box2_icon','box2_title_en','box2_title_ar','box2_desc_en','box2_desc_ar','is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
