<?php

namespace Modules\Contacts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'nullable|email|max:150',
            'phone'      => 'nullable|string|max:30',
            'company'    => 'nullable|string|max:150',
            'address'    => 'nullable|string',
            'notes'      => 'nullable|string',
            'status'     => 'in:lead,customer,inactive',
        ];
    }
}
