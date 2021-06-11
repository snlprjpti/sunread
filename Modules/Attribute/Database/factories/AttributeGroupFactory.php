<?php
namespace Modules\Attribute\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Attribute\Entities\AttributeSet;

class AttributeGroupFactory extends Factory
{
    protected $model = \Modules\Attribute\Entities\AttributeGroup::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        $slug = $this->faker->unique()->slug();
    
        return [
            "attribute_set_id" => AttributeSet::factory()->create()->id,
            "slug" => $slug,
            "name" => $name,
            "position" => rand(1,20)
        ];
    }
}

