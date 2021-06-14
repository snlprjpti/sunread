<?php

namespace Modules\Attribute\Tests\Feature;

use Illuminate\Support\Str;
use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;
use Modules\Attribute\Entities\Attribute;

class AttributeTest extends BaseTestCase
{
    public $non_filterable_fields;

    public function setUp(): void
    {
        $this->model = Attribute::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Attribute";
        $this->route_prefix = "admin.attribute.attributes";
        $this->non_filterable_fields = ["select", "multiselect", "checkbox"];
    }

    public function getCreateData(): array
    {
        return array_merge($this->model::factory()->make([
            "is_user_defined" => 1
        ])->toArray(), [
            "translations" => [
                [
                    "store_id" => Store::factory()->create()->id,
                    "name" => Str::random(10)
                ]
            ]
        ]);
    }

    public function getNonMandotaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "slug" => null
        ]);
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "name" => null
        ]);
    }

    public function testShouldReturnErrorIfNonUserDefinedAttributeIsDeleted()
    {
        $resource_id = $this->model::factory()->create([
            "is_user_defined" => 0
        ])->id;

        $response = $this->withHeaders($this->headers)->delete($this->getRoute("destroy", [$resource_id]));

        $response->assertStatus(403);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testAdminCanDeleteResource()
    {
        $resource_id = $this->model::factory()->create([
            "is_user_defined" => 1
        ])->id; 

        $response = $this->withHeaders($this->headers)->delete($this->getRoute("destroy", [$resource_id]));

        $response->assertNoContent();

        $check_resource = $this->model::whereId($resource_id)->first() ? true : false;
        $this->assertFalse($check_resource);
    }

    public function testAdminCanCreateResource()
    {
        $post_data = $this->getCreateData();
        $post_data = $this->addAttributeOptionIfNecessary($post_data);

        $response = $this->withHeaders($this->headers)->post($this->getRoute("store"), $post_data);

        $response->assertCreated();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanCreateResourceWithNonMandatoryData()
    {
        $post_data = $this->getNonMandodtaryCreateData();
        $post_data = $this->addAttributeOptionIfNecessary($post_data);

        $response = $this->withHeaders($this->headers)->post($this->getRoute("store"), $post_data);
 
        $response->assertCreated();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.create-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanUpdateResource()
    {
        $post_data = $this->getUpdateData();
        $post_data["type"] = $this->model::findOrFail($this->default_resource_id)->type;

        $post_data = $this->addAttributeOptionIfNecessary($post_data);

        $response = $this->withHeaders($this->headers)->put($this->getRoute("update", [$this->default_resource_id]), $post_data);
        
        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }

    public function testAdminCanUpdateResourceWithNonMandatoryData()
    {
        $post_data = $this->getNonMandodtaryUpdateData();
        $post_data["type"] = $this->model::findOrFail($this->default_resource_id)->type;

        $post_data = $this->addAttributeOptionIfNecessary($post_data);
        $response = $this->withHeaders($this->headers)->put($this->getRoute("update", [$this->default_resource_id]), $post_data);

        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => __("core::app.response.update-success", ["name" => $this->model_name])
        ]);
    }

    private function addAttributeOptionIfNecessary(array $post_data): array
    {
        if (in_array($post_data["type"], $this->non_filterable_fields))
        {
            $post_data["attribute_options"] = [
                [
                    "name" => "blue",
                    "position" => 2,
                    "translations" => [
                        [
                            "name" => "nilo",
                            "store_id" => 1
                        ]
                    ]
                ]
            ];   
        }   

        return $post_data;
    }
}
