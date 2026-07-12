<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingSettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                   => $this->id,
            'site_name_en'         => $this->site_name_en,
            'site_name_ar'         => $this->site_name_ar,
            'email'                => $this->email,
            'phone'                => $this->phone,
            'whatsapp'             => $this->whatsapp,
            'address_en'           => $this->address_en,
            'address_ar'           => $this->address_ar,
            'facebook'             => $this->facebook,
            'twitter'              => $this->twitter,
            'linkedin'             => $this->linkedin,
            'instagram'            => $this->instagram,
            'youtube'              => $this->youtube,
            'meta_title_en'        => $this->meta_title_en,
            'meta_title_ar'        => $this->meta_title_ar,
            'meta_description_en'  => $this->meta_description_en,
            'meta_description_ar'  => $this->meta_description_ar,
            'updated_at'           => $this->updated_at,
        ];
    }
}
