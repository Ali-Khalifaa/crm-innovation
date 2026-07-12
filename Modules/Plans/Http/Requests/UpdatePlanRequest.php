<?php

namespace Modules\Plans\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'sometimes|string|max:100',
            'slug'          => 'sometimes|string|max:100|unique:plans,slug,' . $this->route('plan')?->id,
            'description'   => 'nullable|string',
            'monthly_price' => 'sometimes|numeric|min:0',
            'yearly_price'  => 'sometimes|numeric|min:0',
            'max_users'     => 'sometimes|integer|min:0',
            'max_contacts'  => 'sometimes|integer|min:0',
            'features'      => 'nullable|array',
            'features.*'    => 'string',
            'is_active'     => 'boolean',
            'is_featured'   => 'boolean',
            'sort_order'    => 'integer|min:0',
        ];
    }
}
