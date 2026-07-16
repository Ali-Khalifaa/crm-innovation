<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingPartnerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'image'      => upload_general_url($this->image),
            'image_path' => upload_general_basename($this->image),
            'url'        => $this->url,
            'sort_order' => $this->sort_order,
            'is_active'  => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
