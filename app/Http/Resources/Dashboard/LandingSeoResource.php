<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingSeoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'meta_title'          => $this->meta_title ?? ['ar' => '', 'en' => ''],
            'meta_description'    => $this->meta_description ?? ['ar' => '', 'en' => ''],
            'meta_keywords'       => $this->meta_keywords ?? ['ar' => '', 'en' => ''],
            'og_title'            => $this->og_title ?? ['ar' => '', 'en' => ''],
            'og_description'      => $this->og_description ?? ['ar' => '', 'en' => ''],
            'og_image'            => $this->og_image ? upload_general_url($this->og_image) : null,
            'twitter_title'       => $this->twitter_title ?? ['ar' => '', 'en' => ''],
            'twitter_description' => $this->twitter_description ?? ['ar' => '', 'en' => ''],
            'twitter_image'       => $this->twitter_image ? upload_general_url($this->twitter_image) : null,
            'robots'              => $this->robots,
            'author'              => $this->author,
            'theme_color'         => $this->theme_color,
            'canonical_url'       => $this->canonical_url,
            'is_active'           => (bool) $this->is_active,
        ];
    }
}
