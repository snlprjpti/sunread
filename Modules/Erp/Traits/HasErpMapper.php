<?php

namespace Modules\Erp\Traits;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

trait HasErpMapper
{
	protected $url = "https://bc.sportmanship.se:7148/sportmanshipbctestapi/api/NaviproAB/web/beta/";

	public function getList()
	{
		dd($this->getProducts());

		$response = Http::withBasicAuth(env("ERP_API_USERNAME"), env("ERP_API_PASSWORD"))->get($this->url."webAssortments");
		$values = [];
		$values = $response->json()["value"];
		$last = [];
		$last[] = end($values);
		
		while ( true )
		{
			$last_value = end($last);
			$skip = '$skiptoken='."'{$last_value['itemNo']}',".'SR'.",'{$last_value['colorCode']}'";
			$dimensional = Http::withBasicAuth(env("ERP_API_USERNAME"), env("ERP_API_PASSWORD"))->get($this->url."webAssortments",$skip);
			if (empty($dimensional->json()["value"])) break;
			$last[] = end($dimensional->json()["value"]);
			$values = array_merge($dimensional->json()["value"], $values);
		}

		$collection =  collect($values)
			->where("webActive", true)
			->where("webSetup", "SR")
			->chunk(50)
			->flatten(1);
		
		dd($collection);
		




	}

	public function getProducts()
	{
		$response = Http::withBasicAuth(env("ERP_API_USERNAME"), env("ERP_API_PASSWORD"))->get($this->url."webItems");

		$values = [];
		$values = $response->json()["value"];
		$last = [];
		$last[] = end($values);
		
		while ( true )
		{
			$last_value = end($last);
			$skip = '$skiptoken='."'{$last_value['no']}',"."'{$last_value['webAssortmentWeb_Setup']}',"."'{$last_value['webAssortmentColor_Code']}'".",'{$last_value['languageCode']}'".",'{$last_value['auxiliaryIndex1']}'".",'{$last_value['auxiliaryIndex2']}'".",'{$last_value['auxiliaryIndex3']}'".",'{$last_value['auxiliaryIndex4']}'";
			$dimensional = Http::withBasicAuth(env("ERP_API_USERNAME"), env("ERP_API_PASSWORD"))->get($this->url."webItems",$skip);
			if (empty($dimensional->json()["value"])) break;
			$last[] = end($dimensional->json()["value"]);
			$values = array_merge($dimensional->json()["value"], $values);
		}
		dd($values);		
	}
}