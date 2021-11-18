<?php

namespace Modules\ShippingMethods\Traits;

use Modules\Core\Facades\SiteConfig;
use Modules\Sales\Entities\OrderTaxItem;

trait HasShippingCalculation
{
	protected $shipping_method;

	public function getShippingMethods(): mixed
	{
		return SiteConfig::get("delivery_methods");
	}

	public function getInternalShippingValue(object $request, object $order, object $coreCache)
	{
		$delivery_method_path = SiteConfig::fetch($request->shipping_method."_path", "channel", $coreCache?->channel->id);
		$total_shipping_amount = 0.00;
		switch ($delivery_method_path) {
			case "delivery_method_flat_rate":
				$flat_type = SiteConfig::fetch($request->shipping_method."_flat_type", "channel", $coreCache?->channel->id);
				$flat_price = SiteConfig::fetch($request->shipping_method."_price", "channel", $coreCache?->channel->id);
				$total_shipping_amount = $flat_price;
				if ($flat_type == "per_item") {
					$total_qty = $order->order_items->sum('qty');
					$total_shipping_amount = $flat_price * $total_qty;
				}
				break;
			case "delivery_method_free_shipping":
				$minimum_order_amt = SiteConfig::fetch($request->shipping_method."_minimum_order_amt", "channel", $coreCache?->channel->id);
				$incl_tax_to_amt = SiteConfig::fetch($request->shipping_method."_include_tax_to_amt", "channel", $coreCache?->channel->id);
				$total_shipping_amount = $minimum_order_amt;
				if (!$incl_tax_to_amt) {
					$order->order_taxes->map( function ($order_tax) use (&$total_shipping_amount) {
						$order_item_total_amount = $order_tax->order_tax_items
						->filter(fn ($order_tax_item) => ($order_tax_item == "product"))
						->map( function ($order_item) use ($order_tax, &$total_shipping_amount) {
							$amount = (($order_tax->percent/100) * $order_item->amount);
							$total_shipping_amount += $amount;
							$data = [
								"order_id" => $order_tax->id,
								"tax_percent" => $order_tax->percent,
								"amount" => $amount,
								"tax_item_type" => "shipping"
							];
							OrderTaxItem::create($data);
							return ($order_item->amount + $amount);
						})->toArray();

						$order_tax->update(["amount" => array_sum($order_item_total_amount)]);
					});
				}
				break;
		}

		return $total_shipping_amount;
	}
}
