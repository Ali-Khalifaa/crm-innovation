<?php

namespace Modules\Tasks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'         => 'required|string|max:200',
            'description'   => 'nullable|string',
            'due_date'      => 'nullable|date',
            'priority'      => 'in:low,medium,high',
            'status'        => 'in:pending,in_progress,completed',
            'assigned_to'   => 'nullable|integer|exists:users,id',
            'taskable_type' => 'nullable|string',
            'taskable_id'   => 'nullable|integer',
        ];
    }
}
