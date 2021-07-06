<?php

namespace Modules\Page\Traits;

use Illuminate\Http\Request;
use Modules\Page\Entities\PageConfiguration;

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
        $model = new $this->configModels[0]();
        $relations = $model::where([
            ['scope', $params["scope"] ?? null],
            ['scope_id', $params["scope_id"] ?? null]
        ])->first();
        return $relations;
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
