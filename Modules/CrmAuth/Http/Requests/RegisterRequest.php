<?php

namespace Modules\CrmAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name'  => 'required|string|max:150',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'plan_id'       => 'required|integer|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ];
    }
}
