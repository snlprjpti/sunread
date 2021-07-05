<?php

namespace Modules\Country\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Country\Entities\Country;

class CountryRepository extends BaseRepository
{
    public function __construct(Country $country)
    {
        $this->model = $country;
        $this->model_key = "country";
    }
}
