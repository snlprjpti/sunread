<?php

namespace Modules\PaymentAdyen\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Modules\Core\Facades\SiteConfig;
use Modules\Sales\Facades\TransactionLog;
use Modules\CheckOutMethods\Contracts\PaymentMethodInterface;
use Modules\CheckOutMethods\Repositories\BasePaymentMethodRepository;

class AdyenRepository extends BasePaymentMethodRepository implements PaymentMethodInterface
{
    protected object $request;
    protected object $parameter;
    protected string $method_key;

    public function __construct(object $request, object $parameter)
    {
        $this->request = $request;
        $this->method_key = "adyen";
        $this->parameter = $parameter;

        parent::__construct($this->request, $this->method_key);
        $this->urls = $this->getApiUrl();
        $this->base_url = $this->getBaseUrl();
    }

    private function getApiUrl(): Collection
    {
        return $this->collection([
            [
                "type" => "production",
                "url" => "https://checkout-test.adyen.com/checkout/"   // TODO:: take base url of production
            ],
            [
                "type" => "playground",
                "url" => "https://checkout-test.adyen.com/checkout/"
            ],
        ]);
    }

    private function getBaseUrl(): string
    {
        $data = $this->createBaseData();
        return $this->urls->where("type", $data->api_mode)->first()["url"];
    }

    private function createBaseData(): object
    {
        try
        {
            $api_key = SiteConfig::fetch("payment_methods_adyen_api_config_api_key", "channel", $this->coreCache->channel?->id);
            $this->headers = array_merge($this->headers, [ 
                "Content-Type" => "application/json",
                "X-API-Key" => $api_key
            ]);
            $data = [ "api_key" => $api_key ];
            $paths = [
                "api_mode" => "payment_methods_adyen_api_config_mode",
                "api_merchant_account" => "payment_methods_adyen_api_config_merchant_account",
                "client_key" => "payment_methods_adyen_api_config_client_key",
                "default_country" => "default_country",
                "payment_method_label" => "payment_methods_adyen_title",
                "status" => "payment_methods_adyen_new_order_status",
                "environment" => "payment_methods_adyen_environment",
            ];
            foreach ($paths as $key => $path) $data[$key] = SiteConfig::fetch($path, "channel", $this->coreCache->channel?->id);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $this->object($data);
    }

    public function get(): mixed
    {
        try 
        {
            $config_data = $this->createBaseData();
            $data =  $this->getPostData($config_data);
            $response = $this->postClient("v68/sessions", $data);

            $credentials =  [ 'merchantAccount' => 'SailRacingInternationalABECOM'];
            $payment_methods = $this->postClient("v68/paymentMethods", $credentials);
            $this->orderRepository->update([
                "payment_method" => $this->method_key,
                "payment_method_label" => $config_data->payment_method_label,
                "status" => $config_data->status?->slug
            ], $this->parameter->order->id, function ($order) use ($response, $config_data, $payment_methods) {
                $this->orderMetaRepository->create([
                    "order_id" => $order->id,
                    "meta_key" => $this->method_key,
                    "meta_value" => [ 
                        "clientKey" => $config_data->client_key,
                        "merchantAccount" => $config_data->api_merchant_account,
                        "environment" => $config_data->environment,
                        "sessionData" => $response["sessionData"],
                        "paymentMethods" => $payment_methods['paymentMethods']
                    ]
                ]);
            });
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
        TransactionLog::log($this->parameter->order, $this->method_key, $response);
        return true;
    }
    
    private function getPostData(object $config_data): array
    {
        try
        {
            $order = $this->orderModel->whereId($this->parameter->order->id)->first();
            $data = [
                "merchantAccount" => $config_data->api_merchant_account,
                "amount" => [
                "value" => 100, //$order?->grand_total,
                "currency" => $order->currency_code,
                ],
                "returnUrl" => "https://your-company.com/checkout?shopperOrder=12xy..",
                "reference" => "sail-racing-{$order->id}",
                "countryCode" => $config_data->default_country,
            ];
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $data;
    }
}
