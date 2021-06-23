<?php

namespace Modules\Category\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\StoreResource;

class CategoryValueResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "scope" => $this->scope,
            "scope_id" => $this->scope_id,
            "name" => $this->name,
            "slug" => $this->category->slug,
            "parent_id" => $this->category->parent_id,
            "position" => $this->category->position,
            "website_id" => $this->category->website_id,
            "image" => $this->image_url,
            "description" => $this->description,
            "meta_title" => $this->meta_title,
            "meta_description" => $this->meta_description,
            "meta_keywords" => $this->meta_keywords,
            "status" => $this->status,
            "include_in_menu" => $this->include_in_menu,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
