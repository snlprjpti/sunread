<?php

namespace Modules\Core\Repositories;

use Modules\Core\Eloquent\Repository;

class ExchangeRateRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return  Excha;
    }
}
