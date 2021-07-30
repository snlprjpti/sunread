<?php

namespace Modules\Erp\Traits;

use Exception;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Attribute\Entities\AttributeTranslation;
use Modules\Erp\Entities\ErpImport;
use Modules\Inventory\Entities\CatalogInventory;
use Modules\Inventory\Jobs\LogCatalogInventoryItem;
use Modules\Product\Entities\Product;

trait HasErpValueMapper
{
	protected array $erp_types = [
		"webAssortments",
		"listProducts",
		"attributeGroups",
		"salePrices",
		"eanCodes",
		"webInventories",
		"productDescriptions",
		"productVariants"
	];

	protected array $erp_attributes = [
		"Features",
		"Size and Care",
		"Ean Code"
	];

	public function importAll()
	{
		$erp_details = ErpImport::where("type","listProducts")->first()->erp_import_details;

		$erp = ErpImport::all();
		$count = 0;
		foreach ( $erp_details as $detail )
		{
			$product_data = [
				"attribute_set_id" => 1,
				"website_id" => 1,
				"sku" => $detail->sku,
				"type" => "simple",
			];

			$match = [
				"website_id" => 1,
				"sku" => $detail->sku,
			];

			$product = Product::updateOrCreate($match, $product_data);

			$price_detail = $erp->where("type", "salePrices")->first()->erp_import_details()->where("sku", $detail->sku)->get();

			$inventory = $erp->where("type", "webInventories")->first()->erp_import_details()->where("sku", $detail->sku)->get(); 

			$variants = $erp->where("type", "productVariants")->first()->erp_import_details()->where("sku", $detail->sku)->get(); 

			
			
			// $variants = $erp->where("type", "eanCodes")->first()->erp_import_details()->where("sku", $detail->sku)->get(); 


			$count ++;
		
			// $this->createInventory($product, $inventory);
			if ($count > 2) $this->createAttributeValue($erp, $product, $detail);


		}
		// $this->createVariant($product, $variants);
		dd("asd");
	}

	private function createAttributeValue(mixed $erp, object $product, object $erp_product_iteration)
	{
		try
		{
			$attr_groups = $erp->where("type", "attributeGroups")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get(); 
			$ean_code = $erp->where("type", "eanCodes")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get();
			
			
			$description = $erp->where("type", "productDescriptions")->first()->erp_import_details()->where("sku", $erp_product_iteration->sku)->get();
			$description = json_decode($this->getValue($description)->first(), true)["description"];

			dd($erp_product_iteration->value);
			$attribute_data = [
				[
					"attribute_id" => 1,
					"value" => $erp_product_iteration->value["description"]
				],
				[
					"attribute_id" => 16,
					"value" => $description, 
				],
				[
					"attribute_id" => 17,
					"value" => \Str::limit($description, 100), 
				],
				[
					"attribute_id" => 18,
					"value" => null, 
				],
				[
					"attribute_id" => 19,
					"value" => $description, 
				],
				[
					"attribute_id" => 20,
					"value" => $erp_product_iteration->value["description"], 
				],
				[
					"attribute_id" => 21,
					"value" => $description, 
				],
				[
					"attribute_id" => 22,
					"value" => 1, 
				],
				[
					"attribute_id" => 28,
					"value" => $this->getAttributeValue( $attr_groups, "Features" ), 
				],
				[
					"attribute_id" => 29,
					"value" => $this->getAttributeValue( $attr_groups, "Size and care" ), 
				],
				[
					"attribute_id" => 29,
					"value" => $this->getAttributeValue( $attr_groups, "Size and care" ), 
				],
			];
			dd($this->getValue($ean_code));
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}

	}

	private function getAttributeValue(mixed $attr_groups, string $attribute_name): string
	{
		try
		{
			$attach_value = "<ul><li>";

			$this->getValue($attr_groups, function ($value) use (&$attach_value, $attribute_name) {
				if ( $value["attributetype"] == $attribute_name ) $attach_value .= \Str::start("<li>", \Str::finish($value["description"], "</li>"));
			});
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}
		return $attach_value."</ul>";
	}

	private function getValue(mixed $values, callable $callback = null): mixed
	{
		try
		{
			$data = $values->map( function ($value) use($callback){
				if ( $callback ) $data = $callback($value->value);
				return ( $callback ) ? $data : $value->value;
			});
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}

		return $data;
	}

	private function createInventory(object $product, mixed $inventory): object
	{
		try
		{
			$value = array_sum($this->getValue($inventory)->pluck("Inventory")->toArray());
			$data = [
				"quantity" => $value,
				"use_config_manage_stock" => 1,
				"product_id" => $product->id,
				"website_id" => $product->website_id,
				"manage_stock" =>  0,
				"is_in_stock" => (bool) ($value > 0) ? 1 : 0,
			];
	
			$match = [
				"product_id" => $product->id,
				"website_id" => $product->website_id
			];

			$catalog_inventory = CatalogInventory::updateOrCreate($match, $data);
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}

		return $catalog_inventory;
	}

}