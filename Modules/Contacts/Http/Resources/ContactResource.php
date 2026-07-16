<?php

namespace Modules\Contacts\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'first_name'  => $this->first_name,
            'last_name'   => $this->last_name,
            'full_name'   => $this->full_name,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'company'     => $this->company,
            'address'     => $this->address,
            'notes'       => $this->notes,
            'status'      => $this->status,
            'lead_source' => $this->lead_source,
            'owner_id'    => $this->owner_id,
            'company_id'  => $this->company_id,
            'created_by'  => $this->created_by,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,

            // Eager-loaded relations (only when loaded)
            'creator'        => $this->whenLoaded('creator', fn() => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'owner'          => $this->whenLoaded('owner', fn() => [
                'id'   => $this->owner->id,
                'name' => $this->owner->name,
            ]),
            'company_record' => $this->whenLoaded('companyRecord', fn() => $this->companyRecord ? [
                'id'       => $this->companyRecord->id,
                'name'     => $this->companyRecord->name,
                'industry' => $this->companyRecord->industry,
                'website'  => $this->companyRecord->website,
                'phone'    => $this->companyRecord->phone,
                'email'    => $this->companyRecord->email,
            ] : null),
            'deals'          => $this->whenLoaded('deals', fn() => $this->deals->map(fn($d) => [
                'id'                  => $d->id,
                'title'               => $d->title,
                'value'               => $d->value,
                'currency'            => $d->currency,
                'status'              => $d->status,
                'probability'         => $d->probability,
                'expected_close_date' => $d->expected_close_date?->format('Y-m-d'),
                'stage'               => $d->stage ? ['id' => $d->stage->id, 'name' => $d->stage->name, 'color' => $d->stage->color] : null,
            ])),
            'tasks'          => $this->whenLoaded('tasks', fn() => $this->tasks->map(fn($t) => [
                'id'       => $t->id,
                'title'    => $t->title,
                'due_date' => $t->due_date?->format('Y-m-d'),
                'priority' => $t->priority,
                'status'   => $t->status,
                'assignee' => $t->assignee ? ['id' => $t->assignee->id, 'name' => $t->assignee->name] : null,
            ])),
        ];
    }
}
