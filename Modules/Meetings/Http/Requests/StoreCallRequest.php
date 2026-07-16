<?php

namespace Modules\Meetings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCallRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'direction'        => 'required|in:inbound,outbound',
            'duration_seconds' => 'nullable|integer|min:0',
            'outcome'          => 'required|in:answered,no_answer,voicemail,busy',
            'notes'            => 'nullable|string',
            'called_at'        => 'required|date',
            'callable_type'    => 'required|in:contact,deal',
            'callable_id'      => 'required|integer',
        ];
    }
}
