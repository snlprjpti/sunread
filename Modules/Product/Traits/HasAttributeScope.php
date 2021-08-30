<?php

namespace Modules\Product\Traits;

use Exception;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Store;

trait HasAttributeScope
{
    protected $non_filterable_fields = [ "select", "multiselect", "checkbox" ];

    public function value(array $data): mixed
    {
        $existAttributeData = $this->product_attributes()->where($data)->first();
        $default = $existAttributeData ? $existAttributeData->value?->value : $this->getDefaultValues($data);
        return is_json($default) ? json_decode($default) : $default;
    }

    public function getDefaultValues(array $data): mixed
    {
        $attribute = Attribute::findorFail($data["attribute_id"]);
        if(in_array($attribute->type, $this->non_filterable_fields)) $attributeOptions = AttributeOption::whereAttributeId($attribute->id)->first();
        $defaultValue = isset($attributeOptions) ? $attributeOptions->id : $attribute->default_value;

        if($data["scope"] != "website")
        {
            $parent_scope = $this->getParentScope($data);
            $data["scope"] = $parent_scope["scope"];
            $data["scope_id"] = $parent_scope["scope_id"];       
        }
        $item = $this->product_attributes()->where($data)->first();
        return $item ? $item->value?->value : (($data["scope"] != "website") ? $this->getDefaultValues($data) : $defaultValue);    
    }

    public function getParentScope(array $scope): array
    {
        try
        {
            switch($scope["scope"])
            {
                case "store":
                    $data["scope"] = "channel";
                    $data["scope_id"] = Store::find($scope["scope_id"])->channel->id;
                    break;
                    
                case "channel":
                    $data["scope"] = "website";
                    $data["scope_id"] = Channel::find($scope["scope_id"])->website->id;
                    break;
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }
}