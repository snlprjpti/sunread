<?php
namespace Modules\Sales\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Sales\Entities\Order;
use Modules\User\Entities\Admin;

class OrderCommentFactory extends Factory
{
    protected $model = \Modules\Sales\Entities\OrderComment::class;

    public function definition()
    {
        return [
            "order_id" => Order::first()->id,
            "user_id" => Admin::first()->id,
            "comment" => $this->faker->text(),
        ];
    }
}

