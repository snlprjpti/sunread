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
use Modules\Sales\Traits\HasOrderProductDetail;

class OrderTaxesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasOrderProductDetail;

    protected $order_item_details, $order;

    public function __construct(object $order, object $order_item_details)
    {
        $this->order_item_details = $order_item_details;
        $this->order = $order;
    }

    public function handle(): void
    {
        try
        {
            $this->storeOrderTax(function ($order_tax, $order_item_details, $rule) {

                $order_tax_amount = $order_tax->amount;
                $order_item_tax = $this->storeOrderTaxItem($order_tax, $order_item_details, $rule);
                $order_item_tax_amount = $order_item_tax->amount;
                $order_tax_amount = ($order_item_tax_amount + $order_tax_amount);
                $order_tax->update(["amount" => $order_tax_amount]);
            });
        }
        catch(Exception $exception)
        {
            throw $exception;
        }
    }

    public function storeOrderTax(?callable $callback = null): void
    {
        DB::beginTransaction();
        try
        {
            foreach ($this->order_item_details->rules as $rule)
            {
                $amount = (($this->order_item_details->tax_rate_percent/100) * $this->order_item_details->price * $this->order_item_details->qty);
                $data = [
                    "order_id" => $this->order->id,
                    "code" => \Str::slug($rule->name),
                    "title" => $rule->name,
                    "percent" => $this->order_item_details->tax_rate_percent,
                    "amount" => $amount
                ];
                $match = $data;
                unset($match["title"], $match["percent"], $match["amount"]);
                $order_tax = OrderTax::updateOrCreate($match, $data);
                if ($callback) $callback($order_tax, $this->order_item_details, $rule);
            }
        }
        catch ( Exception $exception )
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();
    }

    public function storeOrderTaxItem(object $order_tax, object $order_item_details, mixed $rule): object
    {
        DB::beginTransaction();
        try
        {

            $data = [
                "tax_id" => $order_tax->id,
                "item_id" => $order_item_details->product_id,
                "tax_percent" => $order_tax->percent,
                "amount" => $rule->rates?->pluck("tax_rate_value")->first(),
                "tax_item_type" => "product"
            ];            
            $order_tax_item = OrderTaxItem::create($data);  
        }
        catch ( Exception $exception )
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit(); 
        return $order_tax_item;
    }
}
