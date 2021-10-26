<?php

namespace Modules\Product\Transformers\StoreFront;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Facades\CoreCache;
use Modules\Core\Facades\PriceFormat;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $store = CoreCache::getStoreWithCode($request->header("hc-store"));
        $scope = [
            "scope" => "store",
            "scope_id" => $store->id
        ];
        $price = $this->value(array_merge($scope, [ "attribute_slug" => "price" ]));

        return [
            "id" => $this->id,
            "name" => $this->value(array_merge($scope, [ "attribute_slug" => "name" ])),
            "price" => PriceFormat::get($price, $store->id, "store"),
            "url_key" => $this->value(array_merge($scope, [ "attribute_slug" => "url_key" ])),
            "type" => $this->type,
            "sku" => $this->sku,           
            "status" => (bool) $this->status,
        ];
    }
}
