<?php

namespace Modules\Customer\Transformers;


use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAccountResource extends JsonResource
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
            "name" => $this->name,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "gender" => $this->gender,
            "date_of_birth" => $this->date_of_birth,
            "email" => $this->email,
            "status" => $this->status,
            "addresses" => CustomerAddressResource::collection($this->whenLoaded("addresses")),
            "profil_image" => $this->profile_image_url,
            "subscribed_to_news_letter" => $this->subscribed_to_news_letter,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
