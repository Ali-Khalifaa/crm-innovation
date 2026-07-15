<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingFeatureSection extends Model
{
    protected $table = 'landing_feature_section';

    protected $fillable = [
        'subtitle',
        'headline',
        'is_active',
    ];

    protected $casts = [
        'subtitle'  => 'array',
        'headline'  => 'array',
        'is_active' => 'boolean',
    ];
}
