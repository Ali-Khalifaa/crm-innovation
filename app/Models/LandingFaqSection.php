<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingFaqSection extends Model
{
    protected $table = 'landing_faq_section';

    protected $fillable = [
        'subtitle',
        'headline',
        'intro',
        'bg_image',
        'is_active',
    ];

    protected $casts = [
        'subtitle'  => 'array',
        'headline'  => 'array',
        'intro'     => 'array',
        'is_active' => 'boolean',
    ];
}
