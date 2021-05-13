<?php

namespace Modules\UrlRewrite\Tests\Feature;

use Modules\Core\Tests\BaseTestCase;
use Modules\UrlRewrite\Entities\UrlRewrite;

class UrlRewriteTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->model = UrlRewrite::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "UrlRewrite";
        $this->route_prefix = "admin.url-rewrites";
    }

    public function getInvalidCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "request_path" => null
        ]);
    }

    public function getNonMandodtaryCreateData(): array
    {
        return array_merge($this->getCreateData(), [
            "store_id" => null
        ]);
    }

    public function testShouldReturnErrorIfTypeIsInvalid()
    {
        $post_data = array_merge($this->model::factory()->make()->toArray(), [
            "type" => "invalid"
        ]);
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function testShouldReturnErrorIfParameterIdIsInvalid()
    {
        $post_data = array_merge($this->model::factory()->make()->toArray(), [
            "parameter_id" => 0
        ]);
        $response = $this->withHeaders($this->headers)->post(route("{$this->route_prefix}.store"), $post_data);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            "status" => "error"
        ]);
    }

    public function getInvalidUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "request_path" => null
        ]);
    }

    public function getNonMandodtaryUpdateData(): array
    {
        return array_merge($this->getUpdateData(), [
            "store_id" => null
        ]);
    }
}
