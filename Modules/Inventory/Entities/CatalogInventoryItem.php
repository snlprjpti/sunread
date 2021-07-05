<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Core\Traits\HasFactory;
use Modules\Product\Entities\Product;
use Modules\User\Entities\Admin;

class CatalogInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [ "product_id", "event", "order_id", "adjusted_by", "adjustment_type", "quantity" ];

    public function catalog_inventories(): BelongsToMany
    {
        return $this->belongsToMany(CatalogInventory::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class); 
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, "adjusted_by");
    }
}
