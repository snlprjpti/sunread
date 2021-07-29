<?php

namespace Modules\Erp\Traits;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\Erp\Jobs\ErpImport as JobsErpImport;

trait HasErpMapper
{
	protected $url = "https://bc.sportmanship.se:7148/sportmanshipbctestapi/api/NaviproAB/web/beta/";

	private function basicAuth(): object
	{
		return Http::withBasicAuth(env("ERP_API_USERNAME"), env("ERP_API_PASSWORD"));
	}

	public function erpImport( string $type, string $url ): Collection
	{
		try
		{
			// Get ERP API
			$response = $this->basicAuth()->get($url);
			
			// values refers to response values
			$values = [];
			$values = $response->json()["value"];

			// last refers to response last values
			$last = [];
			$last[] = end($values);
			
			while ( true )
			{
				// Generate skip token
				$last_value = end($last);
				$skip = $this->skipToken($last_value, $type);

				// Get current page values
				$paginated = $this->basicAuth()->get($url, $skip);
				
				// End iteration if current value is empty 
				if (empty($paginated->json()["value"]) || !array_key_exists("@odata.nextLink", $paginated->json())) break;
	
				// Merge last value for generating skip token for next page.
				$last[] = end($paginated->json()["value"]);

				// Merge all values 
				$values = array_merge($values, $paginated->json()["value"]);
			}

			Cache::forget($type);
			Cache::rememberForever($type, function () use ($values, $type) {
				// get all values fron erp api
				return $this->generateCollection($values, $type);
			});

			JobsErpImport::dispatch($type);
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}

		return $this->generateCollection($values, $type);
	}

	private function skipToken( array $data, string $type ): string
	{
		switch ($type) {
			case 'webAssortments':
				$token = '$skiptoken='."'{$data['itemNo']}',".'SR'.",'{$data['colorCode']}'";
				break;
			
			case 'listProducts':
				$token = '$skiptoken='."'{$data['no']}',"."'{$data['webAssortmentWeb_Setup']}',"."'{$data['webAssortmentColor_Code']}'".",'{$data['languageCode']}'".",'{$data['auxiliaryIndex1']}'".",'{$data['auxiliaryIndex2']}'".",'{$data['auxiliaryIndex3']}'".",'{$data['auxiliaryIndex4']}'";
				break;
			
			case 'attributeGroups':
				$token = '$skiptoken='."'{$data['itemNo']}',"."'{$data['sortKey']}',"."'{$data['groupCode']}',"."'{$data['attributeID']}',"."'{$data['name']}',"."'{$data['auxiliaryIndex1']}'";
				break;
			
			case 'productVariants':
				$token = '$skiptoken='."'{$data['pfVerticalComponentCode']}',"."'{$data['itemNo']}'";
				break;

			case 'salePrices':
				$token = '$skiptoken'."'{$data['itemNo']}',"."'{$data['salesCode']}',"."'{$data['currencyCode']}',"."'{$data['startingDate']}',"."'{$data['salesType']}',"."'{$data['minimumQuantity']}',"."'{$data['unitofMeasureCode']}',"."'{$data['variantCode']}'";
				break;

			case 'eanCodes':
				$token = '$skiptoken'."'{$data['itemNo']}',"."'{$data['variantCode']}',"."'{$data['unitofMeasure']}',"."'{$data['crossReferenceType']}',"."'{$data['crossReferenceTypeNo']}',"."'{$data['crossReferenceNo']}'";
				break;

			case 'webInventories':
				$token = '$skiptoken'."'{$data['Item_No']}',"."'{$data['Code']}'";
		}

		return $token;
	}

	private function generateCollection( array $data, string $type ): Collection
	{
		switch ($type) {
			case 'listProducts':
				$collection = collect($data)->where("webAssortmentWeb_Active", true)
					->where("webAssortmentWeb_Setup", "SR")
					->chunk(50)
					->flatten(1);
				break;

			case 'webAssortments':
				$collection = collect($data)->where("webActive", true)
					->where("webSetup", "SR")
					->chunk(50)
					->flatten(1);
				break;

			default :
				$collection = collect($data)
					->chunk(50)
					->flatten(1);
				break;
		}

		return $collection;
	}

	public function storeImage(): bool
	{
		try
		{
			$directories = Storage::disk("ftp")->directories();
			$files = Storage::disk("ftp")->files("/{$directories[0]}");

			foreach ( $files as $file )
			{
				if ( !\Str::contains($file, [".jpg", ".jpeg", ".png", ".bmp"]) ) continue;
				$get_file = Storage::disk("ftp")->get($file);
				Storage::disk("public")->put("ERP Product Images/{$file}", $get_file);
			}
		}
		catch ( Exception $exception )
		{
			throw $exception;
		}

		return true;
	}

	public function storeDescription(): bool
	{
		try
		{
			$api = $this->basicAuth();
			dd("asdwqe");
		}
		catch ( Exception $exception )
		{

		}

		return true;
	}

}