<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingHeroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageRule = $this->isMethod('PUT')
            ? 'nullable|file|mimes:jpeg,jpg,png,webp,svg'
            : 'nullable|file|mimes:jpeg,jpg,png,webp,svg';

        return [
            'title_en'                => 'required|string|max:255',
            'title_ar'                => 'required|string|max:255',
            'subtitle_en'             => 'nullable|string|max:255',
            'subtitle_ar'             => 'nullable|string|max:255',
            'description_en'          => 'nullable|string|max:1000',
            'description_ar'          => 'nullable|string|max:1000',
            'btn_primary_text_en'     => 'nullable|string|max:100',
            'btn_primary_text_ar'     => 'nullable|string|max:100',
            'btn_primary_url'         => 'nullable|string|max:255',
            'btn_secondary_text_en'   => 'nullable|string|max:100',
            'btn_secondary_text_ar'   => 'nullable|string|max:100',
            'btn_secondary_url'       => 'nullable|string|max:255',
            'image'                   => $imageRule,
            'is_active'               => 'boolean',
        ];
    }
}
