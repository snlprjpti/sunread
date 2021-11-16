<?php

namespace Modules\Sales\Traits;

use Exception;
use Modules\Core\Facades\SiteConfig;
use Modules\Tax\Facades\TaxPrice;
use Modules\Coupon\Entities\Coupon;
use Modules\Customer\Entities\Customer;

trait HasOrderCalculation
{
    use HasPayementCalculation, HasShippingCalculation;

    protected $discount_percent, $shipping_amount;

	public function orderCalculationUpdate(object $order, object $request, object $coreCache): void
    {
        try 
        {

            $sub_total = 0.00;
            $sub_total_tax_amount = 0.00;
            $total_qty_ordered = 0.00;
            $item_discount_amount = 0.00;
            
            foreach ( $order->order_items as $item ) {
                $sub_total += $item->row_total;
                $sub_total_tax_amount += $item->row_total_incl_tax;
                $total_qty_ordered += $item->qty;
                $item_discount_amount += $item->discount_amount_tax;
            }

            $taxes = $order->order_taxes?->pluck('amount')->toArray();
            $total_tax = array_sum($taxes);

            $discount_amount = $this->calculateDiscount($order); // To-Do other discount will be added here...
            $shipping_amount = 0.00;  // To-Do need to fetch its actual amount.
            $grand_total = ($sub_total + $total_tax - $discount_amount);

            $order->update([
                "sub_total" => $sub_total,
                "sub_total_tax_amount" => $sub_total_tax_amount,
                "tax_amount" => $total_tax,
                "shipping_amount" => $shipping_amount,
                "grand_total" => $grand_total,
                "total_items_ordered" => $order->order_items->count(),
                "total_qty_ordered" => $total_qty_ordered,
                // "status" => SiteConfig::fetch(""),
                "shipping_method" => SiteConfig::fetch($request->shipping_method."_title", "channel", $coreCache?->channel->id)
            ]);

        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
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
    
            $price_incl_tax = ($price + $tax_amount);
            $row_total = ($price * $qty);
            $row_total_incl_tax = ($row_total + $tax_amount);
            $row_weight = ($weight * $qty);
    
            $discount_amount_tax = 0.00; // this is total discount amount including tax
            $discount_amount = 0.00;
            $discount_percent = 0.00;
    
            $data = [
                "price" => $price,
                "qty" => $qty,
                "weight" => $weight,
                "tax_amount" => $tax_amount,
                "tax_percent" => $tax_percent,
                "price_incl_tax" => $price_incl_tax,
                "row_total" => $row_total,
                "row_total_incl_tax" => $row_total_incl_tax,
                "row_weight" => $row_weight,
                "discount_amount_tax" => $discount_amount_tax,
                "discount_amount" => $discount_amount,
                "discount_percent" => $discount_percent,
            ];
        }
        catch ( Exception $exception )
        {
            throw $exception;
        }
        return $data;
    }

    public function calculateTax(object $request, array $order): ?object
    {
        try
        {
            $get_zip_code = collect($request->get("address"))->where("address_type", "shipping")->first();
            $zip_code = isset($get_zip_code['postal_code']) ? $get_zip_code['postal_code'] : null;

            $product_data = $this->getProductDetail($request, $order);
            if ( auth("customer")->id() ) {
                $customer = Customer::whereId(auth("customer")->id())->with(["group.tax_group"])->first();
                $customer_tax_group_id = $customer?->group?->tax_group?->id;
                $customer_tax = TaxPrice::calculate($request, $product_data->price, customer_tax_group_id:$customer_tax_group_id, zip_code:$zip_code);
            }
            else $customer_tax = TaxPrice::calculate($request, $product_data->price, $product_data->tax_class_id?->id, zip_code:$zip_code);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $customer_tax;
    }

    public function calculateDiscount(object $order): mixed
    {
        if ($order->coupon_code) {
            $coupon = Coupon::whereCode($order->coupon_code)->publiclyAvailable()->first();
            if (!$coupon) throw new Exception("Coupon Expired");  
            $this->discount_percent = $coupon->discount_percent;          
        }

        return $coupon ?? 0; 
    }
}
