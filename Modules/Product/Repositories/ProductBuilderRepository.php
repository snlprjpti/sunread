<?php

namespace Modules\Product\Repositories;

use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;
use Modules\Product\Entities\Product;
use Illuminate\Support\Facades\Storage;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Product\Entities\ProductBuilder;
use Modules\Core\Repositories\BaseRepository;
use Illuminate\Validation\ValidationException;

class ProductBuilderRepository extends BaseRepository
{
    public $config_fields = [];
    protected $parent = [], $config_rules = [], $collect_elements = [], $config_types = [], $product;
    
    public function __construct(ProductBuilder $productBuilder, Product $product)
    {
        $this->model = $productBuilder;
        $this->product = $product;
        $this->config_fields = config("attributes");
        
        $this->rules = [
            "component" => "required",
            "attributes" => "required|array",
            "position" => "sometimes|numeric|min:1",
        ];
    }
    public function component(object $product, string $method, array $value, array $scopeArr): bool
    {
        DB::beginTransaction();
        try
        {
            $productId = $product->id;
            $components = $value['value'];
            if ( !is_array($components) || count($components) == 0 ) return false;
        
                foreach($components as $component)
                {
                    $this->parent = [];
                    $all_attributes = [];
                    $data = $this->validateData(new Request($component));
                    $rules = $this->validateAttribute($component, $method);
                    $attribute_request = new Request($component["attributes"]);
        
                    $data["attributes"] = $attribute_request->validate($rules);

                    foreach($data["attributes"] as $slug => $value)
                    {
                        $type = $this->config_types[$slug];
                        
                        if (is_array($value) && $type == "repeater") $all_attributes[$slug] = $this->getRepeatorType($value, $slug);
                        elseif (is_array($value) && $type == "normal") $all_attributes[$slug] = $this->getNormalType($value, $slug);
                        else $all_attributes[$slug] = $this->getValue($type, $value, $slug);
                    }
                    $input = [
                        "product_id" => $productId,
                        "attribute" => $component["component"],
                        "scope" => $scopeArr["scope"],
                        "scope_id" => $scopeArr["scope_id"]
                    ];

                    $productBuilderData = array_merge($input, ["value" => json_encode($all_attributes), "position" => $data['position'] ?? 1]);
                    $this->model->updateOrCreate($input, $productBuilderData);
                }
        } 
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
        return true;      
    }

