<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSolutionSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'icon',
        'focus_position',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'title'     => 'array',
        'subtitle'  => 'array',
        'is_active' => 'boolean',
    ];
}
