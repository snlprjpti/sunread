<?php

namespace Modules\Page\Tests\Feature;

use Illuminate\Support\Arr;
use Modules\Core\Tests\BaseTestCase;
use Modules\Page\Entities\Page;
use Modules\Page\Entities\PageConfiguration;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageConfigurationTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = PageConfiguration::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Page Configuration";
        $this->route_prefix = "admin.pages.configurations";

        $this->hasFilters = false;
        $this->hasIndexTest = false;
        $this->hasUpdateTest = false;
        $this->hasBulkDestroyTest = false;
        $this->hasStatusTest = false;
    }

    public function getNonMandodtaryCreateData(): array
    {
        $scope = Arr::random(config('page.model_config'));
        $scope_id = app($scope)::factory(1)->create()->first()->id;
        $page_id = Page::factory(1)->create()->first()->id;
        return array_merge($this->getCreateData(), [
            "scope" => $scope,
            "scope_id" => $scope_id,
            "page_id" => $page_id
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "scope_id" => null,
            "page_id" => null,
        ]);
    }

}
