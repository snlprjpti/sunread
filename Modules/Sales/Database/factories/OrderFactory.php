<?php
namespace Modules\Sales\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Sales\Entities\Order;

class OrderFactory extends Factory
{
    protected $model = \Modules\Sales\Entities\Order::class;

    public function definition()
    {
        return [];
    }
}

