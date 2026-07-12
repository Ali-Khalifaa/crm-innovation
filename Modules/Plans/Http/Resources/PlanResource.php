<?php

namespace Modules\Plans\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'monthly_price' => $this->monthly_price,
            'yearly_price'  => $this->yearly_price,
            'max_users'     => $this->max_users,
            'max_contacts'  => $this->max_contacts,
            'features'      => $this->features ?? [],
            'is_active'     => $this->is_active,
            'is_featured'   => $this->is_featured,
            'sort_order'    => $this->sort_order,
            'created_at'    => $this->created_at,
        ];
    }
}
