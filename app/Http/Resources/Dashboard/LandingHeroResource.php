<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingHeroResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                    => $this->id,
            'title_en'              => $this->title_en,
            'title_ar'              => $this->title_ar,
            'subtitle_en'           => $this->subtitle_en,
            'subtitle_ar'           => $this->subtitle_ar,
            'description_en'        => $this->description_en,
            'description_ar'        => $this->description_ar,
            'btn_primary_text_en'   => $this->btn_primary_text_en,
            'btn_primary_text_ar'   => $this->btn_primary_text_ar,
            'btn_primary_url'       => $this->btn_primary_url,
            'btn_secondary_text_en' => $this->btn_secondary_text_en,
            'btn_secondary_text_ar' => $this->btn_secondary_text_ar,
            'btn_secondary_url'     => $this->btn_secondary_url,
            'image'                 => $this->image ? asset('upload/general/' . $this->image) : null,
            'is_active'             => $this->is_active,
            'updated_at'            => $this->updated_at,
        ];
    }
}
