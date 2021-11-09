<?php

namespace Modules\Sales\Repositories;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use Modules\Sales\Entities\Order;
use Illuminate\Support\Facades\DB;
use Modules\Core\Facades\SiteConfig;
use Modules\Core\Repositories\BaseRepository;
use Modules\Coupon\Entities\Coupon;
use Modules\Customer\Entities\Customer;
use Modules\Tax\Facades\TaxPrice;
use Modules\GeoIp\Facades\GeoIp;
use Modules\Product\Entities\Product;

class OrderRepository extends BaseRepository
{
    protected $discount_percent, $product, $total_tax_percent, $row_quantity;

    protected array $product_attribute_slug = [
        "name",
        "tax_class_id",
        "cost",
        "price",
        "weight"
    ];

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
            foreach ( $request->orders as $order ) 
            {
                $asd = $this->calculateTax($request, $order);
            }      
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
            $order = $this->create(array_merge($data, $customer_data));
        } 
        catch (Exception $exception)
        {
            DB::rollback();
            throw $exception;
        }

        DB::commit();        
        return $order;
    }

    public function calculateTax(object $request, array $order): ?object
    {
        try
        {
            $get_zip_code = collect($request->get("address"))->where("address_type", "shipping")->first();
            $zip_code = isset($get_zip_code['postal_code']) ? $get_zip_code['postal_code'] : null;
            $product = Product::whereId($order["product_id"])->with(["product_attributes", "catalog_inventories", "attribute_configurable_products", "attribute_options_child_products"])->first();
            $product_data = $this->getProductDetail($request, $product);
            
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

    public function getProductDetail(object $request, object $product): ?object
    {
        try
        {
            $coreCache = $this->getCoreCache($request);
            foreach ( $this->product_attribute_slug as $slug )
            {
                $data[$slug] = $product->value([
                    "scope" => "store",
                    "scope_id" => $coreCache->store->id,
                    "attribute_slug" => $slug
                ]);
            }
        }
        catch (Exception $exception)
        {
            throw $exception;
        }
        return (object) $data;
    }

}
