<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Configuration as EntitiesConfiguration;
use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node\Expr\Cast\Object_;

trait Configuration
{
    public $configuration;

    public function createModel()
    {
        $this->configuration = new EntitiesConfiguration();
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
    
    protected static function boot()
    {
        parent::boot();

        static::updated(function () {
            self::flushCache();
        });
        static::created(function () {
            self::flushCache();
        });
    }

    public static function flushCache()
    {
        Cache::forget();
    }

    public function getDefaultValues(object $request): string
    {
        return $this->checkCondition($request)->first()->value;
    }

    public static function getValues(object $request, string $pluck): array
    {
        return $request->path::pluck($pluck)->toArray();
    }

    public function cacheQuery(object $request, string $pluck, $timeout = 60): array
    {
        if($data = Cache::get($request->scope.$request->scope_id.$request->path)) return $data;

        return Cache::remember($request->scope.$request->scope_id.$request->path, $timeout, function() use ($request, $pluck) {
            return $this->getValues($request, $pluck);
        });
    }
}
