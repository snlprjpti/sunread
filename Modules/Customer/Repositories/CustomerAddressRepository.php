<?php

namespace Modules\Customer\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Customer\Entities\CustomerAddress;

class CustomerAddressRepository extends BaseRepository
{
    public function __construct(CustomerAddress $customer_address)
    {
        $this->model = $customer_address;
        $this->model_key = "customers.addresses";

        $this->rules = [
            "first_name" => "required|min:2|max:200",
            "middle_name" => "sometimes|min:2|max:200",
            "last_name" => "required|min:2|max:200",

            "address1" => "required|min:2|max:500",
            "address2" => "sometimes",
            "address3" => "sometimes",

            "country_id" => "required|exists:countries,id",
            "region_id" => "sometimes|nullable|exists:regions,id",
            "city_id" => "sometimes|nullable|exists:cities,id",
            "postcode" => "required",
            "phone" => "required",
            "vat_number" => "sometimes",
            "default_billing_address" => "sometimes|boolean",
            "default_shipping_address" => "sometimes|boolean"
        ];
    }

    public function regionAndCityRules(object $request): array
    {
        return [
            "region_id" => "sometimes|nullable|exists:regions,id,country_id,{$request->country_id}",
            "city_id" => "sometimes|nullable|exists:cities,id,region_id,{$request->region_id}",
        ];
    }

    public function unsetOtherAddresses(object $customer, int $address_id): void
    {
        if(isset($data["default_billing_address"]) && $data["default_billing_address"] == 1) $customer->addresses()->where("id", "<>", $address_id)->update([
            "default_billing_address" => 0
        ]);

        if(isset($data["default_shipping_address"]) && $data["default_shipping_address"] == 1) $customer->addresses()->where("id", "<>", $address_id)->update([
            "default_shipping_address" => 0
        ]);
    }
}
