<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Entities\Configuration;
use Illuminate\Validation\ValidationException;
use Modules\Core\Traits\Configuration as TraitsConfiguration;

class ConfigurationHelper
{
    use TraitsConfiguration;

    protected object $model;
    protected array $config_fields;

    public function __construct(Configuration $configuration)
    {
        $this->model = $configuration;
        $this->config_fields = ($data = Cache::get("configurations.all")) ? $data : config("configuration");
    }

    public function fetch(string $absolute_path, string $scope = "global", int $scope_id = 0): mixed
    {
        return $this->getSinglePathValue($absolute_path, $scope, $scope_id);
    }

    public function getSinglePathValue(string $path, string $scope = "global", int $scope_id): string
    {
        try
        {
            $elements = collect($this->config_fields)
                ->pluck("children")->flatten(1)
                ->pluck("subChildren")->flatten(1)
                ->pluck("elements")->flatten(1);

                $element = $elements->where("path", $path)->first();
            if(!$element) return "Invalid path";

            $data = (object) [
                "scope" => $scope,
                "scope_id" => $scope_id,
                "path" => $element->path
            ];

            $fetched = ($this->has((object) $data)) ? $this->getValues($data) : $this->getDefaultValues($data, $element["default"]);
            dd($fetched);
        }
        catch( Exception $exception )
        {
            throw $exception;
        }

        return true;
    }
}
