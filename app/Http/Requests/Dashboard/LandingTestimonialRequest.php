<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LandingTestimonialRequest extends FormRequest
{
    private const MAX_NAME = 100;

    private const MAX_DESIGNATION = 120;

    private const MAX_REVIEW = 1000;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = (bool) $this->route('landingTestimonial');
        $itemId   = $isUpdate ? $this->route('landingTestimonial')->id : null;

        return [
            'name_ar'          => 'required|string|max:' . self::MAX_NAME,
            'name_en'          => 'required|string|max:' . self::MAX_NAME,
            'designation_ar'   => 'required|string|max:' . self::MAX_DESIGNATION,
            'designation_en'   => 'required|string|max:' . self::MAX_DESIGNATION,
            'review_ar'        => 'required|string|max:' . self::MAX_REVIEW,
            'review_en'        => 'required|string|max:' . self::MAX_REVIEW,
            'rating'           => 'required|integer|min:1|max:5',
            'image'            => 'nullable|file|mimes:jpeg,jpg,png,webp,svg|max:5120',
            'sort_order'       => [
                'required',
                'integer',
                'min:1',
                Rule::unique('landing_testimonials', 'sort_order')->ignore($itemId),
            ],
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.required'        => 'الاسم (عربي) مطلوب.',
            'name_en.required'        => 'الاسم (إنجليزي) مطلوب.',
            'designation_ar.required' => 'المسمى الوظيفي (عربي) مطلوب.',
            'designation_en.required' => 'المسمى الوظيفي (إنجليزي) مطلوب.',
            'review_ar.required'    => 'الرأي (عربي) مطلوب.',
            'review_en.required'    => 'الرأي (إنجليزي) مطلوب.',
            'rating.required'       => 'التقييم مطلوب.',
            'rating.min'            => 'التقييم يجب أن يكون بين 1 و 5.',
            'rating.max'            => 'التقييم يجب أن يكون بين 1 و 5.',
            'sort_order.required'   => 'الترتيب مطلوب.',
            'sort_order.min'        => 'الترتيب يجب أن يكون 1 على الأقل.',
            'sort_order.unique'     => 'رقم الترتيب مستخدم بالفعل، اختر رقماً آخر.',
            'image.mimes'           => 'الصورة يجب أن تكون بصيغة مدعومة.',
            'image.max'             => 'حجم الصورة كبير جداً.',
        ];
    }
}
