<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingSettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'site_name_en'  => $this->site_name_en,
            'site_name_ar'  => $this->site_name_ar,
            'logo'          => $this->logo ? upload_general_url($this->logo) : null,
            'logo_footer'   => $this->logo_footer ? upload_general_url($this->logo_footer) : null,
            'favicon'       => $this->favicon ? upload_general_url($this->favicon) : null,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'whatsapp'      => $this->whatsapp,
            'address_en'    => $this->address_en,
            'address_ar'    => $this->address_ar,
            'facebook'      => $this->facebook,
            'twitter'       => $this->twitter,
            'linkedin'      => $this->linkedin,
            'instagram'     => $this->instagram,
            'youtube'       => $this->youtube,
            'updated_at'    => $this->updated_at,
        ];
    }
}
