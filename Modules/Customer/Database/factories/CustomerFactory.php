<?php
namespace Modules\Customer\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Entities\CustomerGroup;

class CustomerFactory extends Factory
{

    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $gender = [ "male", "female", "other" ];
        return [
            "customer_group_id" => CustomerGroup::factory()->create()->id,
            "first_name" => $this->faker->firstName(),
            "last_name" => $this->faker->lastName(),
            "gender" => Arr::random($gender),
            "date_of_birth" => $this->faker->dateTimeBetween('1950-01-01', '2012-12-31'),
            "email" => $this->faker->unique()->safeEmail(),
            "status" => 1,
            "password" => Hash::make("password"),
            "subscribed_to_news_letter" => 0,
            "remember_token" => Str::random(10)
        ];
    }
}

