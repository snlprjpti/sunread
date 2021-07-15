<?php
namespace Modules\Customer\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Country\Entities\City;
use Modules\Country\Entities\Country;
use Modules\Country\Entities\Region;
use Modules\Customer\Entities\Customer;

class CustomerAddressFactory extends Factory
{
    protected $model = \Modules\Customer\Entities\CustomerAddress::class;

    public function definition(): array
    {
        $country = Country::orderBy("created_at", "desc")->first();
        $region = $country ? Region::whereCountryId($country->id)->first() : null;
        $city = $region ? City::whereRegionId($region->id)->first() : null;

        return [
            "customer_id" => Customer::inRandomOrder()->first()->id,
            "first_name" => $this->faker->firstName(),
            "last_name" => $this->faker->lastName(),
            "address1" => $this->faker->address(),
            "address2" => $this->faker->address(),
            "address3" => $this->faker->address(),
            "country_id" => $country->id,
            "region_id" => $region ? $region->id : null,
            "city_id" => $city ? $city->id : null,
            "postcode" => $this->faker->numerify("#####"),
            "phone" => $this->faker->phoneNumber(),
            "default_billing_address" => 1,
            "default_shipping_address" => 1
        ];
    }
}
