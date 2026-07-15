<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class LandingNewsletterSubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('crm.newsletter_email_required'),
            'email.email'    => __('crm.newsletter_email_invalid'),
        ];
    }
}
