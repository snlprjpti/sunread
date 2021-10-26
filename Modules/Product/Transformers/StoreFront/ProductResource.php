<?php

namespace Modules\Product\Transformers\StoreFront;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Facades\CoreCache;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $store = CoreCache::getStoreWithCode($request->header("hc-store"));
        $scope = [
            "scope" => "store",
            "scope_id" => $store->id
        ];

        return [
            "id" => $this->id,
            "name" => $this->value(array_merge($scope, [ "attribute_slug" => "name" ])),
            "price" => $this->value(array_merge($scope, [ "attribute_slug" => "price" ])),
            "type" => $this->type,
            "sku" => $this->sku,           
            "status" => (bool) $this->status,
        ];
    }
}
