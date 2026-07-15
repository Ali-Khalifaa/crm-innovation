<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingProblemSection extends Model
{
    protected $table = 'landing_problem_section';

    protected $fillable = [
        'subtitle',
        'headline',
        'intro',
        'insights',
        'cta',
        'is_active',
    ];

    protected $casts = [
        'subtitle'  => 'array',
        'headline'  => 'array',
        'intro'     => 'array',
        'insights'  => 'array',
        'cta'       => 'array',
        'is_active' => 'boolean',
    ];
}
