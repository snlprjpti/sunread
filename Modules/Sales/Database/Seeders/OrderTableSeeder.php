<?php

namespace Modules\Sales\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Store;
use Modules\Core\Entities\Website;

class OrderTableSeeder extends Seeder
{
    protected $fillable = [];

    public function run():  void
    {
        $data = [
            "website_id" => Website::first()?->id,
            "store_id" => Store::first()?->id,
            "store_name" => Store::first()?->name,
            "shipping_method" => "FedEX",
            "shipping_method_label" => "fedex",
            "payment_method" => "Stripe",
            "payment_method_label" => "stripe",
            "currency_code" => "EUR",
            "sub_total" => 2000.00,
            "sub_total_tax_amount" => 1,
            "discount_amount" => 1,
            "discount_amount_tax" => 1,  
            "coupon_code" => "", 
            "discount_amount" => "", 
            "discount_amount_tax" => "",
            "shipping_amount" => "", 
            "shipping_amount_tax" => "", 
            "sub_total" => "", 
            "sub_total_tax_amount" => "", 
            "tax_amount" => "", 
            "grand_total" => "",  
            "total_items_ordered" => 1, 
            "total_qty_ordered" => 10, 
            "customer_email" => "joe@example.com", 
            "customer_first_name" => "Joe", 
            "customer_last_name" => "Griffen", 
            "customer_phone" => "+977 9846325415", 
            "customer_taxvat" => "", 
            "customer_ip_address" => "127.0.0.1", 
            "status" => "success", 
        ];

        DB::table("orders")->insert($data);
    }
}
