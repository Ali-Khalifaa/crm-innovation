<?php
namespace App\Http\Resources\Dashboard;
use Illuminate\Http\Resources\Json\JsonResource;
class LandingAboutResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'            => $this->id,
            'subtitle_en'   => $this->subtitle_en,
            'subtitle_ar'   => $this->subtitle_ar,
            'title_en'      => $this->title_en,
            'title_ar'      => $this->title_ar,
            'description_en'=> $this->description_en,
            'description_ar'=> $this->description_ar,
            'image'         => $this->image ? asset('upload/general/'.$this->image) : null,
            'box1_icon'     => $this->box1_icon,
            'box1_title_en' => $this->box1_title_en,
            'box1_title_ar' => $this->box1_title_ar,
            'box1_desc_en'  => $this->box1_desc_en,
            'box1_desc_ar'  => $this->box1_desc_ar,
            'box2_icon'     => $this->box2_icon,
            'box2_title_en' => $this->box2_title_en,
            'box2_title_ar' => $this->box2_title_ar,
            'box2_desc_en'  => $this->box2_desc_en,
            'box2_desc_ar'  => $this->box2_desc_ar,
            'is_active'     => $this->is_active,
            'updated_at'    => $this->updated_at,
        ];
    }
}
