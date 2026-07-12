<?php

namespace Modules\Invoices\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Contacts\Http\Resources\ContactResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'invoice_number' => $this->invoice_number,
            'issue_date'     => $this->issue_date,
            'due_date'       => $this->due_date,
            'status'         => $this->status,
            'subtotal'       => $this->subtotal,
            'tax_rate'       => $this->tax_rate,
            'tax_amount'     => $this->tax_amount,
            'discount'       => $this->discount,
            'total'          => $this->total,
            'notes'          => $this->notes,
            'contact_id'     => $this->contact_id,
            'contact'        => $this->whenLoaded('contact', fn() => new ContactResource($this->contact)),
            'items'          => $this->whenLoaded('items', fn() => InvoiceItemResource::collection($this->items)),
            'created_at'     => $this->created_at,
        ];
    }
}
