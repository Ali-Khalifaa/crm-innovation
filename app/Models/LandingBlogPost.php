<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LandingBlogPost extends Model {
    protected $fillable = ['title_en','title_ar','excerpt_en','excerpt_ar','content_en','content_ar','image','author_name','published_at','is_published','sort_order'];
    protected $casts = ['is_published' => 'boolean', 'published_at' => 'datetime'];
}
