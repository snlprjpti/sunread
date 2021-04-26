<?php

namespace Modules\Core\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteResource extends JsonResource
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
            "code" => $this->code,
            "hostname" => $this->hostname,
            "name" => $this->name,
            "description" => $this->description,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
