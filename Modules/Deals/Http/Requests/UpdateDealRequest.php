<?php

namespace Modules\Deals\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDealRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'               => 'sometimes|string|max:200',
            'contact_id'          => 'nullable|integer|exists:contacts,id',
            'stage_id'            => 'nullable|integer|exists:deal_stages,id',
            'value'               => 'nullable|numeric|min:0',
            'currency'            => 'nullable|string|max:10',
            'probability'         => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'assigned_to'         => 'nullable|integer|exists:users,id',
            'status'              => 'in:open,won,lost',
            'notes'               => 'nullable|string',
            'lost_reason'         => 'nullable|string|max:500',
            'company_id'          => 'nullable|integer|exists:companies,id',
        ];
    }
}
