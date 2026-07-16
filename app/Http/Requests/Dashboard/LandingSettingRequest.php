<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class LandingSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_name_en' => 'required|string|max:100',
            'site_name_ar' => 'required|string|max:100',
            'email'        => 'required|email|max:150',
            'phone'        => 'nullable|string|max:30',
            'whatsapp'     => 'nullable|string|max:30',
            'address_en'   => 'nullable|string|max:255',
            'address_ar'   => 'nullable|string|max:255',
            'facebook'     => 'nullable|url|max:255',
            'twitter'      => 'nullable|url|max:255',
            'linkedin'     => 'nullable|url|max:255',
            'instagram'    => 'nullable|url|max:255',
            'youtube'      => 'nullable|url|max:255',
            'logo'         => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2048',
            'logo_footer'  => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2048',
            'favicon'      => 'nullable|image|mimes:jpeg,jpg,png,webp,ico|max:1024',
        ];
    }
}
