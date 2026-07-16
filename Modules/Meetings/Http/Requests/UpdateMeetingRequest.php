<?php

namespace Modules\Meetings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMeetingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'            => 'sometimes|required|string|max:255',
            'description'      => 'nullable|string',
            'scheduled_at'     => 'sometimes|required|date',
            'duration_minutes' => 'nullable|integer|min:1|max:480',
            'location'         => 'nullable|string|max:255',
            'outcome'          => 'nullable|string',
            'status'           => 'nullable|in:scheduled,completed,cancelled,no_show',
            'assigned_to'      => 'nullable|integer|exists:users,id',
        ];
    }
}
