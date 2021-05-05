<?php
namespace Modules\Brand\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Brand\Entities\Brand;

class BrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Brand\Entities\Brand::class;

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
            $old_brand = Brand::where("slug", $slug)->first();
            if (!$old_brand) break;
        }

        return [   
            "name" => $name,
            "slug" => $slug,
            "description" => $this->faker->paragraph
        ];
    }
}

