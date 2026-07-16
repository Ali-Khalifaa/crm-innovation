<?php

namespace Modules\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku'         => 'nullable|string|max:100',
            'unit_price'  => 'required|numeric|min:0',
            'currency'    => 'nullable|string|size:3',
            'type'        => 'nullable|in:product,service,subscription',
            'is_active'   => 'nullable|boolean',
        ];
    }
}
