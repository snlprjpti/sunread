<?php

namespace Modules\Sales\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Sales\Entities\OrderTax;
use Modules\Sales\Entities\OrderItem;
use Modules\Sales\Entities\OrderTaxItem;

trait HasOrderItem
{
	public function store(object $request, object $order, object $order_item_details): object
	{
		try
		{
            $coreCache = $this->getCoreCache($request);
            $calculation = $this->calculateItems($order_item_details);
            $data = array_merge($calculation, [
                "website_id" => $coreCache?->website->id,
                "store_id" => $coreCache?->store->id,
                "order_id" => $order->id,
                "product_id" => $order_item_details->product_id,
                "name" => $order_item_details->name,
                "sku" => $order_item_details->sku,
                "cost" => $order_item_details->cost,
                "product_type" => $order_item_details->type,
                "product_options" => json_encode($order_item_details->product_options),
           ]);
           $order_item = OrderItem::create($data);
           $this->storeOrderTax($order, $order_item_details, function ($order_tax, $rule) use ($order_item) {
                $this->storeOrderTaxItem($order_tax, $order_item, $rule);
                /**
                 * @TODO::Addition 
                 * Sum order tax again for additional order items 
                 * Order Items can be added in callback 
                 * ie. Sum order tax form relations and update order taxes fields
                 * Order Tax array format [["tax_id" => $order_tax->id,".."]]
                 */
           });
		}
		catch (Exception $exception)
		{
			throw $exception;
		}

		return $order_item;
	}

    public function calculateItems(object $order_item_details): array
    {
        try
        {
            $price = $order_item_details->price;
            $qty = $order_item_details->qty;
            $weight = $order_item_details->weight;
    
            $tax_amount = $order_item_details->tax_rate_value;
            $tax_percent = $order_item_details->tax_rate_percent;
    
            $price_incl_tax = $price + $tax_amount;
            $row_total = $price * $qty;
            $row_total_incl_tax = $row_total + $tax_amount;
            $row_weight = $weight * $qty;
    
            $discount_amount_tax = 0.00;
            $discount_amount = 0.00;
            $discount_percent = 0.00;
    
            $data = [
                "price" => $price,
                "qty" => $qty,
                "weight" => $weight,
                "tax_amount" => $tax_amount,
                "tax_percent" => $tax_percent,
                "price_incl_tax" =>$price_incl_tax,
                "row_total" =>$row_total,
                "row_total_incl_tax" =>$row_total_incl_tax,
                "row_weight" =>$row_weight,
                "discount_amount_tax" =>$discount_amount_tax,
                "discount_amount" =>$discount_amount,
                "discount_percent" =>$discount_percent,
            ];
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
        return $data;
    }

}
