<?php

namespace Modules\Sales\Repositories\StoreFront;

use Exception;
use Modules\GeoIp\Facades\GeoIp;
use Modules\Sales\Entities\Order;
use Illuminate\Support\Facades\DB;
use Modules\Cart\Entities\CartItem;
use Modules\CheckOutMethods\Services\BaseCheckOutMethods;
use Modules\CheckOutMethods\Services\CheckOutProcessResolver;
use Modules\Core\Facades\CoreCache;
use Modules\Core\Facades\SiteConfig;
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

    protected $orderItemRepository, $orderAddressRepository, $orderMetaRepository, $check_out_method_helper;

    public function __construct(Order $order,
    OrderItemRepository $orderItemRepository,
    OrderAddressRepository $orderAddressRepository,
    OrderMetaRepository $orderMetaRepository,
    BaseCheckOutMethods $check_out_method_helper)
    {
        $this->model = $order;
        $this->model_key = "orders";
        $this->orderItemRepository = $orderItemRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->orderMetaRepository = $orderMetaRepository;
        $this->check_out_method_helper = $check_out_method_helper;
        $this->rules = [
            "cart_id" => "required|exists:carts,id",
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
            $this->validateRequestData($request);
            $data = $this->orderData($request);
            $coreCache = $this->getCoreCache($request);

            $order = $this->create($data, function ($order) use ($request) {
                $this->orderAddressRepository->store($request, $order);
            });

            $items = CartItem::whereCartId($request->cart_id)->select("product_id", "qty")->get()->toArray();
            foreach ( $items as $order_item ) 
            {
                $order_item_details = $this->getProductItemData($request, $order_item);
                $this->orderItemRepository->store($request, $order, $order_item_details); 
                $this->createOrderTax($order, $order_item_details);
            }
            $this->updateOrderTax($order, $request);
            $this->orderCalculationUpdate($order, $request, $coreCache);
        }
        catch (Exception $exception)
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();        
        return $order;
    }

    private function getProductItemData(object $request, mixed $order_item): mixed
    {
        return $this->getProductDetail($request, $order_item, function ($product) use (&$tax, $request, $order_item) {
            $tax = $this->calculateTax($request, $order_item)->toArray();
            $product_options = $this->getProductOptions($request, $product);
            return array_merge($product_options, $tax, $order_item);
        });
    }

    private function validateRequestData(object $request, ?callable $callback = null): ?array
    {
        try
        {
            $validation = [ "shipping_method" => new MethodValidationRule($request), "payment_method" => new MethodValidationRule($request) ];
            if ($callback) $validation = array_merge($validation, $callback());
            $data = $this->validateData($request, $validation, function ($request) {
                $data = [];
                if (!auth("customer")->id()) {
                    $data = array_merge($data, [
                        "email" => "required|email",
                        "first_name" => "required",
                        "last_name" => "required",
                        "phone" => "required",
                    ]);
                }
                return $data;
            });
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return $data;
    }

    public function orderData(object $request, ?callable $callback = null): array
    {
        try
        {
            $coreCache = $this->getCoreCache($request);
            $currency_code = SiteConfig::fetch("channel_currency", "channel", $coreCache?->channel->id);
            $data = [
                "website_id" => $coreCache?->website->id,
                "store_id" => $coreCache?->store->id,
                "customer_id" => auth("customer")->id(),
                "store_name" => $coreCache?->store->name,
                "is_guest" => auth("customer")->id() ? 0 : 1,
                "currency_code" => $currency_code->code,
                "shipping_method" => $request->shipping_method ?? "proxy_checkout_method",
                "shipping_method_label" => $request->shipping_method_label ?? "proxy_checkout_method",
                "payment_method" => $request->payment_method,
                "payment_method_label" => $request->payment_method_label ?? "cash-on-delivery",
                "status" => "pending",
                "sub_total" => 0.00,
                "sub_total_tax_amount" => 0.00,
                "tax_amount" => 0.00,
                "grand_total" => 0.00,
                "total_items_ordered" => 0.00,
                "total_qty_ordered" => 0.00,
                "customer_email" => $request->email ?? auth("customer")->user()?->email,
                "customer_first_name" => $request->first_name ?? auth("customer")->user()?->first_name,
                "customer_middle_name" => $request->middle_name ?? auth("customer")->user()?->middle_name,
                "customer_last_name" => $request->last_name ?? auth("customer")->user()?->last_name,
                "customer_phone" => $request->phone ?? auth("customer")->user()?->phone,
                "customer_taxvat" => $request->taxvat ?? auth("customer")->user()?->tax_number,
                "customer_ip_address" => GeoIp::requestIp(),
            ];
            if ($callback) $data = array_merge($data, $callback());
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $data;
    }

    public function createOrderTax(object $order, object $order_item_details): void
    {
        $this->storeOrderTax($order, $order_item_details, function ($order_tax, $order_tax_item_details, $rule) {
            $this->storeOrderTaxItem($order_tax, $order_tax_item_details, $rule);
        });
    }

    public function updateOrderTax(object $order, object $request): void
    {
        try
        {
            $order->order_taxes()->get()->map( function ($order_tax) {
                $order_tax_item_amount = $order_tax->order_tax_items()->get()->map( function ($order_item) {
                    return $order_item->amount;
                })->toArray();
                $order_tax->update(["amount" => array_sum($order_tax_item_amount)]);
            });
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
    }

    public function getMethodList(object $request): mixed
    {
        try
        {
            $check_out_resolver = new CheckOutProcessResolver($request);
            $check_out_methods = $check_out_resolver->getCheckOutMethods(callback:function ($methods, $check_out_method) use ($check_out_resolver) {
                if ($check_out_method == "delivery_methods") {
                    $check_custom_handler = $check_out_resolver->allow_custom_checkout("delivery_methods");
                    if ($check_custom_handler) $methods[] = [ "slug" => "proxy_checkout_method", "title" => "Proxy Checkout Method", "custom_logic" => true, "visible" => false ];
                }
                return $methods;
            });
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $check_out_methods;
    }
}
