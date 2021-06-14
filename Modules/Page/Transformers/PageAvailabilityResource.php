<?php

namespace Modules\Page\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class PageAvailabilityResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "page" => new PageResource($this->whenLoaded("page")),
            "model_type" => $this->model_type,
            "model_id" => $this->model,
            "status" => $this->status
        ];
    }
}
