<?php

namespace App\Http\Resources\Dashboard;

use App\Models\LandingLegalPage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingLegalPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'type'       => $this->type,
            'type_label' => LandingLegalPage::typeLabel((int) $this->type),
            'title'      => $this->title ?? ['ar' => '', 'en' => ''],
            'content'    => $this->content ?? ['ar' => '', 'en' => ''],
            'is_active'  => (bool) $this->is_active,
        ];
    }
}
