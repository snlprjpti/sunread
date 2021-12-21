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
                "url" => "https://checkout-test.adyen.com/checkout"	
            ],
            [
                "type" => "playground",
                "url" => "https://checkout-test.adyen.com/checkout"	
            ],
        ]);
    }

    private function getBaseUrl(): string
    {
        $data = $this->createBaseData();
        $api_endpoint_data = $this->urls->where("type", $data->api_mode)->first();
        return $api_endpoint_data['url'];
    }

    private function createBaseData(): object
    {
        try
        {
            $data = [];
            $paths = [
                "api_mode" => "payment_methods_adyen_api_config_mode",
                "api_base_url" => "payment_methods_adyen_api_config_base_url",
                "api_merchant_account" => "payment_methods_adyen_api_config_merchant_account",
                "api_key" => "payment_methods_adyen_api_config_api_key",
                "client_key" => "payment_methods_adyen_api_config_client_key",
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
            $coreCache = $this->getCoreCache();
            $config_data = $this->createBaseData();
            $channel_id = $coreCache?->channel->id;
            $order = $this->orderModel->whereId($this->parameter->order->id)->first();
            $url = "{$this->base_url}/v68/sessions";
            $data =  [
                'merchantAccount' => $config_data["api_merchant_account"],
                'amount' => [
                  'value' => 100, //$order?->grand_total,
                  'currency' => $order->currency_code,
                ],
                'returnUrl' => 'https://your-company.com/checkout?shopperOrder=12xy..',
                'reference' => "{$order->id}",
                'countryCode' => $coreCache?->channel->code,
            ];

            $headers = [ 
                "Content-Type" => "application/json",
                "X-API-Key" => $config_data["api_key"]
            ];
            $response = Http::withHeaders($headers)
                        ->post("{$url}", $data)
                        ->throw()
                        ->json();
            dd($response);

            $order_data = [
                "payment_method" => $this->method_key,
                "payment_method_label" => SiteConfig::fetch("payment_methods_{$this->method_key}_title", "channel", $channel_id),
                "status" => SiteConfig::fetch("payment_methods_{$this->method_key}_new_order_status", "channel", $channel_id)?->slug
            ];
            
            $this->orderRepository->update($order_data, $this->parameter->order->id, function ($order) use ($order_data) {
                $this->orderMetaRepository->create([
                    "order_id" => $order->id,
                    "meta_key" => $this->method_key,
                    "meta_value" => $order_data
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
}
