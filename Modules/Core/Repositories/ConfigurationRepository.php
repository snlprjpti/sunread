<?php

namespace Modules\Core\Repositories;

use Modules\Core\Entities\Configuration;
use Modules\Core\Repositories\BaseRepository;
use Modules\Core\Traits\Configuration as TraitsConfiguration;

class ConfigurationRepository extends BaseRepository
{
    protected $config_fields;
    use TraitsConfiguration;

    public function __construct(Configuration $configuration)
    {
        $this->model = $configuration;
        $this->model_key = "core.configuration";
        $this->rules = [
           /* General */
            "scope" => [ "required", "in:default,website,channel,store" ],
            "scope_id" => "required|integer|min:0",
            "path" => "required",
            "value" => "nullable"
        ];
        $this->config_fields = config("configuration");
        $this->createModel();
    }

    public function getConfigData(object $request): array
    {
        $fetched = $this->config_fields;
        $checkKey = [ 'scope' => $request->scope, 'scope_id' => $request->scope_id ];
        foreach($fetched as $key => $data)
        {
            if(!isset($data['children'])) continue;
            foreach($data['children'] as $i => $children)
            {
                if(!isset($children['subChildren'])) continue;
                foreach($children['subChildren'] as $j => &$subchildren)
                {
                    if(!isset($subchildren['elements'])) continue;
                    foreach($subchildren['elements'] as $k => &$element)
                    {
                        if(!in_array($request->scope, $element['showIn']))
                        {
                            unset($subchildren['elements'][$k]);
                            continue;
                        }
                        $checkKey["path"] = $element['path'];
                        $element['default'] = $this->has((object) $checkKey) ? $this->getDefaultValues((object) $checkKey) : "" ;
                        $element['value'] = $this->cacheQuery((object) $checkKey, $element['pluck']);
                        $element['absolute_path'] = $key.'.children.'.$i.'.subChildren.'.$j.'.elements.'.$k;
                        $subchildren['elements'][$k] = $element;
                        
                    }
                    $children['subChildren'][$j] = $subchildren;
                }
                $data['children'][$i] = $children;
            }
            $fetched[$key] = $data;
        }
        return $fetched;
    }
}
