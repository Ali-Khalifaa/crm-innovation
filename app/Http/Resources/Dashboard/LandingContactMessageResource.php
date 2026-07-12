<?php
namespace App\Http\Resources\Dashboard;
use Illuminate\Http\Resources\Json\JsonResource;
class LandingContactMessageResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'subject'    => $this->subject,
            'message'    => $this->message,
            'status'     => $this->status,
            'ip_address' => $this->ip_address,
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
