<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingHero extends Model
{
    use HasFactory;

    protected $table = 'landing_hero';

    protected $fillable = [
        'title_en', 'title_ar',
        'subtitle_en', 'subtitle_ar',
        'description_en', 'description_ar',
        'btn_primary_text_en', 'btn_primary_text_ar', 'btn_primary_url',
        'btn_secondary_text_en', 'btn_secondary_text_ar', 'btn_secondary_url',
        'image', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
