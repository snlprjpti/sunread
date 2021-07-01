<?php

namespace Modules\Inventory\Repositories;

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
		];
	}	
}
