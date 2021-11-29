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
            $minimum_order_amt = SiteConfig::fetch("delivery_methods_{$this->method_key}_minimum_order_amt", "channel", $coreCache?->channel->id);
            $incl_tax_to_amt = SiteConfig::fetch("delivery_methods_{$this->method_key}_include_tax_to_amt", "channel", $coreCache?->channel->id);
            
            $sub_total = $this->parameter->order->order_items->sum('row_total');
            $sub_total_incl_tax = $this->parameter->order->order_items->sum('row_total_incl_tax');

            if (($incl_tax_to_amt && ($minimum_order_amt > $sub_total_incl_tax))) {
                throw new FreeShippingNotAllowedException("Total order must be more than {$sub_total_incl_tax}", 403);
            } 
            elseif (!$incl_tax_to_amt && ($minimum_order_amt > $sub_total)) {
                throw new FreeShippingNotAllowedException("Total order must be more than {$sub_total}", 403);
            }

            $arr_shipping = [ "shipping_amount" => 0.00, "shipping_tax" => false ];
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $arr_shipping;
    }

    
}