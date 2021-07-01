<?php

namespace Modules\Country\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Country\Entities\Region;

class RegionRepository extends BaseRepository
{
    public function __construct(Region $region)
    {
        $this->model = $region;
        $this->model_key = "region";
    }
}
