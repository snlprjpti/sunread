<?php

namespace Modules\Inventory\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Transformers\ProductResource;
use Modules\Core\Transformers\WebsiteResource;

class CatalogInventoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "product" => new ProductResource($this->whenLoaded("product")),
            "website" => new WebsiteResource($this->whenLoaded("website")),
            "catalog_inventory_items" => CatalogInventoryResource::collection($this->whenLoaded("catalog_inventory_items")),
            "quantity" => $this->quantity,
            "manage_stock" => (bool) $this->manage_stock,
            "is_in_stock" => (bool) $this->is_in_stock,
            "use_config_manage_stock" => (bool) $this->use_config_manage_stock,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
