<?php

namespace Modules\Meetings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'scheduled_at'     => 'required|date',
            'duration_minutes' => 'nullable|integer|min:1|max:480',
            'location'         => 'nullable|string|max:255',
            'status'           => 'nullable|in:scheduled,completed,cancelled,no_show',
            'meetable_type'    => 'required|in:contact,deal',
            'meetable_id'      => 'required|integer',
            'assigned_to'      => 'nullable|integer|exists:users,id',
        ];
    }
}
