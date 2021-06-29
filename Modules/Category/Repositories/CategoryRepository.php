<?php

namespace Modules\Category\Repositories;

use Modules\Category\Entities\Category;
use Modules\Category\Entities\CategoryValue;
use Modules\Category\Traits\HasScope;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    use HasScope;

    protected $repository;

    public function __construct(Category $category, CategoryValue $categoryValue)
    {
        $this->model = $category;
        $this->value_model = $categoryValue;
        $this->model_key = "catalog.categories";
        
        $this->rules = [
            // category validation
            "position" => "sometimes|numeric",

            "parent_id" => "nullable|numeric|exists:categories,id",
            "website_id" => "required|exists:websites,id",
        ];

        $this->createModel();
    }

    public function show(array $data): array
    {
        $values = [];
        $config_arrays = config("category.attributes");
        foreach($config_arrays as $config_array){
            $key = $config_array["title"];

            if(count($data)){
                if($this->scopeFilter($data["scope"], $config_array["scope"])) continue;
                $exist_data = $this->checkCondition($data) ? $this->getValues($data) : $this->getDefaultValues($data);
                $exist_data = $exist_data->toArray();
            }

            $values[$key]["value"] = isset($exist_data) ? $exist_data[$key] : null;
            $values[$key]["use_in_default"] = isset($exist_data) ? (($data["scope"] != "website" && $exist_data[$key] == $this->getDefaultValues($data)->$key) ? 1  : 0) : 0;
        }
        $item["attributes"]  = $values;
        return isset($data["category_id"]) ? array_merge($this->model->find($data["category_id"])->toArray(), $item) : $item;
    }

    public function getValidationRules(object $request, ?string $method = null): array
    {
        return collect(config('category.attributes'))->map(function($data) {
            return $data;
        })->reject(function ($data) use($request) {
            return $this->scopeFilter($request->scope ?? "website", $data["scope"]);
        })->mapWithKeys(function($item) use($method) {
            $key = "attributes.0";
            $path =  "$key.{$item['title']}.value";
            $value_rule = ( $item["is_required"] == 1 ) ? (($method == "updated") ? "required_without:$key.{$item['title']}.use_default_value|{$item["rules"]}" : "required|{$item["rules"]}") : "{$item["rules"]}";

            if($method == "updated"){
                $path1 =  "$key.{$item['title']}.use_default_value";
                $default_rule = ( $item["is_required"] == 1 ) ? "required_without:$key.{$item['title']}.value" : "boolean";
                return [ 
                    $path => $value_rule,
                    $path1 => $default_rule,
                ];
            }
            return [ 
                $path => $value_rule
            ];
        })->toArray();
    }
}

