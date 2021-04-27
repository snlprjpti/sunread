<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "currency" => $this->currency,
            "name" => $this->name,
            "slug" => $this->slug,
            "locale" => $this->locale,
            "image" => $this->image,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
