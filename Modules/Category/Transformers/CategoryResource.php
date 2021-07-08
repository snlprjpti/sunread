<?php

namespace Modules\Category\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Transformers\ChannelResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {    
        return [
            "id" => $this->id,
            "website_id" => $this->website_id,
            "parent_id" => $this->parent_id,

            "values" => $this->values,
            "channels" => ChannelResource::collection($this->whenLoaded("channels")),

            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
