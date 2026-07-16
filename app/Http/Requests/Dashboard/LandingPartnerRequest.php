<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LandingPartnerRequest extends FormRequest
{
    private const MAX_NAME = 120;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate  = (bool) $this->route('partner');
        $partnerId = $isUpdate ? $this->route('partner')->id : null;

        return [
            'name' => 'required|string|max:' . self::MAX_NAME,
            'image' => $isUpdate
                ? 'nullable|file|mimes:jpeg,jpg,png,webp,svg|max:5120'
                : 'required|file|mimes:jpeg,jpg,png,webp,svg|max:5120',
            'url' => 'nullable|url|max:255',
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('landing_partners', 'sort_order')->ignore($partnerId),
            ],
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'اسم الشريك مطلوب.',
            'name.max'            => 'اسم الشريك يجب ألا يتجاوز ' . self::MAX_NAME . ' حرف.',
            'image.required'      => 'لوجو الشريك مطلوب.',
            'image.mimes'         => 'لوجو الشريك يجب أن يكون بصيغة مدعومة.',
            'image.max'           => 'حجم لوجو الشريك كبير جداً.',
            'url.url'             => 'رابط الموقع غير صالح.',
            'url.max'             => 'رابط الموقع طويل جداً.',
            'sort_order.required' => 'الترتيب مطلوب.',
            'sort_order.min'      => 'الترتيب يجب أن يكون 1 على الأقل.',
            'sort_order.unique'   => 'رقم الترتيب مستخدم بالفعل، اختر رقماً آخر.',
        ];
    }
}
