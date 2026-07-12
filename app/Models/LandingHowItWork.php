<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingHowItWork extends Model
{
    use HasFactory;

    protected $table = 'landing_how_it_works';

    protected $fillable = [
        'step_number', 'image',
        'title_en', 'title_ar',
        'description_en', 'description_ar',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
