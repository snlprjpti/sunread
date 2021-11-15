<?php

namespace Modules\Sales\Repositories;

use Exception;
use Illuminate\Bus\Batch;
use Modules\GeoIp\Facades\GeoIp;
use Modules\Sales\Entities\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Modules\Cart\Entities\CartItem;
use Modules\Core\Facades\SiteConfig;
use Modules\Sales\Jobs\OrderTaxesJob;
use Modules\Sales\Jobs\OrderCalculation;
use Modules\Core\Repositories\BaseRepository;
use Modules\Sales\Traits\HasOrderCalculation;
use Modules\Sales\Traits\HasOrderProductDetail;
use Modules\Sales\Repositories\OrderItemRepository;
use Modules\Sales\Repositories\OrderMetaRepository;

class OrderRepository extends BaseRepository
{
    use HasOrderProductDetail, HasOrderCalculation;

    protected $orderItemRepository, $orderAddressRepository, $orderMetaRepository;

    public function __construct(Order $order, OrderItemRepository $orderItemRepository, OrderAddressRepository $orderAddressRepository, OrderMetaRepository $orderMetaRepository)
    {
        $this->model = $order;
        $this->model_key = "orders";
        $this->orderItemRepository = $orderItemRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->orderMetaRepository = $orderMetaRepository;
        $this->rules = [
            "orders" => "array",
            "orders.*.product_id" => "required|exists:products,id",
            "orders.*.qty" => "required|decimal",
            "coupon_code" => "sometimes|exists:coupons,code",
            "shipping_method" => "required",
            "payment_method" => "required"
        ];
    }

    public function store(object $request): mixed
    {
        DB::beginTransaction();
        try
        {
            $this->validateData($request);
            
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
                "shipping_method_label" => $request->shipping_method_label ?? 'online-delivery',
                "payment_method" => $request->payment_method,
                "payment_method_label" => $request->payment_method_label ?? 'stripe',
                "sub_total" => 0.00,
                "sub_total_tax_amount" => 0.00,
                "tax_amount" => 0.00,
                "grand_total" => 0.00,
                "total_items_ordered" => 0.00,
                "total_qty_ordered" => 0.00
            ];

            if ( $data['is_guest'] ) {
                $customer_data = [
                    "customer_email" => $request->customer_details["email"],
                    "customer_first_name" => $request->customer_details["first_name"],
                    "customer_middle_name" => $request->customer_details["middle_name"],
                    "customer_last_name" => $request->customer_details["last_name"],
                    "customer_phone" => $request->customer_details["phone"],
                    "customer_taxvat" => $request->customer_details["taxvat"],
                    "customer_ip_address" => GeoIp::requestIp(),
                    "status" => "pending"
                ];
            }
            $customer_data = isset($data['is_guest']) ? $customer_data : []; 
            $data = array_merge($data, $customer_data);
            
            $order = $this->create($data, function ($order) use ($request) {
                $this->orderAddressRepository->store($request, $order);
                $this->orderMetaRepository->store($request, $order);
            });

            $items = CartItem::whereCartId($request->cart_hash_id)->select("product_id", "qty")->get()->toArray();

            $jobs = [];
            foreach ( $items as $order_item ) 
            {
                $order_item_details = $this->getProductDetail($request, $order_item, function ($product) use ($coreCache, &$tax, $request, $order_item) {
                    $tax = $this->calculateTax($request, $order_item)->toArray();
                    $product_options = $this->getProductOptions($coreCache, $product);
                    return array_merge($product_options, $tax, $order_item);
                });
                $jobs[] = new OrderTaxesJob($order, $order_item_details);
                $order_item = $this->orderItemRepository->store($request, $order, $order_item_details);
            }
            Bus::batch($jobs)->then( function (Batch $batch) use ($order) {

                OrderCalculation::dispatchSync($order);
                // $order->order_taxes->map( function ($order_tax) {
                //     $order_tax_item_amount = $order_tax->order_tax_items->map( function ($order_item) {
                //         return $order_item->amount;
                //     })->toArray();
                //     $order_tax->update(["amount" => array_sum($order_tax_item_amount)]);
                // });
            })->dispatch();
            
            // $this->orderCalculationUpdate($order);

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
