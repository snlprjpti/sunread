<?php
namespace Modules\Review\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Customer\Entities\Customer;
use Modules\Review\Entities\Review;

class ReviewVoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Review\Entities\ReviewVote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $customer = Customer::factory()->create();
        $review = Review::factory()->create();

        return [
            'customer_id' => $customer->id,
            'review_id' => $review->id,
            'vote_type' => rand(0,1)
        ];
    }
}

