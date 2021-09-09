<?php

namespace Modules\Customer\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Country\Entities\Country;
use Modules\Country\Transformers\CityResource;
use Modules\Country\Transformers\CountryResource;
use Modules\Country\Transformers\RegionResource;

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
            "full_name" => $this->name,
            "first_name" => $this->first_name,
            "middle_name" => $this->middle_name,
            "last_name" => $this->last_name,
            "address1" => $this->address1,
            "address2" => $this->address2,
            "address3" => $this->address3,
            "country" => new CountryResource($this->whenLoaded('country')),
            "region" => ($this->region_id) ? new RegionResource($this->whenLoaded('regions')) : $this->region,
            "city" => ($this->city_id) ? new CityResource($this->whenLoaded('cities')) : $this->city,
            "postcode" => $this->postcode,
            "phone" => $this->phone,
            "vat_number" => $this->vat_number,
            "default_billing_address" => (bool) $this->default_billing_address,
            "default_shipping_address" => (bool) $this->default_shipping_address,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
