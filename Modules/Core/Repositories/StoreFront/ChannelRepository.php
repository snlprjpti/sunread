<?php

namespace Modules\Core\Repositories\StoreFront;

use Exception;
use Modules\Core\Entities\Channel;
use Modules\Core\Facades\Resolver;
use Modules\Core\Repositories\BaseRepository;

class ChannelRepository extends BaseRepository
{
    protected $repository;

    public function __construct(Channel $channel)
    {
        $this->model = $channel;
    }

    public function getChannelList(object $request): object
    {
        try
        {
            $website = Resolver::fetch($request);
            $channels = $this->model->whereWebsiteId($website["id"])->get();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $channels;     
    }
}

