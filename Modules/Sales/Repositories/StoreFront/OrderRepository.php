<?php

namespace Modules\Sales\Repositories\StoreFront;

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
use Modules\Sales\Rules\MethodValidationRule;
use Modules\Sales\Traits\HasOrderCalculation;
use Modules\Sales\Traits\HasOrderProductDetail;
use Modules\Sales\Repositories\OrderMetaRepository;
use Modules\Sales\Repositories\OrderAddressRepository;
use Modules\Sales\Repositories\StoreFront\OrderItemRepository;

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
            "cart_id" => "required|exists:carts,id",
            // "orders" => "array",
            // "orders.*.product_id" => "required|exists:products,id",
            // "orders.*.qty" => "required|decimal",
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
            if (!auth("customer")->id()) {
                $guestValidation = [
                    "email" => "required|email",
                    "first_name" => "required",
                    "last_name" => "required",
                    "phone" => "required",
                ];
            }
            $validation = [
                "shipping_method" => new MethodValidationRule($request),
                "payment_method" => new MethodValidationRule($request) 
            ];
            $guestValidation = isset($guestValidation) ? $guestValidation : [];
            $validate = array_merge($guestValidation, $validation);
            $this->validateData($request, $validate);
            $coreCache = $this->getCoreCache($request);
            $currency_code = SiteConfig::fetch('channel_currency', 'channel', $coreCache?->channel->id);
            $data = [
                "website_id" => $coreCache?->website->id,
                "store_id" => $coreCache?->store->id,
                "customer_id" => auth("customer")->id(),
                "store_name" => $coreCache?->store->name,
                "is_guest" => auth("customer")->id() ? 0 : 1,
                "currency_code" => $currency_code->code,
                "shipping_method" => $request->shipping_method,
                "shipping_method_label" => $request->shipping_method_label ?? 'free-delivery',
                "payment_method" => $request->payment_method,
                "payment_method_label" => $request->payment_method_label ?? 'cash-on-delivery',
                "status" => "pending",
                "sub_total" => 0.00,
                "sub_total_tax_amount" => 0.00,
                "tax_amount" => 0.00,
                "grand_total" => 0.00,
                "total_items_ordered" => 0.00,
                "total_qty_ordered" => 0.00
            ];

            $customer_data = [
                    "customer_email" => $request->email ?? auth('customer')->user()?->email,
                    "customer_first_name" => $request->first_name ?? auth('customer')->user()?->first_name,
                    "customer_middle_name" => $request->middle_name ?? auth('customer')->user()?->middle_name,
                    "customer_last_name" => $request->last_name ?? auth('customer')->user()?->last_name,
                    "customer_phone" => $request->phone ?? auth('customer')->user()?->phone,
                    "customer_taxvat" => $request->taxvat ?? auth('customer')->user()?->tax_number,
                    "customer_ip_address" => GeoIp::requestIp(),
            ];

            $data = array_merge($data, $customer_data);
            
            $order = $this->create($data, function ($order) use ($request) {
                $this->orderAddressRepository->store($request, $order);
                $this->orderMetaRepository->store($request, $order);
                // $order->load(["order_items", "order_taxes.order_items", "order_metas", "order_addresses"]);
            });

            $items = CartItem::whereCartId($request->cart_id)->select("product_id", "qty")->get()->toArray();
            // $jobs = [];
            foreach ( $items as $order_item ) 
            {
                $order_item_details = $this->getProductDetail($request, $order_item, function ($product) use ($coreCache, &$tax, $request, $order_item) {
                    $tax = $this->calculateTax($request, $order_item)->toArray();
                    $product_options = $this->getProductOptions($coreCache, $product);
                    return array_merge($product_options, $tax, $order_item);
                });
                // $jobs[] = new OrderTaxesJob($order, $order_item_details);
                $order_item = $this->orderItemRepository->store($request, $order, $order_item_details); 
                $this->createOrderTax($order, $order_item_details);
            }
            $this->updateOrderTax($order, $request, $coreCache);
        } 
        catch (Exception $exception)
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();        
        return $order;
    }

    public function createOrderTax(object $order, object $order_item_details): void
    {
        $this->storeOrderTax($order, $order_item_details, function ($order_tax, $order_tax_item_details, $rule) {
            $this->storeOrderTaxItem($order_tax, $order_tax_item_details, $rule);
        });
    }

    public function updateOrderTax(object $order, object $request, object $coreCache): void
    {
        $order->order_taxes->map( function ($order_tax) {
            $order_tax_item_amount = $order_tax->order_tax_items->map( function ($order_item) {
                return $order_item->amount;
            })->toArray();
            $order_tax->update(["amount" => array_sum($order_tax_item_amount)]);
        });

        $this->orderCalculationUpdate($order, $request, $coreCache);
    }
}
