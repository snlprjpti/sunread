<?php

namespace Modules\Page\Traits;

trait HasPageConfiguration
{
    public function getAttribute($name)
    {
        $params = $this->getURL();
        if($params)
        {
            $translation = $this->getConfigData($params);
            if($translation)
            {
                array_map(function($attribute) use($translation) {
                    parent::setAttribute($attribute, $translation->$attribute);
                    return $this->$attribute = $translation->$attribute;
                }, $this->configAttributes);
            }
        }
        return parent::getAttribute($name);
    }

    public function getConfigData($params)
    {
        $result = $this->checkCondition($params);

        if (!$result)
        {
            $configValue = (config('page.model_config'));
            foreach($configValue as $key => $value)
            {
                $scopeId = isset($data["scope_id"]) ? $data["scope_id"] : $params["scope_id"] ?? null;
                $params["scope"] = isset($data["scope"]) ? $data["scope"] : $params["scope"] ?? null;
                $relation = $value["parent"];
                if($relation != null && $params["scope"] == $value["scope"] && $scopeId != null)
                {
                    $data["scope_id"] = (app($value["scope"])->find($scopeId)->$relation->id) ?? null;
                    $data["scope"] = $value["parent_scope"];
                    $result = $this->checkCondition($data);
                    if(isset($result)) break;
                }
            }
        }

        return $result;
    }

    public function checkCondition($params)
    {
        $model = new $this->configModels[0]();
        $result = $model::where([
            ['scope', $params["scope"] ?? null],
            ['scope_id', $params["scope_id"] ?? null]
        ])->first();

        return $result;
    }

    public function getURL()
    {
        $url = url()->full();
        $url_components = parse_url($url);
        if(isset($url_components['query'])){
            parse_str($url_components['query'], $params);
            return $params;
        }

        return false;
    }
}