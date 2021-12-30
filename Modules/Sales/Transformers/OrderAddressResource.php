<?php

namespace Modules\Sales\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderAddressResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "middle_name" => $this->middle_name,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "phone" => $this->phone,
            "email" => $this->email,
            "address1" => $this->address1,
            "address2" => $this->address2,
            "address3" => $this->address3,
            "postcode" => $this->postcode,
            "country_id" => $this->country_id,
            "region_id" => $this->region_id,
            "city_id" => $this->city_id,
            "region_name" => $this->region_name,
            "city_name" => $this->city_name,
            "vat_number" => $this->vat_number,
        ];
    }
}
