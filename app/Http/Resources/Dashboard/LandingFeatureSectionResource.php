<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingFeatureSectionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'subtitle'   => $this->subtitle ?? ['ar' => '', 'en' => ''],
            'headline'   => $this->headline ?? [],
            'is_active'  => $this->is_active,
            'updated_at' => $this->updated_at,
        ];
    }
}
