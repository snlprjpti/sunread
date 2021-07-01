<?php

namespace Modules\Tax\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Tax\Entities\TaxRate;

class TaxRateTest extends BaseTestCase
{
    public $non_filterable_fields;

    public function setUp(): void
    {
        $this->model = TaxRate::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Tax Rate";
        $this->route_prefix = "admin.taxes.tax-rates";
    }

    public function getNonMandotaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "country_id" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "use_zip_range" => "Invalid data type"
        ]);
    }
}
