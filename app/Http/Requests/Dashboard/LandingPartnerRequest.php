<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingPartnerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'name'       => 'nullable|string|max:120',
            'image'      => $isUpdate ? 'nullable|image|max:2048' : 'required|image|max:2048',
            'url'        => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|in:0,1',
        ];
    }
}
