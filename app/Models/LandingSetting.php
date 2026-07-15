<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name_en', 'site_name_ar',
        'logo', 'logo_footer', 'favicon',
        'email', 'phone', 'whatsapp',
        'address_en', 'address_ar',
        'facebook', 'twitter', 'linkedin', 'instagram', 'youtube',
    ];
}
