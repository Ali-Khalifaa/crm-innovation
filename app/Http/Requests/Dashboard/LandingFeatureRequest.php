<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'icon'           => 'nullable|string|max:100',
            'title_en'       => 'required|string|max:200',
            'title_ar'       => 'required|string|max:200',
            'description_en' => 'required|string|max:500',
            'description_ar' => 'required|string|max:500',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'boolean',
        ];
    }
}
