<?php
namespace App\Http\Requests\Dashboard;
use Illuminate\Foundation\Http\FormRequest;
class LandingAboutRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'subtitle_en'   => 'nullable|string|max:120',
            'subtitle_ar'   => 'nullable|string|max:120',
            'title_en'      => 'nullable|string|max:200',
            'title_ar'      => 'nullable|string|max:200',
            'description_en'=> 'nullable|string',
            'description_ar'=> 'nullable|string',
            'image'         => 'nullable|image|max:3072',
            'box1_icon'     => 'nullable|string|max:80',
            'box1_title_en' => 'nullable|string|max:120',
            'box1_title_ar' => 'nullable|string|max:120',
            'box1_desc_en'  => 'nullable|string',
            'box1_desc_ar'  => 'nullable|string',
            'box2_icon'     => 'nullable|string|max:80',
            'box2_title_en' => 'nullable|string|max:120',
            'box2_title_ar' => 'nullable|string|max:120',
            'box2_desc_en'  => 'nullable|string',
            'box2_desc_ar'  => 'nullable|string',
            'is_active'     => 'nullable|in:0,1',
        ];
    }
}
