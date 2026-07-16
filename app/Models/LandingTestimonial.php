<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingTestimonial extends Model
{
    protected $fillable = [
        'name',
        'designation',
        'review',
        'rating',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'name'        => 'array',
        'designation' => 'array',
        'review'      => 'array',
        'is_active'   => 'boolean',
    ];
}
