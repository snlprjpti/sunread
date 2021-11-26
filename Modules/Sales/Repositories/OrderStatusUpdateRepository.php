<?php

namespace Modules\Sales\Repositories;

use Exception;
use Modules\Sales\Entities\Order;
use Illuminate\Support\Facades\DB;
use Modules\Product\Entities\Product;
use Modules\Sales\Entities\OrderStatus;
use Modules\Core\Repositories\BaseRepository;
use Modules\Inventory\Jobs\LogCatalogInventoryItem;

class OrderStatusUpdateRepository extends BaseRepository
{    
    public function __construct(OrderStatus $orderStatus)
    {
        $this->model = $orderStatus;
        $this->model_key = "order_statuses";
        $this->rules = [
            "order_status_id" => "required|exists:order_statuses,id",
            "order_id" => "required|exists:orders,id"
        ];
    }

    public function orderStatus(object $request): object
    {
        DB::beginTransaction();
        try
        {
            $this->validateData($request);

            $status = $this->fetch($request->order_status_id, ["order_status_state"]);

            $order = Order::find($request->order_id);

            $order->update(["status" => $status->name]);

            if ($status->order_status_state->state == "completed") {
                $order_items = $order->order_items;
                foreach ($order_items->count() > 0 as $item) {
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
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
        return $order;
    }
}
