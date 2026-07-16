<?php

namespace Modules\Invoices\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'method'       => 'required|in:cash,bank_transfer,check,card,other',
            'reference'    => 'nullable|string|max:100',
            'notes'        => 'nullable|string',
        ];
    }
}
