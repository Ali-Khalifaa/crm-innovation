<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingContactSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'cta_subtitle'   => $this->cta_subtitle ?? ['ar' => '', 'en' => ''],
            'cta_headline'   => $this->cta_headline ?? [],
            'cta_intro'      => $this->cta_intro ?? ['ar' => '', 'en' => ''],
            'cta_btn1_text'  => $this->cta_btn1_text ?? ['ar' => '', 'en' => ''],
            'cta_btn1_link'  => $this->cta_btn1_link,
            'cta_btn2_text'  => $this->cta_btn2_text ?? ['ar' => '', 'en' => ''],
            'cta_btn2_link'  => $this->cta_btn2_link,
            'form_subtitle'  => $this->form_subtitle ?? ['ar' => '', 'en' => ''],
            'form_title'     => $this->form_title ?? ['ar' => '', 'en' => ''],
            'form_intro'     => $this->form_intro ?? ['ar' => '', 'en' => ''],
            'bg_image'       => $this->bg_image ? upload_general_url($this->bg_image) : null,
            'is_active'      => (bool) $this->is_active,
        ];
    }
}
