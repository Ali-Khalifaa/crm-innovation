<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSolutionSection extends Model
{
    protected $table = 'landing_solution_section';

    protected $fillable = [
        'subtitle',
        'headline',
        'description',
        'button',
        'is_active',
    ];

    protected $casts = [
        'subtitle'    => 'array',
        'headline'    => 'array',
        'description' => 'array',
        'button'      => 'array',
        'is_active'   => 'boolean',
    ];
}
