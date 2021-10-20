<?php

namespace Modules\Tax\Observers;

use Modules\Core\Facades\Audit;
use Modules\Tax\Entities\ProductTaxGroup;
use Modules\Tax\Facades\TaxCache;

class ProductTaxGroupObserver
{
    public function created(ProductTaxGroup $productTaxProductTaxGroup)
    {
        Audit::log($productTaxProductTaxGroup, __FUNCTION__);
		TaxCache::setProductTaxGroup();
    }

    public function updated(ProductTaxGroup $productTaxProductTaxGroup)
    {
        Audit::log($productTaxProductTaxGroup, __FUNCTION__);
		TaxCache::setProductTaxGroup();
    }

    public function deleted(ProductTaxGroup $productTaxProductTaxGroup)
    {
        Audit::log($productTaxProductTaxGroup, __FUNCTION__);
		TaxCache::setProductTaxGroup();
    }
}
