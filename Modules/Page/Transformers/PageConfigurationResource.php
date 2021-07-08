<?php

namespace Modules\Page\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PageConfigurationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "page" => new PageResource($this->whenLoaded("page")),
            "scope" => $this->scope,
            "scope_id" => $this->scope_id,
            "title" => $this->title,
            "description" => $this->description,
            "status" => (bool) $this->status,
            "meta_title" => $this->meta_title,
            "meta_description" => $this->meta_description,
            "meta_keywords" => $this->meta_keywords,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
