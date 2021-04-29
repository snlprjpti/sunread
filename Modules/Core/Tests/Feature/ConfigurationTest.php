<?php

namespace Modules\Core\Tests\Feature;

use Modules\Core\Entities\Configuration;
use Modules\Core\Tests\BaseTestCase;

class ConfigurationTest extends BaseTestCase
{
    protected object $admin;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model = Configuration::class;
        $this->model_name = "Configuration";
        $this->route_prefix = "admin.configurations";
        $this->default_resource_id = Configuration::latest()->first()->id;
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
            "value" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "scope_id" => null
        ]);
    }

    public function getUpdateData(): array
    {
        return $this->model::factory()->make()->toArray();
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "value" => null
        ]);
    }

    public function getInvalidUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "scope_id" => null
        ]);
    }
}
