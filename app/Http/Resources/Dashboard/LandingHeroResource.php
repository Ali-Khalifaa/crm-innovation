<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingHeroResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'subtitle'         => $this->subtitle ?? ['ar' => '', 'en' => ''],
            'headline'         => $this->headline ?? [],
            'description'      => $this->description ?? ['ar' => '', 'en' => ''],
            'btn_primary'      => $this->btn_primary ?? ['text' => ['ar' => '', 'en' => ''], 'url' => '', 'icon' => 'fa-plus'],
            'btn_secondary'    => $this->btn_secondary ?? ['text' => ['ar' => '', 'en' => ''], 'url' => '', 'type' => 'video'],
            'bg_image'         => $this->bg_image ? asset('upload/general/' . $this->bg_image) : null,
            'bg_overlay_image' => $this->bg_overlay_image ? asset('upload/general/' . $this->bg_overlay_image) : null,
            'is_active'        => $this->is_active,
            'updated_at'       => $this->updated_at,
        ];
    }
}
