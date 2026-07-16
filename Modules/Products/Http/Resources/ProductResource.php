<?php

namespace Modules\Products\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'sku'         => $this->sku,
            'unit_price'  => (float) $this->unit_price,
            'currency'    => $this->currency,
            'type'        => $this->type,
            'is_active'   => $this->is_active,
            'created_at'  => $this->created_at?->toISOString(),
        ];
    }
}
