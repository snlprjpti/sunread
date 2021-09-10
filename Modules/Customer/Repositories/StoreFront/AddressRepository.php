<?php

namespace Modules\Customer\Repositories\StoreFront;

use Exception;
use Modules\Core\Repositories\BaseRepository;
use Modules\Country\Entities\City;
use Modules\Country\Entities\Region;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerAddress;

class AddressRepository extends BaseRepository
{
    public function __construct(CustomerAddress $customer_address)
    {
        $this->model = $customer_address;
        $this->model_key = "customers.addresses";

        $this->rules = [
            "first_name" => "required|min:2|max:200",
            "middle_name" => "sometimes|nullable|min:2|max:200",
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
            "default_shipping_address" => "sometimes|boolean",
            "region" => "sometimes",
            "city" => "sometimes"
        ];
    }

    public function regionAndCityRules(object $request, string $name): array
    {
        return [
            "{$name}.region_id" => "required_without:{$name}.region|exists:regions,id,country_id,{$request->{$name}["country_id"]}",
            "{$name}.city_id" => "required_without:{$name}.city",
            "{$name}.region" => "required_without:{$name}.region_id",
            "{$name}.city" => "required_without:{$name}.city_id",
        ];
    }

    public function checkShippingAddress(int $customer_id): object
    {
        try
        {
            $address = $this->model->whereCustomerId($customer_id)->whereDefaultShippingAddress(1);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $address;
    }

    public function checkBillingAddress(int $customer_id): object
    {
        try
        {
            $address = $this->model->whereCustomerId($customer_id)->whereDefaultBillingAddress(1);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $address;
    }

    public function createOrUpdate(object $request, int $customer_id): array
    {
        try
        {
            if($request->shipping) {
                $data = $this->validateAddress($request, $customer_id, "shipping");
                $shipping = $this->checkShippingAddress($customer_id)->first();
                $data = $this->checkRegionAndCity($data, "shipping");
                if ($shipping) {
                    $created["shipping"] = $this->update($data, $shipping->id);
                } else {
                    $data["default_shipping_address"] = 1;
                    $data["default_billing_address"] = 0;
                    $created["shipping"] = $this->create($data);
                }
            }

            if($request->billing) {
                $data = $this->validateAddress($request, $customer_id, "billing");
                $billing = $this->checkBillingAddress($customer_id)->first();
                $data = $this->checkRegionAndCity($data, "billing");
                if ($billing) {
                    $created["billing"] = $this->update($data, $billing->id);
                }
                else {
                    $data["default_shipping_address"] = 0;
                    $data["default_billing_address"] = 1;
                    $created["billing"] = $this->create($data);
                }
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $created;
    }

    public function validateAddress(object $request, int $customer_id, string $name): array
    {
        try
        {
            foreach ($this->rules as $key => $value)
            {
                $new_rules[ $name."." . $key] = $value;
            }
            $this->rules = $new_rules;

            $data = $this->validateData($request, array_merge($this->regionAndCityRules($request, $name)), function () use ($customer_id) {
                return [
                    "customer_id" => Customer::findOrFail($customer_id)->id,
                ];
            });

            $old_data = $data[$name];
            unset($data[$name]);
            $data = array_merge($old_data,$data);

            $this->rules = [];
            foreach ($new_rules as $key => $value) {
                $key = str_replace("$name.", "", $key);
                $this->rules[$key] = $value;
            }

        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function checkRegionAndCity(array $data, string $name): array
    {
        try
        {
            if(isset($data["region_id"])) {
                $data["region"] = null;
                if(isset($data["city_id"])) {
                    $data["city"] = null;
                }
                else {
                    $cities = City::whereRegionId($data["region_id"])->count();
                    if($cities > 0) throw new Exception(__("core::app.response.please-choose", [ "name" => "{$name} City" ]));
                }
            }
            else {
                $region = Region::whereCountryId($data["country_id"])->count();
                if($region > 0) throw new Exception(__("core::app.response.please-choose", [ "name" => "{$name} Region" ]));
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }
}
