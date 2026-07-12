<?php

namespace Modules\Invoices\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'contact_id'        => 'nullable|integer|exists:contacts,id',
            'issue_date'        => 'sometimes|date',
            'due_date'          => 'sometimes|date',
            'status'            => 'in:draft,sent,paid,overdue,cancelled',
            'tax_rate'          => 'nullable|numeric|min:0|max:100',
            'discount'          => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string',
            'items'             => 'sometimes|array|min:1',
            'items.*.description' => 'required_with:items|string',
            'items.*.quantity'    => 'required_with:items|numeric|min:0.01',
            'items.*.unit_price'  => 'required_with:items|numeric|min:0',
        ];
    }
}
