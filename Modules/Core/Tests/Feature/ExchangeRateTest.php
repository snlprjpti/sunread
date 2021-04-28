<?php

namespace Modules\Core\Tests\Feature;

use Modules\Core\Entities\ExchangeRate;
use Modules\Core\Tests\BaseTestCase;

class ExchangeRateTest extends BaseTestCase
{
    protected object $admin;
    protected array $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model = ExchangeRate::class;
        $this->model_name = "Exchange rate";
        $this->route_prefix = "admin.exchange_rates";
        $this->default_resource_id = ExchangeRate::latest()->first()->id;
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
    
    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "rate" => null
        ]);
    }

    public function getUpdateData(): array
    {
        return $this->model::factory()->make()->toArray();
    }

    public function getInvalidUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "rate" => null
        ]);
    }
}