<?php

namespace Modules\Sales\Repositories;

use Exception;
use Modules\Sales\Entities\Order;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\BaseRepository;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $order)
    {
        $this->model = $order;
        $this->model_key = "orders";
    }

    public function store(object $request): mixed
    {
        DB::beginTransaction();
        try
        {
            
        } 
        catch ( Exception $exception )
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();        
        return  '';
    }

}
