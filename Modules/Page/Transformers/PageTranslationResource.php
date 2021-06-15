<?php

namespace Modules\Page\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\StoreResource;

class PageTranslationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "page" => new PageResource($this->whenLoaded("page")),
            "store" => new StoreResource($this->whenLoaded("store")),
            "meta_title" => $this->meta_title,
            "meta_description" => $this->meta_description,
            "meta_keywords" => $this->meta_keywords
        ];
    }
}
