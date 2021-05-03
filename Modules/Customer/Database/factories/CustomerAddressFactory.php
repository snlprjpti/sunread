<?php
namespace Modules\Customer\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerAddress;

class CustomerAddressFactory extends Factory
{

    protected $model = CustomerAddress::class;

    public function definition()
    {
        return [
          "customer_id" => Customer::first()->id,
          "address1" => $this->faker->address,
          "address2" => $this->faker->address,
          "country" => $this->faker->country,
          "state" => $this->faker->state,
          "city" => $this->faker->city,
          "postcode" => $this->faker->numerify('#####'),
          "phone" => $this->faker->phoneNumber,
          "default_address" => 1,
          "name" => $this->faker->name,
        ];
    }
}

