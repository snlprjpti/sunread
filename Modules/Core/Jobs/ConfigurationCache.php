<?php

namespace Modules\Core\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConfigurationCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   public $configurationData;

    public function __construct(array $configurationData)
    {
        $this->configurationData = $configurationData;
    }

   
    public function handle(): void
    {
        try
        {
            foreach($this->configurationData as $config){
            if(Redis::exists("configuration-data-{$config->scope}-{$config->scope_id}-{$config->path}")) {
                Redis::del("configuration-data-{$config->scope}-{$config->scope_id}-{$config->path}");
            }
            Redis::set("configuration-data-{$config->scope}-{$config->scope_id}-{$config->path}", serialize($config->value));
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
