<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Entities\CatalogInventoryItem;
use Modules\Core\Traits\HasFactory;
use Modules\Product\Entities\Product;
use Modules\Core\Entities\Website;

class CatalogInventory extends Model
{
    use HasFactory;

    protected $fillable = ["product_id", "website_id", "quantity", "is_in_stock", "manage_stock", "use_config_manage_stock"];

    public function catalog_inventory_items()
    {
        return $this->belongsToMany(CatalogInventoryItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class); 
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
