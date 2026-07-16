<?php

namespace Modules\Meetings\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'scheduled_at'     => $this->scheduled_at?->toISOString(),
            'duration_minutes' => $this->duration_minutes,
            'location'         => $this->location,
            'outcome'          => $this->outcome,
            'status'           => $this->status,
            'meetable_type'    => $this->meetable_type,
            'meetable_id'      => $this->meetable_id,
            'assigned_to'      => $this->assigned_to,
            'assignee'         => $this->whenLoaded('assignee', fn() => [
                'id'   => $this->assignee->id,
                'name' => $this->assignee->name,
            ]),
            'meetable' => $this->whenLoaded('meetable', fn() => [
                'id'   => $this->meetable->id,
                'name' => $this->meetable->first_name
                    ? trim("{$this->meetable->first_name} {$this->meetable->last_name}")
                    : ($this->meetable->name ?? $this->meetable->title ?? ''),
            ]),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
