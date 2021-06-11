<?php

namespace Modules\Core\Repositories;

use Modules\Core\Entities\Website;
use Modules\Core\Repositories\BaseRepository;

class WebsiteRepository extends BaseRepository
{
    public function __construct(Website $website)
    {
        $this->model = $website;
        $this->model_key = "core.website";
        $this->relationships = ["channels.default_store", "channels.stores"];
        $this->rules = [
            /* General */
            "code" => "required|unique:websites,code",
            "hostname" => "nullable|unique:websites,hostname",
            "name" => "required",
            "description" => "nullable",
            "position" => "sometimes|numeric",
            "status" => "sometimes|boolean"
        ];
    }
}
