<?php

namespace Modules\CheckOutMethods\Traits;

use Modules\Core\Facades\SiteConfig;
use Exception;
use Modules\Sales\Entities\OrderTaxItem;
use Modules\Sales\Exceptions\FreeShippingNotAllowedException;

trait HasShippingCalculation
{
	protected $shipping_method;

	public function getShippingMethods(): mixed
	{
		return SiteConfig::get("delivery_methods");
	}

	public function getInternalShippingValue(object $request, object $order, object $coreCache): ?array
	{
		try
		{
			
			$total_shipping_amount = 0.00;
			$arr_shipping = [ "shipping_tax" => false ];
			switch ($request->shipping_method) {
				case "flat_rate":
					$flat_type = SiteConfig::fetch("delivery_methods_{$request->shipping_method}_flat_type", "channel", $coreCache?->channel->id);
					$flat_price = SiteConfig::fetch("delivery_methods_{$request->shipping_method}_flat_price", "channel", $coreCache?->channel->id);
					if ($flat_type == "per_order") $total_shipping_amount = $flat_price;
					else {
						$order->order_taxes->map( function ($order_tax) use (&$total_shipping_amount) {
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
				break;

				case "free_shipping":
					$minimum_order_amt = SiteConfig::fetch("delivery_methods_{$request->shipping_method}_minimum_order_amt", "channel", $coreCache?->channel->id);
					$incl_tax_to_amt = SiteConfig::fetch("delivery_methods_{$request->shipping_method}_include_tax_to_amt", "channel", $coreCache?->channel->id);
					
					$sub_total = $order->order_items->sum('row_total');
					$sub_total_incl_tax = $order->order_items->sum('row_total_incl_tax');

					if (($incl_tax_to_amt && ($minimum_order_amt > $sub_total_incl_tax))) {
						throw new FreeShippingNotAllowedException("Total order must be more than {$sub_total_incl_tax}", 403);
					} 
					elseif (!$incl_tax_to_amt && ($minimum_order_amt > $sub_total)) {
						throw new FreeShippingNotAllowedException("Total order must be more than {$sub_total}", 403);
					}
				break;
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
