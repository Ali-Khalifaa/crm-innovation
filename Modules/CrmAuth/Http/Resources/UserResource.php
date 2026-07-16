<?php

namespace Modules\CrmAuth\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'title'     => $this->title,
            'role'      => $this->role,
            'status'    => $this->status,
            'tenant_id' => $this->tenant_id,
            'tenant'    => $this->whenLoaded('tenant', fn() => new TenantResource($this->tenant)),
            'created_at' => $this->created_at,
        ];
    }
}
