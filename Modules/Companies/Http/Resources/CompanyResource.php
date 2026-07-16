<?php

namespace Modules\Companies\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'industry'        => $this->industry,
            'website'         => $this->website,
            'phone'           => $this->phone,
            'email'           => $this->email,
            'address'         => $this->address,
            'employees_count' => $this->employees_count,
            'annual_revenue'  => $this->annual_revenue,
            'status'          => $this->status,
            'notes'           => $this->notes,
            'created_by'      => $this->created_by,
            'contacts_count'  => $this->whenCounted('contacts'),
            'deals_count'     => $this->whenCounted('deals'),
            'contacts'        => $this->whenLoaded('contacts', fn() => $this->contacts->map(fn($c) => [
                'id'         => $c->id,
                'first_name' => $c->first_name,
                'last_name'  => $c->last_name,
                'email'      => $c->email,
                'phone'      => $c->phone,
                'status'     => $c->status,
            ])),
            'deals'           => $this->whenLoaded('deals', fn() => $this->deals->map(fn($d) => [
                'id'     => $d->id,
                'title'  => $d->title,
                'value'  => $d->value,
                'status' => $d->status,
                'stage'  => $d->stage ? ['id' => $d->stage->id, 'name' => $d->stage->name, 'color' => $d->stage->color] : null,
            ])),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
