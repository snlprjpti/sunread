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
			"product_id" => "required|integer|exists:products,id",
			"event" => "sometimes|nullable",
			"order_id" => "required", // To do include exists rule 
			"adjusted_by" => "required",
			"adjustment_type" => "sometimes|nullable",
			"quantity" => "required"
		];
	}	
}
