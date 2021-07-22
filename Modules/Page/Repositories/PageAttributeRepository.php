<?php

namespace Modules\Page\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Facades\Event;
use Modules\Core\Traits\Configuration;
use Modules\Page\Entities\PageAttribute;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PageAttributeRepository extends BaseRepository
{
    use Configuration;
    protected $config_fields, $children = [], $parent = [], $attributes = [];

    public function __construct(PageAttribute $pageAttribute)
    {
        $this->model = $pageAttribute;
        $this->model_key = "page.attribute";
        $this->config_fields = config("attributes");

        $this->rules = [
            "component" => "required",
            "position" => "sometimes|numeric",
            "attributes" => "required|array"
        ];
    }

    public function validateAttribute(array $component): array
    {
        $this->attributes = [];

        $all_component_slugs = collect($this->getComponents())->pluck("slug")->toArray();
        if(!in_array($component["component"], $all_component_slugs)) throw ValidationException::withMessages(["component" => "Invalid Component name"]);


        $elements = collect($this->config_fields)->where("slug", $component["component"])->pluck("attributes")->first();
        $this->getAttributes($elements);

        return collect($this->attributes)->mapWithKeys(function($element) {
            $rule = ($element["is_required"] == 1) ? "required" : "nullable";
            return [
                "attributes.{$element["slug"]}" => "$rule|{$element["rules"]}"
            ];
        })->toArray();
    }

    public function updateOrCreate(array $components, object $parent):void
    {
        if ( !is_array($components) || count($components) == 0 ) return;

        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.sync.before");
        try
        {
            $page_attributes = [];
            foreach($components as $component)
            {
                $all_attributes = [];
                $rules = $this->validateAttribute($component);
                $data = $this->validateData(new Request($component), $rules);

                foreach($data["attributes"] as $slug => $value)
                {
                    $attributeData = collect($this->attributes)->where("slug", $slug)->first();
                    $item["slug"] = $slug;
                    $item["value"] = ($attributeData["type"] == "file" && $value) ? $this->storeScopeImage($value, "page") : $value;
                    $all_attributes[] = $item;
                }

                $input = [
                    "value" => $all_attributes,
                    "position" => $data["position"]
                ];
                $match = [
                    "page_id" => $parent->id,
                    "attribute" => $data["component"]
                ];
                $page_attributes[] = $this->model->updateOrCreate($match, $input);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.sync.after", $page_attributes);
        DB::commit();
    }

    public function show(string $slug, array $values = []): array
    {
        $this->children = []; 
        $this->parent = [];

        $data = collect($this->config_fields)->where("slug", $slug)->first();
        $this->getChildren($data["attributes"], values:$values);

        return [
            "title" => $data["title"],
            "slug" => $data["slug"],
            "attributes" => $this->parent
        ];        
    }

    public function getChildren(array $elements, ?int $key = null, array $values): void
    {
        if(count($this->children) > 0) $this->parent[] = $this->children;
        $this->children = [];
        if(isset($key)) $this->parent[$key]["attributes"] = [];
        foreach($elements as $i => &$element)
        {
            if($element["hasChildren"] == 0)
            {
                if( $element["provider"] !== "") $element["options"] = $this->cacheQuery((object) $element, $element["pluck"]);
                unset($element["pluck"], $element["provider"], $element["rules"]);

                if(count($values) > 0)
                {
                    $exist = collect($values)->where("slug", $element["slug"])->first();
                    $element["default"] = $exist ? $exist["value"] : $element["default"];
                }
                
                if(isset($key)) $this->parent[$key]["attributes"][] = $element;
                else $this->parent[$i] = $element;
                
                continue;
            }
            $this->children = $element;
            $this->getChildren($element["attributes"], $i, $values);
        }
    }

    public function getAttributes(array $elements): void
    {
        foreach($elements as $element)
        {
            if($element["hasChildren"] == 0)
            {
                $this->attributes[] = $element;
                continue;
            }
            $this->getAttributes($element["attributes"]);
        }
    }

    public function getComponents(): array
    {
        $component = [];
        foreach($this->config_fields as $field)
        {
            unset($field["attributes"]);
            $component[] = $field;
        }
        return $component;
    }
}
