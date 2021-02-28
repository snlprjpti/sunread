<?php

namespace Modules\Core\Repositories;

use Modules\Core\Eloquent\Repository;

use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Channel;


class ChannelRepository extends Repository
{

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return Channel::class;
    }

}
