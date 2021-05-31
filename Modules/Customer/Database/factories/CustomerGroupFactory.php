<?php
namespace Modules\Customer\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerGroupFactory extends Factory
{
    protected $model = \Modules\Customer\Entities\CustomerGroup::class;

<<<<<<< HEAD
    public function definition(): array
=======
    protected $model = CustomerGroup::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
>>>>>>> 5fdfc1b (refactor customer fixed)
    {
        return [
            "name" => $this->faker->name(),
            "slug" => $this->faker->unique()->slug(),
            "is_user_defined" => 0,
        ];
    }
}
