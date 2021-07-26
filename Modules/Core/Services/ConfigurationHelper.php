<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Configuration;

class ConfigurationHelper
{
    protected $model;

    public function __construct(Configuration $configuration)
    {
        $this->model = $configuration;
    }

    public function fetch(string $absolute_path, string $scope = "global", int $scope_id = 0): mixed
    {
        return [
            "path" => $absolute_path,
            "scope" => $scope,
            "scope_id" => $scope_id
        ];
    }
}
