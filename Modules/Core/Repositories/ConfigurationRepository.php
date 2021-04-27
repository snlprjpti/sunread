<?php

namespace Modules\Core\Repositories;

use Modules\Core\Entities\Configuration;
use Modules\Core\Repositories\BaseRepository;

class ConfigurationRepository extends BaseRepository
{
    public function __construct(Configuration $configuration)
    {
        $this->model = $configuration;
        $this->model_key = "core.configuration";
        $this->rules = [
           /* General */
            "scope" => "required",
            "scope_id" => "required|integer|min:0",
            "path" => "required",
            "value" => "nullable"
        ];
    }
}
