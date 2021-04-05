<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\Modules\Core\Entities\Locale::class, function (Faker $faker) {
    return [
        'code' => \Str::random(16),
        'name' => $faker->company
    ];
});
