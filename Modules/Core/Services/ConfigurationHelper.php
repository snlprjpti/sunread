<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Configuration;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;
use Modules\Core\Traits\Configuration as TraitsConfiguration;

class ConfigurationHelper
{
    use TraitsConfiguration;

    protected object $model, $channel_model, $store_model;
    protected array $config_fields;

    public function __construct(Configuration $configuration)
    {
        $this->model = $configuration;
        $this->config_fields = ($data = Cache::get("configurations.all")) ? $data : config("configuration");
        $this->channel_model = new Channel();
        $this->store_model = new Store();
        $this->createModel();
    }

    public function fetch(string $path, string $scope = "global", int $scope_id = 0): mixed
    {
        try
        {
            $element = $this->getElement($path);

            $data = (object) [
                "scope" => $scope,
                "scope_id" => $scope_id,
                "path" => $path
            ];

            $values = $this->has($data) ? $this->getValues($data) : $this->getDefaultValues($data, $element["default"]);
            $fetched = $this->getProviderData($element, $values);
            if($fetched && $element["type"] == "file") $fetched = Storage::url($fetched);
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $fetched;
    }

    public function getElement(string $path): array
    {
        try
        {
            $elements = collect($this->config_fields)
                ->pluck("children")->flatten(1)
                ->pluck("subChildren")->flatten(1)
                ->pluck("elements")->flatten(1);

            $element = $elements->where("path", $path)->first();
            if(!$element) return throw ValidationException::withMessages(["path" => "Invalid path for configuration."]);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $element;
    }

    public function getValues(object $request): mixed
    {
        try
        {
            $value = $this->model->where([
                ["scope", $request->scope],
                ["scope_id", $request->scope_id],
                ["path", $request->path]
            ])->first()?->value;
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    
        return $value;
    }

    public function getDefaultValues(object $data, mixed $configValue = null): mixed
    {
        try
        {
            if($data->scope != "global") {
                $input = ["path" => $data->path];

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

                if ( $item = $this->checkCondition((object) $input)->first() ) {
                    $configValue = $item->value;
                } else {
                    $configValue = $input["scope"] == "global" ? $configValue : $this->getDefaultValues((object)$input, $configValue);
                }
            }
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $configValue;
    }

    public function getProviderData(array $element, mixed $values): mixed
    {
        try
        {
            $values = $values == "" ? null : $values;
            $fetched = $values;

            if ( class_exists($element["provider"]) && $values !== null ) {
                $model = new $element["provider"];
                $fetched = is_array($values)
                    ? $model->whereIn("id", $values)->get()
                    : $model->find($values);
                if ( !$fetched ) throw ValidationException::withMessages(["path" => "Invalid value for configuration."]);
            }
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return $fetched;   
    }
}
