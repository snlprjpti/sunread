<?php

namespace Modules\Category\Traits;

use Modules\Category\Entities\CategoryValue;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;

trait HasScope
{
    public $channel_model, $store_model, $value_model;
    
    public function createModel(): void
    {
        $this->channel_model = new Channel();
        $this->store_model = new Store();
        $this->value_model = new CategoryValue();
    }

    public function getDefaultValues(array $data): ?object
    {
        if($data["scope"] != "website")
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
            return $this->has($data) ? $this->getValues($data) : $this->getDefaultValues($data);
        }
        return $this->has($data) ? $this->getValues($data) : null;
    }

    public function getValues(array $data): object
    {
        return $this->checkCondition($data)->first();
    }

    public function scopeFilter(string $scope, string $element_scope): bool
    {
        if($scope == "channel" && in_array($element_scope, ["website"])) return true;
        if($scope == "store" && in_array($element_scope, ["website", "channel"])) return true;
        return false;
    }

    public function has(array $data)
    {
        return (boolean) $this->checkCondition($data)->count();
    }

    public function checkCondition(array $data): object
    {
        return $this->value_model->whereCategoryId($data["category_id"])->whereScope($data["scope"])->whereScopeId($data["scope_id"])->whereAttribute($data["attribute"]);  
    }

    public function checkSlug(object $request, ?string $slug, ?int $id = null): ?object
    {
        return $this->model->whereParentId($request->parent_id)->whereWebsiteId($request->website_id)->whereHas("values", function ($query) use ($slug, $request, $id) {
            if($id) $query = $query->where('category_id', '!=', $id);
            $query->whereAttribute("slug")->whereValue($slug);
        })->first();
    }
}
