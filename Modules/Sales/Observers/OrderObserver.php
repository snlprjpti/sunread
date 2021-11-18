<?php

namespace Modules\Sales\Observers;

use Modules\Core\Facades\Audit;
use Modules\Sales\Entities\Order;
use Modules\Sales\Facades\TransactionLog;

class OrderObserver
{
    public function created(Order $order)
    {
        Audit::log($order, __FUNCTION__);
        // TransactionLog::log($order);
    }

    public function updated(Order $order)
    {
        Audit::log($order, __FUNCTION__);
    }

    public function deleted(Order $order)
    {
        Audit::log($order, __FUNCTION__);
    }
}
