<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingStatSection extends Model
{
    protected $table = 'landing_stat_section';

    protected $fillable = [
        'subtitle',
        'headline',
        'bg_image',
        'is_active',
    ];

    protected $casts = [
        'subtitle'  => 'array',
        'headline'  => 'array',
        'is_active' => 'boolean',
    ];
}
