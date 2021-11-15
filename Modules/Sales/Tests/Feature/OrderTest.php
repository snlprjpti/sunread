<?php

namespace Modules\Sales\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Sales\Entities\Order;

class OrderTest extends BaseTestCase
{

    public function setUp(): void
    {
        $this->model = Order::class;
        parent::setUp();
        
        $this->admin = $this->createAdmin();
        // $this->filter = [
        //     "website_id" => 1
        // ];

        $this->model_name = "Order";
        $this->route_prefix = "admin.sales.orders";

        $this->createFactories = false;
        $this->hasStoreTest = false;
        $this->hasUpdateTest = false;
        $this->hasDestroyTest = false;
    }
}
