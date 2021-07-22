<?php

namespace Modules\Erp\Traits;

use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

trait HasErpMapper
{

	public array $credentials = [
		"Username" => "SPORTMANSHIP\exthbg",
		"Password" => "uFsR+z2862hZSWqcKt3ehPWpbakTSJ+OxQaFW/+MTUc="
	];

	public function getList()
	{
		$url = "https://bc.sportmanship.se:7148/sportmanshipbctestapi/api/NaviproAB/web/beta/webItems";
		// $response = Http::get("https://bc.sportmanship.se:7148/sportmanshipbctestapi/api/NaviproAB/web/beta/webItems", $this->credentials);
		// dd("asd");
		$client = new Client();
// 		$response = $client->get("https://bc.sportmanship.se:7148/sportmanshipbctestapi/api/NaviproAB/web/beta/webItems",
// $this->credentials);
		// $response
		$credentials = base64_encode("SPORTMANSHIP\exthbg:uFsR+z2862hZSWqcKt3ehPWpbakTSJ+OxQaFW/+MTUc=");
		$asd = "U1BPUlRNQU5TSElQG3h0aGJnOnVGc1IrejI4NjJoWlNXcWNLdDNlaFBXcGJha1RTSitPeFFhRlcvK01UVWM9";
		// dd($credentials);
		$auth = "U1BPUlRNQU5TSElQXGV4dGhiZzp1RnNSK3oyODYyaFpTV3FjS3QzZWhQV3BiYWtUU0orT3hRYUZXLytNVFVjPQ==";
		$response = $client->request("GET", $url, [
			// "auth" => ["SPORTMANSHIP\exthbg","uFsR+z2862hZSWqcKt3ehPWpbakTSJ+OxQaFW/+MTUc="],
			"headers" => [
				"Authorization" => "Basic {$auth}"
			]
		]);
		dd($response->getBody());
	}
}