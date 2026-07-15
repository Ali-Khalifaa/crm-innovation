<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingNewsletterSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title ?? ['ar' => '', 'en' => ''],
            'placeholder' => $this->placeholder ?? ['ar' => '', 'en' => ''],
            'button_text' => $this->button_text ?? ['ar' => '', 'en' => ''],
            'bg_image'    => $this->bg_image ? upload_general_url($this->bg_image) : null,
            'is_active'   => (bool) $this->is_active,
        ];
    }
}
