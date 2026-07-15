<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingStatItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'value'      => $this->value,
            'value_ar'   => $this->value['ar'] ?? '',
            'value_en'   => $this->value['en'] ?? '',
            'suffix'     => $this->suffix ?? '',
            'label'      => $this->label,
            'label_ar'   => $this->label['ar'] ?? '',
            'label_en'   => $this->label['en'] ?? '',
            'image'      => upload_general_url($this->image),
            'image_file' => upload_general_basename($this->image),
            'sort_order' => $this->sort_order,
            'is_active'  => $this->is_active,
            'updated_at' => $this->updated_at,
        ];
    }
}