    public function validateAttribute(array $component, ?string $method): array
    {
        try
        {
            $this->config_rules = [];
            $this->config_types = [];

            $all_component_slugs = collect($this->getComponents())->pluck("slug")->toArray();
            if (!in_array($component["component"], $all_component_slugs)) throw ValidationException::withMessages(["component" => "Invalid Component name"]);


            $group_elements = collect($this->config_fields)->where("slug", $component["component"])->pluck("mainGroups")->flatten(1);
            foreach($group_elements as $group_element)
            {
                if($group_element["type"] == "module") {
                    foreach($group_element["subGroups"] as $module) $this->collect_elements = array_merge($this->collect_elements, $module["groups"]);
                    continue;
                }
                $this->collect_elements = array_merge($this->collect_elements, $group_element["groups"]);
            }

            $this->getRules($component, $this->collect_elements, method:$method);
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $this->config_rules;
    }

    public function getComponents(): array
    {
        try
        {
            $component = [];
            foreach($this->config_fields as $field)
            {
                unset($field["mainGroups"]);
                $component[] = $field;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }  
        return $component;
    }

    private function getRules(array $component, array $elements, ?string $key = null, ?string $method): void
    {
        try
        {
            foreach($elements as &$element)
            {
                $state = 1;
                if (!isset($element["type"])) 
                {
                    $this->getRules($component, $element["attributes"], method:$method);
                    continue;
                }

                if (count($element["conditions"]) > 0) $state = $this->checkConditions($element, $component);
                if ($state == 0) continue;

                $rule = ($element["is_required"] == 1) ? "required" : "nullable";
                $append_key = isset($key) ? "$key.{$element["slug"]}" : "{$element["slug"]}";

                $this->config_rules[$append_key] = "$rule|{$element["rules"]}"; 
                $this->config_types[$element["slug"]] = $element["type"];

                if ($method == "update" && isset($component["id"]) && $element["type"] == "file") $this->handleFileIssue($component, $append_key);

                if ($element["hasChildren"] == 0) continue;

                if ($element["type"] == "repeater") {
                    $count = ($item = $component["attributes"][$element["slug"]]) ? count($item) : 0;
                    for( $i=0; $i < $count; $i++ )
                    {
                        $this->getRules($component, $element["attributes"][0], "$append_key.$i", $method);
                    } 
                    continue;     
                } 
        
                $this->getRules($component, $element["attributes"], $append_key, $method);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        } 
    }

    public function handleFileIssue(array $component, string $append_key): void
    {
        try
        {
            $exist_component = $this->model->findOrFail($component["id"]);
            $exist_values = $exist_component->value;
            $request_element_value = getDotToArray("attributes.$append_key", $component);
            if ($request_element_value && !is_file($request_element_value)) {
                $db_value = getDotToArray($append_key, $exist_values);
                if ($db_value) {
                    $this->config_rules[$append_key] = "";
                    setDotToArray($append_key, $this->parent, $db_value);  
                }
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        } 
    }

    private function getRepeatorType(array $repeators, string $parent_slug): array
    {
        try
        {
            $element = [];
            foreach($repeators as $i => $repeator)
            {
                $data = [];
                foreach($repeator as $slug => $value)
                {
                    $append_key = "$parent_slug.$i.$slug";
                    $type = $this->config_types[$slug];
                    $data[$slug] = $this->getValue($type, $value, $append_key);
                }  
                $element[] = $data;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $element;
    }

    private function getNormalType(array $normals, string $parent_slug): array
    {
        try
        {
            $element = [];       
            foreach($normals as $slug => $value)
            {
                $type = $this->config_types[$slug];
                $element[$slug] = $this->getValue($type, $value, "$parent_slug.$slug");
            } 
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $element;
    }

    private function getValue(string $type, mixed $value, string $slug): mixed
    {
        try
        {
            if ($global_value = getDotToArray($slug, $this->parent)) return $global_value;
            $default = ($type == "file" && $value && is_file($value)) ? $this->storeScopeImage($value, "product-page") : $value; 
        }
        catch (Exception $exception)
        {
            throw $exception;
        } 

        return $default;
    }

    public function checkConditions(array $element, array $component): int
    {
        try
        {
            $state = 0;
            foreach($element["conditions"]["condition"] as $conditions)
            {
                if ($state == 1) break;
                foreach($conditions as $k => $condition)
                {
                    if (isset($component["attributes"][$k]) && $component["attributes"][$k] == $condition) $state = 1;
                    else {
                        $state = 0;
                        break;
                    }
                }
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }  

        return $state;     
    }

    public function getProductBuilder(int $id, array $scope): array
    {
        try
        {
            $product = $this->product::findOrFail($id);
            AttributeSet::findOrFail($product->attribute_set_id);
            $whereCondition = array_merge($scope, ["product_id" => $id]);

            $attributes = [];
            $components = $product->productBuilderValues()->where($whereCondition)->get();
            foreach($components as $component)
            {
                $attributes[] = $this->getParent($component);
            }

            $item = $attributes;
        }
        catch( Exception $exception )
        {
            throw $exception;
        }
        return $item;
    }

    public function getParent(object $component): array
    {
        try
        {
            $this->parent = [];

            $data = collect($this->pageAttributeRepository->config_fields)->where("slug", $component->attribute)->first();
            $this->getChildren($data["mainGroups"], values:json_decode($component->value, true));
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return [
            "id" => $component->id,
            "title" => $data["title"],
            "slug" => $data["slug"],
            "mainGroups" => $this->parent
        ];
    }

    private function getChildren(array $elements, ?string $key = null, array $values, ?string $slug_key = null): void
    {
        try
        {
            foreach($elements as $i => &$element)
            {
                $append_key = isset($key) ? "$key.$i" : $i;
                $append_slug_key = isset($slug_key) ? "$slug_key.{$element["slug"]}" : $element["slug"];

                if(isset($element["groups"])) {
                    setDotToArray($append_key, $this->parent,  $element);
                    $this->getChildren($element["groups"], "$append_key.groups", $values);
                    continue;
                }

                if(isset($element["subGroups"])) {
                    setDotToArray($append_key, $this->parent,  $element);
                    $this->getChildren($element["subGroups"], "$append_key.subGroups", $values);
                    continue;
                }
                
                if ($element["hasChildren"] == 0) {
                    if ( $element["provider"] !== "" ) $element["options"] = $this->pageAttributeRepository->cacheQuery((object) $element, $element["pluck"]);
                    unset($element["pluck"], $element["provider"], $element["rules"]);

                    if (count($values) > 0) {
                        $default = getDotToArray($append_slug_key, $values);
                        if($default) $element["default"] = ($element["type"] == "file") ? Storage::url($default) : $default;
                    }

                    setDotToArray($append_key, $this->parent, $element);
                    continue;
                }

                if (isset($element["type"])) {
                    unset($element["pluck"], $element["provider"], $element["rules"]);

                    if ($element["type"] == "repeater") {
                        $count = isset($values[$element["slug"]]) ? count($values[$element["slug"]]) : 0;
                        $fake_element = $element;
                        $fake_element["attributes"] = [];
                        for($j=0; $j<$count; $j++)
                        {
                            if ($j==0) setDotToArray($append_key, $this->parent, $fake_element);
                            $this->getChildren($element["attributes"][0], "$append_key.attributes.$j", $values, "$append_slug_key.$j");
                        }
                        continue;
                    }
                    if ($element["type"] == "normal") {
                        setDotToArray($append_key, $this->parent,  $element);
                        $this->getChildren($element["attributes"], "$append_key.attributes", $values, $append_slug_key);
                        continue;
                    }
                }

                setDotToArray($append_key, $this->parent,  $element);
                $this->getChildren($element["attributes"], "$append_key.attributes", $values);
            }
        }
        catch( Exception $exception )
        {
            throw $exception;
        }
    }

    
}