<?php

namespace Modules\Core\Repositories;

use Modules\Core\Entities\Website;
use Modules\Core\Traits\WebsiteResolveable;
use Modules\Core\Repositories\BaseRepository;

class ResolveRepository extends BaseRepository
{
    use WebsiteResolveable;

    public function __construct(Website $website)
    {
        $this->model = $website;
        $this->model_name = "Website";
        $this->model_key = "core.website";
    }
}
