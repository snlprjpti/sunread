<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Core\Repositories\ChannelRepository;


class Core extends Model
{
    protected $channelRepository;
    protected $fillable = [];

}
