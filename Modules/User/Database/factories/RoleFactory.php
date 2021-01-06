<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Modules\User\Entities\Role;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'name' => $this->faker->name,
        'slug' => $this->faker->slug,
        'description' => $this->faker->sentence,
        'permission_type' => 'custom',
        'permissions' => [
            '*'
        ]
    ];
});
