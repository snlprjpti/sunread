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

    public function __construct($configurationData)
    {
        $this->configurationData = $configurationData;
    }

   
    public function handle(): void
    {
        try
        {
            Redis::set("get-all-configuration-data", $this->configurationData);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
