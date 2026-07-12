<?php
namespace App\Http\Resources\Dashboard;
use Illuminate\Http\Resources\Json\JsonResource;
class LandingBlogPostResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id'           => $this->id,
            'title_en'     => $this->title_en,
            'title_ar'     => $this->title_ar,
            'excerpt_en'   => $this->excerpt_en,
            'excerpt_ar'   => $this->excerpt_ar,
            'content_en'   => $this->content_en,
            'content_ar'   => $this->content_ar,
            'image'        => $this->image ? asset('upload/general/'.$this->image) : null,
            'author_name'  => $this->author_name,
            'published_at' => $this->published_at?->format('Y-m-d'),
            'is_published' => $this->is_published,
            'sort_order'   => $this->sort_order,
            'created_at'   => $this->created_at,
        ];
    }
}
