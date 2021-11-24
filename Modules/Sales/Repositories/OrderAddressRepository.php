<?php

namespace Modules\Sales\Repositories;

use Exception;
use Modules\Sales\Rules\RegionRule;
use Modules\Sales\Entities\OrderAddress;
use Modules\Core\Repositories\BaseRepository;
use Modules\Customer\Entities\CustomerAddress;

class OrderAddressRepository extends BaseRepository
{    
    public function __construct(OrderAddress $orderAddress)
    {
        $this->model = $orderAddress;
        $this->model_key = "order_addresses";
        $this->rules = [
            "address" => "required|array",
            // "address.*.customer_address_id" => "sometimes|exists:customer_addresses,id",
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
                    "customer_id" => auth("customer")->id(),
                    "first_name" => $order_address['first_name'],
                    "middle_name" => $order_address['middle_name'] ?? null,
                    "last_name" => $order_address['last_name'],
                    "phone" => $order_address['phone'],
                    "address1" => $order_address['address_line_1'],
                    "address2" => $order_address['address_line_2'] ?? null,
                    "postcode" => $order_address['postal_code'],
                    "phone" => $order_address['phone'],
                    "vat_number" => $order_address['vat_number'] ?? null,
                    "country_id" => $order_address["country_id"],
                    "region_id" => $order_address["region_id"] ?? null,
                    "city_id" => $order_address["city_id"] ?? null,
                    "region_name" => $order_address["region_name"] ?? null,
                    "city_name" => $order_address["city_name"] ?? null
                ];
                if (auth("customer")->id() && $order_address["type"] == "billing") {
                    $data = ["default_billing_address" => 1];
                    $orderAddressData["default_billing_address"] = 1;
                } elseif (auth("customer")->id() && $order_address["type"] == "shipping") {
                    $data = ["default_shipping_address" => 1];
                    $orderAddressData["default_shipping_address"] = 1;
                }

                if (auth("customer")->id()) {
                    $data['customer_id'] = auth("customer")->id();
                    $customer_address = CustomerAddress::updateOrCreate($data, $orderAddressData);
                }
                unset($orderAddressData["address1"], $orderAddressData["address2"], $orderAddressData["postcode"]);
                $orderAddressData["order_id"] = $order->id;
                $orderAddressData["customer_address_id"] = isset($customer_address) ? $customer_address->id : null; 
                $orderAddressData["address_type"] = $order_address['type'];
                $orderAddressData["email"] = $order_address['email'];
                $orderAddressData["address_line_1"] = $order_address['address_line_1'];
                $orderAddressData["address_line_2"] = $order_address['address_line_2'] ?? null;
                $orderAddressData["postal_code"] = $order_address['postal_code'] ?? null;
                $this->create($orderAddressData);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
