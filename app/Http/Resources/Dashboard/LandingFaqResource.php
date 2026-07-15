<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingFaqResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'question'    => $this->question,
            'question_ar' => $this->question['ar'] ?? '',
            'question_en' => $this->question['en'] ?? '',
            'answer'      => $this->answer,
            'answer_ar'   => $this->answer['ar'] ?? '',
            'answer_en'   => $this->answer['en'] ?? '',
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
            'updated_at'  => $this->updated_at,
        ];
    }
}
