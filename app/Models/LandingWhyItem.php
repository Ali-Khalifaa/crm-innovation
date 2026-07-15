<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingWhyItem extends Model
{
    protected $table = 'landing_why_items';

    protected $fillable = [
        'title',
        'description',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'title'       => 'array',
        'description' => 'array',
        'is_active'   => 'boolean',
    ];
}
