<?php

namespace Modules\Inventory\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Inventory\Events\InventoryItemEvent;

class InventoryItemListener
{
    public function handle(InventoryItemEvent $inventory): void
    {
        $this->syncItem($inventory->catalogInventory, request(), $inventory->event);
    }

    public function syncItem(object $inventory, object $request, string $method = "created"): object
    {
        DB::beginTransaction();
        Event::dispatch("CatalogInventories.create.before");
        
        try
        {
            $data = [
                "product_id" => $inventory->product_id,
                "event"  => ($method == "created") ? "Created" : "Updated",
                "adjusted_by" => auth()->guard("admin")->id(),
                "adjustment_type" => $this->adjustment($request, $inventory),
                "quantity" => $request->quantity,
            ];
            $Inventoryitem = $inventory->catalog_inventory_items()->create($data);
        } 
        catch (\Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        Event::dispatch("CatalogInventories.create.after", $Inventoryitem);
        DB::commit();

        return $Inventoryitem;
    }

    public function adjustment(object $request, object $inventory): string
    {
        try
        {
            if ($request->adjustment_type == "deduction")
            {
                $value = ($inventory->quantity - $request->quantity);
                if ((substr(strval($value), 0, 1) == "-")) throw new \Exception("Couldn't deduct. Stock quantity is {$inventory->quantity}");
                $inventory->update(["quantity" => $value]);
                $adjustment = "deduction";
            }
            else
            {
                $inventory->update(["quantity" => ($inventory->quantity + $request->quantity)]);
                $adjustment = "addition";
            }
        }
        catch (\Exception $exception)
        {
            throw $exception;
        }

        return $adjustment;
    }


}
