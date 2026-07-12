<?php

namespace Modules\Deals\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'               => 'required|string|max:200',
            'contact_id'          => 'nullable|integer|exists:contacts,id',
            'stage_id'            => 'nullable|integer|exists:deal_stages,id',
            'value'               => 'nullable|numeric|min:0',
            'currency'            => 'nullable|string|max:10',
            'probability'         => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'assigned_to'         => 'nullable|integer|exists:users,id',
            'status'              => 'in:open,won,lost',
            'notes'               => 'nullable|string',
        ];
    }
}
