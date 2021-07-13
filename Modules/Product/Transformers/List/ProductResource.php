<?php

namespace Modules\Product\Transformers\List;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Transformers\AttributeGroupResource;
use Modules\Brand\Transformers\BrandResource;
use Modules\Category\Transformers\CategoryResource;
use Modules\Core\Transformers\WebsiteResource;
use Modules\Inventory\Transformers\CatalogInventoryResource;
use Modules\Product\Transformers\ProductImageResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $attributeIdforName = Attribute::whereSlug("name")->first()->id;
        $attributeIdforVisibility = Attribute::whereSlug("visibility")->first()->id;
        $product_attributes = $this->product_attributes()->whereScope($request->scope ?? "website")->whereScopeId($request->scope_id ?? $this->website_id);
        $name = $product_attributes->whereAttributeId($attributeIdforName)->first();
        $visibility = $product_attributes->whereAttributeId($attributeIdforVisibility)->first();
        $images = $this->images()->where('main_image', 1)->first();
        $stock = $this->catalog_inventories()->first();

        return [
            "id" => $this->id,
            "name" => $name ? $name->value->value : null,
            "sku" => $this->sku,           
            "stock" => $stock ? $stock->quantity : null,
            "status" => (bool) $this->status,
            "categories" => CategoryResource::collection($this->whenLoaded("categories")),
            "visibility" => $visibility ? $visibility->value->value : null,
            "images" => $images ? $images->path : null,
            "created_at" => $this->created_at->format("M d, Y H:i A")
        ];
    }
}
