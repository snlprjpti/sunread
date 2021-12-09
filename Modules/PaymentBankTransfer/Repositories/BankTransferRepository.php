<?php

namespace Modules\PaymentBankTransfer\Repositories;

use Exception;
use Modules\Core\Facades\SiteConfig;
use Modules\Sales\Entities\OrderMeta;
use Modules\Sales\Facades\TransactionLog;
use Modules\CheckOutMethods\Contracts\PaymentMethodInterface;
use Modules\Sales\Exceptions\BankTransferNotAllowedException;
use Modules\CheckOutMethods\Repositories\BasePaymentMethodRepository;

class BankTransferRepository extends BasePaymentMethodRepository implements PaymentMethodInterface
{
    protected object $request;
    protected object $parameter;
    protected string $method_key;

    public function __construct(object $request, object $parameter)
    {
        $this->request = $request;
        $this->method_key = "bank_transfer";
        $this->parameter = $parameter;
        
        parent::__construct($this->request, $this->method_key);
    }

    public function get(): mixed
    {
        try 
        {
            $coreCache = $this->getCoreCache();
            $channel_id = $coreCache?->channel->id;
            $minimum_order_total = SiteConfig::fetch("payment_methods_{$this->method_key}_minimum_total_order", "channel", $channel_id);
            $maximum_order_total = SiteConfig::fetch("payment_methods_{$this->method_key}_maximum_total_order", "channel", $channel_id);
            if (($this->parameter->order->sub_total_tax_amount < $minimum_order_total) || ($this->parameter->order->sub_total_tax_amount > $maximum_order_total)) {
                throw new BankTransferNotAllowedException(__("core::app.sales.payment-transfer-not-allowed", ["minimum_order_total" => $minimum_order_total, "maximum_order_total" => $maximum_order_total]), 403);
            }
            
            $payment_method_data = [
                "payment_method" => $this->method_key,
                "payment_method_label" => SiteConfig::fetch("payment_methods_{$this->method_key}_title", "channel", $channel_id),
                "minimum_order_total" => $minimum_order_total,
                "maximum_order_total" => $maximum_order_total,
                "status" => SiteConfig::fetch("payment_methods_{$this->method_key}_new_order_status", "channel", $channel_id)?->slug
            ];
            $order_data = $payment_method_data;
            unset($order_data["minimum_order_total"], $order_data["maximum_order_total"]);

            $this->orderRepository->update($order_data, $this->parameter->order->id, function ($order) use ($payment_method_data) {
                $this->orderMetaRepository->create([
                    "order_id" => $order->id,
                    "meta_key" => $this->method_key,
                    "meta_value" => $payment_method_data
                ]);
            });
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
        
        TransactionLog::log($this->parameter->order, $this->method_key, "success", 201);
        return true;
    }
}
