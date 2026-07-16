<?php

namespace Modules\Deals\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DealStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
            'order' => 'nullable|integer|min:0',
        ];
    }
}
