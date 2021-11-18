<?php

namespace Modules\Sales\Repositories;

use Exception;
use Modules\Sales\Entities\Order;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Modules\Sales\Entities\OrderStatus;
use Modules\Core\Repositories\BaseRepository;
use Modules\Inventory\Jobs\LogCatalogInventoryItem;

class OrderStatusRepository extends BaseRepository
{    
    public function __construct(OrderStatus $orderStatus)
    {
        $this->model = $orderStatus;
        $this->model_key = "order_statuses";
        $this->rules = [
            "status" => "required|exists:order_statuses,slug",
        ];
    }

    public function orderStatus(object $request, int $order_id): void
    {
        try
        {
            $this->validateData($request);
            if ($request->status == 'completed') {
                $order = Order::findOrFail($order_id);
                $order_items = $order->order_items;
                foreach ($order_items as $item) {
                    Product::findOrFail($item->product_id);
                    LogCatalogInventoryItem::dispatchSync([
                        "product_id" => $item->product_id,
                        "website_id" => $item->website_id,
                        "event" => "{$this->model_key}.order_status_updated",
                        "adjustment_type" => "deduction",
                        "order_id" => $request->order_id,
                        "quantity" => $item->qty
                    ]);
                }
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }
}
