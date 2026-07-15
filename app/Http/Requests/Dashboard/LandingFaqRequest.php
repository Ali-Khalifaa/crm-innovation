<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LandingFaqRequest extends FormRequest
{
    private const MAX_QUESTION = 500;

    private const MAX_ANSWER = 2000;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = (bool) $this->route('landingFaq');
        $itemId   = $isUpdate ? $this->route('landingFaq')->id : null;

        return [
            'question_ar' => 'required|string|max:' . self::MAX_QUESTION,
            'question_en' => 'required|string|max:' . self::MAX_QUESTION,
            'answer_ar'   => 'required|string|max:' . self::MAX_ANSWER,
            'answer_en'   => 'required|string|max:' . self::MAX_ANSWER,
            'sort_order'  => [
                'required',
                'integer',
                'min:1',
                Rule::unique('landing_faqs', 'sort_order')->ignore($itemId),
            ],
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'question_ar.required' => 'السؤال (عربي) مطلوب.',
            'question_en.required' => 'السؤال (إنجليزي) مطلوب.',
            'answer_ar.required'   => 'الإجابة (عربي) مطلوبة.',
            'answer_en.required'   => 'الإجابة (إنجليزي) مطلوبة.',
            'sort_order.required'  => 'الترتيب مطلوب.',
            'sort_order.min'       => 'الترتيب يجب أن يكون 1 على الأقل.',
            'sort_order.unique'    => 'رقم الترتيب مستخدم بالفعل، اختر رقماً آخر.',
        ];
    }
}
