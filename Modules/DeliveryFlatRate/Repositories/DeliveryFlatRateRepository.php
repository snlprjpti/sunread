<?php

namespace Modules\DeliveryFlatRate\Repositories;

use Exception;
use Modules\CheckOutMethods\Contracts\DeliveryMethodInterface;
use Modules\CheckOutMethods\Repositories\BaseDeliveryMethodRepository;
use Modules\Core\Facades\SiteConfig;
use Modules\Sales\Entities\OrderTaxItem;

class DeliveryFlatRateRepository extends BaseDeliveryMethodRepository implements DeliveryMethodInterface
{
    protected object $request;
    protected string $method_key;
    protected object $parameter;

    public function __construct(object $request, object $parameter)
    {
        $this->request = $request;
        $this->parameter = $parameter;
        $this->method_key = "flat_rate";
        parent::__construct($this->request, $this->method_key);
    }

    public function get(): mixed
    {
        try
        {
            $coreCache = $this->getCoreCache();
            $arr_shipping = [ "shipping_amount" => 0.00, "shipping_tax" => false ];
            $flat_type = SiteConfig::fetch("delivery_methods_{$this->method_key}_flat_type", "channel", $coreCache?->channel->id);
            $flat_price = SiteConfig::fetch("delivery_methods_{$this->method_key}_flat_price", "channel", $coreCache?->channel->id);
            $total_shipping_amount = 0.00;
            if ($flat_type == "per_order") $total_shipping_amount = $flat_price;
            else {
                $this->parameter->order->order_taxes->map( function ($order_tax) use (&$total_shipping_amount) {
                    $order_item_total_amount = $order_tax->order_tax_items->filter(fn ($order_tax_item) => ($order_tax_item->tax_item_type == "product"))->map( function ($order_item) use ($order_tax, &$total_shipping_amount) {
                        $amount = (float) (($order_tax->percent/100) * $order_item->amount);
                        $total_shipping_amount += $amount;
                        $data = [
                            "tax_id" => $order_tax->id,
                            "tax_percent" => (float) $order_tax->percent,
                            "amount" => $amount,
                            "tax_item_type" => "shipping"
                        ];
                        OrderTaxItem::create($data);
                        return ($order_item->amount + $amount);
                    })->toArray();
    
                    $order_tax->update(["amount" => array_sum($order_item_total_amount)]);
                });
                $arr_shipping["shipping_tax"] = true;
            }
            $arr_shipping["shipping_amount"] = $total_shipping_amount;
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $arr_shipping;
    }


    
}