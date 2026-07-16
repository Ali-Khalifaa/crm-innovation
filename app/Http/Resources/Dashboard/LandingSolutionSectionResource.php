<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingSolutionSectionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'subtitle'    => $this->subtitle ?? ['ar' => '', 'en' => ''],
            'headline'    => $this->headline ?? [],
            'description' => $this->description ?? ['ar' => '', 'en' => ''],
            'button'      => $this->button ?? ['text' => ['ar' => '', 'en' => ''], 'url' => ''],
            'is_active'   => $this->is_active,
            'updated_at'  => $this->updated_at,
        ];
    }
}
