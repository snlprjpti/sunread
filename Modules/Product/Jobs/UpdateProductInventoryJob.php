<?php

namespace Modules\Product\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Inventory\Entities\CatalogInventory;

class UpdateProductInventoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product, $event;

    public function __construct(object $product, string $event)
    {
        $this->product = $product;
        $this->event = $event;
    }

    public function handle(): void
    {
        try
        {
            if ( $this->product->parent )
            {
                $other_variants = $this->product->parent->variants;
                $other_variant_quantities = $other_variants->map( function ($other_variant) {
                    return $other_variant->catalog_inventories()->first()?->quantity;
                })->toArray();

                $value = array_sum($other_variant_quantities); 

                $data = [
                    "quantity" => $value,
                    "use_config_manage_stock" => 1,
                    "product_id" => $this->product->parent->id,
                    "website_id" => $this->product->parent->website_id,
                    "manage_stock" =>  0,
                    "is_in_stock" => ($value > 0) ? 1 : 0,
                ];

                $match = [
                    "product_id" => $this->product->parent->id,
                    "website_id" => $this->product->parent->website_id
                ];

                unset($data["quantity"]); 
                CatalogInventory::updateOrCreate($match, $data);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

    }
}
