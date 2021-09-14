<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisClear extends Command
{
    protected $signature = 'redis:clear';

    protected $description = 'This command will clear all redis cache';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): bool
    {
        Redis::del(Redis::keys("*"));
        return true;
    }
}
