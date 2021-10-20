<?php

namespace Modules\Product\Jobs;

use Elasticsearch\ClientBuilder;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeOption;
use Modules\Product\Traits\ElasticSearch\HasIndexing;

class SingleIndexing implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasIndexing;

    public $product, $store, $method;

    public function __construct(object $product, object $store, ?string $method = null)
    {
        $this->product = $product;
        $this->store = $store;
        $this->method = $method;
    }

    public function handle(): void
    {
        try
        {
            if(!$this->method) {
                $is_visibility = $this->product->value([
                    "scope" => "store",
                    "scope_id" => $this->store->id,
                    "attribute_slug" => "visibility"
                ]);
                
                // if($is_visibility?->name != "Not Visible Individually") $this->singleIndexing($this->product, $this->store);
                // else $this->removeIndex(collect($this->product), $this->store);
            }
            // else $this->removeIndex($this->product, $this->store);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
