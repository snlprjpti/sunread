<?php

namespace Modules\Sales\Repositories;

use Exception;
use Modules\Sales\Rules\RegionRule;
use Modules\Sales\Entities\OrderAddress;
use Modules\Core\Repositories\BaseRepository;

class OrderAddressRepository extends BaseRepository
{    
    public function __construct(OrderAddress $orderAddress)
    {
        $this->model = $orderAddress;
        $this->model_key = "order_addresses";
        $this->rules = [
            "address" => "required|array",
            "address.*.customer_address_id" => "sometimes|exists:customer_addresses,id",
            "address.*.type" => "required|in:shipping,billing",
            "address.*.first_name" => "required",
            "address.*.last_name" => "required",
            "address.*.phone" => "required",
            "address.*.email" => "required|email",
            "address.*.address_line_1" => "required",
            "address.*.postal_code" => "required",
            "address.*.country_id" => "required|exists:countries,id",
            "address.*.region_id" => "sometimes|exists:regions,id",
            "address.*.city_id" => "sometimes|exists:cities,id",
        ];
    }

    public function store(object $request, object $order): void
    {
        try
        {
            $this->validateData($request, [
                "address" => [new RegionRule($request->address)]
            ]);

            foreach ($request->address as $order_address) {
                $orderAddressData = [
                    "order_id" => $order->id,
                    "customer_id" => auth("customer")->id(),
                    "customer_address_id" => $order_address['customer_address_id'] ?? null,
                    "address_type" => $order_address['type'],
                    "first_name" => $order_address['first_name'],
                    "middle_name" => $order_address['middle_name'] ?? null,
                    "last_name" => $order_address['last_name'],
                    "phone" => $order_address['phone'],
                    "email" => $order_address['email'],
                    "address_line_1" => $order_address['address_line_1'],
                    "address_line_2" => $order_address['address_line_2'] ?? null,
                    "postal_code" => $order_address['postal_code'],
                    "country_id" => $order_address["country_id"],
                    "region_id" => $order_address["region_id"] ?? null,
                    "city_id" => $order_address["city_id"] ?? null,
                    "region_name" => $order_address["region_name"] ?? null,
                    "city_name" => $order_address["city_name"] ?? null,
                    "vat_number" => $order_address['vat_number'] ?? null
                ];
                $this->create($orderAddressData);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
