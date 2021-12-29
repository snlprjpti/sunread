<?php

namespace Modules\PaymentAdyen\Repositories;

use Exception;
use Modules\Sales\Entities\Order;
use Illuminate\Support\Facades\DB;
use Modules\Sales\Entities\OrderMeta;
use Modules\Sales\Facades\TransactionLog;
use Modules\Core\Repositories\BaseRepository;

class AdyenPaymentStatusRepository extends BaseRepository
{
    public function __construct(Order $order)
    {
        $this->model = $order;
        $this->model_key = "orders";
        $this->rules = [
            "resultCode" => "required",
            "order_id" => "required|exists:orders,id",
        ];
    }

    public function updateOrderStatus(object $request): ?array
    {
        DB::beginTransaction();
        try
        {
            $this->validateData($request);

            $resultCode = $request->resultCode;
            $orderMetaCartData = OrderMeta::whereOrderId($request->order_id)->whereMetaKey('cart')->pluck('meta_value')->first();
            $cartId = $orderMetaCartData['cart_id'];
            $order = $this->model::find($request->order_id);
            $message = "";
            $status = "pending";
            switch($resultCode){
                case "Authorised":
                    Cart::whereId($cartId)->delete();                    
                    $message = "payment is authorised";
                    $status = "processing";
                    break;
                case "Refused":
                case "Expired":
                    $message = "order cancelled";
                    $status = "cancelled";
                    break;
                default:
                    $message = "something went wrong! order is cancelled";
                    $status = "cancelled";
            }
            $this->update(["status" => $status], $order->id);
            TransactionLog::log($order, $request, "Payment Authorised & Processing", $resultCode);
            $data = [
                "message" => $message,
                "resultCode" => $resultCode
            ];
        }
        catch (Exception $exception)
        {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
        return $data;
    }
}