<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingStatItem extends Model
{
    protected $table = 'landing_stat_items';

    protected $fillable = [
        'value',
        'suffix',
        'label',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'value'     => 'array',
        'label'     => 'array',
        'is_active' => 'boolean',
    ];
}
