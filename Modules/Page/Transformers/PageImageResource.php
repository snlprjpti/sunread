<?php

namespace Modules\Page\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PageImageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "page" => new PageResource($this->whenLoaded("page")),
            "path" => $this->path_url,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
