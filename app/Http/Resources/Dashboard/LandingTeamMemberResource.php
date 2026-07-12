<?php
namespace App\Http\Resources\Dashboard;
use Illuminate\Http\Resources\Json\JsonResource;
class LandingTeamMemberResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'         => $this->id,
            'name_en'    => $this->name_en,
            'name_ar'    => $this->name_ar,
            'role_en'    => $this->role_en,
            'role_ar'    => $this->role_ar,
            'photo'      => $this->photo ? asset('upload/general/'.$this->photo) : null,
            'facebook'   => $this->facebook,
            'twitter'    => $this->twitter,
            'linkedin'   => $this->linkedin,
            'instagram'  => $this->instagram,
            'sort_order' => $this->sort_order,
            'is_active'  => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
