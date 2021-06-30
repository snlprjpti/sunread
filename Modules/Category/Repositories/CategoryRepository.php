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

    protected $repository, $config_fields;

    public function __construct(Category $category, CategoryValue $categoryValue)
    {
        $this->model = $category;
        $this->value_model = $categoryValue;
        $this->model_key = "catalog.categories";
        
        $this->rules = [
            // category validation
            "position" => "sometimes|nullable|numeric",

            "parent_id" => "nullable|numeric|exists:categories,id",
            "website_id" => "required|exists:websites,id",
        ];

        $this->config_fields = config('category.attributes');

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
                    $element_title = $element["title"];
                    $existData = $this->checkCondition($data);
                    if($data["scope"] != "website") $element["use_default_value"] = $existData ? 0 : 1;
                    $element["value"] = $existData ? $this->getValues($data)->$element_title : $this->getDefaultValues($data)->$element_title;
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
        return collect(config('category.attributes'))->pluck('elements')->flatten(1)->map(function($data) {
            return $data;
        })->reject(function ($data) use($scope) {
            return $this->scopeFilter($scope, $data["scope"]);
        })->mapWithKeys(function($item) use($scope) {
            $prefix = "attributes.0";
            $value_path = "$prefix.{$item['title']}.value";
            $default_path = "$prefix.{$item['title']}.use_default_value";

            $value_rule = ($item["is_required"] == 1) ? (($scope != "website") ? "required_without:$default_path|{$item['rules']}" : "required|{$item['rules']}") : $item['rules'];
            if($scope != "website") $default_rule = ($item["is_required"] == 1) ? "required_without:$value_path|{$item['rules']}" : "boolean";

            $rules = [
                $value_path => $value_rule
            ];
            return isset($default_rule) ? array_merge($rules, [ $default_path => $default_rule ]) : $rules;
        })->toArray();
    }
}

