<?php

namespace Modules\Product\Jobs;

use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Product\Traits\ElasticSearch\ConfigurableProductHandler;

class ConfigurableIndexing implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ConfigurableProductHandler;

    public $parent, $store;

    public function __construct(object $parent, object $store)
    {
        $this->parent = $parent;
        $this->store = $store;
    }

    public function handle(): void
    {
        try
        {
            $this->createProduct($this->parent, $this->store);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
