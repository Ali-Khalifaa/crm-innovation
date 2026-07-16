<?php

namespace Modules\Deals\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Contacts\Http\Resources\ContactResource;

class DealResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'value'               => $this->value,
            'currency'            => $this->currency,
            'probability'         => $this->probability,
            'expected_close_date' => $this->expected_close_date,
            'status'              => $this->status,
            'lost_reason'         => $this->lost_reason,
            'closed_at'           => $this->closed_at,
            'company_id'          => $this->company_id,
            'notes'               => $this->notes,
            'stage'               => $this->whenLoaded('stage', fn() => new DealStageResource($this->stage)),
            'contact'             => $this->whenLoaded('contact', fn() => new ContactResource($this->contact)),
            'assigned_to'         => $this->assigned_to,
            'stage_id'            => $this->stage_id,
            'contact_id'          => $this->contact_id,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
        ];
    }
}
