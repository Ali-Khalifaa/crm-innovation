<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingStatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'value_en'   => 'required|string|max:50',
            'value_ar'   => 'required|string|max:50',
            'suffix'     => 'nullable|string|max:10',
            'label_en'   => 'required|string|max:100',
            'label_ar'   => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
        ];
    }
}
