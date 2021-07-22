<?php

namespace Modules\Page\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Modules\Core\Entities\Store;
use Modules\Core\Tests\BaseTestCase;
use Modules\Page\Entities\Page;
use Illuminate\Support\Str;
use Modules\Core\Entities\Website;

class PageTest extends BaseTestCase
{
    protected $attributes = [], $config_fields;

    public function setUp(): void
    {
        $this->model = Page::class;

        parent::setUp();
        $this->admin = $this->createAdmin();

        $this->model_name = "Page";
        $this->route_prefix = "admin.pages";

        $this->default_resource_id = $this->model::latest('id')->first()->id;
    }

    public function getCreateData(): array
    {
        $this->config_fields = config("attributes");
        $scopes = [];
        $components = [];

        for($i = 0; $i < rand(1,2); $i++)
        {
            $data["scope"] = Arr::random([ "website", "store" ]);

            switch ($data["scope"]) {
                case "website":
                    $data["scope_id"] = Website::factory()->create()->id;
                    break;
    
                case "store":
                    $data["scope_id"] = Store::factory()->create()->id;
                    break;
            }
            $scopes[] = $data;
        }

        for($i = 0; $i < rand(1,2); $i++)
        {
            $components[] = Arr::random([ "banner", "content" ]);
        }

        $singleItem = [];
        foreach($components as $component)
        {
            $this->attributes = [];
            $elements = collect($this->config_fields)->where("slug", $component)->pluck("attributes")->first();
            $this->getAttributes($elements);
            $singleItem[] = [
                "component" => $component,
                "attributes" => $this->attributes,
                "position" => rand(1,2)
            ];
        }
        return array_merge($this->model::factory()->make()->toArray(), [
            "scopes" => $scopes,
            "components" => $singleItem
        ]);
    }

    private function getAttributes(array $elements): void
    {
        foreach($elements as $element)
        {
            if($element["hasChildren"] == 0)
            {
                $this->attributes[$element["slug"]] = $this->value($element["type"]);
                continue;
            }
            $this->getAttributes($element["attributes"]);
        }
    }

    private function value(string $type): mixed
    {
        switch($type)
        {
            case "radio" : 
                $value = true;
                break;

            case ($type == "datetime" || $type == "date"):
                $value = now();
                break;

            case "number":
                $value = rand(1,1000);
                break;

            case "file":
                $value = UploadedFile::fake()->image('image.jpeg');
                break;

            default:
                $value = Str::random(10);
                break;
        }
        return $value;
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
            "title" => null
        ]);
    }
}
