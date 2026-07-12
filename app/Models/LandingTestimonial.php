<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LandingTestimonial extends Model {
    protected $fillable = ['name','designation_en','designation_ar','review_en','review_ar','rating','photo','sort_order','is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
