<?php

namespace Modules\Sales\Repositories;

use Exception;
use Modules\Core\Repositories\BaseRepository;
use Modules\Country\Entities\City;
use Modules\Country\Entities\Country;
use Modules\Country\Entities\Region;
use Modules\Sales\Entities\OrderAddress;

class OrderAddressRepository extends BaseRepository
{    
    protected $country, $city, $region;

    public function __construct(OrderAddress $orderAddress, Country $country, City $city, Region $region)
    {
        $this->model = $orderAddress;
        $this->model_key = "order_addresses";
        $this->country = $country;
        $this->city = $city;
        $this->region = $region;
        $this->rules = [
            "country_id" => "required|exists:countries,id",
            "region_id" => "sometimes|exists:regions,id",
            "city_id" => "sometimes|exists:cities,id"
        ];
    }

    public function store(object $request, object $order): bool
    {
        try
        {
            // To-Do validation
            $countryId = $this->country->whereId($request->country_id)->pluck('id')->first();
            $city = $this->city->whereId($request->city_id)->first();
            $region = $this->region->whereId($request->region_id)->first();

            foreach ($request->address as $order_address) {
                $orderAddressData = [
                    "order_id" => $order->id,
                    "customer_id" => auth("customer")->id(),
                    "customer_address_id" => $order_address['customer_address_id'],
                    "address_type" => $order_address['address_type'],
                    "first_name" => $order_address['first_name'],
                    "middle_name" => $order_address['middle_name'],
                    "last_name" => $order_address['last_name'],
                    "phone" => $order_address['phone'],
                    "email" => $order_address['email'],
                    "address_line_1" => $order_address['address_line_1'],
                    "address_line_2" => $order_address['address_line_2'],
                    "postal_code" => $order_address['postal_code'],
                    "country_id" => $order_address["country_id"],
                    "region_id" => $order_address["region_id"],
                    "city_id" => $order_address["city_id"],
                    "region_name" => $order_address["region_name"],
                    "city_name" => $order_address["city_name"],
                    "vat_number" => $order_address['vat_number']
                ];

                $this->create($orderAddressData);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return true;
    }
}
