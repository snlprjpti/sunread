<?php

namespace Modules\Customer\Transformers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressResource extends JsonResource
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
            "address1" => $this->address1,
            "address2" => $this->address2,
            "country" => $this->country,
            "state" => $this->state,
            "city" => $this->city,
            "postcode" => $this->postcode,
            "phone" => $this->phone,
            "default_address" => ($this->default_address) ? $this->default_address : 0,
            "created_at" => Carbon::parse($this->created_at)->format('M j\\,Y H:i A')
        ];
    }
}
