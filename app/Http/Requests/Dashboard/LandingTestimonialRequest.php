<?php
namespace App\Http\Requests\Dashboard;
use Illuminate\Foundation\Http\FormRequest;
class LandingTestimonialRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        return [
            'name'           => 'required|string|max:100',
            'designation_en' => 'required|string|max:120',
            'designation_ar' => 'required|string|max:120',
            'review_en'      => 'required|string',
            'review_ar'      => 'required|string',
            'rating'         => 'nullable|integer|min:1|max:5',
            'photo'          => $isUpdate ? 'nullable|image|max:2048' : 'nullable|image|max:2048',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'nullable|in:0,1',
        ];
    }
}
