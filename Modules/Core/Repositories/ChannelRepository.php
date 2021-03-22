<?php


namespace Modules\Core\Repositories;


use Modules\Core\Eloquent\Repository;
use Modules\Core\Entities\Channel;

class ChannelRepository extends Repository
{

    public function model()
    {
         return Channel::class;
    }
}
