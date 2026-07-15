<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingTestimonialSectionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'subtitle'      => $this->subtitle ?? ['ar' => '', 'en' => ''],
            'headline'      => $this->headline ?? [],
            'bg_image'      => upload_general_url($this->bg_image),
            'bg_image_file' => upload_general_basename($this->bg_image),
            'is_active'     => $this->is_active,
            'updated_at'    => $this->updated_at,
        ];
    }
}
