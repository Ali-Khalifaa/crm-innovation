<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'value_en', 'value_ar', 'suffix',
        'label_en', 'label_ar',
        'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
