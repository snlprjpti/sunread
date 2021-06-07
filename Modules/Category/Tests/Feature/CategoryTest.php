<?php

namespace Modules\Category\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Entities\Category;
use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;

class CategoryTest extends BaseTestCase
{
    protected int $root_category_id;

    public function setUp(): void
    {
        $this->model = Category::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Category";
        $this->route_prefix = "admin.catalog.categories";

        $this->model::factory(10)->create();
        $this->default_resource_id = $this->model::latest('id')->first()->id;
        $this->root_category_id = $this->model::oldest('id')->first()->id;
        $this->hasStatusTest = true;
    }

    public function getCreateData(): array
    {
        Storage::fake();
        $store = Store::factory()->create();

        return array_merge($this->model::factory()->make([
            "image" => UploadedFile::fake()->image("image.png")
        ])->toArray(), [
            "parent_id" => $this->root_category_id,
            "translation" => [
                "store_id" => $store->id,
                "name" => "Test"
            ]
        ]);
    }

    public function getNonMandotaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "position" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "name" => null,
            "parent_id" => null
        ]);
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(),[
            "image" => null
        ]);
    }

    public function basicAdminHeader()
    {
        $this->createAdmin(["role_slug" => "basic-admin"]);
        return $this->headers;
    }

    public function testShouldReturnErrorIfBasicAdminTryToStoreRootCategory()
    {
        $post_data = $this->getCreateData();
        $response = $this->withHeaders($this->basicAdminHeader())->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testShouldReturnErrorIfBasicAdminTryToUpdateRootCategory()
    {
        $post_data = array_merge($this->getUpdateData(), ["parent_id" => $this->default_resource_id]);
        $response = $this->withHeaders($this->basicAdminHeader())->put(route("{$this->route_prefix}.update", $this->root_category_id), $post_data);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testShouldReturnErrorIfBasicAdminTryToFetchRootCategory()
    {
        $response = $this->withHeaders($this->basicAdminHeader())->get(route("{$this->route_prefix}.show", $this->root_category_id));

        $response->assertStatus(403);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testShouldReturnErrorIfBasicAdminTryToDeleteRootCategory()
    {
        $response = $this->withHeaders($this->basicAdminHeader())->delete(route("{$this->route_prefix}.destroy", $this->root_category_id));

        $response->assertStatus(403);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }
}
