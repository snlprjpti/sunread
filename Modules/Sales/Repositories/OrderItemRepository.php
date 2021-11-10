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
            // $this->validateData($request);
            $coreCache = $this->getCoreCache($request);

            $data = [
                "website_id" => $coreCache?->website->id,
                "store_id" => $coreCache?->store->id,
                "product_id" => $order_item_details->id,
                "order_id" => $order->id,
                "product_options" => $order_item_details->configurable_attribute_options,
                "product_type" => $order_item_details->type,
                "sku" => $order_item_details->sku,
                "name" => $order_item_details->name,
                "weight" => $order_item_details->weight,
                "qty" => $order_item_details->qty,
                "cost" => $order_item_details->cost,
                "price" => $order_item_details->price,
                "row_total" => $order_item_details->price * $order_item_details->qty,
                "row_weight" => $order_item_details->weight * $order_item_details->qty,
            ];

            $this->create($data);

        } 
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return  true;
    }

}
