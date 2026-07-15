<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingSeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meta_title'          => 'nullable|array',
            'meta_title.ar'       => 'nullable|string|max:160',
            'meta_title.en'       => 'nullable|string|max:160',
            'meta_description'    => 'nullable|array',
            'meta_description.ar' => 'nullable|string|max:500',
            'meta_description.en' => 'nullable|string|max:500',
            'meta_keywords'       => 'nullable|array',
            'meta_keywords.ar'    => 'nullable|string|max:500',
            'meta_keywords.en'    => 'nullable|string|max:500',
            'og_title'            => 'nullable|array',
            'og_title.ar'         => 'nullable|string|max:160',
            'og_title.en'         => 'nullable|string|max:160',
            'og_description'      => 'nullable|array',
            'og_description.ar'   => 'nullable|string|max:500',
            'og_description.en'   => 'nullable|string|max:500',
            'og_image'            => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'twitter_title'       => 'nullable|array',
            'twitter_title.ar'    => 'nullable|string|max:160',
            'twitter_title.en'    => 'nullable|string|max:160',
            'twitter_description' => 'nullable|array',
            'twitter_description.ar' => 'nullable|string|max:500',
            'twitter_description.en' => 'nullable|string|max:500',
            'twitter_image'       => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'robots'              => 'nullable|string|max:120',
            'author'              => 'nullable|string|max:120',
            'theme_color'         => 'nullable|string|max:20',
            'canonical_url'       => 'nullable|url|max:255',
            'is_active'           => 'nullable|boolean',
        ];
    }
}
