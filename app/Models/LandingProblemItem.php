<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingProblemItem extends Model
{
    protected $table = 'landing_problem_items';

    protected $fillable = [
        'title',
        'description',
        'image',
        'impact_label',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'title'        => 'array',
        'description'  => 'array',
        'impact_label' => 'array',
        'is_active'    => 'boolean',
    ];
}
