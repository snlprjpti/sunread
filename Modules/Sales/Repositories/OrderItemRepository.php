<?php

namespace Modules\Sales\Repositories;

use Exception;
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

    public function store(object $request, object $order, object $orider_item_details): mixed
    {
        try
        {
            // $this->validateData($request);
            $coreCache = $this->getCoreCache($request);
            foreach ($request->orders as $item) {
                $data = [
                    "website_id" => $coreCache?->website->id,
                    "store_id" => $coreCache?->store->id,
                    "product_id" => $item->product_id,
                    "order_id" => $order->id,
                    "product_options" => $item->product_options,
                    "product_type" => $product->parent_id ? "configurable" : "simple",
                    "sku" => $item->sku,
                    "name" => $item->name,
                    "weight" => $item->weight,
                    "qty" => $item->qty,
                    "price" => 0.00,
                ];

                $this->create($data);
            }

        } 
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return  true;
    }

}
