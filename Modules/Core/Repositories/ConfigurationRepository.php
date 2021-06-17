<?php

namespace Modules\Core\Repositories;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Configuration;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Repositories\BaseRepository;
use Modules\Core\Rules\ConfigurationRule;
use Modules\Core\Traits\Configuration as TraitsConfiguration;

class ConfigurationRepository extends BaseRepository
{
    protected $config_fields;
    protected $channel_model, $store_model;
    use TraitsConfiguration;

    public function __construct(Configuration $configuration, Channel $channel_model, Store $store_model)
    {
        $this->model = $configuration;
        $this->model_key = "core.configuration";
        $this->rules = [
            "scope" => [ "sometimes", "in:global,website,channel,store" ]
        ];
        $this->config_fields = ($data = Cache::get("configurations.all")) ? $data : config("configuration");
        $this->createModel();
        
        $this->channel_model = $channel_model;
        $this->store_model = $store_model;
    }

    public function getConfigData(object $request): array
    {
        try
        {
            $this->validateData($request, $this->scopeValidation($request));
    
            $fetched = $this->config_fields;
            $checkKey = [ "scope" => $request->scope ?? "global", "scope_id" => $request->scope_id ?? 0 ];

            foreach($fetched as $key => $data)
            {
                if(!isset($data["children"])) continue;
                foreach($data["children"] as $i => $children)
                {
                    if(!isset($children["subChildren"])) continue;
                    foreach($children["subChildren"] as $j => &$subchildren)
                    {
                        if(!isset($subchildren["elements"])) continue;
                        foreach($subchildren["elements"] as $k => &$element)
                        {
                            if($this->scopeFilter($checkKey["scope"], $element["scope"]))
                            {
                                unset($subchildren["elements"][$k]);
                                continue;
                            }
                            $checkKey["path"] = $element["path"];
                            $checkKey["provider"] = $element["provider"];

                            $existData = $this->has((object) $checkKey);
                            if($request->scope != "global") $element["use_default_value"] = $existData ? 0 : 1;
                            $element["default"] = $existData ? $this->getValues((object) $checkKey) : $this->getDefaultValues((object)$checkKey, $element["default"]);

                            if( $element["provider"] !== "") $element["options"] = $this->cacheQuery((object) $checkKey, $element["pluck"]);
                            $element["absolute_path"] = $key.".children.".$i.".subChildren.".$j.".elements.".$k;
                            
                            unset($element["pluck"], $element["provider"], $element["rules"], $element["showIn"]);
                            $subchildren["elements"][$k] = $element;
                        }
                        $children["subChildren"][$j] = $subchildren;
                    }
                    $data["slug"] = Str::slug($data["title"]);
                    $data["children"][$i] = $children;
                    $data["children"][$i]["absolute_path"] = "{$key}.children.{$i}.subChildren";
                    $data["children"][$i]["slug"] = Str::slug($children["title"]);
                }
                $fetched[$key] = $data;
            }
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
        
        return $fetched;
    }

    public function scopeFilter(string $scope, string $element_scope): bool
    {
        if($scope == "website" && in_array($element_scope, ["global"])) return true;
        if($scope == "channel" && in_array($element_scope, ["global", "website"])) return true;
        if($scope == "store" && in_array($element_scope, ["global", "website", "channel"])) return true;
        return false;
    }

    public function scopeValidation(object $request)
    {
        return ((isset($request->scope) && $request->scope != "global") || isset($request->scope_id)) ? [
            "scope_id" => ["required", "integer", "min:0", new ConfigurationRule($request->scope)]
        ] : [];
    }

    public function add(object $request): object
    {
        $item['scope'] = $request->scope;
        $item['scope_id'] = $request->scope_id;
        foreach($request->items as $key => $val)
        {
            if(!isset($val["absolute_path"])) throw ValidationException::withMessages([ "absolute_path" => __("core::app.response.absolute_path_missing", ["name" => $key]) ]);
            if(!array_key_exists("value", $val)) throw ValidationException::withMessages([ "value" => __("core::app.response.value_missing", ["name" => $key]) ]);

            $configDataArray = config("configuration.{$val["absolute_path"]}");
            if(!$configDataArray) throw ValidationException::withMessages([ "absolute_path" =>  __("core::app.response.absolute_path_not_exist", ["name" => $key]) ]);

            if($configDataArray["path"] != $key) throw ValidationException::withMessages([ "absolute_path" =>  __("core::app.response.wrong_absolute_path", ["name" => $key])]);

            if($this->scopeFilter($item['scope'], $configDataArray["scope"])) continue;
            
            $item['path'] = $key;
            $item['value'] = $val['value'];
            
            if($configData = $this->checkCondition((object) $item)->first())
            {
                if(isset($val['use_default_value'])  && $val['use_default_value'] == 1) $configData->delete();
                else $created_data['data'][] = $this->update($item, $configData->id);
                continue;
            }
            $created_data['data'][] = $this->create($item);
        }
        $created_data['message'] = 'create-success';
        $created_data['code'] = 201;
        return (object) $created_data; 
    }

    public function getValues(object $request): mixed
    {
        return $this->checkCondition($request)->first()->value;
    }

    public function getDefaultValues(object $data, mixed $configValue=null): mixed
    {
        if($data->scope != "global")
        {
            $input["path"] = $data->path;
            switch($data->scope)
            {
                case "store":
                    $input["scope"] = "channel";
                    $input["scope_id"] = $this->store_model->find($data->scope_id)->channel->id;
                    break;
                
                case "channel":
                    $input["scope"] = "website";
                    $input["scope_id"] = $this->channel_model->find($data->scope_id)->website->id;
                    break;

                case "website":
                    $input["scope"] = "global";
                    $input["scope_id"] = 0;
                    break;
            }
            return ($item = $this->checkCondition((object) $input)->first()) ? $item->value : (( $input["scope"] == "global") ? $configValue : $this->getDefaultValues((object)$input, $configValue));           
        }
        return $configValue;
    }
}
