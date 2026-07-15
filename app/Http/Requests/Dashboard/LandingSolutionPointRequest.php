<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LandingSolutionPointRequest extends FormRequest
{
    private const MAX_TEXT = 120;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = (bool) $this->route('landingSolutionPoint');
        $pointId  = $isUpdate ? $this->route('landingSolutionPoint')->id : null;

        return [
            'text_ar'    => 'required|string|max:' . self::MAX_TEXT,
            'text_en'    => 'required|string|max:' . self::MAX_TEXT,
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('landing_solution_points', 'sort_order')->ignore($pointId),
            ],
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'text_ar.required'    => 'النص (عربي) مطلوب.',
            'text_en.required'    => 'النص (إنجليزي) مطلوب.',
            'text_ar.max'         => 'النص (عربي) يجب ألا يتجاوز ' . self::MAX_TEXT . ' حرف.',
            'text_en.max'         => 'النص (إنجليزي) يجب ألا يتجاوز ' . self::MAX_TEXT . ' حرف.',
            'sort_order.required' => 'الترتيب مطلوب.',
            'sort_order.min'      => 'الترتيب يجب أن يكون 1 على الأقل.',
            'sort_order.unique'   => 'رقم الترتيب مستخدم بالفعل، اختر رقماً آخر.',
        ];
    }
}
