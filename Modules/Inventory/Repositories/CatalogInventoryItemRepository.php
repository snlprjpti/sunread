<?php

namespace Modules\Inventory\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Inventory\Entities\CatalogInventoryItem;
use Modules\Core\Repositories\BaseRepository;

class CatalogInventoryItemRepository extends BaseRepository
{
	public function __construct(CatalogInventoryItem $catalogInventoryItem)
	{
		$this->model = $catalogInventoryItem;
		$this->model_key = "Catalog Inventory Item";
		$this->rules = [
			"product_id" => "required|exists:products,id",
			"event" => "sometimes|nullable",
			"order_id" => "sometimes", // [To::do] include exists rule 
			"adjustment_type" => "required|in:addition,deduction",
			"quantity" => "required",
			"catalog_inventories" => "sometimes|array",
			"catalog_inventories.*" => "sometimes|exists:catalog_inventories,id"
		];
	}

	public function adjustment(object $inventoryItem, object $request): ?bool
	{
		DB::beginTransaction();
        Event::dispatch("{$this->model_key}.create.before");

		try
		{
			foreach ($inventoryItem->catalog_inventories()->get() as $key => $inventory)
			{
				if ($request->adjustment_type == "deduction")
				{
					$value = ($inventory->quantity - $request->quantity);
					if ((substr(strval($value), 0, 1) == "-")) throw new \Exception("Couldn't deduct. Stock quantity is {$inventoryItem->quantity}");
					$inventory->update(["quantity" => $value]);
				}
				else
				{
					$inventory->update(["quantity" => ($inventoryItem->quantity + $request->quantity)]);
				}
			}
		}
		catch (\Exception $exception)
		{
			DB::rollBack();
			throw $exception;
		}

		Event::dispatch("{$this->model_key}.create.after", $inventory);
        DB::commit();

		return true;
	}
}
