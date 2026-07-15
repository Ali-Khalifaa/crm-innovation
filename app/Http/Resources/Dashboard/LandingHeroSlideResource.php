<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingHeroSlideResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'title_ar'       => $this->title['ar'] ?? '',
            'title_en'       => $this->title['en'] ?? '',
            'subtitle'       => $this->subtitle,
            'subtitle_ar'    => $this->subtitle['ar'] ?? '',
            'subtitle_en'    => $this->subtitle['en'] ?? '',
            'image'          => upload_general_url($this->image),
            'image_file'     => $this->image,
            'sort_order'     => $this->sort_order,
            'is_active'      => $this->is_active,
            'updated_at'     => $this->updated_at,
        ];
    }
}
