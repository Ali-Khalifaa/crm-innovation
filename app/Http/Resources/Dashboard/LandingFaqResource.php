<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingFaqResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'question_en' => $this->question_en,
            'question_ar' => $this->question_ar,
            'answer_en'   => $this->answer_en,
            'answer_ar'   => $this->answer_ar,
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
            'created_at'  => $this->created_at,
        ];
    }
}
