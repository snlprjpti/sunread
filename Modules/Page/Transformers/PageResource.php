<?php

namespace Modules\Page\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "parent" => new PageResource($this->whenLoaded("parent")),
            "slug" => $this->slug,
            "title" => $this->title,
            "position" => $this->position,
            "description" => $this->description,
            "status" => (bool) $this->status,
            "meta_title" => $this->meta_title,
            "meta_description" => $this->meta_description,
            "meta_keywords" => $this->meta_keywords,
            "translations" => PageTranslationResource::collection($this->whenLoaded("translations")),
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
