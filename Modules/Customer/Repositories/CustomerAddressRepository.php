<?php

namespace Modules\Customer\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Country\Entities\City;
use Modules\Country\Entities\Region;
use Modules\Customer\Entities\Customer;
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

    public function regionAndCityRules(object $request): array
    {
        return [
            "region_id" => "required_without|exists:regions,id,country_id,{$request->country_id}",
            "city_id" => "required_without|exists:cities,id,region_id,{$request->region_id}",
            "region_name" => "required_without:region_id",
            "city_name" => "required_without:city_id",
        ];
    }

    public function checkRegionAndCity(array $data): array
    {
        try
        {
            if(isset($data["region_id"])) {
                $data["region_name"] = null;
                if(isset($data["city_id"])) $data["city_name"] = null;
                else {
                    $data["city_id"] = null;
                    $cities = City::whereRegionId($data["region_id"])->count();
                    if($cities > 0) throw new Exception(__("core::app.response.please-choose", [ "name" => "City" ]));
                }
            }
            else {
                $data["region_id"] = null;
                $data["city_id"] = null;
                $region = Region::whereCountryId($data["country_id"])->count();
                if($region > 0) throw new Exception(__("core::app.response.please-choose", [ "name" => "Region" ]));
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function unsetDefaultAddresses(array $data, int $customer_id, int $address_id): void
    {
        DB::beginTransaction();

        try
        {
            $customer = Customer::findOrFail($customer_id);

            foreach (["default_billing_address", "default_shipping_address"] as $address_type) {
                if ( !isset($data[$address_type]) ) continue;
                if ( $data[$address_type] != 1 ) continue;

                $customer->addresses()->where("id", "<>", $address_id)->update([
                    $address_type => 0
                ]);
            }
        }
        catch(Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
    }

    public function updateDefaultAddress(array $data, int $customer_id, int $address_id, ?callable $callback = null): object
    {
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.updated-default.before");

        try
        {
            $updated = $this->model::whereCustomerId($customer_id)->whereId($address_id)->firstOrFail();
            $updated->fill($data);
            $updated->save();

            if ($callback) $callback($updated);
        }
        catch(Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.updated-default.after", $updated);
        DB::commit();

        return $updated;
    }
}
