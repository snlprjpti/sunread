<?php

namespace Modules\Category\Repositories;

use Illuminate\Support\Arr;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\CategoryValue;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Repositories\BaseRepository;
use phpDocumentor\Reflection\Types\Boolean;

class CategoryRepository extends BaseRepository
{
    protected $repository, $fetched;
    protected $channel_model, $store_model;

    public function __construct(Category $category, CategoryValue $categoryValue, Channel $channel_model, Store $store_model)
    {
        $this->model = $category;
        $this->value_model = $categoryValue;
        $this->model_key = "catalog.categories";
        
        $key = "attributes.0";
        $this->rules = [
            // category validation
            "scope" => "sometimes|in:website,channel,store",
            "scope_id" => "sometimes|integer|min:1",
            "position" => "sometimes|numeric",

            "parent_id" => "nullable|numeric|exists:categories,id",
            "website_id" => "required|exists:websites,id",
        ];

        $this->channel_model = $channel_model;
        $this->store_model = $store_model;
        $this->fetched = [ "name", "image", "description", "meta_title", "meta_keywords", "meta_description", "status", "include_in_menu" ];
    }

    public function getValues(array $data): object
    {
        return $this->value_model->whereScope($data["scope"])->whereScopeId($data["scope_id"])->whereCategoryId($data["category_id"])->first();
    }

    public function getDefaultValues(array $data): object
    {
        switch($data["scope"])
        {
            case "store":
            $data["scope"] = "channel";
            $data["scope_id"] = $this->store_model->find($data["scope_id"])->channel->id;
            break;
                        
            case "channel":
            $data["scope"] = "website";
            $data["scope_id"] = $this->channel_model->find($data["scope_id"])->website->id;
            break;
        }
        return $this->getValues($data) ?? $this->getDefaultValues($data);
    }

    public function scopeFilter(string $scope, string $element_scope): bool
    {
        if($scope == "channel" && in_array($element_scope, ["website"])) return true;
        if($scope == "store" && in_array($element_scope, ["website", "channel"])) return true;
        return false;
    }

    public function show(array $data): array
    {
        $values = [];
        foreach($this->fetched as $key)
        {
            if(count($data)){
                $exist_data = $this->checkCondition($data) ? $this->getValues($data) : $this->getDefaultValues($data);
                $exist_data = $exist_data->toArray();
            }

            $values[$key]["value"] = isset($exist_data) ? $exist_data[$key] : null;
            $values[$key]["use_in_default"] = isset($exist_data) ? (($data["scope"] != "website" && $exist_data[$key] == $this->getDefaultValues($data)->$key) ? 1  : 0) : 0;
        }
        return $values;
    }

    public function checkCondition(array $data)
    {
        return (bool) $this->value_model->whereCategoryId($data["category_id"])->whereScope($data["scope"])->whereScopeId($data["scope_id"])->count();
    }

    public function getValidationRules(object $request)
    {
        return collect(config('category.attributes'))->map(function($data) {
            return $data;
        })->reject(function ($data) use($request) {
            return $this->scopeFilter($request->scope ?? "website", $data["scope"]);
        })->mapWithKeys(function($item) {
            $key = "attributes.0";
            $path =  "$key.{$item['title']}.value";
            $path1 =  "$key.{$item['title']}.use_default_value";

            $value_rule = ($item["is_required"]==1) ? "required_without:$key.{$item['title']}.use_default_value|{$item["rules"]}" : "{$item["rules"]}";
            $default_rule = ($item["is_required"]==1) ? "required_without:$key.{$item['title']}.value" : "";
            return [ 
                $path => $value_rule,
                $path1 => $default_rule,
            ];
        })->toArray();
    }

}

