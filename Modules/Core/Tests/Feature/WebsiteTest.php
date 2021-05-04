<?php

namespace Modules\Core\Tests\Feature;

use Modules\Core\Entities\Website;
use Modules\Core\Tests\BaseTestCase;

class WebsiteTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = Website::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Website";
        $this->route_prefix = "admin.websites";
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
            "code" => null
        ]);
    }
}
