<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LandingFeatureItemRequest extends FormRequest
{
    private const MAX_SHORT = 100;

    private const MAX_DESC = 500;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = (bool) $this->route('landingFeatureItem');
        $itemId   = $isUpdate ? $this->route('landingFeatureItem')->id : null;

        return [
            'title_ar'       => 'required|string|max:' . self::MAX_SHORT,
            'title_en'       => 'required|string|max:' . self::MAX_SHORT,
            'description_ar' => 'required|string|max:' . self::MAX_DESC,
            'description_en' => 'required|string|max:' . self::MAX_DESC,
            'image'          => 'nullable|file|mimes:jpeg,jpg,png,webp,svg|max:5120',
            'sort_order'     => [
                'required',
                'integer',
                'min:1',
                Rule::unique('landing_feature_items', 'sort_order')->ignore($itemId),
            ],
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title_ar.required'       => 'العنوان (عربي) مطلوب.',
            'title_en.required'       => 'العنوان (إنجليزي) مطلوب.',
            'description_ar.required' => 'الوصف (عربي) مطلوب.',
            'description_en.required' => 'الوصف (إنجليزي) مطلوب.',
            'sort_order.required'     => 'الترتيب مطلوب.',
            'sort_order.min'          => 'الترتيب يجب أن يكون 1 على الأقل.',
            'sort_order.unique'       => 'رقم الترتيب مستخدم بالفعل، اختر رقماً آخر.',
            'image.mimes'             => 'الصورة يجب أن تكون بصيغة مدعومة.',
            'image.max'               => 'حجم الصورة كبير جداً.',
        ];
    }
}
