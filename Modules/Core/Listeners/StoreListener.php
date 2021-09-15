<?php

namespace Modules\Core\Listeners;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Store;
use Modules\Core\Facades\CoreCache;
use Modules\Product\Entities\Product;
use Modules\Product\Jobs\SingleIndexing;

class StoreListener
{
   public function create($store)
   {
       CoreCache::createStoreCache($store);

       //indexing products in elasticsearch for new store
       $website = $store?->channel?->website;
       $products = Product::whereWebsiteId($website->id)->get();
       $batch = Bus::batch([])->dispatch();
       foreach($products as $product) $batch->add(new SingleIndexing($product, $store));
   }

   public function beforeUpdate($store)
   {
       CoreCache::updateBeforeStoreCache($store);
   }

   public function update($store)
   {
       CoreCache::createStoreCache($store);
   }

   public function delete($store)
   {
       CoreCache::deleteStoreCache($store);
   }
}
