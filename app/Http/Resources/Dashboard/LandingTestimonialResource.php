<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingTestimonialResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'name_ar'          => $this->name['ar'] ?? '',
            'name_en'          => $this->name['en'] ?? '',
            'designation'      => $this->designation,
            'designation_ar'   => $this->designation['ar'] ?? '',
            'designation_en'   => $this->designation['en'] ?? '',
            'review'           => $this->review,
            'review_ar'        => $this->review['ar'] ?? '',
            'review_en'        => $this->review['en'] ?? '',
            'rating'           => $this->rating,
            'image'            => upload_general_url($this->image),
            'image_file'       => upload_general_basename($this->image),
            'sort_order'       => $this->sort_order,
            'is_active'        => $this->is_active,
            'updated_at'       => $this->updated_at,
        ];
    }
}
