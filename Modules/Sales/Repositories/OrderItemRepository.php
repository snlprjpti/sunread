<?php

namespace Modules\Sales\Repositories;

use Exception;
use Modules\Sales\Entities\OrderItem;
use Modules\Core\Repositories\BaseRepository;

class OrderItemRepository extends BaseRepository
{
    public function __construct(OrderItem $orderItem)
    {
        $this->model = $orderItem;
        $this->model_key = "order_items";
    }

    public function store(object $request): mixed
    {
        try
        {
            $this->validateData($request);
        } 
        catch ( Exception $exception )
        {
            throw $exception;
        }

        return  '';
    }

}
