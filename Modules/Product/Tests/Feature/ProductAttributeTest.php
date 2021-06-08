<?php

namespace Modules\Product\Tests\Feature;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Modules\Core\Tests\BaseTestCase;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Storage;
use Modules\Attribute\Entities\Attribute;
use Modules\Category\Entities\Category;
use Modules\Product\Entities\ProductAttribute;
use Modules\Product\Entities\ProductImage;

class ProductAttributeTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = ProductAttribute::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Product Attribute";
        $this->route_prefix = "admin.catalog.products.attributes";

        $this->createFactories = false;
        $this->hasFilters = false;
        $this->hasIndexTest = false;
        $this->hasShowTest = false;
        $this->hasStoreTest = true;
        $this->hasUpdateTest = false;
        $this->hasDestroyTest = false;
        $this->hasBulkDestroyTest = false;
        $this->hasStatusTest = false;
    }

    public function testAdminCanCreateResourceWithNonMandatoryData()
    {
        $this->markTestSkipped("All data are mandatory.");
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "product_id" => null
        ]);
    }
}
