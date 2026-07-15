<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingNewsletterSection extends Model
{
    protected $table = 'landing_newsletter_section';

    protected $fillable = [
        'title',
        'placeholder',
        'button_text',
        'bg_image',
        'is_active',
    ];

    protected $casts = [
        'title'       => 'array',
        'placeholder' => 'array',
        'button_text' => 'array',
        'is_active'   => 'boolean',
    ];
}
