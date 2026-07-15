<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LandingHeroSlideRequest extends FormRequest
{
    private const MAX_SHORT = 100;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = (bool) $this->route('landingHeroSlide');
        $slideId  = $isUpdate ? $this->route('landingHeroSlide')->id : null;

        return [
            'title_ar'    => 'required|string|max:' . self::MAX_SHORT,
            'title_en'    => 'required|string|max:' . self::MAX_SHORT,
            'subtitle_ar' => 'nullable|string|max:' . self::MAX_SHORT,
            'subtitle_en' => 'nullable|string|max:' . self::MAX_SHORT,
            'image'       => $isUpdate
                ? 'nullable|file|mimes:jpeg,jpg,png,webp,svg|max:5120'
                : 'required|file|mimes:jpeg,jpg,png,webp,svg|max:5120',
            'sort_order'  => [
                'required',
                'integer',
                'min:1',
                Rule::unique('landing_hero_slides', 'sort_order')->ignore($slideId),
            ],
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title_ar.required'      => 'العنوان (عربي) مطلوب.',
            'title_en.required'      => 'العنوان (إنجليزي) مطلوب.',
            'title_ar.max'           => 'العنوان (عربي) يجب ألا يتجاوز ' . self::MAX_SHORT . ' حرف.',
            'title_en.max'           => 'العنوان (إنجليزي) يجب ألا يتجاوز ' . self::MAX_SHORT . ' حرف.',
            'subtitle_ar.max'        => 'الوصف (عربي) يجب ألا يتجاوز ' . self::MAX_SHORT . ' حرف.',
            'subtitle_en.max'        => 'الوصف (إنجليزي) يجب ألا يتجاوز ' . self::MAX_SHORT . ' حرف.',
            'sort_order.required'    => 'الترتيب مطلوب.',
            'sort_order.min'         => 'الترتيب يجب أن يكون 1 على الأقل.',
            'sort_order.unique'      => 'رقم الترتيب مستخدم بالفعل، اختر رقماً آخر.',
            'image.required'         => 'صورة الشريحة مطلوبة.',
            'image.mimes'            => 'صورة الشريحة يجب أن تكون بصيغة مدعومة.',
            'image.max'              => 'حجم صورة الشريحة كبير جداً.',
        ];
    }
}
