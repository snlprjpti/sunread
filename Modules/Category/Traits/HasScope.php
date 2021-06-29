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
        return $this->checkCondition($data) ? $this->getValues($data) : $this->getDefaultValues($data);
    }

    public function getValues(array $data): object
    {
        return $this->value_model->whereScope($data["scope"])->whereScopeId($data["scope_id"])->whereCategoryId($data["category_id"])->first();
    }

    public function scopeFilter(string $scope, string $element_scope): bool
    {
        if($scope == "channel" && in_array($element_scope, ["website"])) return true;
        if($scope == "store" && in_array($element_scope, ["website", "channel"])) return true;
        return false;
    }

    public function checkCondition(array $data)
    {
        return (bool) $this->value_model->whereCategoryId($data["category_id"])->whereScope($data["scope"])->whereScopeId($data["scope_id"])->count();
    }
}
