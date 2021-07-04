<?php

namespace Modules\Inventory\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Core\Repositories\BaseRepository;

class CatalogInventoryRepository extends BaseRepository
{
	public function __construct(CatalogInventory $catalogInventory)
	{
		$this->model = $catalogInventory;
		$this->model_key = "Catalog Inventory";
		$this->rules = [
			"product_id" => "required|integer|exists:products,id",
			"website_id" => "required|integer|exists:websites,id",
			"quantity" => "required|numeric",
			"is_in_stock" => "sometimes|boolean",
			"manage_stock" => "sometimes|boolean",
			"use_config_manage_stock" => "sometimes|boolean",
			"adjustment_type" => "sometimes|in:addition,deduction",
		];
	}

	public function syncItem(object $inventory, object $request, string $method = "store"): object
	{
		DB::beginTransaction();
        Event::dispatch("{$this->model_key}.create.before");
		
		try
		{
			$data = [
				"product_id" => $inventory->product_id,
				"event"  => ($method == "store") ? "Created" : "Updated",
				"adjusted_by" => auth()->guard("admin")->check() ? auth()->guard("admin")->id() : null,
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

        Event::dispatch("{$this->model_key}.create.after", $Inventoryitem);
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
