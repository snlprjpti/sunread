<?php

namespace Modules\Core\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Auth\User;
use Modules\Core\Entities\ActivityLog;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition()
    {
        return [
            'log_name' => 'default',
            'subject_id' => rand(1,20),
            'subject_type' => $this->faker->paragraph,
            'causer_id' => rand(1,20),
            'causer_type' => 1,
            'properties' => $this->faker->paragraph,
            'description' => $this->faker->paragraph,
            'action' => $this->faker->name,
            'activity' => $this->faker->paragraph
        ];
    }
}
