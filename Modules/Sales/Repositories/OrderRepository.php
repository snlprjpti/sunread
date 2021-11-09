<?php

namespace Modules\Sales\Repositories;

use Exception;
use Modules\Sales\Entities\Order;
use Illuminate\Support\Facades\DB;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Repositories\BaseRepository;
use Modules\Coupon\Entities\Coupon;
use Modules\Customer\Entities\Customer;
use Modules\Tax\Facades\TaxPrice;
use Modules\GeoIp\Facades\GeoIp;
use Modules\Product\Entities\Product;
use Modules\Sales\Repositories\OrderItemRepository;

class OrderRepository extends BaseRepository
{
    protected $orderItemRepository, $orderAddressRepository;

    protected $discount_percent, $product, $total_tax_percent, $row_quantity, $product_data;

    protected array $product_attribute_slug = [
        "name",
        "tax_class_id",
        "cost",
        "price",
        "weight"
    ];

    public function __construct(Order $order, OrderItemRepository $orderItemRepository, OrderAddressRepository $orderAddressRepository)
    {
        $this->model = $order;
        $this->model_key = "orders";
        $this->rules = [
            "orders" => "array",
            "orders.*.product_id" => "required|exists:products,id",
            "orders.*.qty" => "required|decimal",
            "coupon_code" => "sometimes|exists:coupons,code"
        ];

        $this->orderItemRepository = $orderItemRepository;
        $this->orderAddressRepository = $orderAddressRepository;
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
                "shipping_method" => $request->shipping_method ?? 'online-delivery',
                "shipping_method_label" => $request->shipping_method_label ?? 'online-delivery',
                "payment_method" => $request->payment_method ?? 'stripe',
                "payment_method_label" => $request->payment_method_label ?? 'stripe',
                "sub_total" => $request->sub_total ?? 0.00,
                "sub_total_tax_amount" => $request->sub_total_tax_amount ?? 0.00,
                "tax_amount" => $request->tax_amount ?? 0.00,
                "grand_total" => $request->grand_total ?? 0.00,
                "total_items_ordered" => $request->total_items_ordered ?? 0.00,
                "total_qty_ordered" => $request->total_qty_ordered ?? 0.00
            ];

            if ( $data['is_guest'] ) {
                $customer_data = [
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
            $customer_data = isset($data['is_guest']) ? $customer_data : []; 
            $data = array_merge($data, $customer_data);

            // $order = $this->create($data, function ($order) use ($request) {
            //     $this->orderAddressRepository->store($request, $order);
            // });

            foreach ( $request->orders as $order ) 
            {
                $tax = $this->calculateTax($request, $order)->toArray();
                $product_detail = (array) $this->getProductDetail($request, $order);
                $order_item_details = (object) array_merge($order, $tax, $product_detail);
                $this->orderItemRepository->store($request, $order, $order_item_details);
            }  
            dd('asd');
            
        } 
        catch (Exception $exception)
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();        
        return $order;
    }

    public function getProductDetail(object $request, array $order, ?callable $callback = null): ?object
    {
        try
        {
            $coreCache = $this->getCoreCache($request);
            $with = [
                "product_attributes",
                "catalog_inventories",
                "attribute_options_child_products"
            ];
            $product = Product::whereId($order["product_id"])->with($with)->first();
            foreach ( $this->product_attribute_slug as $slug )
            {
                $data[$slug] = $product->value([
                    "scope" => "store",
                    "scope_id" => $coreCache->store->id,
                    "attribute_slug" => $slug
                ]);
            }
            $product_data = [ 
                "sku" => $product->sku,
                "type" => $product->type,
                "configurable_attribute_options" => null
            ];
            $data = array_merge($data, $product_data);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return (object) $data;
    }

    public function calculateTax(object $request, array $order): ?object
    {
        try
        {
            $get_zip_code = collect($request->get("address"))->where("address_type", "shipping")->first();
            $zip_code = isset($get_zip_code['postal_code']) ? $get_zip_code['postal_code'] : null;

            $product_data = $this->getProductDetail($request, $order);

            if ( auth("customer")->id() ) {
                $customer = Customer::whereId(auth("customer")->id())->with(["group.tax_group"])->first();
                $customer_tax_group_id = $customer?->group?->tax_group?->id;
                $customer_tax = TaxPrice::calculate($request, $product_data->price, customer_tax_group_id:$customer_tax_group_id, zip_code:$zip_code);
            }
            else $customer_tax = TaxPrice::calculate($request, $product_data->price, $product_data->tax_class_id?->id, zip_code:$zip_code);
        }
        catch (Exception $exception)
        {
            throw $exception;
        }

        return $customer_tax;
    }

    public function calculateDiscount(object $request): ?object
    {
        if ($request->coupon_code) {
            $coupon = Coupon::whereCode($request->coupon_code)->publiclyAvailable()->first();
            if (!$coupon) throw new Exception("Coupon Expired");  
            $this->discount_percent = $coupon->discount_percent;          
        }

        return $coupon; 
    }

}
