<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(\Modules\User\Entities\Admin::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'remember_token' => Str::random(10),
    ];
});
