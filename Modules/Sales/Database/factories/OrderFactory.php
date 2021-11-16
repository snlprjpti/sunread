<?php
namespace Modules\Sales\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cart\Entities\Cart;
use Modules\Sales\Entities\Order;
use Symfony\Component\VarDumper\Exception\ThrowingCasterException;

class OrderFactory extends Factory
{
    protected $model = \Modules\Sales\Entities\Order::class;

    public function definition()
    {
        return [];
    }
}

