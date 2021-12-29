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
    }

    public function updateAdyenPaymentStatus(object $request): ?array
    {
        DB::beginTransaction();
        try
        {
            $resultCode = $request->resultCode;
            $orderMetaCartData = OrderMeta::whereOrderId($request->order_id)->whereMetaKey('cart')->pluck('meta_value')->first();
            $cartId = $orderMetaCartData['cart_id'];
            $order = $this->model::find($request->order_id);
            $message = "";
            $status = "pending";
            switch($resultCode){
                case "Authorised":
                    Cart::where('id', $cartId)->delete();                    
                    $message = "payment is authorised";
                    $status = "processing";
                    break;
                case "Refused":
                    $message = "payment is refused";
                    $status = "cancelled";
                    break;
                case "Expired":
                    $message = "payment process expired";
                    $status = "cancelled";
                    break;
                default:
                    $message = "something went wrong";
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