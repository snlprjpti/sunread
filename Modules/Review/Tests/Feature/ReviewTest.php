<?php

namespace Modules\Review\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Review\Entities\Review;

class ReviewTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Review::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Review";
        $this->route_prefix = "admin.reviews";
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "description" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "rating" => null
        ]);
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "tile" => null
        ]);
    }

    public function getInvalidUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "customer_id" => null
        ]);
    }
}
