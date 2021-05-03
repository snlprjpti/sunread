<?php
namespace Modules\Customer\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customer\Entities\CustomerGroup;

class CustomerGroupFactory extends Factory
{

    protected $model = CustomerGroup::class;

    public function definition()
    {
        return [
            "name" => $this->faker->name(),
            "slug" => $this->faker->slug(),
            "is_user_defined" => 0,
        ];
    }
}

