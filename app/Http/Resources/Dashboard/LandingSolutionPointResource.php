<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingSolutionPointResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'text'     => $this->text,
            'text_ar'  => $this->text['ar'] ?? '',
            'text_en'  => $this->text['en'] ?? '',
            'sort_order' => $this->sort_order,
            'is_active'  => $this->is_active,
            'updated_at' => $this->updated_at,
        ];
    }
}
