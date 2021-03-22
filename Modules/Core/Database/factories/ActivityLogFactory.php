<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\Modules\Core\Entities\ActivityLog::class, function (Faker $faker) {
    return [
        'log_name' => 'default',
        'description' => $faker->paragraph,
        'action' => $faker->paragraph,
        'activity' => $faker->paragraph
    ];
});
