<?php
namespace App\Http\Requests\Dashboard;
use Illuminate\Foundation\Http\FormRequest;
class LandingTeamMemberRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        return [
            'name_en'    => 'required|string|max:100',
            'name_ar'    => 'required|string|max:100',
            'role_en'    => 'required|string|max:120',
            'role_ar'    => 'required|string|max:120',
            'photo'      => $isUpdate ? 'nullable|image|max:2048' : 'nullable|image|max:2048',
            'facebook'   => 'nullable|url|max:255',
            'twitter'    => 'nullable|url|max:255',
            'linkedin'   => 'nullable|url|max:255',
            'instagram'  => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|in:0,1',
        ];
    }
}
