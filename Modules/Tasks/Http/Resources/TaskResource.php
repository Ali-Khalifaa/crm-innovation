<?php

namespace Modules\Tasks\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'due_date'      => $this->due_date,
            'priority'      => $this->priority,
            'status'        => $this->status,
            'assigned_to'   => $this->assigned_to,
            'taskable_type' => $this->taskable_type,
            'taskable_id'   => $this->taskable_id,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
