<?php

namespace Modules\Page\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Facades\Event;
use Modules\Core\Traits\Configuration;
use Modules\Page\Entities\PageAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class PageAttributeRepository extends BaseRepository
{
    use Configuration;
    public $config_fields = [];
    protected $parent = [], $config_rules = [], $collect_elements = [], $config_types = [];

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

    public function validateAttribute(array $component, ?string $method): array
    {
        try
        {
            $this->config_rules = [];
            $this->config_types = [];

            $all_component_slugs = collect($this->getComponents())->pluck("slug")->toArray();
            if (!in_array($component["component"], $all_component_slugs)) throw ValidationException::withMessages(["component" => "Invalid Component name"]);


            $this->collect_elements = collect($this->config_fields)->where("slug", $component["component"])->pluck("groups")->first();
            $this->getRules($component, $this->collect_elements, method:$method);
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $this->config_rules;
    }

    public function updateOrCreate(array $components, object $parent, ?string $method = null): void
    {
        if ( !is_array($components) || count($components) == 0 ) return;

        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.sync.before");
        try
        {
            $page_attributes = [];
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
                    "page_id" => $parent->id,
                    "attribute" => $data["component"],
                    "position" => isset($data["position"]) ? $data["position"] : null,
                    "value" => $all_attributes
                ];
                $page_attributes[] = isset($component["id"]) ? $this->update($input, $component["id"]) : $this->create($input);
            }
            $parent->page_attributes()->whereNotIn('id', array_filter(Arr::pluck($page_attributes, 'id')))->delete();
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.sync.after", $page_attributes);
        DB::commit();
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
            $default = ($type == "file" && $value && is_file($value)) ? $this->storeScopeImage($value, "page") : $value; 
        }
        catch (Exception $exception)
        {
            throw $exception;
        } 

        return $default;
    }

    public function show(string $slug): array
    { 
        try
        {
            $this->parent = [];

            $data = collect($this->config_fields)->where("slug", $slug)->first();
            $this->getChildren($data["groups"]);

        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return [
            "title" => $data["title"],
            "slug" => $data["slug"],
            "groups" => $this->parent
        ];        
    }

    private function getChildren(array $elements, ?string $key = null): void
    {
        try
        {
            foreach($elements as $i => &$element)
            {
                $append_key = isset($key) ? "$key.$i" : $i;

                if (isset($element["type"])) unset($element["rules"]);

                if ($element["hasChildren"] == 0) {
                    if ( $element["provider"] !== "" ) $element["options"] = $this->cacheQuery((object) $element, $element["pluck"]);
                    unset($element["pluck"], $element["provider"]);

                    setDotToArray($append_key, $this->parent, $element);           
                    continue;
                }

                setDotToArray($append_key, $this->parent,  $element);           
                $this->getChildren($element["attributes"], "$append_key.attributes");
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }        
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
                if (isset($element["options"]) && count($element["options"]) > 0) {
                    $options = Arr::pluck($element["options"], "value");
                    $option_str = implode(",", $options);
                    $rule = "$rule|in:$option_str";
                }

                $append_key = isset($key) ? "$key.{$element["slug"]}" : "{$element["slug"]}";

                $this->config_rules[$append_key] = "$rule|{$element["rules"]}"; 
                $this->config_types[$element["slug"]] = $element["type"];

                if ($method == "update" && isset($component["id"]) && $element["type"] == "file") $this->handleFileIssue($component, $append_key);

                if ($element["hasChildren"] == 0) continue;

                if ($element["type"] == "repeater") {
                    $count = ($item = $component["attributes"][$element["slug"]]) ? count($item) : 0;
                    for( $i=0; $i < $count; $i++ )
                    {
                        $this->getRules($component, $element["attributes"], "$append_key.$i", $method);
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

    public function getComponents(): array
    {
        try
        {
            $component = [];
            foreach($this->config_fields as $field)
            {
                unset($field["groups"]);
                $component[] = $field;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }  

        return $component;
    }
}
