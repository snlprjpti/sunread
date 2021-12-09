<?php

namespace Modules\DeliveryFreeShipping\Repositories;

use Exception;
use Modules\CheckOutMethods\Contracts\DeliveryMethodInterface;
use Modules\CheckOutMethods\Repositories\BaseDeliveryMethodRepository;
use Modules\Core\Facades\SiteConfig;
use Modules\Sales\Exceptions\FreeShippingNotAllowedException;

class DeliveryFreeShippingRepository extends BaseDeliveryMethodRepository implements DeliveryMethodInterface
{
    protected object $request;
    protected string $method_key;
    protected object $parameter;

    public function __construct(object $request, object $parameter)
    {
        $this->request = $request;
        $this->parameter = $parameter;
        $this->method_key = "free_shipping";
        parent::__construct($this->request, $this->method_key);
    }

    public function get(): mixed
    {
        try
        {
            $coreCache = $this->getCoreCache();
            $channel_id = $coreCache?->channel->id;
            $order = $this->orderModel->whereId($this->parameter->order->id)->first();

            $minimum_order_amt = SiteConfig::fetch("delivery_methods_{$this->method_key}_minimum_order_amt", "channel", $channel_id);
            $incl_tax_to_amt = SiteConfig::fetch("delivery_methods_{$this->method_key}_include_tax_to_amt", "channel", $channel_id);
        
            $sub_total = $order->order_items->sum('row_total');
            $sub_total_incl_tax = $order->order_items->sum('row_total_incl_tax');

            if (($incl_tax_to_amt && ($minimum_order_amt > $sub_total_incl_tax))) {
                throw new FreeShippingNotAllowedException("Total order must be more than {$sub_total_incl_tax}", 403);
            } 
            elseif (!$incl_tax_to_amt && ($minimum_order_amt > $sub_total)) {
                throw new FreeShippingNotAllowedException("Total order must be more than {$sub_total}", 403);
            }

            $arr_shipping = [ "shipping_amount" => 0.00, "shipping_tax" => false ];

            $this->orderRepository->update([
                "shipping_method" => $this->method_key,
                "shipping_method_label" => SiteConfig::fetch("delivery_methods_{$this->method_key}_title", "channel", $channel_id)
            ], $this->parameter->order->id, function ($order) use ($arr_shipping) {
                $this->orderMetaRepository->create([
                    "order_id" => $order->id,
                    "meta_key" => "shipping",
                    "meta_value" => [
                        "shipping_method" => $order->shipping_method,
                        "shipping_method_label" => $order->shipping_method_label,
                        "taxes" => $arr_shipping
                    ]
                ]);
            });

        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $arr_shipping;
    }

    
}