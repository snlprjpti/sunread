<?php

namespace Modules\Inventory\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Core\Traits\HasFactory;
use Modules\Product\Entities\Product;
use Modules\User\Entities\Admin;

class CatalogInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [ "product_id", "event", "order_id", "adjusted_by", "adjustment_type", "quantity" ];

    public function catalog_inventories()
    {
        return $this->belongsToMany(CatalogInventory::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class); 
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, "adjusted_by");
    }
}
