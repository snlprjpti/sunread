<?php

namespace Modules\Country\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "country" =>  new CountryResource($this->whenLoaded("country")),
            "code" => $this->code,
            "name" => $this->name,
            "created_at" => $this->created_at->format('M d, Y H:i A'),
        ];
    }
}
