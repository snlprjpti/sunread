<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
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
        if(Redis::exists("configuration-data-{$request->scope}-{$request->scope_id}-{$request->path}")) {
            return (boolean) true;
        } else{
        return (boolean) $this->checkCondition($request)->count();
        }
    }

    public function checkCondition(object $request): object
    {   
        return $this->configuration->where([
            ['scope', $request->scope],
            ['scope_id', $request->scope_id],
            ['path', $request->path]
        ]);  
        
    }

    public function cacheQuery(object $request, array $pluck): array
    {
        $resources = Cache::rememberForever($request->provider, function() use ($request) {
           return $request->provider::orderBy("name", "asc")->get()->toArray();
        });
        $data = [];
        foreach($resources as $resource)
        {
            array_push($data, [
               'value' => $resource[$pluck[1]],
               'label' => $resource[$pluck[0]]
            ]);
        }
        return $data;
    }
}
