<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingStatResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'value_en'   => $this->value_en,
            'value_ar'   => $this->value_ar,
            'suffix'     => $this->suffix,
            'label_en'   => $this->label_en,
            'label_ar'   => $this->label_ar,
            'sort_order' => $this->sort_order,
            'is_active'  => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
