<?php

namespace Modules\Erp\Traits;

use Exception;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeGroup;
use Modules\Attribute\Entities\AttributeSet;
use Modules\Attribute\Entities\AttributeTranslation;
use Modules\Erp\Entities\ErpImport;

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

	public function importAll()
	{
		$erp = ErpImport::query();
		$erp_details = $erp->whereType("listProducts")->first()->erp_import_details;
		dd($erp_details);
		//1111703, OR161610
		// dd($erp_details->last());
		// dd($erp_details->where("sku","OR161610")->map( function ($data) {
		// 	return json_decode($data->value, true);
		// }));
		// dd("asd");
		$attributes = $this->getAttributes();


		foreach ( $erp_details as $detail )
		{
			$type = ($erp_details->where("sku", "OR161610")->count() > 1) ? "configurable" : "simple";

			// product updateOrCreate data
			$data = [
				"attribute_set_id" => 1,
				"website_id" => 1,
				"sku" => $erp_details->sku,
				"type" => $type,
				"created_at" => now(),
				"updated_at" => now()
			];

			

			$erp->whereType("eanCodes")->first()->erp_import_details->where("value->code");


		}
		dd($attributes);

		

		dd(json_decode($erp_details->value, true));

		dd($erp_details->first());

		dd("asd");
	}

	public function getAttributes(): array
	{
		try
		{
			$attributes = [
				[
					"name" => "Features",
					"type" => "texteditor",
					"scope" => "store",
					"is_searchable" => 1,
					"search_weight" => 1
				],
				[
					"name" => "Size and Care",
					"type" => "texteditor",
					"scope" => "store",
					"is_searchable" => 1,
					"search_weight" => 1
				],
				[
					"name" => "EAN Code",
					"type" => "text",
					"is_unique" => 1,
					"scope" => "website"
				]
			];

			$attribute_arr = [];

			foreach ( $attributes as $attribute )
			{
				$attribute_array = [
					"slug" => \Str::slug($attribute["name"]),
					"name" => $attribute["name"],
					"type" => $attribute["type"],
					"scope" => $attribute["scope"] ?? "website",
					"validation" => $attribute["validation"] ?? null,
					"is_required" => $attribute["is_required"] ?? 0,
					"is_unique" => $attribute["is_unique"] ?? 0,
					"use_in_layered_navigation" => $attribute["use_in_layered_navigation"] ?? 0,
					"comparable_on_storefront" => $attribute["comparable_on_storefront"] ?? 0,
					"is_searchable" => $attribute["is_searchable"] ?? 0,
					"search_weight" => $attribute["search_weight"] ?? null,
					"is_user_defined" => $attribute["is_user_defined"] ?? 0,
					"is_visible_on_storefront" => $attribute["is_visible_on_storefront"] ?? 0,
					"default_value" => null
				];

				$attribute_data = Attribute::withoutEvents( function () use ($attribute_array) {
					return Attribute::updateOrCreate($attribute_array);
				});

				AttributeTranslation::updateOrCreate([
					"store_id" => 1,
					"name" => $attribute["name"],
					"attribute_id" => $attribute_data->id
				]);

				$attribute_arr[] = [
					"id" => $attribute_data->id,
					"slug" => \Str::slug($attribute["name"]),
				];
			}

			$attribute_set_data = [
				"name" => "Import",
				"is_user_defined" => 0,
				"created_at" => now(),
				"updated_at" => now()
			];

			$attribute_set = AttributeSet::withoutEvents( function () use ($attribute_set_data) {
				return AttributeSet::updateOrCreate($attribute_set_data);
			});

			$attribute_group_data = [
                "name" => "Extra",
                "position" => 5,
                "attribute_set_id" => $attribute_set->id,
                "created_at" => now(),
                "updated_at" => now()
            ];

			$attribute_group = AttributeGroup::create($attribute_group_data);

			//add default attributes
            $attribute_group->attributes()->sync(collect($attribute_arr)->pluck("id")->toArray());
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}

		return $attribute_arr;
	}
}