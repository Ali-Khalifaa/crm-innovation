<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingProblemItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'title_ar'         => $this->title['ar'] ?? '',
            'title_en'         => $this->title['en'] ?? '',
            'description'      => $this->description,
            'description_ar'   => $this->description['ar'] ?? '',
            'description_en'   => $this->description['en'] ?? '',
            'impact_label'     => $this->impact_label,
            'impact_label_ar'  => $this->impact_label['ar'] ?? '',
            'impact_label_en'  => $this->impact_label['en'] ?? '',
            'image'            => upload_general_url($this->image),
            'image_file'       => upload_general_basename($this->image),
            'sort_order'       => $this->sort_order,
            'is_active'        => $this->is_active,
            'updated_at'       => $this->updated_at,
        ];
    }
}
