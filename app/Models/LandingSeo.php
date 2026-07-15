<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSeo extends Model
{
    protected $table = 'landing_seo';

    protected $fillable = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'robots',
        'author',
        'theme_color',
        'canonical_url',
        'is_active',
    ];

    protected $casts = [
        'meta_title'          => 'array',
        'meta_description'    => 'array',
        'meta_keywords'       => 'array',
        'og_title'            => 'array',
        'og_description'      => 'array',
        'twitter_title'       => 'array',
        'twitter_description' => 'array',
        'is_active'           => 'boolean',
    ];

    public function localized(string $field, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $locale = $locale === 'ar' ? 'ar' : 'en';
        $value  = $this->{$field};

        if (is_array($value)) {
            return (string) ($value[$locale] ?? $value['en'] ?? $value['ar'] ?? '');
        }

        return '';
    }
}
