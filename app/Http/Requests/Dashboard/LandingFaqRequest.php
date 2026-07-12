<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_en' => 'required|string|max:500',
            'question_ar' => 'required|string|max:500',
            'answer_en'   => 'required|string|max:2000',
            'answer_ar'   => 'required|string|max:2000',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ];
    }
}
