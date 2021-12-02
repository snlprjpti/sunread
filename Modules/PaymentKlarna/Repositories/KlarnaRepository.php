<?php

namespace Modules\PaymentKlarna\Repositories;

use Exception;
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
		$data = $this->getPostData(function ($order) {	
			$customer = [
				"customer" => [
					"date_of_birth" => $order->customer?->date_of_birth,
					"type" => $order->customer?->customer_type,
					"gender" => $order->customer?->gender
				]
			];
			return ($order->customer_id) ? $customer : [];
		});
		//$response = $this->postBasicClient("checkout/v3/orders", $data);
		//dd($response['html_snippet'], $response);
	}

	private function getPostData(?callable $callback = null): array
	{
		try
		{
			$coreCache = $this->getCoreCache();
			$with = [
				"order_items.order",
				"order_taxes.order_tax_items",
				"website",
				"billing_address", 
				"shipping_address",
				"customer",
				"order_status.order_status_state",
				"order_addresses.city",
				"order_addresses.region",
				"order_addresses.country",
			];
			
			$order = Order::whereId($this->parameter->order->id)->with($with)->first();
	
			$sum_tax_amount = 0;
			$sum_total_amount = 0;
			$shipping_address = $this->getShippingDetail($order->order_addresses, "shipping");
			$data = [
				"purchase_country" => SiteConfig::fetch("default_country", "channel", $this->coreCache->channel?->id)?->iso_2_code,
				"purchase_currency" => $order?->currency_code,
				"locale" => SiteConfig::fetch("store_locale", "channel", $coreCache->channel?->id)?->code,
				"order_lines" => $order->order_items->map(function ($order_item) use (&$sum_tax_amount, &$sum_total_amount) {
				
					$total_amount =  (($order_item->price * 100) * ($order_item->qty) - ($order_item->discount_amount_tax * 100));
					$tax_rate = (float) ($order_item->tax_percent * $order_item->qty * 100);
					
					$total_tax_amount = ($total_amount - $total_amount * 10000 / (10000 + $tax_rate));
					$sum_tax_amount += $total_tax_amount;
					$sum_total_amount += $total_amount; 

					return [
						"type" => "physical",
						"reference" => $order_item->sku,
						"name" => $order_item->name,
						"quantity" => (float) $order_item->qty,
						"quantity_unit" => "pcs", // TODO:: add unit
						"unit_price" => (float) ($order_item->price * 100),
						"tax_rate" => $tax_rate,
						"total_amount" => (float) $total_amount,
						"total_discount_amount" => (float) ($order_item->discount_amount_tax * 100),
						"total_tax_amount" => (float) $total_tax_amount,
					];
				})->toArray(),
	
				"order_amount" => (float) ($this->parameter->sub_total * 100  - $this->parameter->order->discount_amount_tax * 100),
				"order_tax_amount" => (float) $sum_tax_amount,
				
				"merchant_urls" => [
				  "terms" => "https://www.example.com/terms.html",
				  "checkout" => "https://www.example.com/checkout.html?order_id={checkout.order.id}",
				  "confirmation" => route("confirmation"),
				  "push" => route("klarna")
				],
				"merchant_reference1" => $order->id,
	
				"billing_address" => $this->getShippingDetail($order->order_addresses, "billing"),
				"shipping_address" => $shipping_address,
				
				"selected_shipping_option" => [
					"id" => 1, // TODO::update shipping id form order_meta table 
					"name" => $order->shipping_method_label,
					"description" => $order->shipping_method,
					// "promo" => "Christmas Promotion", //TODO::add coupons code 
					"price" => (float) $order->shipping_amount_tax,  // including tax
					"tax_amount" => (float) $this->parameter->shipping_amount, 
					//"tax_rate" => (float) $this->parameter->shipping_amount,
					"shipping_method" => $order->shipping_method_label,
					"delivery_details" => [
						"pickup_location" => [
							"id" => $shipping_address["reference"],
							"name" => $shipping_address["street_address"],
							"address" => $shipping_address
						]
					]
				]
			];
			if ($callback) $data = array_merge($data, $callback($order));
		}
		catch (Exception $exception)
		{
			throw $exception;
		}
		
		return $data;
	}

	private function getShippingDetail(mixed $order_addresses, string $address_type): array
	{
		try
		{
			$address = $order_addresses->where("address_type", $address_type)->first();
 
			$city = ($address->city_id) ? $address->city->name : $address->city_name;
			$region = ($address->region_id) ? $address->region->name : $address->region_name;
	
			$address_data = [
				"given_name" => $address->first_name,
				"family_name" => $address->last_name,
				"email" => $address->email,
				"street_address" => $address->address_line_1,
				"street_address2" => $address->address_line_2,
				"postal_code" => $address->postal_code,
				"city" => $city,
				"region" => $region,
				"phone" => $address->phone,
				"country" => $address->country->iso_2_code,
				"reference" => $address->id,
			];
		}
		catch (Exception $exception)
		{
			throw $exception;
		}

		return $address_data;
	}
}
