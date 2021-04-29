<?php
namespace Modules\Category\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Category\Entities\Category;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Category\Entities\Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        while(true) {
            $name = $this->faker->name();
            $slug = \Str::slug($name);
            $old_store = Category::whereSlug($slug)->first();
            if(!$old_store) break;
        }

        return [
            "slug" => $slug,
            "name" => $name,
            "position" => $this->faker->randomDigit(),
            "status" => 1 
        ];
    }
}

