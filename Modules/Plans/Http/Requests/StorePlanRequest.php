<?php

namespace Modules\Plans\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:100',
            'slug'          => 'required|string|max:100|unique:plans,slug',
            'description'   => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price'  => 'required|numeric|min:0',
            'max_users'     => 'required|integer|min:0',
            'max_contacts'  => 'required|integer|min:0',
            'features'      => 'nullable|array',
            'features.*'    => 'string',
            'is_active'     => 'boolean',
            'is_featured'   => 'boolean',
            'sort_order'    => 'integer|min:0',
        ];
    }
}
