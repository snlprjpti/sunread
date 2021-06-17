<?php

namespace Modules\Customer\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAccountResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "gender" => $this->gender,
            "date_of_birth" => $this->date_of_birth,
            "email" => $this->email,
            "status" => (bool) $this->status,
            "addresses" => CustomerAddressResource::collection($this->whenLoaded("addresses")),
            "profile_image" => $this->profile_image_url,
            "subscribed_to_news_letter" => $this->subscribed_to_news_letter,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
