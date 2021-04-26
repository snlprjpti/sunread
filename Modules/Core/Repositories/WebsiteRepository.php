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
        $this->rules = [
            /* General */
            "code" => "nullable|unique:websites,code",
            "hostname" => "nullable|unique:websites,hostname",
            "name" => "required",
            "description" => "nullable"
        ];
    }
}
