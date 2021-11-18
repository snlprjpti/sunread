<?php

namespace Modules\Sales\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Sales\Entities\OrderComment;

class OrderCommentTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = OrderComment::class;
        parent::setUp();

        $this->admin = $this->createAdmin();
        $this->createFactories = false;

        $this->model_name = "Order Comment";
        $this->route_prefix = "admin.sales.comments";
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "comment" => null
        ]);
    }
    
}
