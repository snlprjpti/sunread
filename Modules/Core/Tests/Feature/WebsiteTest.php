<?php

namespace Modules\Core\Tests\Feature;

use Modules\Core\Entities\Website;
use Modules\Core\Tests\BaseTestCase;

class WebsiteTest extends BaseTestCase
{
    protected object $admin;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model = Website::class;
        $this->model_name = "Website";
        $this->route_prefix = "admin.websites";
        $this->default_resource_id = Website::latest()->first()->id;
        $this->fake_resource_id = 0;

        $this->filter = [
            "sort_by" => "id",
            "sort_order" => "asc"
        ];
    }

    public function getCreateData(): array
    {
        return $this->model::factory()->make()->toArray();
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

    public function getUpdateData(): array
    {
        return $this->model::factory()->make()->toArray();
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "description" => null
        ]);
    }

    public function getInvalidUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "code" => null
        ]);
    }
}
