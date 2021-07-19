<?php

namespace Modules\Category\Repositories;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\CategoryValue;
use Modules\Category\Traits\HasScope;
use Modules\Core\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    use HasScope;

    protected $repository, $config_fields;
    protected bool $without_pagination = true;

    public function __construct(Category $category, CategoryValue $categoryValue)
    {
        $this->model = $category;
        $this->value_model = $categoryValue;
        $this->model_key = "catalog.categories";
        
        $this->rules = [
            // category validation
            "position" => "sometimes|nullable|numeric",

            "parent_id" => "nullable|numeric|exists:categories,id",

            "products" => "sometimes|array",
            "products.*" => "sometimes|exists:products,id"
        ];

        $this->config_fields = config("category.attributes");

        $this->createModel();
    }

    public function getConfigData(array $data): array
    {
        $fetched = $this->config_fields;
       
        foreach($fetched as $key => $children){
            if(!isset($children["elements"])) continue;

            $children_data["title"] = $children["title"];
            $children_data["elements"] = [];
            
            foreach($children["elements"] as &$element){
                if($this->scopeFilter($data["scope"], $element["scope"])) continue;

                if(isset($data["category_id"])){
                    $data["attribute"] = $element["slug"];

                    $existData = $this->has($data);
                    
                    if($data["scope"] != "website") $element["use_default_value"] = $existData ? 0 : 1;
                    $elementValue = $existData ? $this->getValues($data) : $this->getDefaultValues($data);
                    $element["value"] = $elementValue?->value ?? null;
                    if ($element["type"] == "file" && $element["value"]) $element["value"] = Storage::url($element["value"]);
                }
                unset($element["rules"]);

                $children_data["elements"][] = $element;
            }
            $fetched[$key] = $children_data;
        }
        return $fetched;
    }

    public function getValidationRules(object $request): array
    {
        $scope = $request->scope ?? "website";
        return collect(config("category.attributes"))->pluck("elements")->flatten(1)->map(function($data) {
            return $data;
        })->reject(function ($data) use ($scope) {
            return $this->scopeFilter($scope, $data["scope"]);
        })->mapWithKeys(function ($item) use ($scope) {
            $prefix = "items.{$item["slug"]}";
            $value_path = "{$prefix}.value";
            $default_path = "{$prefix}.use_default_value";

            $value_rule = ($item["is_required"] == 1) ? (($scope != "website") ? "required_without:{$default_path}" : "required") : "nullable";
            $value_rule = "$value_rule|{$item["rules"]}";
            if($scope != "website") $default_rule = ($item["is_required"] == 1) ? "required_without:{$value_path}|{$item["rules"]}" : "boolean";

            $rules = [
                $value_path => $value_rule
            ];
            return isset($default_rule) ? array_merge($rules, [ $default_path => $default_rule ]) : $rules;
        })->toArray();
    }

    public function createUniqueSlug(object $request, ?int $id = null)
    {
        $slug = Str::slug($request->items["name"]["value"]);
        $original_slug = $slug;

        $count = 1;

        while ($this->checkSlug($request, $slug, $id)) {
            $slug = "{$original_slug}-{$count}";
            $count++;
        }
        return $slug;
    }

    public function updatePosition(array $data, int $id): object
    {        
        DB::beginTransaction();
        Event::dispatch("{$this->model_key}.update.before");

        try
        {
            $category = $this->model->findOrFail($id);
            $parent_id = isset($data["parent_id"]) ? $data["parent_id"] : null;
            if($parent_id != $category->parent_id) $category->update(["parent_id" => $parent_id]);

            $all_category = $parent_id ? $this->model->findOrFail($parent_id)->children : $this->model->whereParentId(null)->get();
            if($data["position"] > count($all_category)) $data["position"] = count($all_category);
            
            $allnodes = $all_category->sortBy('_lft')->values();
            $position_category = $allnodes->get(($data["position"]-1));
            $key = key(collect($allnodes)->where('id', $id)->toArray()) + 1;
            
            ($position_category->_lft < $category->_lft) ? $category->up($key-$data["position"]) : $category->down($data["position"]-$key);

        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("{$this->model_key}.update.after", $category);
        DB::commit();

        return $category;
    }
}

