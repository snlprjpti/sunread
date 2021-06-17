<?php

namespace Modules\Customer\Transformers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            "status" => (bool) $this->status,
            "profile_image" => $this->profile_image_url,
            "group" => new CustomerGroupResource($this->whenLoaded('group')),
            "subscribed_to_news_letter" => $this->subscribed_to_news_letter,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
