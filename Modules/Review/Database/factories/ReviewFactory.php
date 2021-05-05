<?php
namespace Modules\Review\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customer\Entities\Customer;
use Modules\Product\Entities\Product;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Review\Entities\Review::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        return [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'rating' => rand(1,5),
            'title' => $this->faker->name(),
            'description' => $this->faker->paragraph()
        ];
    }
}

