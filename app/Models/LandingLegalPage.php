<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingLegalPage extends Model
{
    public const TYPE_PRIVACY = 1;

    public const TYPE_TERMS = 2;

    protected $fillable = [
        'type',
        'title',
        'content',
        'is_active',
    ];

    protected $casts = [
        'title'     => 'array',
        'content'   => 'array',
        'is_active' => 'boolean',
        'type'      => 'integer',
    ];

    public static function typeLabel(int $type): string
    {
        return match ($type) {
            self::TYPE_PRIVACY => 'سياسة الخصوصية',
            self::TYPE_TERMS   => 'الشروط والأحكام',
            default            => '—',
        };
    }
}
