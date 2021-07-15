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
            "name" => $this->name,
            "address1" => $this->address1,
            "address2" => $this->address2,
            "address3" => $this->address3,
            "country" => new CountryResource($this->whenLoaded('country')),
            "region" => new RegionResource($this->whenLoaded('region')),
            "city" => new CityResource($this->whenLoaded('city')),
            "postcode" => $this->postcode,
            "phone" => $this->phone,
            "vat_number" => $this->vat_number,
            "default_billing_address" => $this->default_billing_address,
            "default_shipping_address" => $this->default_shipping_address,
            "created_at" => $this->created_at->format('M d, Y H:i A')
        ];
    }
}
