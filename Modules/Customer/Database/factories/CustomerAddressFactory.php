<?php
namespace Modules\Customer\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customer\Entities\Customer;

class CustomerAddressFactory extends Factory
{
    protected $model = \Modules\Customer\Entities\CustomerAddress::class;

    public function definition(): array
    {
        return [
            "customer_id" => Customer::inRandomOrder()->first()->id,
            "address1" => $this->faker->address(),
            "address2" => $this->faker->address(),
            "country" => $this->faker->country(),
            "state" => $this->faker->state(),
            "city" => $this->faker->city(),
            "postcode" => $this->faker->numerify('#####'),
            "phone" => $this->faker->phoneNumber(),
            "default_address" => 1,
            "name" => $this->faker->name(),
        ];
    }
}
