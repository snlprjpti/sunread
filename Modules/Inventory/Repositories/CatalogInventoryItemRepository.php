<?php

namespace Modules\Inventory\Repositories;

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
			"order_id" => "required", // [To::do] include exists rule 
			"adjusted_by" => "required",
			"adjustment_type" => "required|in:addition,deduction",
			"quantity" => "required",
			"catalog_inventories" => "required|array",
			"catalog_inventories.*" => "required|exists:catalog_inventories,id"
		];
	}

	public function adustment(object $catalogInventoryItem)
	{
		try
		{
			dd($catalogInventoryItem);
		}
		catch (Exception $exception)
		{
			
		}

		return true;
	}
}
