<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\Modules\Core\Entities\Channel::class, function (Faker $faker) {
    return [
        'code' => \Illuminate\Support\Str::random(16),
        'name' => $faker->company,
        'description' => $faker->paragraph,
        'timezone' => $faker->timezone,
        'theme' => \Illuminate\Support\Str::random(16),
        'default_locale_id' => factory(\Modules\Core\Entities\Locale::class)->create()->id,
        'base_currency_id' => factory(\Modules\Core\Entities\Currency::class)->create()->id,
    ];
});
