<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSolutionPoint extends Model
{
    protected $fillable = [
        'text',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'text'      => 'array',
        'is_active' => 'boolean',
    ];
}
