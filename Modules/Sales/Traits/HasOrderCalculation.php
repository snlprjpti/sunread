<?php

namespace Modules\Sales\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Tax\Facades\TaxPrice;
use Modules\Coupon\Entities\Coupon;
use Modules\Core\Facades\SiteConfig;
use Modules\Customer\Entities\Customer;
use Modules\Sales\Entities\OrderTax;
use Modules\Sales\Entities\OrderTaxItem;
use Modules\CheckOutMethods\Traits\HasShippingCalculation;

trait HasOrderCalculation
{
    use HasShippingCalculation;

    protected $discount_percent, $shipping_amount;

    public function orderCalculationUpdate(object $order, object $request, object $coreCache): mixed
    {
        try 
        {
            $channel_id = $coreCache?->channel->id;

            $sub_total = 0.00;
            $sub_total_tax_amount = 0.00;
            $total_qty_ordered = 0;
            $item_discount_amount = 0.00;
            
            $total_items = 0;
            foreach ( $order->order_items as $item ) {
                $sub_total += (float) $item->row_total;
                $sub_total_tax_amount += (float) $item->row_total_incl_tax;
                $total_qty_ordered += (float) $item->qty;
                $total_items += 1;
                $item_discount_amount += (float) $item->discount_amount_tax;
            }

            $discount_amount = (float) $this->calculateDiscount($order); // To-Do other discount will be added here...

            $check_out_method_helper = $this->check_out_method_helper;
            $check_out_method_helper = new $check_out_method_helper($request->shipping_method);
            $arr_shipping_amount = $check_out_method_helper->process($request, ["order" => $order]);

            //TODO:: move this fn to respective repo and this should on queue.       
            $this->updateOrderCalculation($arr_shipping_amount, $order, $sub_total, $discount_amount, $sub_total_tax_amount, $total_qty_ordered, $total_items);

            $check_out_method_shipping_helper = new $check_out_method_helper($request->payment_method);
            $data = $check_out_method_shipping_helper->process($request, ["order" => $order]);

        }
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $data;
    }

    public function calculateItems(object $order_item_details): array
    {
        try
        {
            $price = (float) $order_item_details->price;
            $qty = (float) $order_item_details->qty;
            $weight = (float) $order_item_details->weight;
            $tax_amount = (float) $order_item_details->tax_rate_value;

            $tax_percent = (float) $order_item_details->tax_rate_percent;
    
            $price_incl_tax = (float) ($price + $tax_amount);
            $row_total = (float) ($price * $qty);
            $row_total_incl_tax = (float) ($row_total + $tax_amount);
            $row_weight = (float) ($weight * $qty);
    
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
            $get_zip_code = $request->get("address")["shipping"];
            $zip_code = isset($get_zip_code['postcode']) ? $get_zip_code['postcode'] : null;

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

    public function storeOrderTax(object $order, object $order_item_details, ?callable $callback = null): void
    {
        DB::beginTransaction();
        try
        {
            foreach ($order_item_details->rules as $rule)
            {
                $data = [
                    "order_id" => $order->id,
                    "code" => \Str::slug($rule->name),
                    "title" => $rule->name,
                    "percent" => $order_item_details->tax_rate_percent,
                    "amount" => 0
                ];
                $match = $data;
                unset($match["title"], $match["percent"], $match["amount"]);
                $order_tax = OrderTax::updateOrCreate($match, $data);
                if ($callback) $callback($order_tax, $order_item_details, $rule);
            }
        }
        catch ( Exception $exception )
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();
    }

    public function storeOrderTaxItem(object $order_tax, object $order_item_details, mixed $rule): object
    {
        DB::beginTransaction();
        try
        {
            $data = [
                "tax_id" => $order_tax->id,
                "item_id" => $order_item_details->product_id,
                "tax_percent" => $order_tax->percent,
                "amount" => ($rule->rates?->pluck("tax_rate_value")->first() * $order_item_details->qty),
                "tax_item_type" => "product"
            ];            
            $order_tax_item = OrderTaxItem::create($data);  
        }
        catch ( Exception $exception )
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit(); 
        return $order_tax_item;
    }
}
