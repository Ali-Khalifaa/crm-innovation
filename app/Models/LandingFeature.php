<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'icon',
        'title_en', 'title_ar',
        'description_en', 'description_ar',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
