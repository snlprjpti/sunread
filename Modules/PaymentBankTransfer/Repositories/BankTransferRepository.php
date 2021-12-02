<?php

namespace Modules\PaymentBankTransfer\Repositories;

use Exception;
use Modules\Core\Facades\SiteConfig;
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
            if (($this->parameter->sub_total_tax_amount < $minimum_order_total) || ($this->parameter->sub_total_tax_amount > $maximum_order_total)) {
                throw new BankTransferNotAllowedException(__("core::app.sales.payment-transfer-not-allowed", ["minimum_order_total" => $minimum_order_total, "maximum_order_total" => $maximum_order_total]), 403);
            }
            
            $this->parameter->order->update([
                "payment_method" => $this->method_key,
                "payment_method_label" => SiteConfig::fetch("payment_methods_{$this->method_key}_title", "channel", $channel_id)
            ]);
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
    }
}
