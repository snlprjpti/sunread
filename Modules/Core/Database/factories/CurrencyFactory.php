<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\Modules\Core\Entities\Currency::class, function (Faker $faker) {
    return [
        'code' => $faker->currencyCode,
        'name' => $faker->currencyCode,
        'symbol' => $faker->currencyCode
    ];
});
