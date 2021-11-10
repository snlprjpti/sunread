<?php

namespace Modules\Sales\Repositories;

use Exception;
use Modules\Cart\Entities\CartItem;
use Modules\Sales\Entities\OrderItem;
use Modules\Core\Repositories\BaseRepository;
use Modules\Product\Entities\Product;

class OrderItemRepository extends BaseRepository
{
    protected $product;

    public function __construct(OrderItem $orderItem, Product $product)
    {
        $this->model = $orderItem;
        $this->model_key = "order_items";
        $this->product = $product;
        $this->rules = [
            
        ];
    }

    public function store(object $request, object $order, object $order_item_details): mixed
    {
        try
        {
            $coreCache = $this->getCoreCache($request);

            $row_total = ($order_item_details->price * $order_item_details->qty);
            //$discount_amount_tax = 0;
            $row_total_incl_tax = ( $row_total + $order_item_details->tax_rate_value );
            $row_weight = ( $order_item_details->weight * $order_item_details->qty );
            $data = [
                "website_id" => $coreCache?->website->id,
                "store_id" => $coreCache?->store->id,
                "order_id" => $order->id,

                "product_id" => $order_item_details->product_id,
                "name" => $order_item_details->name,
                "sku" => $order_item_details->sku,
                "weight" => $order_item_details->weight,
                "qty" => $order_item_details->qty,
                "price" => $order_item_details->price,
                "cost" => $order_item_details->cost,
                "product_type" => $order_item_details->type,
                "product_options" => json_encode($order_item_details->product_options),

                "price_incl_tax" => $order_item_details->tax_rate_value,
                "tax_amount" => $order_item_details->tax_rate_value,

                "tax_percent" => $order_item_details->tax_rate_percent,
                "row_total" => $row_total,
                "row_total_incl_tax" => $row_total_incl_tax,
                "row_weight" => $row_weight
            ];
            $this->create($data);

        } 
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return true;
    }

}
