<?php
namespace Modules\User\Database\factories;

use Illuminate\Support\Str;
use Modules\User\Entities\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\User\Entities\Admin::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "role_id" => Role::where("slug", "<>", "super-admin")->first()->id,
            "first_name" => $this->faker->firstName(),
            "last_name" => $this->faker->lastName(),
            "company" => $this->faker->company(),
            "address" => $this->faker->address(),
            "email" => $this->faker->unique()->safeEmail(),
            "password" => Hash::make("password"),
            "remember_token" => Str::random(10),
            "status" => 1
        ];
    }
}

