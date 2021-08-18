<?php

namespace Modules\Product\Jobs;

use Elasticsearch\ClientBuilder;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Product\Traits\ElasticSearch\HasIndexing;

class SingleIndexing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasIndexing;

    public $products, $stores, $model, $method;

    public function __construct(object $products, object $stores, string $model, ?string $method = null)
    {
        $this->products = $products;
        $this->stores = $stores;
        $this->model = $model;
        $this->method = $method;
    }

    public function handle(): void
    {
        try
        {
            if ($this->model == "product") {
                foreach($this->stores as $store)
                {
                    if(!$this->method) $this->singleIndexing($this->products, $store);
                    else $this->removeIndex($this->products, $store);
                }
            }
    
            if ($this->model == "store") {
                foreach ($this->products as $product)
                {
                    $product->load("categories", "product_attributes", "catalog_inventories", "images");
                    if(!$this->method) $this->singleIndexing($product, $this->stores);  
                } 
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
