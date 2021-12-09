<?php

namespace Modules\PaymentKlarna\Repositories;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Modules\CheckOutMethods\Contracts\PaymentMethodInterface;
use Modules\Core\Facades\SiteConfig;
use Modules\CheckOutMethods\Repositories\BasePaymentMethodRepository;
use Modules\Sales\Facades\TransactionLog;

class KlarnaRepository extends BasePaymentMethodRepository implements PaymentMethodInterface
{
    protected object $request;
    protected object $parameter;
    protected string $method_key;
    protected mixed $urls;
    public string $base_url;
    public string $user_name, $password;
    public mixed $base_data;
    public array $relations;

    public function __construct(object $request, object $parameter)
    {
        $this->request = $request;
        $this->method_key = "klarna";

        parent::__construct($this->request, $this->method_key);
        $this->parameter = $parameter;
        $this->method_detail = array_merge($this->method_detail, $this->createBaseData());
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

    private function createBaseData(): array
    {
        try
        {
            $this->user_name = SiteConfig::fetch("payment_methods_klarna_api_config_username", "channel", $this->coreCache->channel?->id);
            $this->password = SiteConfig::fetch("payment_methods_klarna_api_config_password", "channel", $this->coreCache->channel?->id);
            $data = [
                "user_name" => $this->user_name,
                "password" =>  $this->password,
            ];
            $paths = [
                "api_mode" => "payment_methods_klarna_api_config_mode",
                "api_endpoint" => "payment_methods_klarna_api_config_endpoint",
                "color_button" => "payment_methods_klarna_design_color",
                "color_button_text" => "payment_methods_klarna_design_text_color",
                "color_checkbox" => "payment_methods_klarna_design_color_checkbox",
                "color_checkbox_checkmark" => "payment_methods_klarna_design_color_checkbox_checkmark",
                "color_header" => "payment_methods_klarna_design_color_header",
                "color_link" => "payment_methods_klarna_design_color_link",
                "locale" => "store_locale",
                "purchase_country" => "default_country",
                "terms" => "payment_methods_klarna_api_config_terms_page",
                "checkout" => "payment_methods_klarna_api_config_checkout_page",
                "confirmation" => "payment_methods_klarna_api_config_confirmation_page",
                "push" => "payment_methods_klarna_api_config_push_notify",
            ];
            foreach ($paths as $key => $path) $data[$key] = SiteConfig::fetch($path, "channel", $this->coreCache->channel?->id);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function get(): mixed
    {
        try
        {
            $coreCache = $this->getCoreCache();
            $this->createKlarnaData();
            $data = $this->getPostData(function ($order, $shipping_address) {	
                return [
                    "merchant_urls" => $this->getMerchantUrl(),
                    "selected_shipping_option" => $this->getSelectedShippingOption($order, $shipping_address),
                    "options" => $this->getDesignOption(),
                    "shipping_options" => $this->getShippingOptions($order, $shipping_address),
                ];
            });
            $response = $this->postBasicClient("checkout/v3/orders", $data);

            $this->orderRepository->update([
                "payment_method" => $this->method_key,
                "payment_method_label" => SiteConfig::fetch("payment_methods_{$this->method_key}_title", "channel", $coreCache->channel?->id),
                "status" => SiteConfig::fetch("payment_methods_{$this->method_key}_new_order_status", "channel", $coreCache->channel?->id)?->slug
            ], $this->parameter->order->id, function ($order) use ($response) {
                $this->orderMetaRepository->create([
                    "order_id" => $order->id,
                    "meta_key" => $this->method_key,
                    "meta_value" => ["html_snippet" => $response["html_snippet"]]
                ]);
            });
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        // TODO::update transaction log on base repo
        TransactionLog::log($this->parameter->order, $data, $response, 201);
        return $this->object(["html_snippet" => $response["html_snippet"]]);
    }

    private function createKlarnaData(): void
    {
        $coreCache = $this->getCoreCache();
        $methods = SiteConfig::get("delivery_methods");
        $shipping_methods = $methods->map(function ($method) use ($coreCache) {
            return [
                "is_enable" => SiteConfig::fetch("delivery_methods_{$method["slug"]}", "channel", $coreCache->channel->id),
                "label" => SiteConfig::fetch("delivery_methods_{$method["slug"]}_method_name", "channel", $coreCache->channel->id),
                "shipping_method" => $method["slug"],
                "name" => SiteConfig::fetch("delivery_methods_{$method["slug"]}_title", "channel", $coreCache->channel->id),
                "repository" => $method["repository"]
            ];
        })->reject(fn ($method) => $method["is_enable"] == 0)->toArray();

        $this->orderMetaRepository->create([
            "order_id" => $this->parameter->order->id,
            "meta_key" => "shipping_collection",
            "meta_value" => $shipping_methods
        ]);
    }

    private function getPostData(?callable $callback = null): array
    {
        try
        {
            $order = $this->orderModel->whereId($this->parameter->order->id)->first();
            $shipping_address = $this->getShippingDetail($order->order_addresses, "shipping");
            $data = [
                "purchase_country" => $this->base_data->purchase_country?->iso_code,
                "purchase_currency" => $order?->currency_code,
                "locale" => $this->base_data->locale?->code,
                "merchant_reference1" => $order->id,
                "billing_address" => $this->getShippingDetail($order->order_addresses, "billing"),
                "shipping_address" => $shipping_address,
                "customer" => $this->getCustomer($order),
            ];
            if ($callback) $data = array_merge($data, $callback($order, $shipping_address), $this->getOrderLine($order));
            if (!$order->customer_id) Arr::forget($data, "customer");
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        
        return $data;
    }

    private function getOrderLine(object $order): array
    {
        try
        {
            $sum_tax_amount = 0;
            $sum_total_amount = 0;
            $data = [
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
                "order_amount" => (float) ($order->sub_total * 100  - $order->discount_amount_tax * 100),
                "order_tax_amount" => (float) $sum_tax_amount,
            ];
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

    private function getSelectedShippingOption(object $order, mixed $shipping_address): array
    {
        try
        {
            $data = [
                "id" => $order->order_metas->filter(fn ($order_meta) => ($order_meta->meta_key == $order->shipping_method))->first()?->id,
                "name" => $order->shipping_method_label,
                "description" => $order->shipping_method,
                // "promo" => "Christmas Promotion", //TODO::add coupons code 
                "price" => (float) $order->shipping_amount_tax,  // including tax
                "tax_amount" => (float) $order->shipping_amount, 
                // "tax_rate" => (float) $order->shipping_amount,
                "shipping_method" => $order->shipping_method_label,
                "delivery_details" => [
                    "pickup_location" => [
                        "id" => $shipping_address["reference"],
                        "name" => $shipping_address["street_address"],
                        "address" => $shipping_address
                    ]
                ]
            ];
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    private function getShippingOptions(object $order, mixed $shipping_address): array
    {
        try
        {
            $order_shipping_collection_meta = $order->order_metas->where("meta_key", "shipping_collection")->first();
            $order_shipping_id = $order->order_metas->where("meta_key", "shipping")->first()?->id;
            
            $shipping_data = [];
            foreach ( $order_shipping_collection_meta->meta_value as $shipping_method )
            {
                if ($shipping_method["shipping_method"] !== $order->shipping_method) break;
                $shipping_calculated_data = $this->calculateShipping($order, $shipping_method["shipping_method"]);
                $id = ($shipping_method["shipping_method"] == $order->shipping_method) ? $order_shipping_id : $order_shipping_collection_meta->id;
                $shipping_data[] = array_merge($shipping_calculated_data, [
                    "id" => $id,
                    "name" => $shipping_method["name"],
                    "description" => $shipping_method["name"],
                    "preselected" => ($shipping_method["shipping_method"] == $order->shipping_method) ? true : false,
                    //"promo" => "Christmas Promotion",
                    "delivery_details" => [
                        "carrier" => $shipping_method["name"],
                        "class" => $shipping_method["repository"],
                        "product" => [
                            "name" => $shipping_method["name"],
                            "identifier" => $id
                        ],
                        //TODO::merge dynamic timeslot
                        // "timeslot" => [
                        // 	"id" => "string",
                        // 	"start" => "string",
                        // 	"end" => "string"
                        // ],
                        "pickup_location" => [
                            "id" => $shipping_address["reference"],
                            "name" => $shipping_address["street_address"],
                            "address" => $shipping_address
                        ]
                    ]
                ]);

            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $shipping_data;
    }

    private function calculateShipping(object $order, string $shipping_method): array
    {		
        // $checkout_method_helper = new BaseCheckOutMethods($shipping_method);
        // $shipping_method_repository = $checkout_method_helper->process($this->request, ["order" => $order]);
        // $shipping_method_repository_data = $this->object($shipping_method_repository);
        // $coreCache = $this->getCoreCache();
        //TODO::calculate all options taxes.
        
        return [
            "shipping_method" => $shipping_method,
            "price" => 0,
            "tax_price" => 0.00,
            "tax_rate" => 0.00,
            "tax_amount" => 0
        ];
    }

    private function getDesignOption(): array
    {
        return [
            "color_button" => $this->base_data->color_button,
            "color_button_text" => $this->base_data->color_button_text,
            "color_checkbox" => $this->base_data->color_checkbox,
            "color_checkbox_checkmark" => $this->base_data->color_checkbox_checkmark,
            "color_header" => $this->base_data->color_header,
            "color_link" => $this->base_data->color_link,
        ];
    }

    private function getMerchantUrl(): array
    {
        return [
            "terms" => $this->base_data->terms,
            "checkout" => $this->base_data->checkout,
            "confirmation" => $this->base_data->confirmation,
            "push" => $this->base_data->push,
        ];
    }
    
    private function getCustomer(object $order): array
    {
        $customer = [
            "date_of_birth" => $order->customer?->date_of_birth,
            "type" => $order->customer?->customer_type,
            "gender" => $order->customer?->gender
        ];
        return $order->customer_id ? $customer : [];
    }
}
