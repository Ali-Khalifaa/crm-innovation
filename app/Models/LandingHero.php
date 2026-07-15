<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingHero extends Model
{
    use HasFactory;

    protected $table = 'landing_hero';

    protected $fillable = [
        'subtitle',
        'headline',
        'description',
        'btn_primary',
        'btn_secondary',
        'bg_image',
        'bg_overlay_image',
        'is_active',
    ];

    protected $casts = [
        'subtitle'      => 'array',
        'headline'      => 'array',
        'description'   => 'array',
        'btn_primary'   => 'array',
        'btn_secondary' => 'array',
        'is_active'     => 'boolean',
    ];
}
