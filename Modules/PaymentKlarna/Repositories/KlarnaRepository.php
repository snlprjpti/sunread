<?php

namespace Modules\PaymentKlarna\Repositories;

use Modules\CheckOutMethods\Contracts\PaymentMethodInterface;
use Modules\CheckOutMethods\Repositories\BasePaymentMethodRepository;

class KlarnaRepository extends BasePaymentMethodRepository implements PaymentMethodInterface
{
	protected object $request;
	protected object $parameter;
	protected string $method_key;

	protected array $urls = [
		[
			"type" => "live",
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
			"type" => "testing",
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
	];

	public function __construct(object $request, object $parameter)
	{
		$this->request = $request;
		$this->method_key = "klarna";
		// $this->rules = [
		// 	"status" => "sometimes|nullable",
		// 	"locale" => "required",
		// 	"customer" => "required|array",
		// 	"options" => ""			
		// ];

		parent::__construct($this->request, $this->method_key);
	}

	public function get(): mixed
	{
		   
	}
	

	private function validateData()
	{
		# code...
	}

	




}
