<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingProblemSectionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'subtitle'   => $this->subtitle ?? ['ar' => '', 'en' => ''],
            'headline'   => $this->headline ?? [],
            'intro'      => $this->intro ?? ['ar' => '', 'en' => ''],
            'insights'   => $this->insights ?? [],
            'cta'        => $this->cta ?? ['bridge' => ['ar' => '', 'en' => ''], 'text' => ['ar' => '', 'en' => ''], 'url' => ''],
            'is_active'  => $this->is_active,
            'updated_at' => $this->updated_at,
        ];
    }
}
