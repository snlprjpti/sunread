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
        if($this->has($request))
        {
            return $this->set($request);
        }
        $created_data['data'] = $this->configuration->create([
            "path" => $request->path,
            "value" => $request->value,
            "scope" => $request->scope,
            "scope_id" => $request->scope_id
        ]);
        $created_data['message'] = 'create-success';
        $created_data['code'] = 201;
        return (object) $created_data; 
    }

    public function set(object $request): object
    {
        if($configData = $this->checkCondition($request)->first())
        {
            $configData->update([
                "path" => $request->path,
                "value" => $request->value,
                "scope" => $request->scope,
                "scope_id"=> $request->scope_id
            ]);
            $updated_data['data'] = $this->configuration->findorfail($configData->id);
            $updated_data['message'] = 'update-success';
            $updated_data['code'] = 200;
            return (object) $updated_data; 
        }
        return $this->add($request);
    }

    public function getDefaultValues(object $request): string
    {
        return $this->checkCondition($request)->first()->value;
    }

    public function cacheQuery(object $request, array $pluck): array
    {
        $resources = Cache::rememberForever($request->path, function() use ($request) {
           return $request->path::get();
        });

       return $resources->pluck(isset($pluck[0]) ? $pluck[0] : "id", isset($pluck[1]) ? $pluck[1] : "id")->toArray();
    }
}
