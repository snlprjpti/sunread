<?php

namespace Modules\Category\Repositories;

use Illuminate\Support\Arr;
use Modules\Category\Entities\Category;
use Modules\Category\Entities\CategoryValue;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;
use Modules\Core\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    protected $repository, $fetched;
    protected $channel_model, $store_model;

    public function __construct(Category $category, CategoryValue $categoryValue, Channel $channel_model, Store $store_model)
    {
        $this->model = $category;
        $this->value_model = $categoryValue;
        $this->model_key = "catalog.categories";
        $this->rules = [
            // category validation
            "name" => "required",
            "scope" => "sometimes|in:website,channel,store",
            "scope_id" => "sometimes|integer|min:1",
            "position" => "sometimes|numeric",
            "image" => "required|mimes:jpeg,jpg,bmp,png",
            "description" => "sometimes|nullable",
            "meta_title" => "sometimes|nullable",
            "meta_description" => "sometimes|nullable",
            "meta_keywords" => "sometimes|nullable",
            "status" => "sometimes|boolean",
            "include_in_menu" => "sometimes|boolean",
            "parent_id" => "nullable|numeric|exists:categories,id",
            "website_id" => "required|exists:websites,id",
        ];

        $this->channel_model = $channel_model;
        $this->store_model = $store_model;
        $this->fetched = [ "name", "image", "description", "meta_title", "meta_keywords", "meta_description" ];
    }

    public function getDefaultValues(object $data, int $id)
    {
        $exist_values = Category::with(['values' => function ($query) use($data) {
            $query = $query->whereScope($data->scope ?? "website");
            if($data->scope_id) $query->whereScopeId($data->scope_id);
        }])->findOrFail($id);

        if($data->scope != "website" && $exist_values->values->count()==0)
        {
            $exist_values = $this->scopeFilter($data, $id);           
        }
        return $exist_values;
    }

    public function scopeFilter(object $data, int $id)
    {
        $input = [];
        switch($data->scope)
        {
            case "store":
            $input["scope"] = "channel";
            $input["scope_id"] = $this->store_model->find($data->scope_id)->channel->id;
            break;
                    
            case "channel":
            $input["scope"] = "website";
            $input["scope_id"] = $this->channel_model->find($data->scope_id)->website->id;
            break;
        }
        return $this->model->with(['values' => function ($query) use($input){
            $query = $query->whereScope(isset($input["scope"]) ? $input["scope"] : "website");
            if(isset($input["scope_id"])) $query->whereScopeId($input["scope_id"]);
        }])->findOrFail($id) ?? $this->scopeFilter((object)$input, $id);
    }

    public function show(object $data, int $id)
    {
        $exist_values = $this->getDefaultValues($data, $id)->toArray();

        foreach($exist_values["values"][0] as $key => $exist_value)
        {
            if(!in_array($key, $this->fetched)) continue;

            $exist_values[$key]["value"] = $exist_value;
            dd($this->scopeFilter($data, $id)->toArray());
            $exist_values[$key]["use_in_default"] = ($data->scope && $data->scope != "website" && $exist_value == $this->scopeFilter($data, $id)->values->toArray()[0][$key]) ? 1 : 0;
        }
        unset($exist_values["values"]);
        return $exist_values;
    }

}

