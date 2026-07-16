<?php

namespace Modules\Tasks\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request): array
    {
        $taskable = null;
        if ($this->taskable) {
            $t = $this->taskable;
            // Contact has first_name/last_name; Deal has title
            $label = isset($t->first_name)
                ? trim($t->first_name . ' ' . $t->last_name)
                : ($t->title ?? $t->name ?? '');

            $taskable = [
                'id'    => $t->id,
                'label' => $label,
                'type'  => class_basename($t),
            ];
        }

        // With morphMap registered, taskable_type is already stored as the alias ('contact'/'deal')
        $typeAlias = $this->taskable_type;

        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'due_date'      => $this->due_date?->format('Y-m-d'),
            'priority'      => $this->priority,
            'status'        => $this->status,
            'assigned_to'   => $this->assigned_to,
            'taskable_type' => $typeAlias,
            'taskable_id'   => $this->taskable_id,
            'taskable'      => $taskable,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
