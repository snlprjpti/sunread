<?php
namespace Modules\Review\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Review\Entities\Review;

class ReviewReplyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Review\Entities\ReviewReply::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $review = Review::factory()->create();

        return [
            'review_id' => $review->id,
            'description' => $this->faker->paragraph()
        ];
    }
}

