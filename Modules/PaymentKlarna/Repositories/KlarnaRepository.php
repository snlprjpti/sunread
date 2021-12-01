<?php

namespace Modules\PaymentKlarna\Repositories;

use Illuminate\Support\Collection;
use Modules\CheckOutMethods\Contracts\PaymentMethodInterface;
use Modules\CheckOutMethods\Repositories\BasePaymentMethodRepository;
use Modules\Core\Facades\SiteConfig;
use Modules\Sales\Entities\Order;

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
		$data = $this->getPostData();
		// dd($data);
		$response = $this->postBasicClient("checkout/v3/orders", $data);
		dd($response);
	}

	public function getPostData(): array
	{
		$coreCache = $this->getCoreCache();
		$with = [
			"order_items.order",
			"order_taxes.order_tax_items",
			"website",
			"billing_address", 
			"shipping_address",
			"customer",
			"order_status.order_status_state"
		];
		
		$order = Order::whereId($this->parameter->order->id)->with($with)->first();
		// dd($order);
		// "customer_id" => auth("customer")->id(),
		// "first_name" => $order_address['first_name'],
		// "middle_name" => $order_address['middle_name'] ?? null,
		// "last_name" => $order_address['last_name'],
		// "phone" => $order_address['phone'],
		// "address1" => $order_address['address_line_1'],
		// "address2" => $order_address['address_line_2'] ?? null,
		// "postcode" => $order_address['postal_code'],
		// "phone" => $order_address['phone'],
		// "vat_number" => $order_address['vat_number'] ?? null,
		// "country_id" => $order_address["country_id"],
		// "region_id" => $order_address["region_id"] ?? null,
		// "city_id" => $order_address["city_id"] ?? null,
		// "region_name" => $order_address["region_name"] ?? null,
		// "city_name" => $order_address["city_name"] ?? null
		return [
			"purchase_country" => SiteConfig::fetch("default_country", "channel", $this->coreCache->channel?->id)?->iso_2_code,
			"purchase_currency" => $order?->currency_code,
			"locale" => SiteConfig::fetch("store_locale", "channel", $coreCache->channel?->id)?->code,
			"order_amount" => (float) ($this->parameter->sub_total * 100  - $this->parameter->order->discount_amount_tax * 100), //$this->parameter->grand_total,
			"order_tax_amount" => (float) $this->parameter->total_tax * 100,//$this->parameter->total_tax,//$this->parameter->total_tax,
			"order_lines" => $order->order_items->map(function ($order_item) use ($order) {
				$total_amount =  (($order_item->price * 100) * ($order_item->qty * 100) - ($order_item->discount_amount_tax * 100));
				$tax_rate = (float) ($order_item->tax_percent * 100);
				return [
					"type" => "physical",
					"name" => $order_item->name,
					"quantity" => (float) ($order_item->qty * 100),
					"quantity_unit" => "pcs", // TODO:: add unit
					"unit_price" => (float) ($order_item->price * 100),
					"tax_rate" => $tax_rate,
					"total_amount" => (float) $total_amount,
					"total_discount_amount" => (float) ($order_item->discount_amount_tax * 100),
					"total_tax_amount" => (float) (27630.00 * 100)
				];
			})->toArray(),
			"merchant_urls" => [
			  "terms" => "https://www.example.com/terms.html",
			  "checkout" => "https://www.example.com/checkout.html?order_id={checkout.order.id}",
			  "confirmation" => "https://www.example.com/confirmation.html?order_id={checkout.order.id}",
			  "push" => "https://www.example.com/api/push?order_id={checkout.order.id}"
			],
			// "selected_shipping_option" => [
			// 	"id" => "express_priority",
			// 	"name" => "EXPRESS 1-2 Days",
			// 	"description" => "Delivery by 4:30 pm",
			// 	"promo" => "Christmas Promotion",
			// 	"price" => 0,
			// 	"preselected" => false,
			// 	"tax_amount" => 0,
			// 	"tax_rate" => 0,
			// 	"shipping_method" => "PickUpStore",
			// 	"delivery_details" => [
			// 		"carrier" => "string",
			// 		"class" => "string",
			// 		"product" => [
			// 			"name" => "string",
			// 			"identifier" => "string"
			// 		],
			// 		"timeslot" => [
			// 			"id" => "string",
			// 			"start" => "string",
			// 			"end" => "string"
			// 		],
			// 		"pickup_location" => [
			// 			"id" => "string",
			// 			"name" => "string",
			// 			"address" => [
			// 				"given_name" => "John",
			// 				"family_name" => "Doe",
			// 				"organization_name" => "string",
			// 				"email" => "john@doe.com",
			// 				"title" => "Mr",
			// 				"street_address" => "Lombard St 10",
			// 				"street_address2" => "Apt 214",
			// 				"street_name" => "Lombard St",
			// 				"street_number" => "10",
			// 				"house_extension" => "B",
			// 				"postal_code" => "90210",
			// 				"city" => "Beverly Hills",
			// 				"region" => "CA",
			// 				"phone" => "333444555",
			// 				"country" => "US",
			// 				"care_of" => "C/O",
			// 				"reference" => "string",
			// 				"attention" => "string"
			// 			]
			// 		]
			// 	],
			// 	"tms_reference" => "a1b2c3d4-e4f6-g7h8-i9j0-k1l2m3n4o5p6",
			// 	"selected_addons" => [
			// 		[
			// 			"type" => "string",
			// 			"price" => 0,
			// 			"external_id" => "string",
			// 			"user_input" => "string"
			// 		]
			// 	]
			// ]		
		];
	}

	public function postData(): array
	{
		return [
			"status" => "sometimes|exists:order_statuses,slug",
            "locale" => "required",
            "customer" => "sometimes|array",
            "customer.*.type" => "sometimes|in:person,organization",
            "customer.*.gender" => "sometimes|in:male,female",
            "customer.*.date_of_birth" => "sometimes|date_format:Y-m-d",
            "customer.*.vat_id" => "sometimes",
            "gui" => "sometimes|array",
            "gui.*.options" => "sometimes|array",
            "recurring" => "sometimes|boolean",
            "tags" => "sometimes|array",
            "purchase_country" => "required",
            "purchase_currency" => "required",
            "billing_address" => "sometimes|array",
            "billing_address.*.given_name" => "sometimes",
            "billing_address.*.family_name" => "sometimes",
            "billing_address.*.organization_name" => "sometimes",
            "billing_address.*.email" => "sometimes|email",
            "billing_address.*.title" => "sometimes",
            "billing_address.*.street_address" => "sometimes",
            "billing_address.*.street_address2" => "sometimes",
            "billing_address.*.street_name" => "sometimes",
            "billing_address.*.street_number" => "sometimes",
            "billing_address.*.house_extension" => "sometimes",
            "billing_address.*.postal_code" => "sometimes",
            "billing_address.*.city" => "sometimes",
            "billing_address.*.region" => "sometimes",
            "billing_address.*.phone" => "sometimes",
            "billing_address.*.country" => "sometimes",
            "billing_address.*.care_of" => "sometimes",
            "billing_address.*.reference" => "sometimes",
            "billing_address.*.attention" =>  "sometimes",
            "shipping_address" => "sometimes|array",
            "shipping_address.*.given_name" => "sometimes",
            "shipping_address.*.family_name" => "sometimes",
            "shipping_address.*.organization_name" => "sometimes",
            "shipping_address.*.email" => "sometimes|email",
            "shipping_address.*.title" => "sometimes",
            "shipping_address.*.street_address" => "sometimes",
            "shipping_address.*.street_address2" => "sometimes",
            "shipping_address.*.street_name" => "sometimes",
            "shipping_address.*.street_number" => "sometimes",
            "shipping_address.*.house_extension" => "sometimes",
            "shipping_address.*.postal_code" => "sometimes",
            "shipping_address.*.city" => "sometimes",
            "shipping_address.*.region" => "sometimes",
            "shipping_address.*.phone" => "sometimes",
            "shipping_address.*.country" => "sometimes",
            "shipping_address.*.care_of" => "sometimes",
            "shipping_address.*.reference" => "sometimes",
            "shipping_address.*.attention" =>  "sometimes",
            "order_amount" => "required",
            "order_amount" => "required",
            "order_tax_amount" => "required",
            "order_lines" => "required|array",
            "order_lines.*.type" => "sometimes",
            "order_lines.*.reference" => "sometiems",
            "order_lines.*.name" => "required",
            "order_lines.*.quantity" => "required",
            "order_lines.*.quantity_unit" => "sometimes",
            "order_lines.*.unit_price" => "required",
            "order_lines.*.tax_rate" => "required",
            "order_lines.*.total_amount" => "required",
            "order_lines.*.total_discount_amount" => "sometimes",
            "order_lines.*.total_tax_amount" => "required",
            "order_lines.*.merchant_data" => "sometimes",
            "order_lines.*.product_url" => "sometimes",
            "order_lines.*.image_url" => "sometimes"
		];
	}

	public function getConfigData(): mixed
	{

	}
}
