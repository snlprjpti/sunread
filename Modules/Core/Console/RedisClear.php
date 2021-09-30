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
        if(count(Redis::keys("*")) > 0) {
            Redis::del(Redis::keys("*"));
            $this->info("Redis cache cleared");
        }
        else {
            $this->info("No cache data found");
        }
        return true;
    }
}
