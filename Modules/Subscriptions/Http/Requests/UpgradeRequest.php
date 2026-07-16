<?php

namespace Modules\Subscriptions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpgradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => 'required|integer|exists:plans,id',
            'message' => 'nullable|string|max:500',
        ];
    }
}
