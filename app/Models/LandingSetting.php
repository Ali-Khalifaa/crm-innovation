<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name_en', 'site_name_ar',
        'email', 'phone', 'whatsapp',
        'address_en', 'address_ar',
        'facebook', 'twitter', 'linkedin', 'instagram', 'youtube',
        'meta_title_en', 'meta_title_ar',
        'meta_description_en', 'meta_description_ar',
    ];
}
