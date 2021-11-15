<?php

namespace Modules\Sales\Repositories;

use Exception;
use Modules\Sales\Entities\OrderMeta;
use Modules\Core\Repositories\BaseRepository;

class OrderMetaRepository extends BaseRepository
{    
    public function __construct(OrderMeta $orderMeta)
    {
        $this->model = $orderMeta;
        $this->model_key = "order_metas";
        $this->rules = [
            "shipping_method" => "required",
            "payment_method" => "required",
        ];
    }

    public function store(object $request, object $order): void
    {
        try
        {
            $this->validateData($request);

            $this->storeShippingMethod($request, $order);
            $this->storePaymentMethod($request, $order);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }

    private function storeShippingMethod(object $request, object $order): void
    {
        $data = [
            "order_id" => $order->id,
            "meta_key" => $request->shipping_method,
            "meta_value" => 0.00
        ];
        $this->create($data);
    }

    private function storePaymentMethod(object $request, object $order): void
    {
        $data = [
            "order_id" => $order->id,
            "meta_key" => $request->payment_method,
            "meta_value" => 0.00
        ];
        $this->create($data);
    }
}
