<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingHowItWorkResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'step_number'    => $this->step_number,
            'image'          => $this->image ? asset('upload/general/' . $this->image) : null,
            'title_en'       => $this->title_en,
            'title_ar'       => $this->title_ar,
            'description_en' => $this->description_en,
            'description_ar' => $this->description_ar,
            'sort_order'     => $this->sort_order,
            'is_active'      => $this->is_active,
            'created_at'     => $this->created_at,
        ];
    }
}
