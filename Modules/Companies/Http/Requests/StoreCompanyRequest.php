<?php

namespace Modules\Companies\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:150',
            'industry'        => 'nullable|string|max:100',
            'website'         => 'nullable|url|max:255',
            'phone'           => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:150',
            'address'         => 'nullable|string',
            'employees_count' => 'nullable|integer|min:0',
            'annual_revenue'  => 'nullable|numeric|min:0',
            'status'          => 'nullable|in:prospect,customer,churned',
            'notes'           => 'nullable|string',
        ];
    }
}
