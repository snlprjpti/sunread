<?php
namespace Modules\Customer\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customer\Entities\Customer;

class CustomerAddressFactory extends Factory
{
    protected $model = \Modules\Customer\Entities\CustomerAddress::class;

<<<<<<< HEAD
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
=======
    protected $model = CustomerAddress::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
          "customer_id" => Customer::first()->id,
          "address1" => $this->faker->address(),
          "address2" => $this->faker->address(),
          "country" => $this->faker->country(),
          "state" => $this->faker->state(),
          "city" => $this->faker->city(),
          "postcode" => $this->faker->numerify('#####'),
          "phone" => $this->faker->phoneNumber(),
          "default_address" => 1,
          "name" => $this->faker->name(),
>>>>>>> 5fdfc1b (refactor customer fixed)
        ];
    }
}
