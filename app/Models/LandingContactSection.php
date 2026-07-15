<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingContactSection extends Model
{
    protected $table = 'landing_contact_section';

    protected $fillable = [
        'cta_subtitle',
        'cta_headline',
        'cta_intro',
        'cta_btn1_text',
        'cta_btn1_link',
        'cta_btn2_text',
        'cta_btn2_link',
        'form_subtitle',
        'form_title',
        'form_intro',
        'bg_image',
        'is_active',
    ];

    protected $casts = [
        'cta_subtitle'  => 'array',
        'cta_headline'  => 'array',
        'cta_intro'     => 'array',
        'cta_btn1_text' => 'array',
        'cta_btn2_text' => 'array',
        'form_subtitle' => 'array',
        'form_title'    => 'array',
        'form_intro'    => 'array',
        'is_active'     => 'boolean',
    ];
}
