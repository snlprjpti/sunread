<?php

namespace Modules\Customer\Repositories\StoreFront;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Facades\SiteConfig;
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
            "region_name" => "sometimes",
            "city_name" => "sometimes"
        ];
    }

    public function regionAndCityRules(object $request, string $name): array
    {
//        return [
//            "region_id" => "required_without:region_name|exists:regions,id,country_id,{$request->country_id}",
//            "city_id" => "required_without:city_name",
//            "region_name" => "required_without:region_id",
//            "city_name" => "required_without:city_id",
//        ];

        return [
            "{$name}.region_id" => "required_without:{$name}.region_name|exists:regions,id,country_id,{$request->{$name}["country_id"]}",
            "{$name}.city_id" => "required_without:{$name}.city_name",
            "{$name}.region_name" => "required_without:{$name}.region_id",
            "{$name}.city_name" => "required_without:{$name}.city_id",
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
            $customer = Customer::findOrFail($customer_id);
            $countries = $this->getCountry($request);

            $new_rules = $this->rules;
            if($request->shipping) {

                if(!isset($request->shipping["country_id"])) throw new Exception(__("core::app.response.not-found",["name" => "Country"]));
                if (!$countries->contains("id", $request->shipping["country_id"])) throw new Exception(__("core::app.response.invalid-country"));

                $this->rules = [];
                foreach($new_rules as $key => $value)
                {
                    $this->rules["shipping.".$key] = $value;
                }

                $data = $this->validateData($request, array_merge($this->regionAndCityRules($request,"shipping")));
                $data["shipping"] = array_merge($data["shipping"], ["customer_id" => $customer->id]);

                $shipping = $this->checkShippingAddress($customer->id)->first();
                $data = $this->checkRegionAndCity($data["shipping"], "shipping");

                if ($shipping) {
                    $created["shipping"] = $this->update($data, $shipping->id);
                } else {
                    $data["default_shipping_address"] = 1;
                    $data["default_billing_address"] = 0;
                    $created["shipping"] = $this->create($data);
                }
            }

            if($request->billing) {

                if(!isset($request->billing["country_id"])) throw new Exception(__("core::app.response.not-found",["name" => "Country"]));

                if (!$countries->contains("id", $request->billing["country_id"])) throw new Exception(__("core::app.response.invalid-country"));

                $this->rules = [];
                foreach($new_rules as $key => $value)
                {
                    $this->rules["billing.".$key] = $value;
                }

                $data = $this->validateData($request, array_merge($this->regionAndCityRules($request,"billing")));
                $data["billing"] = array_merge($data["billing"], ["customer_id" => $customer->id]);

                $billing = $this->checkBillingAddress($customer->id)->first();
                $data = $this->checkRegionAndCity($data["billing"], "billing");
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

    public function checkRegionAndCity(array $data, string $name): array
    {
        try
        {
            if(isset($data["region_id"])) {
                $data["region_name"] = null;
                if(isset($data["city_id"])) $data["city_name"] = null;
                else {
                    $data["city_id"] = null;
                    $cities = City::whereRegionId($data["region_id"])->count();
                    if($cities > 0) throw new Exception(__("core::app.response.please-choose", [ "name" => "{$name} City" ]));
                }
            }
            else {
                $data["region_id"] = null;
                $data["city_id"] = null;
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

    public function getCountry(object $request): object
    {
        try
        {
            $data = $this->getCoreCache($request);

            if (!$data->channel) throw new Exception(__("core::app.response.not-found", ["name" => "Country"]));
            $allow = SiteConfig::fetch("allow_countries", "channel", $data->channel->id);
            $default[] = SiteConfig::fetch("default_country", "channel", $data->channel->id);

            $fetched = $allow->merge($default);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $fetched;
    }
}
