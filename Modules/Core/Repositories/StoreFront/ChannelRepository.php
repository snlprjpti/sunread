<?php

namespace Modules\Core\Repositories\StoreFront;

use Exception;
use Modules\Core\Entities\Channel;
use Modules\Core\Entities\Website;
use Modules\Core\Facades\CoreCache;
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
            $website = CoreCache::getWebsite($request->header("hc-host"));
            $channels = $this->model->with("default_store")->whereWebsiteId($website->id)->get();
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $channels;     
    }
}

