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
        if(Redis::exists("configuration_data_{$request->scope}_{$request->scope_id}_{$request->path}")) {
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

    public function cacheQuery(array $element, object $request, array $scope): array
    {
        $resources = Cache::rememberForever($element["provider"], function() use ($element, $scope) {
            $provider = $element["provider"];
            $pluck = $element["pluck"];

            $model = new $provider();
            $model = $model->select("{$pluck[1]} AS value", "{$pluck[0]} AS label");

            if($element["path"] == "website_default_channel") $model->where("website_id", $scope["scope_id"]);

            if(isset($element["sort_by"]) && $element["sort_by"] != "") $model = $model->orderBy($element["sort_by"], "asc");
            return $model->get();
        });

        return $resources->toArray();
    }
}
