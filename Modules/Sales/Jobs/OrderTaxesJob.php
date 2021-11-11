<?php

namespace Modules\Sales\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Modules\Sales\Entities\OrderTax;
use Modules\Sales\Entities\OrderTaxItem;

class OrderTaxesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $items, $order;

    public function __construct(object $order, object $items)
    {
        $this->items = $items;
        $this->order = $order;

    }

    public function handle(): void
    {
        try
        {
            foreach ( $this->items as $item ) 
            {
                   
            }
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
    }

    public function storeOrderTax(object $order, object $order_item_details, ?callable $callback = null): object
    {
        DB::beginTransaction();
        try
        {
            foreach ($order_item_details->rules as $rule)
            {
                $percent = array_sum($rule->rates->pluck("tax_rate")->toArray());
                $amount = array_sum($rule->rates->pluck("tax_rate_value")->toArray());
                
                $data = [
                    "order_id" => $order->id,
                    "code" => $rule->name,
                    "title" => $rule->name,
                    "percent" => $percent,
                    "amount" => $amount
                ];

                $order_tax = OrderTax::create($data);
                if ($callback) $callback($order_tax, $rule);
            }
        }
        catch ( Exception $exception )
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();        
        return $order;
    }

    public function storeOrderTaxItem(object $order_tax, object $order_item, mixed $rule, ?callable $callback = null): void
    {
        DB::beginTransaction();
        try
        {
            $data = [];
            foreach ($rule->rates as $rate) 
            {
                $data[] = [
                    "tax_id" => $order_tax->id,
                    "item_id" => $order_item->product_id,
                    "tax_percent" => $rate->tax_rate,
                    "amount" => $rate->tax_rate_value,
                    "tax_item_type" => "product"
                ];
            }

            if ($callback) $data = array_merge($data, $callback());
            
            OrderTaxItem::insert($data);  
        }
        catch ( Exception $exception )
        {
            DB::rollback();
            throw $exception;
        }
        DB::commit(); 
    }
}
