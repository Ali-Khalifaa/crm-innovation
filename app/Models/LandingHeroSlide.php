<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingHeroSlide extends Model
{
    use HasFactory;

    protected $table = 'landing_hero_slides';

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'focus_position',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'title'    => 'array',
        'subtitle' => 'array',
        'is_active' => 'boolean',
    ];
}
