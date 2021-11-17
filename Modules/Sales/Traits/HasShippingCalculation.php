<?php

namespace Modules\Sales\Traits;

trait HasShippingCalculation
{
	protected $shipping_method;

	public function getShippingMethod()
	{
		
	}

	public function getInternalShippingValue(object $request, object $order, object $order_item_details, object $coreCache)
	{
		$deliveryMethodPath = SiteConfig::fetch($request->shipping_method."_path", "channel", $coreCache?->channel->id);
		switch ($deliveryMethodPath) {
			case "delivery_method_flat_rate":
				$SiteConfig::fetch($request->shipping_method."_path", "channel", $coreCache?->channel->id);
				break;
			case "delivery_method_free_shipping":
				break;


		}

	}
}
