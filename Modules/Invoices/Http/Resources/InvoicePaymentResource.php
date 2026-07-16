<?php

namespace Modules\Invoices\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoicePaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'invoice_id'   => $this->invoice_id,
            'amount'       => (float) $this->amount,
            'payment_date' => $this->payment_date?->toDateString(),
            'method'       => $this->method,
            'reference'    => $this->reference,
            'notes'        => $this->notes,
            'creator'      => $this->whenLoaded('creator', fn() => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
