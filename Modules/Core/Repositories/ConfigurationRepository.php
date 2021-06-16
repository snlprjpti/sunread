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
use Illuminate\Http\Request;

class ConfigurationRepository extends BaseRepository
{
    protected $config_fields, $created_data, $use_default_value, $request_scope = [];
    protected $website_model, $channel_model, $store_model;
    use TraitsConfiguration;

    public function __construct(Configuration $configuration, Website $website_model, Channel $channel_model, Store $store_model)
    {
        $this->model = $configuration;
        $this->model_key = "core.configuration";
        $this->rules = [
            "scope" => [ "sometimes", "in:global,website,channel,store" ]
        ];
        $this->config_fields = ($data = Cache::get("configurations.all")) ? $data : config("configuration");
        $this->createModel();

        $this->website_model = $website_model;
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
                            if($request->scope && !in_array($request->scope, $element["showIn"]))
                            {
                                unset($subchildren["elements"][$k]);
                                continue;
                            }
                            $checkKey["path"] = $element["path"];
                            $checkKey["provider"] = $element["provider"];
    
                            $element["default"] = $this->has((object) $checkKey) ? $this->getDefaultValues((object) $checkKey) : $element["default"];
                            if( $element["provider"] !== "") $element["options"] = $this->cacheQuery((object) $checkKey, $element["pluck"]);
                           // $element["absolute_path"] = $key.".children.".$i.".subChildren.".$j.".elements.".$k;
                            
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

    public function scopeValidation(object $request)
    {
        return ((isset($request->scope) && $request->scope != "global") || isset($request->scope_id)) ? [
            "scope_id" => ["required", "integer", "min:0", new ConfigurationRule($request->scope)]
        ] : [];
    }

    public function add(object $request): object
    {
        $this->request_scope["scope"] = $request->scope;
        $this->request_scope["scope_id"] = $request->scope_id;

        foreach($request->items as $key => $val)
        {
            if(!isset($val['scope']) || !in_array($val['scope'], [ 'global', 'website', 'channel', 'store' ]) )
            throw ValidationException::withMessages(["Scope of $key doesnt exists"]);
        
            $item['path'] = $key;
            $item['value'] = $val['value'];
            $item['config_scope'] = $val['scope'];
            $this->use_default_value = $val['use_default_value'] ?? 1;

            $this->scopeWiseOperations($item);
        }

        $this->created_data['message'] = 'create-success';
        $this->created_data['code'] = 201;
        return (object) $this->created_data; 
    }

    public function scopeWiseOperations(array $item): void
    {
        switch($item['config_scope'])
        {
            case "global":
                $this->globalScope($item);
                break;

            case "website":
                if($this->request_scope["scope"] == "global") $this->independentItem($item);
                else{
                    if($this->request_scope["scope"] == "website") $website = $this->website_model->find($this->request_scope["scope_id"]);
                    if($this->request_scope["scope"] == "channel") $website = $this->channel_model->find($this->request_scope["scope_id"])->website;
                    if($this->request_scope["scope"] == "store") $website = $this->store_model->find($this->request_scope["scope_id"])->channel->website;

                    if($website) $this->websiteScope($item, $website);
                }
                break;

            case "channel":
                if($this->request_scope["scope"] == "global" || $this->request_scope["scope"] == "website") $this->independentItem($item);
                else{
                    if($this->request_scope["scope"] == "channel") $channel = $this->channel_model->find($this->request_scope["scope_id"]);
                    if($this->request_scope["scope"] == "store") $channel = $this->store_model->find($this->request_scope["scope_id"])->channel;

                    if($channel) $this->channelScope($item, $channel);
                }
                break;

            case "store":
                $this->independentItem($item);
        }
    }

    public function independentItem(array $item, string $scope=null, int $scope_id=null): void
    {
        $item['scope'] = $scope ?? $this->request_scope["scope"];
        $item['scope_id'] = $scope_id ?? $this->request_scope["scope_id"];
        $this->created_data['data'][] = $this->createORUpdate($item);
    }

    public function globalScope(array $item): void
    {
        $item["scope"] = "global";
        $item["scope_id"] = 0;
        $this->created_data['data'][] = $this->createORUpdate($item);
        foreach($this->website_model->get() as $website)
        {
            $this->websiteScope($item, $website); 
        }
    }

    public function websiteScope(array $item, object $website): void
    {
        $item["scope"] = "website";
        $item["scope_id"] = $website->id;
        $this->created_data['data'][] = $this->createORUpdate($item);
        if($website->channels) foreach($website->channels as $channel) $this->channelScope($item, $channel); 
    }

    public function channelScope(array $item, object $channel): void
    {
        $item["scope"] = "channel";
        $item["scope_id"] = $channel->id;
        $this->created_data['data'][] = $this->createORUpdate($item);
        if($channel->stores) foreach($channel->stores as $store) $this->independentItem($item, 'store', $store->id); 
    }

    public function createORUpdate(array $item): object
    {
        if($this->use_default_value == 0 && $item["scope"] == $this->request_scope["scope"]  && strval($item["scope_id"]) == $this->request_scope["scope_id"]) $item["use_default_value"] = $this->use_default_value;

        if($configData = $this->checkCondition((object) $item)->first())
        {
            $configData->update($item);
            return $this->configuration->findorfail($configData->id);
        }
        return $this->configuration->create($item);
    }

    public function getDefaultValues(object $request): mixed
    {
        $data = $this->checkCondition($request)->first();
        if($data && $data->scope != "global" && $data->use_default_value == 0)
        {
            $input["path"] = $request->path;
            switch($data->scope)
            {
                case "store":
                    $input['scope'] = "channel";
                    $input['scope_id'] = $this->store_model->find($data->scope_id)->channel->id;
                    break;
                
                case "channel":
                    $input['scope'] = "website";
                    $input['scope_id'] = $this->channel_model->find($data->scope_id)->website->id;
                    break;

                case "website":
                    $input['scope'] = "global";
                    $input['scope_id'] = 0;
                    break;
            }
            return $this->getDefaultValues((object) $input);
            
        }
        return  ($data) ? $data->value : "";
    }
}
