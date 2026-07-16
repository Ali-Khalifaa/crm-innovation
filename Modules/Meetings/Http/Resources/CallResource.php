<?php

namespace Modules\Meetings\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CallResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'direction'        => $this->direction,
            'duration_seconds' => $this->duration_seconds,
            'outcome'          => $this->outcome,
            'notes'            => $this->notes,
            'called_at'        => $this->called_at?->toISOString(),
            'callable_type'    => $this->callable_type,
            'callable_id'      => $this->callable_id,
            'user_id'          => $this->user_id,
            'user'             => $this->whenLoaded('user', fn() => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),
            'callable' => $this->whenLoaded('callable', fn() => [
                'id'   => $this->callable->id,
                'name' => $this->callable->first_name
                    ? trim("{$this->callable->first_name} {$this->callable->last_name}")
                    : ($this->callable->name ?? $this->callable->title ?? ''),
            ]),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
