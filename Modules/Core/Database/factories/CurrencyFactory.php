<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\Modules\Core\Entities\Currency::class, function (Faker $faker) {
    return [
        'code' => \Illuminate\Support\Str::random(16),
        'name' => \Illuminate\Support\Str::random(16),
        'symbol' => \Illuminate\Support\Str::random(16)
    ];
});
