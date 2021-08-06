<?php

namespace Modules\Page\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Modules\Core\Tests\BaseTestCase;
use Modules\Page\Entities\Page;
use Illuminate\Support\Str;
use Modules\Core\Entities\Store;
use Modules\Page\Entities\PageScope;

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
        $this->hasStatusTest = true;
    }

    public function getCreateData(): array
    {
        $this->config_fields = config("attributes");
        $components = [];

        for($i = 0; $i < rand(1,2); $i++)
        {
            $components[] = Arr::random([ "banner" ]);
        }

        $singleItem = [];
        foreach($components as $component)
        {
            $this->attributes = [];
            $elements = collect($this->config_fields)->where("slug", $component)->pluck("groups")->first();
            $this->getAttributes($elements);
            unset($this->attributes["background-video"]);
            $singleItem[] = [
                "component" => $component,
                "attributes" => $this->attributes,
                "position" => rand(1,2)
            ];
        }
        
        return array_merge($this->model::factory()->make()->toArray(), [
            "stores" => [0],
            "components" => $singleItem
        ]);
    }

    private function getAttributes(array $elements, ?string $key = null): void
    {
        foreach($elements as &$element)
        {
            if(!isset($element["type"]))
            {
                $this->getAttributes($element["attributes"]);
                continue;
            }
            $append_key = isset($key) ? "$key.{$element["slug"]}" : $element["slug"];

            if($element["hasChildren"] == 0)
            {
                setDotToArray($append_key, $this->attributes, $this->value($element));           
                continue;
            }  
            if($element["type"] == "repeater")
            {
                $this->getAttributes($element["attributes"][0], "$append_key.0");
                continue;     
            } 
            $this->getAttributes($element["attributes"], $append_key);
        }
    }

    private function value(array $element): mixed
    {
        switch($element["type"])
        {
            case "radio" : 
                $value = true;
                break;

            case ($element["type"] == "datetime" || $element["type"] == "date"):
                $value = now();
                break;

            case "number":
                $value = rand(1,1000);
                break;

            case "file":
                $value = UploadedFile::fake()->image('image.jpeg');
                break;

            case "select":
                $value = (count($element["options"]) > 0) ? $element["options"][0]["value"] : Str::random(10);
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
