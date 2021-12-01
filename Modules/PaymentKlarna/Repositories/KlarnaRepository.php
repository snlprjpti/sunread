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
        $order = $this->parameter->order_id;
        // PK21291_f93bbbc9e7cf refrence
        dd($this->getBasicClient("checkout/v3/orders/840d3c8a-0be5-6087-8212-28b07bf4c4f3"));
        dd($data);
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
