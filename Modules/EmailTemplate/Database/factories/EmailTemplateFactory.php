<?php
namespace Modules\EmailTemplate\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmailTemplateFactory extends Factory
{
    protected $model = \Modules\EmailTemplate\Entities\EmailTemplate::class;

    public function definition(): array
    {
        return [
            "template_name" => $this->faker->name(),
            "template_subject" => $this->faker->name(),
            "template_content" => $this->faker->paragraph(),
            "template_style" => $this->faker->name()
        ];
    }
}

