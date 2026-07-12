<?php

namespace Modules\CrmAuth\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Plans\Http\Resources\PlanResource;

class TenantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'status'         => $this->status,
            'billing_cycle'  => $this->billing_cycle,
            'trial_ends_at'  => $this->trial_ends_at,
            'plan_starts_at' => $this->plan_starts_at,
            'plan_ends_at'   => $this->plan_ends_at,
            'plan'           => $this->whenLoaded('plan', fn() => new PlanResource($this->plan)),
        ];
    }
}
