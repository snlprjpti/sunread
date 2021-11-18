<?php

namespace Modules\Sales\Traits;

use Exception;
use Modules\Sales\Entities\OrderTaxItem;

trait HasShippingCalculation
{
	protected $shipping_method;

	public function getInternalShippingValue(object $request, object $order, object $coreCache)
	{
		try {
		$delivery_method_path = SiteConfig::fetch($request->shipping_method."_path", "channel", $coreCache?->channel->id);
		$total_shipping_amount = 0.00;
		$arr_shipping = [
			"shipping_tax" => false
		];
		switch ($delivery_method_path) {
			case "delivery_method_flat_rate":
				$flat_type = SiteConfig::fetch($request->shipping_method."_flat_type", "channel", $coreCache?->channel->id);
				$flat_price = SiteConfig::fetch($request->shipping_method."_price", "channel", $coreCache?->channel->id);
				if ($flat_type == "per_order") {
					$total_shipping_amount = $flat_price;
				}
				else {
					$order->order_taxes->map( function ($order_tax) use (&$total_shipping_amount) {
						$order_item_total_amount = $order_tax->order_tax_items->filter(fn ($order_tax_item) => ($order_tax_item == "product"))->map( function ($order_item) use ($order_tax, &$total_shipping_amount) {
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
				$arr_shipping["shipping_tax"] = true;
				}
				break;

			case "delivery_method_free_shipping":
				$minimum_order_amt = SiteConfig::fetch($request->shipping_method."_minimum_order_amt", "channel", $coreCache?->channel->id);
				$incl_tax_to_amt = SiteConfig::fetch($request->shipping_method."_include_tax_to_amt", "channel", $coreCache?->channel->id);
				$sub_total = $order->order_items->sum('row_total');
				$sub_total_incl_tax = $order->order_items->sum('row_total_incl_tax');
				if (!($incl_tax_to_amt && $minimum_order_amt >= $sub_total_incl_tax)) {
					throw new Exception(["total order must be more than {$sub_total_incl_tax}", 403]);

				} 
				elseif (!$incl_tax_to_amt && $minimum_order_amt >= $sub_total) {
					throw new Exception(["total order must be more than {$sub_total}", 403]);
				}
				break;
		}
		$arr_shipping["shipping_amount"] = $total_shipping_amount;
		return $arr_shipping;
	}
	catch ( Exception $exception )
        {
            throw $exception;
        }
	}
}
