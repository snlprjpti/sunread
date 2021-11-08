<?php

namespace Modules\Sales\Repositories;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Modules\Sales\Entities\Order;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\BaseRepository;
use Modules\Coupon\Entities\Coupon;
use Modules\Tax\Facades\TaxPrice;

class OrderRepository extends BaseRepository
{
    protected $discount_percent;

    public function __construct(Order $order)
    {
        $this->model = $order;
        $this->model_key = "orders";
        $this->rules = [
            "orders" => "array",
            "orders.*.product_id" => "required|exists:products,id",
            "orders.*.qty" => "required|decimal",
            "coupon_code" => "sometimes|exists:coupons,code"
        ];
    }

    public function store(object $request): mixed
    {
        DB::beginTransaction();
        try
        {
            $this->validateData($request);
            $this->calculate($request);
            

            // dd($coupon);
            dd($request->all());
            // validate params
            
            
            
            
        } 
        catch ( Exception $exception )
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();        
        return  '';
    }

    public function getTax(object $request): ?object
    {
        try
        {
            $data = '';
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function calculate(object $request): ?object
    {
        // Discount 
        if ($request->coupon_code) {
            $coupon = Coupon::whereCode($request->coupon_code)->publiclyAvailable()->first();
            if (!$coupon) throw new Exception("Coupon Expired");         
            $this->discount_percent = $coupon->discount_percent;          
        }

        $customer_tax = TaxPrice::calculate($request, 1000, customer_tax_group_id:1);
        dd($customer_tax);

        



        // Tax
    }



}
