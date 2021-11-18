<?php

namespace Modules\Sales\Repositories\StoreFront;

use Exception;
use Modules\Product\Entities\Product;
use Modules\Sales\Entities\OrderItem;
use Modules\Core\Repositories\BaseRepository;
use Modules\Sales\Traits\HasOrderCalculation;

class OrderItemRepository extends BaseRepository
{   
    use HasOrderCalculation;
    
    protected $product;

    public function __construct(OrderItem $orderItem, Product $product)
    {
        $this->model = $orderItem;
        $this->model_key = "order_items";
        $this->product = $product;
    }

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
           
           $order_item = $this->create($data); 
        } 
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return $order_item;
    }
}
