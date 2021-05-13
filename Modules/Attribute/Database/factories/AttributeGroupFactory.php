<?php
namespace Modules\Attribute\Database\factories;

use Modules\Attribute\Entities\AttributeFamily;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeGroupFactory extends Factory
{
    protected $model = \Modules\Attribute\Entities\AttributeGroup::class;

    public function definition(): array
    {
        $name = $this->faker->name();
        $slug = $this->faker->unique()->slug();
    
        return [
            "attribute_family_id" => AttributeFamily::factory()->create()->id,
            "slug" => $slug,
            "name" => $name,
            "position" => rand(1,20)
        ];
    }
}

