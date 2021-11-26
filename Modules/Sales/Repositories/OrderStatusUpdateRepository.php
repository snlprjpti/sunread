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
            "order_status_name" => "required|exists:order_statuses,name",
            "order_status_id" => "required|exists:order_statuses,id",
            "order_id" => "required|exists:orders,id"
        ];
    }

    public function orderStatus(object $request): void
    {
        DB::beginTransaction();
        try
        {
            $this->validateData($request);

            Order::whereId($request->order_id)->update(["status" => $request->name]);

            $status = $this->fetch($request->order_status_id, ["order_status_state"]);
            if ($status->order_status_state->state == "completed") {
                $order = Order::find($request->order_id);
                $order_items = $order?->order_items;
                foreach ($order_items ?? [] as $item) {
                    $product = Product::find($item->product_id);
                    if (!$product) throw new Exception("product not found", 404);
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
    }
}
