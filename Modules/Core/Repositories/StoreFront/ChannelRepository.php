<?php

namespace Modules\Core\Repositories\StoreFront;

use Exception;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;
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
            $website = Website::whereHostname($request->header("hc-host"))->firstOrFail();
            $channels = $this->model->whereWebsiteId($website->id)->get();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $channels;     
    }
}

