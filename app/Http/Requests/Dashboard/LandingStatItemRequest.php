<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LandingStatItemRequest extends FormRequest
{
    private const MAX_VALUE = 50;

    private const MAX_LABEL = 100;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = (bool) $this->route('landingStatItem');
        $itemId   = $isUpdate ? $this->route('landingStatItem')->id : null;

        return [
            'value_ar'   => 'required|string|max:' . self::MAX_VALUE,
            'value_en'   => 'required|string|max:' . self::MAX_VALUE,
            'label_ar'   => 'required|string|max:' . self::MAX_LABEL,
            'label_en'   => 'required|string|max:' . self::MAX_LABEL,
            'suffix'     => 'nullable|string|max:20',
            'image'      => 'nullable|file|mimes:jpeg,jpg,png,webp,svg|max:5120',
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('landing_stat_items', 'sort_order')->ignore($itemId),
            ],
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'value_ar.required' => 'القيمة (عربي) مطلوبة.',
            'value_en.required' => 'القيمة (إنجليزي) مطلوبة.',
            'label_ar.required' => 'الوصف (عربي) مطلوب.',
            'label_en.required' => 'الوصف (إنجليزي) مطلوب.',
            'sort_order.required' => 'الترتيب مطلوب.',
            'sort_order.min'      => 'الترتيب يجب أن يكون 1 على الأقل.',
            'sort_order.unique'   => 'رقم الترتيب مستخدم بالفعل، اختر رقماً آخر.',
            'suffix.max'          => 'اللاحقة يجب ألا تتجاوز 20 حرف.',
            'image.mimes'         => 'الصورة يجب أن تكون بصيغة مدعومة.',
            'image.max'           => 'حجم الصورة كبير جداً.',
        ];
    }
}
