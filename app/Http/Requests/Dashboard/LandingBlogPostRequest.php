<?php
namespace App\Http\Requests\Dashboard;
use Illuminate\Foundation\Http\FormRequest;
class LandingBlogPostRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'title_en'     => 'required|string|max:200',
            'title_ar'     => 'required|string|max:200',
            'excerpt_en'   => 'nullable|string',
            'excerpt_ar'   => 'nullable|string',
            'content_en'   => 'nullable|string',
            'content_ar'   => 'nullable|string',
            'image'        => 'nullable|image|max:3072',
            'author_name'  => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'is_published' => 'nullable|in:0,1',
            'sort_order'   => 'nullable|integer|min:0',
        ];
    }
}
