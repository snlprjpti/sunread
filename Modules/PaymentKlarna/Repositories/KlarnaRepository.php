<?php

namespace Modules\PaymentKlarna\Repositories;

use Illuminate\Support\Collection;
use Modules\CheckOutMethods\Contracts\PaymentMethodInterface;
use Modules\CheckOutMethods\Repositories\BasePaymentMethodRepository;
use Modules\Core\Facades\SiteConfig;

class KlarnaRepository extends BasePaymentMethodRepository implements PaymentMethodInterface
{
	protected object $request;
	protected object $parameter;
	protected string $method_key;
	protected mixed $urls;
	public string $base_url;
	public string $user_name, $password;

	public function __construct(object $request, object $parameter)
	{
		$this->request = $request;
		$this->method_key = "klarna";

		parent::__construct($this->request, $this->method_key);
		
		$this->parameter = $parameter;
		$this->method_detail = array_merge($this->method_detail, $this->data());
		$this->urls = $this->getApiUrl();
		$this->base_url = $this->getBaseUrl();
	}

	private function getApiUrl(): Collection
	{
		return $this->collection([
			[
				"type" => "production",
				"urls" => [
					[
						"name" => "Europe",
						"slug" => "europe",
						"url" => "https://api.klarna.com/"
					],
					[
						"name" => "North America:",
						"slug" => "north-america",
						"url" => "https://api-na.klarna.com/"
					],
					[
						"name" => "Oceania",
						"slug" => "oceania",
						"url" => "https://api-oc.klarna.com/"
					],
				]	
			],
			[
				"type" => "playground",
				"urls" => [
					[
						"name" => "Europe",
						"slug" => "europe",
						"url" => "https://api.playground.klarna.com/"
					],
					[
						"name" => "North America:",
						"slug" => "north-america",
						"url" => "https://api-na.playground.klarna.com/"
					],
					[
						"name" => "Oceania",
						"slug" => "oceania",
						"url" => "https://api-oc.playground.klarna.com/"
					],
				]	
			],
		]);
	}

	private function getBaseUrl(): string
	{
		$data = $this->methodDetail();
		$api_endpoint_data = $this->urls->where("type", $data->api_mode)->map(function ($mode) use ($data) {
			$end_point_data = $this->collection($mode["urls"])->where("slug", $data->api_endpoint)->first();
			return $this->object($end_point_data);
		})->first();
		return $api_endpoint_data->url;
	}

	private function data(): array
	{
		$this->user_name = SiteConfig::fetch("payment_methods_klarna_api_config_username", "channel", $this->coreCache->channel?->id);
		$this->password = SiteConfig::fetch("payment_methods_klarna_api_config_password", "channel", $this->coreCache->channel?->id);
		return [
			"api_mode" => SiteConfig::fetch("payment_methods_klarna_api_config_mode", "channel", $this->coreCache->channel?->id),
			"api_endpoint" => SiteConfig::fetch("payment_methods_klarna_api_config_endpoint", "channel", $this->coreCache->channel?->id),
			"user_name" => $this->user_name,
			"password" =>  $this->password,
		];
	}

	public function get(): mixed
	{

		$coreCache = $this->getCoreCache();
		$data = $this->methodDetail();

		// PK21291_f93bbbc9e7cf refrence
		dd($this->getBasicClient("checkout/v3/orders/840d3c8a-0be5-6087-8212-28b07bf4c4f3"));
		dd($data);
	}

	public function postData(): array
	{
		return [
			"staus" => ""
		];
	}

	public function getConfigData(): mixed
	{

	}



}
