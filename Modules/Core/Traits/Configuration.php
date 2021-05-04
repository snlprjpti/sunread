<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Configuration as EntitiesConfiguration;

trait Configuration
{
    public $configuration;

    public function createModel()
    {
        $this->configuration = new EntitiesConfiguration();
    }

    public function getAllConfigurations()
    {
        return Cache::remember("configurations.all", 60, function(){
            return config("configuration");
        }); 
    }

    public function has(object $request)
    {
        return (boolean) $this->checkCondition($request)->count();
    }

    public function checkCondition(object $request): object
    {
        return $this->configuration->where([
            ['scope', $request->scope],
            ['scope_id', $request->scope_id],
            ['path', $request->path]
        ]);  
    }

    public function add(object $request): object
    {
        $item['scope'] = $request->scope;
        $item['scope_id'] = $request->scope_id;
        foreach($request->items as $key => $val)
        {
            $item['path'] = $key;
            $item['value'] = $val;
            if($configData = $this->checkCondition((object) $item)->first())
            {
                $configData->update($item);
                $created_data['data'][] = $this->configuration->findorfail($configData->id);
                continue;
            }
            $created_data['data'][] = $this->configuration->create($item);
        }
        $created_data['message'] = 'create-success';
        $created_data['code'] = 201;
        return (object) $created_data; 
    }

    public function getDefaultValues(object $request): mixed
    {
        return $this->checkCondition($request)->first()->value;
    }

    public function cacheQuery(object $request, array $pluck): array
    {
        $resources = Cache::rememberForever($request->provider, function() use ($request) {
           return $request->provider::get();
        });

       return $resources->pluck(isset($pluck[0]) ? $pluck[0] : "id", isset($pluck[1]) ? $pluck[1] : "id")->toArray();
    }

    public function getValidationRules($absolute_path): array
    {
        return collect(config('configuration.'.$absolute_path))->pluck('elements')->flatten(1)->pluck('rules','path')->mapWithKeys(function($val, $key) {
           return ['items.'.$key => $val];
        })->toArray();
    }
}
