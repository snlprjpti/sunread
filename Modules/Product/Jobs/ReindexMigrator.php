<?php

namespace Modules\Product\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Bus;
use Modules\Product\Entities\Product;

class ReindexMigrator implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $chunk_products = Product::with(["variants", "categories", "product_attributes", "catalog_inventories", "attribute_options_child_products"])->whereParentId(null)->get()->chunk(50);
        
        foreach ($chunk_products as $products)
        {
            $batch = Bus::batch([])->onQueue("index")->dispatch();
            foreach ($products as $product)
            {
                $batch->add(new ReIndexer($product));
            }
        }
    }
}
