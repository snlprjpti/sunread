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

    public $parent;

    public function __construct(object $parent)
    {
        $this->parent = $parent;
    }

    public function handle(): void
    {
        try
        {
            // $visibility = Attribute::whereSlug("visibility")->first();
            // $visibility_option = AttributeOption::whereAttributeId($visibility?->id)->whereName("Not Visible Individually")->first();
            // $is_visibility = $this->parent->value([
            //     "scope" => "store",
            //     "scope_id" => $this->store->id,
            //     "attribute_id" => $visibility?->id
            // ]);
            
            // if($is_visibility != $visibility_option?->id) 
            $this->createProduct($this->parent);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
