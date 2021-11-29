<?php

namespace Modules\Sales\Services;
use Exception;
use Modules\GeoIp\Facades\GeoIp;
use Modules\Sales\Entities\OrderTransactionLog;

class TransactionLog {

    public function log(object $order, mixed $server_request, mixed $server_response, string|int $response_code): bool
    {
        try
        {
            $data = [
                "order_id" => $order->id,
                "amount" => $order->grand_total,
                "currency" => $order->currency_code,
                "ip_address" => GeoIp::requestIp(),
                "request" => $server_request,
                "response" => $server_response,
                "response_code" => $response_code
            ];

            OrderTransactionLog::create($data);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return true;
    }
}
