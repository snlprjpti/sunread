<?php

namespace Modules\Tax\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\Tax\Entities\TaxRateTaxRule;

class TaxRateTaxRuleTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = TaxRateTaxRule::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Tax Rate Tax Rule";
        $this->route_prefix = "admin.taxes.tax-rates-tax-rules";
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "tax_rate_id" => null,
            "tax_rule_id" => null
        ]);
    }
}
