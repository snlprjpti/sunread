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
            "address_line_1" => $this->address_line_1,
            "address_line_2" => $this->email,
            "postal_code" => $this->postal_code,
            "country_id" => $this->country_id,
            "region_id" => $this->region_id,
            "city_id" => $this->city_id,
            "region_name" => $this->region_name,
            "city_name" => $this->city_name,
            "vat_number" => $this->vat_number,
        ];
    }
}
