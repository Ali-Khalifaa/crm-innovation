<?php

namespace Modules\Deals\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DealStageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'color'      => $this->color,
            'order'      => $this->order,
            'deals_count' => $this->whenCounted('deals'),
        ];
    }
}
