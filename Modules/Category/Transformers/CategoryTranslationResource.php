<?php

namespace Modules\Category\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\StoreResource;

class CategoryTranslationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "url" => $this->url,
            "store" => new StoreResource($this->whenLoaded("store")),
            "meta_title" => $this->meta_title,
            "meta_description" => $this->meta_description,
            "meta_keywords" => $this->meta_keywords,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
