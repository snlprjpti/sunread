<?php

namespace Modules\Sales\Repositories;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Modules\Sales\Entities\Order;
use Illuminate\Support\Facades\DB;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Repositories\BaseRepository;
use Modules\GeoIp\Facades\GeoIp;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $order)
    {
        $this->model = $order;
        $this->model_key = "orders";
        $this->rules = [
            //website_id
            /**
             * website_id
             * store_id
             * product_id
             * shipping_method_id
             * payment_method_id
             * currency_code
             * coupon_code
             * qty
             * ///is guest 
             * customer_name
             * customer_phone
             * customer_taxVat
             * // customer details
             * 
             */
            "orders" => "required|array",
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
            $coreCache = $this->getCoreCache($request);
            $currency_code = SiteConfig::fetch('channel_currency', 'channel', $coreCache?->channel->id);
            $data = [
                "website_id" => $coreCache?->website->id,
                "store_id" => $coreCache?->store->id,
                "customer_id" => auth("customer")->id(),
                "store_name" => $coreCache?->store->name,
                "is_guest" => auth("customer")->id() ? 0 : 1,
                "billing_address_id" => $request->billing_address_id,
                "shipping_address_id" => $request->shipping_address_id,
                "currency_code" => $currency_code->code,
                "shipping_method" => $request->shipping_method,
                "shipping_method_label" => $request->shipping_method_label,
                "payment_method" => $request->payment_method,
                "payment_method_label" => $request->payment_method_label,
                "sub_total" => $request->sub_total ?? 0,
                "sub_total_tax_amount" => $request->sub_total_tax_amount ?? 0,
                "tax_amount" => $request->tax_amount ?? 0,
                "grand_total" => $request->grand_total ?? 0,
                "total_items_ordered" => $request->total_items_ordered ?? 0,
                "total_qty_ordered" => $request->total_qty_ordered ?? 0
            ];

            if ($data['is_guest']) {
                $data1 = [
                    "customer_email" => $request->customer_details['email'],
                    "customer_first_name" => $request->customer_details['first_name'],
                    "customer_middle_name" => $request->customer_details['middle_name'],
                    "customer_last_name" => $request->customer_details['last_name'],
                    "customer_phone" => $request->customer_details['phone'],
                    "customer_taxvat" => $request->customer_details['taxvat'],
                    "customer_ip_address" => GeoIp::requestIp(),
                    "status" => "pending"
                ];
            }
            $order = $this->create(array_merge($data, $data1));
        } 
        catch (Exception $exception)
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();        
        return $order;
    }



}
