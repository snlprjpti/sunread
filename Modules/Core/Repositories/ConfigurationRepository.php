<?php

namespace Modules\Core\Repositories;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Configuration;
use Modules\Core\Repositories\BaseRepository;
use Modules\Core\Rules\ConfigurationRule;
use Modules\Core\Traits\Configuration as TraitsConfiguration;

class ConfigurationRepository extends BaseRepository
{
    protected $config_fields;
    use TraitsConfiguration;

    public function __construct(Configuration $configuration)
    {
        $this->model = $configuration;
        $this->model_key = "core.configuration";
        $this->rules = [];
        $this->config_fields = ($data = Cache::get("configurations.all")) ? $data : config("configuration");
        $this->createModel();
    }

    public function getConfigData(object $request): array
    {
        $fetched = $this->config_fields;
        $checkKey = [ 'scope' => ($request->scope) ? $request->scope : 'default', 'scope_id' => ($request->scope_id) ? $request->scope_id : 0 ];
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
                        if($request->scope && !in_array($request->scope, $element['showIn']))
                        {
                            unset($subchildren['elements'][$k]);
                            continue;
                        }
                        $checkKey["path"] = $element['path'];
                        $checkKey["provider"] = $element['provider'];

                        $element['default'] = $this->has((object) $checkKey) ? $this->getDefaultValues((object) $checkKey) : $element['default'];
                        if( $element['provider'] !== "") $element['options'] = $this->cacheQuery((object) $checkKey, $element['pluck']);
                        // $element['absolute_path'] = $key.'.children.'.$i.'.subChildren.'.$j.'.elements.'.$k;
                        
                        unset($element['pluck'], $element['provider'], $element['rules'], $element['showIn']);
                        $subchildren['elements'][$k] = $element;
                    }
                    $children['subChildren'][$j] = $subchildren;
                }
                $data["slug"] = Str::slug($data["title"]);
                $data['children'][$i] = $children;
                $data['children'][$i]['absolute_path'] =  $key.'.children.'.$i.'.subChildren.';
                $data["children"][$i]["slug"] = Str::slug($children["title"]);
            }
            $fetched[$key] = $data;
        }
        return $fetched;
    }

    public function scopeValidation(object $request)
    {
        return (isset($request->scope) && isset($request->scope_id)) ? [
            "scope" => [ "required", "in:default,website,channel,store" ],
            "scope_id" => ["required", "integer", "min:0", new ConfigurationRule($request->scope)]
        ] : [];
    }
}
