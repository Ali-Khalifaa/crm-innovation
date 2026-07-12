<?php
namespace App\Http\Resources\Dashboard;
use Illuminate\Http\Resources\Json\JsonResource;
class LandingTestimonialResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'designation_en' => $this->designation_en,
            'designation_ar' => $this->designation_ar,
            'review_en'      => $this->review_en,
            'review_ar'      => $this->review_ar,
            'rating'         => $this->rating,
            'photo'          => $this->photo ? asset('upload/general/'.$this->photo) : null,
            'sort_order'     => $this->sort_order,
            'is_active'      => $this->is_active,
            'created_at'     => $this->created_at,
        ];
    }
}
